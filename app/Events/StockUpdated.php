<?php
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

// class StockUpdated implements ShouldBroadcast
// {
//     public $variantId;
//     public $newStock;

//     public function __construct($variantId, $newStock)
//     {
//         $this->variantId = $variantId;
//         $this->newStock = $newStock;
//     }

//     public function broadcastOn()
//     {
//         return new Channel('variant-stock.' . $this->variantId);
//     }

//     public function broadcastWith()
//     {
//         return [
//             'variant_id' => $this->variantId,
//             'new_stock' => $this->newStock,
//         ];
//     }
// }
