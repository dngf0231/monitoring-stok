<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\User;
use Illuminate\Database\Seeder;

class BarangKeluarSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first()
            ?? User::where('email', 'admin@example.com')->first();

        if (!$user) {
            return;
        }

        $rows = [
            ['kode' => 'ATK-001', 'jumlah' => 10, 'tanggal' => '2026-07-10', 'status' => 'approved'],
            ['kode' => 'ATK-003', 'jumlah' => 8, 'tanggal' => '2026-07-12', 'status' => 'pending'],
            ['kode' => 'ATK-006', 'jumlah' => 2, 'tanggal' => '2026-07-15', 'status' => 'rejected'],
        ];

        foreach ($rows as $row) {
            $barang = Barang::where('kode', $row['kode'])->first();

            if (!$barang) {
                continue;
            }

            BarangKeluar::updateOrCreate(
                [
                    'barang_id' => $barang->id,
                    'user_id' => $user->id,
                    'tanggal' => $row['tanggal'],
                    'jumlah' => $row['jumlah'],
                ],
                [
                    'barang_id' => $barang->id,
                    'user_id' => $user->id,
                    'tanggal' => $row['tanggal'],
                    'jumlah' => $row['jumlah'],
                    'status' => $row['status'],
                ]
            );
        }
    }
}
