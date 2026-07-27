<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['kode' => 'ATK-001', 'nama' => 'Pulpen Hitam', 'stok' => 120],
            ['kode' => 'ATK-002', 'nama' => 'Pensil 2B', 'stok' => 90],
            ['kode' => 'ATK-003', 'nama' => 'Buku Tulis', 'stok' => 75],
            ['kode' => 'ATK-004', 'nama' => 'Kertas A4 80gsm', 'stok' => 45],
            ['kode' => 'ATK-005', 'nama' => 'Map Plastik', 'stok' => 60],
            ['kode' => 'ATK-006', 'nama' => 'Stapler', 'stok' => 25],
            ['kode' => 'ATK-007', 'nama' => 'Isi Staples', 'stok' => 110],
            ['kode' => 'ATK-008', 'nama' => 'Spidol Whiteboard', 'stok' => 35],
        ];

        foreach ($items as $item) {
            Barang::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
