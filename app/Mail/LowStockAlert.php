<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct(Collection $products)
    {
        $this->products = $products;
    }

    public function build()
    {
        return $this->subject('⚠️ CẢNH BÁO: Sản phẩm sắp hết hàng')
            ->view('emails.low_stock_alert');
    }
}
