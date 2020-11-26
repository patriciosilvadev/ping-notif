<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class SuperAdminBillingController extends Controller
{
    public function index()
    {
        $billing = DB::table('tb_billing')
            ->select('wa_account.number', 'users.name', 'tb_billing.id_billing', 'tb_billing.masa_aktif', DB::raw('DATE(tb_billing.created_at) AS awal'))
            ->join('users', 'tb_billing.id_user', '=', 'users.id')
            ->join('wa_account', 'tb_billing.id_wa', '=', 'wa_account.id')
            ->orderBy('tb_billing.id_billing', 'desc')
            ->get();


        // $pengguna = DB::table('users')
        //     ->select('users.name', DB::raw('DATE(users.created_at) as daftar'), DB::raw('COUNT(wa_account.user_id) as jumlah_wa'))
        //     ->leftJoin('wa_account', 'users.id', '=', 'wa_account.user_id')
        //     ->groupBy('users.name')
        //     ->groupBy('daftar')
        //     ->orderBy('user.name', 'asc')
        //     ->get();

        return view('superadmin/billing/billing', compact('billing'));
    }

    public function grafik_data()
    {
        $data_graf = [];

        for ($i = 0; $i < 12; $i++) {
            $data = DB::table('history_billing')
                ->select(DB::raw('MONTH(created_at) AS bulan, SUM(nominal) AS jumlah_bulanan'))
                ->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(CURDATE() - INTERVAL ' . $i . ' MONTH)'))
                ->where('status', 'lunas')
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->get();

            if ($data->count() > 0) {
                $data_graf[$i]["bulan"] = date('Y-') . $data[0]->bulan;
                $data_graf[$i]["jumlah_bulanan"] = $data[0]->jumlah_bulanan;
            } else {
                $data_graf[$i]["bulan"] = date('Y-m', strtotime('-' . $i . 'month'));
                $data_graf[$i]["jumlah_bulanan"] = 0;
            }
        }

        return json_encode($data_graf, true);
    }
}