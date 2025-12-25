<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class UpdateOrderQrCodesSeeder extends Seeder
{
    public function run()
    {
        // Generate QR codes for any existing orders without them
        Order::whereNull('pickup_qr_code')->get()->each(function ($order) {
            $order->update([
                'pickup_qr_code' => $order->order_number . '|' . bin2hex(random_bytes(8))
            ]);
        });
        
        echo "QR codes updated for all orders.\n";
    }
}
