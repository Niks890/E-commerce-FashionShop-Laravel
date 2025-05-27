<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $voucher;
    public $messageContent; // Để lưu trữ lời nhắn
    public $expiryDate;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $voucher, $messageContent = null, $expiryDate = null)
    {
        $this->customer = $customer;
        $this->voucher = $voucher;
        $this->messageContent = $messageContent;
        $this->expiryDate = $expiryDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Chúc mừng bạn đã nhận được Voucher từ chúng tôi!',
            to: $this->customer->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'sites.emails.voucher', // Tạo file Blade template này
            with: [
                'customerName' => $this->customer->name,
                'voucherCode' => $this->voucher->vouchers_code,
                'voucherDescription' => $this->voucher->vouchers_description,
                'messageContent' => $this->messageContent,
                'expiryDate' => $this->expiryDate
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
