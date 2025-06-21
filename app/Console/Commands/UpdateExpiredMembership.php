<?php

namespace App\Console\Commands;

use App\Models\Membership;
use App\Models\MembershipPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateExpiredMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired membership status to inactive based on payment active_until dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired membership update process...');

        // Ambil semua membership yang statusnya active
        $activeMemberships = Membership::where('status', 'active')
            ->with([
                'payments' => function ($query) {
                    $query->where('status', 'approved')
                        ->orderBy('active_until', 'desc');
                }
            ])
            ->get();

        $expiredCount = 0;
        $checkedCount = 0;

        foreach ($activeMemberships as $membership) {
            $checkedCount++;

            // Cek apakah membership sudah expired berdasarkan payment
            $activePayment = $membership->getActivePayment();

            if (!$activePayment) {
                // Jika tidak ada payment yang aktif, set membership jadi inactive
                $membership->status = 'inactive';
                $membership->save();
                $expiredCount++;

                $this->line("Membership ID {$membership->id} - No active payment found, set to inactive");
                continue;
            }

            // Cek apakah payment sudah expired
            if ($activePayment->isExpired()) {
                $membership->status = 'inactive';
                $membership->save();
                $expiredCount++;

                $this->line("Membership ID {$membership->id} - Payment expired on {$activePayment->active_until}, set to inactive");
            }
        }

        // Update semua membership yang memiliki payment expired tapi belum diupdate statusnya
        $expiredPayments = MembershipPayment::where('status', 'approved')
            ->whereDate('active_until', '<', Carbon::now()->toDateString())
            ->whereHas('membership', function ($query) {
                $query->where('status', 'active');
            })
            ->with('membership')
            ->get();

        foreach ($expiredPayments as $payment) {
            $membership = $payment->membership;

            // Double check apakah ada payment lain yang masih aktif
            $stillActivePayment = $membership->payments()
                ->where('status', 'approved')
                ->whereDate('active_until', '>=', Carbon::now()->toDateString())
                ->exists();

            if (!$stillActivePayment) {
                $membership->status = 'inactive';
                $membership->save();
                $expiredCount++;

                $this->line("Membership ID {$membership->id} - All payments expired, set to inactive");
            }
        }

        $this->info("Process completed!");
        $this->info("Total memberships checked: {$checkedCount}");
        $this->info("Total memberships set to inactive: {$expiredCount}");

        // Log summary
        if ($expiredCount > 0) {
            $this->warn("Updated {$expiredCount} expired memberships to inactive status.");
        } else {
            $this->info("No expired memberships found.");
        }

        return Command::SUCCESS;
    }
}
