<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function getProvinces()
    {
        return response()->json(
            DB::table('provinces')
                ->select('prov_id as id', 'prov_name as name')
                ->orderBy('prov_name')
                ->get()
        );
    }

    public function getCities($prov_id)
    {
        return response()->json(
            DB::table('cities')
                ->where('prov_id', $prov_id)
                ->select('city_id as id', 'city_name as name')
                ->orderBy('city_name')
                ->get()
        );
    }

    public function getDistricts($city_id)
    {
        return response()->json(
            DB::table('districts')
                ->where('city_id', $city_id)
                ->select('dis_id as id', 'dis_name as name')
                ->orderBy('dis_name')
                ->get()
        );
    }

    public function getSubdistricts($dis_id)
    {
        return response()->json(
            DB::table('subdistricts')
                ->where('dis_id', $dis_id)
                ->select('subdis_id as id', 'subdis_name as name')
                ->orderBy('subdis_name')
                ->get()
        );
    }

    public function getPostalCode($subdis_id)
    {
        $postal = DB::table('postalcode')
            ->where('subdis_id', $subdis_id)
            ->value('postal_code');

        return response()->json(['postal_code' => $postal]);
    }
}
