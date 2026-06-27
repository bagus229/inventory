<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ===== User Admin Default =====
        $this->db->table('users')->insert([
            'nama'       => 'Administrator',
            'username'   => 'admin',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('users')->insert([
            'nama'       => 'Staff Gudang',
            'username'   => 'staff',
            'password'   => password_hash('staff123', PASSWORD_DEFAULT),
            'role'       => 'staff',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // ===== Kategori =====
        $kategoriData = [
            ['nama_kategori' => 'Elektronik', 'deskripsi' => 'Barang-barang elektronik', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Alat Tulis Kantor', 'deskripsi' => 'ATK dan perlengkapan kantor', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_kategori' => 'Furniture', 'deskripsi' => 'Perabotan dan furniture', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('kategori')->insertBatch($kategoriData);

        // ===== Supplier =====
        $supplierData = [
            ['nama_supplier' => 'PT Sumber Makmur', 'alamat' => 'Jl. Industri No. 10, Jakarta', 'telepon' => '021-5550101', 'email' => 'sumbermakmur@mail.com', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_supplier' => 'CV Berkah Jaya', 'alamat' => 'Jl. Pelita No. 5, Bandung', 'telepon' => '022-5550202', 'email' => 'berkahjaya@mail.com', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('supplier')->insertBatch($supplierData);

        // ===== Barang (relasi ke kategori & supplier) =====
        $barangData = [
            [
                'kategori_id' => 1,
                'supplier_id' => 1,
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Laptop Asus Vivobook',
                'harga_beli'  => 6500000,
                'harga_jual'  => 7500000,
                'stok'        => 15,
                'satuan'      => 'unit',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'kategori_id' => 2,
                'supplier_id' => 2,
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Pulpen Standard AE7',
                'harga_beli'  => 2500,
                'harga_jual'  => 3500,
                'stok'        => 200,
                'satuan'      => 'pcs',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'kategori_id' => 3,
                'supplier_id' => 1,
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Kursi Kantor Ergonomis',
                'harga_beli'  => 450000,
                'harga_jual'  => 600000,
                'stok'        => 30,
                'satuan'      => 'unit',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('barang')->insertBatch($barangData);
    }
}
