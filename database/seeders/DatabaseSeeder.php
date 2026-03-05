<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin POS',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@pos.com',
            'password' => Hash::make('password'),
        ]);

        // Site Settings
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'NedaPOS', 'type' => 'text', 'group' => 'general', 'label' => 'Nama Toko'],
            ['key' => 'store_address', 'value' => 'Jl. Raya No. 123, Jakarta', 'type' => 'text', 'group' => 'general', 'label' => 'Alamat Toko'],
            ['key' => 'store_phone', 'value' => '08123456789', 'type' => 'text', 'group' => 'general', 'label' => 'No. Telepon'],
            ['key' => 'store_email', 'value' => 'info@nedapos.com', 'type' => 'text', 'group' => 'general', 'label' => 'Email Toko'],
            ['key' => 'store_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Logo Toko'],

            // Appearance
            ['key' => 'color_primary_50', 'value' => '#eef2ff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 50'],
            ['key' => 'color_primary_100', 'value' => '#e0e7ff', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 100'],
            ['key' => 'color_primary_200', 'value' => '#c7d2fe', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 200'],
            ['key' => 'color_primary_300', 'value' => '#a5b4fc', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 300'],
            ['key' => 'color_primary_400', 'value' => '#818cf8', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 400'],
            ['key' => 'color_primary_500', 'value' => '#6366f1', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 500 (Utama)'],
            ['key' => 'color_primary_600', 'value' => '#4f46e5', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 600'],
            ['key' => 'color_primary_700', 'value' => '#4338ca', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 700'],
            ['key' => 'color_primary_800', 'value' => '#3730a3', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 800'],
            ['key' => 'color_primary_900', 'value' => '#312e81', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary 900'],
            ['key' => 'color_accent', 'value' => '#10b981', 'type' => 'color', 'group' => 'appearance', 'label' => 'Warna Aksen'],
            ['key' => 'sidebar_color', 'value' => '#1e1b4b', 'type' => 'color', 'group' => 'appearance', 'label' => 'Warna Sidebar'],

            // POS
            ['key' => 'default_tax', 'value' => '0', 'type' => 'text', 'group' => 'pos', 'label' => 'Pajak Default (%)'],
            ['key' => 'enable_customer', 'value' => '1', 'type' => 'boolean', 'group' => 'pos', 'label' => 'Aktifkan Fitur Pelanggan'],
            ['key' => 'enable_discount', 'value' => '1', 'type' => 'boolean', 'group' => 'pos', 'label' => 'Aktifkan Fitur Diskon'],
            ['key' => 'auto_print_receipt', 'value' => '0', 'type' => 'boolean', 'group' => 'pos', 'label' => 'Auto Print Struk'],

            // Receipt
            ['key' => 'receipt_header', 'value' => 'NedaPOS - Sistem Kasir Modern', 'type' => 'text', 'group' => 'receipt', 'label' => 'Header Struk'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih atas kunjungan Anda! Barang yang sudah dibeli tidak dapat dikembalikan.', 'type' => 'textarea', 'group' => 'receipt', 'label' => 'Footer Struk'],
            ['key' => 'receipt_show_logo', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt', 'label' => 'Tampilkan Logo di Struk'],
        ];

        foreach ($settings as $s) {
            SiteSetting::create($s);
        }

        // Categories
        $categories = [
            ['name' => 'Makanan', 'slug' => 'makanan', 'description' => 'Berbagai macam makanan', 'color' => '#ef4444', 'icon' => 'utensils'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai macam minuman', 'color' => '#3b82f6', 'icon' => 'coffee'],
            ['name' => 'Snack', 'slug' => 'snack', 'description' => 'Makanan ringan dan camilan', 'color' => '#f59e0b', 'icon' => 'star'],
            ['name' => 'Peralatan', 'slug' => 'peralatan', 'description' => 'Peralatan rumah tangga', 'color' => '#8b5cf6', 'icon' => 'home'],
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Peralatan elektronik', 'color' => '#06b6d4', 'icon' => 'zap'],
            ['name' => 'Pakaian', 'slug' => 'pakaian', 'description' => 'Baju dan aksesoris', 'color' => '#ec4899', 'icon' => 'shirt'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Products
        $products = [
            ['category_id' => 1, 'name' => 'Nasi Goreng Spesial', 'sku' => 'MKN-001', 'cost_price' => 12000, 'selling_price' => 18000, 'stock' => 50, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => 1, 'name' => 'Mie Goreng', 'sku' => 'MKN-002', 'cost_price' => 10000, 'selling_price' => 15000, 'stock' => 45, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => 1, 'name' => 'Ayam Goreng', 'sku' => 'MKN-003', 'cost_price' => 15000, 'selling_price' => 22000, 'stock' => 30, 'min_stock' => 8, 'unit' => 'pcs'],
            ['category_id' => 1, 'name' => 'Sate Ayam 10 tusuk', 'sku' => 'MKN-004', 'cost_price' => 18000, 'selling_price' => 25000, 'stock' => 20, 'min_stock' => 5, 'unit' => 'pcs'],
            ['category_id' => 1, 'name' => 'Bakso Urat', 'sku' => 'MKN-005', 'cost_price' => 11000, 'selling_price' => 17000, 'stock' => 35, 'min_stock' => 8, 'unit' => 'pcs'],
            ['category_id' => 2, 'name' => 'Es Teh Manis', 'sku' => 'MNM-001', 'cost_price' => 2000, 'selling_price' => 5000, 'stock' => 100, 'min_stock' => 20, 'unit' => 'pcs'],
            ['category_id' => 2, 'name' => 'Kopi Hitam', 'sku' => 'MNM-002', 'cost_price' => 3000, 'selling_price' => 7000, 'stock' => 80, 'min_stock' => 15, 'unit' => 'pcs'],
            ['category_id' => 2, 'name' => 'Jus Jeruk Segar', 'sku' => 'MNM-003', 'cost_price' => 5000, 'selling_price' => 10000, 'stock' => 40, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => 2, 'name' => 'Air Mineral 600ml', 'sku' => 'MNM-004', 'cost_price' => 2000, 'selling_price' => 4000, 'stock' => 200, 'min_stock' => 30, 'unit' => 'botol'],
            ['category_id' => 2, 'name' => 'Cappuccino', 'sku' => 'MNM-005', 'cost_price' => 7000, 'selling_price' => 15000, 'stock' => 60, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => 3, 'name' => 'Keripik Kentang', 'sku' => 'SNK-001', 'cost_price' => 5000, 'selling_price' => 8000, 'stock' => 70, 'min_stock' => 15, 'unit' => 'pack'],
            ['category_id' => 3, 'name' => 'Coklat Batang', 'sku' => 'SNK-002', 'cost_price' => 8000, 'selling_price' => 12000, 'stock' => 3, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => 3, 'name' => 'Kacang Mete', 'sku' => 'SNK-003', 'cost_price' => 15000, 'selling_price' => 22000, 'stock' => 25, 'min_stock' => 8, 'unit' => 'pack'],
            ['category_id' => 3, 'name' => 'Biskuit Keju', 'sku' => 'SNK-004', 'cost_price' => 4000, 'selling_price' => 7000, 'stock' => 0, 'min_stock' => 10, 'unit' => 'pack'],
            ['category_id' => 4, 'name' => 'Sapu Lantai', 'sku' => 'PRL-001', 'cost_price' => 15000, 'selling_price' => 25000, 'stock' => 15, 'min_stock' => 5, 'unit' => 'pcs'],
            ['category_id' => 4, 'name' => 'Ember Plastik', 'sku' => 'PRL-002', 'cost_price' => 10000, 'selling_price' => 18000, 'stock' => 20, 'min_stock' => 5, 'unit' => 'pcs'],
            ['category_id' => 5, 'name' => 'Charger USB-C', 'sku' => 'ELK-001', 'cost_price' => 25000, 'selling_price' => 45000, 'stock' => 30, 'min_stock' => 5, 'unit' => 'pcs'],
            ['category_id' => 5, 'name' => 'Earphone Bluetooth', 'sku' => 'ELK-002', 'cost_price' => 50000, 'selling_price' => 85000, 'stock' => 15, 'min_stock' => 3, 'unit' => 'pcs'],
            ['category_id' => 6, 'name' => 'Kaos Polos Hitam', 'sku' => 'PKN-001', 'cost_price' => 30000, 'selling_price' => 55000, 'stock' => 25, 'min_stock' => 5, 'unit' => 'pcs'],
            ['category_id' => 6, 'name' => 'Topi Baseball', 'sku' => 'PKN-002', 'cost_price' => 20000, 'selling_price' => 35000, 'stock' => 18, 'min_stock' => 5, 'unit' => 'pcs'],
        ];

        foreach ($products as $prod) {
            Product::create($prod);
        }

        // Customers
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '08123456789', 'email' => 'budi@email.com', 'address' => 'Jl. Merdeka No. 45, Jakarta'],
            ['name' => 'Siti Aminah', 'phone' => '08198765432', 'email' => 'siti@email.com', 'address' => 'Jl. Sudirman No. 12, Bandung'],
            ['name' => 'Ahmad Rizki', 'phone' => '08567891234', 'email' => null, 'address' => 'Jl. Gatot Subroto No. 78'],
            ['name' => 'Dewi Lestari', 'phone' => '08234567890', 'email' => 'dewi@email.com', 'address' => null],
            ['name' => 'Eko Prasetyo', 'phone' => '08111222333', 'email' => null, 'address' => 'Jl. Pahlawan No. 33'],
        ];

        foreach ($customers as $cust) {
            Customer::create($cust);
        }
    }
}
