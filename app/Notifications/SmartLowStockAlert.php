<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SmartLowStockAlert extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $productId,
        public string $productName,
        public int $currentStock,
        public int $predictedDaysLeft
    ) {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = "Stok {$this->productName} diprediksi habis dalam {$this->predictedDaysLeft} hari.";

        return [
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'current_stock' => $this->currentStock,
            'predicted_days_left' => $this->predictedDaysLeft,
            'message' => $message,
            'type' => 'low_stock_alert',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

