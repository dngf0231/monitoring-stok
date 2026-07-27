<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Database\Seeder;

class BarangMasukSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['kode' => 'ATK-001', 'jumlah' => 40, 'tanggal' => '2026-07-01'],
            ['kode' => 'ATK-002', 'jumlah' => 30, 'tanggal' => '2026-07-03'],
            ['kode' => 'ATK-004', 'jumlah' => 20, 'tanggal' => '2026-07-05'],
            ['kode' => 'ATK-008', 'jumlah' => 15, 'tanggal' => '2026-07-08'],
        ];

        foreach ($rows as $row) {
            $barang = Barang::where('kode', $row['kode'])->first();

            if (!$barang) {
                continue;
            }

            BarangMasuk::updateOrCreate(
                [
                    'barang_id' => $barang->id,
                    'tanggal' => $row['tanggal'],
                    'jumlah' => $row['jumlah'],
                ],
                [
                    'barang_id' => $barang->id,
                    'tanggal' => $row['tanggal'],
                    'jumlah' => $row['jumlah'],
                ]
            );
        }
    }
}
