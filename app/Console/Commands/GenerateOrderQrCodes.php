<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class GenerateOrderQrCodes extends Command
{
    protected $signature = 'orders:generate-qr';
    protected $description = 'Generate QR codes for orders that don\'t have them';

    public function handle()
    {
        $orders = Order::whereNull('pickup_qr_code')->get();
        
        if ($orders->isEmpty()) {
            $this->info('All orders already have QR codes!');
            return 0;
        }

        $this->info("Found {$orders->count()} orders without QR codes.");

        foreach ($orders as $order) {
            $qrCode = $order->order_number . '|' . bin2hex(random_bytes(8));
            $order->update(['pickup_qr_code' => $qrCode]);
            $this->info("✓ Updated order {$order->order_number}");
        }

        $this->info("\nSuccess! All orders now have QR codes.");
        return 0;
    }
}
