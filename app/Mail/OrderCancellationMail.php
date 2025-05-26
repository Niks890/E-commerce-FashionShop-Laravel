<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order; // Import model Order

class OrderCancellationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order; // Khai báo biến public để truyền dữ liệu đơn hàng

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order) // Truyền đối tượng Order vào constructor
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận hủy đơn hàng - Đơn hàng #' . $this->order->id ." thành công", // Tiêu đề email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'sites.emails.order_cancellation',
            // markdown: 'sites.emails.order_confirmation', // View template cho email
            with: [
                'order' => $this->order, // Truyền biến $order vào view
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
