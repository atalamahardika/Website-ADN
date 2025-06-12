<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldName = preg_replace('/\s+/', '-', strtolower($user->getOriginal('name')));
        $newName = preg_replace('/\s+/', '-', strtolower($request->name));
        $role = $user->role ?? 'default';

        $oldPhotoPath = public_path($user->profile_photo);
        $oldFolder = public_path("images/profile-user/{$role}/{$oldName}");
        $newFolder = public_path("images/profile-user/{$role}/{$newName}");

        // Update field dari form (nama, email)
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // CASE 1: Jika upload foto baru
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && file_exists($oldPhotoPath)) {
                unlink($oldPhotoPath); // hapus foto lama
            }

            $file = $request->file('profile_photo');
            $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();

            if (!file_exists($newFolder)) {
                mkdir($newFolder, 0755, true);
            }

            $file->move($newFolder, $filename);
            $user->profile_photo = "images/profile-user/{$role}/{$newName}/{$filename}";
        }

        // CASE 2: Nama berubah tapi tidak upload foto
        elseif ($oldName !== $newName && $user->profile_photo && file_exists($oldPhotoPath)) {
            if (!file_exists($newFolder)) {
                mkdir($newFolder, 0755, true);
            }

            $filename = basename($oldPhotoPath);
            $newPhotoPath = $newFolder . '/' . $filename;

            rename($oldPhotoPath, $newPhotoPath);
            $user->profile_photo = "images/profile-user/{$role}/{$newName}/{$filename}";
        }

        $user->save();

        // Hapus folder lama jika nama berubah dan foldernya ada
        if ($oldName !== $newName && is_dir($oldFolder)) {
            rmdir($oldFolder);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }



    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
