<?php

declare(strict_types=1);

namespace App\Console;

use App\Enums\PaymentStatus;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';

    protected $description = 'Cancel unpaid orders older than 24 hours and restock inventory.';

    protected OrderRepositoryInterface $orderRepository;

    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        parent::__construct();

        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function handle(): int
    {
        $dateTime = new DateTimeImmutable('-24 hours');
        $canceledCount = 0;
        $failedCount = 0;

        try {
            Log::info('Starting unpaid orders cancellation job', [
                'cutoff_date' => $dateTime->format('Y-m-d H:i:s'),
            ]);

            $orders = $this->orderRepository->getUnpaidOlderThan($dateTime);

            $this->info(sprintf('Found %d unpaid orders older than 24 hours.', $orders->count()));

            foreach ($orders as $order) {
                try {
                    DB::transaction(function () use ($order) {
                        $this->orderRepository->cancel($order);

                        if ($order->payment && $order->payment->status === PaymentStatus::PENDING) {
                            $this->paymentRepository->markCanceled($order->payment);
                        }

                        foreach ($order->items as $item) {
                            $product = $item->product;

                            if ($product) {
                                $product->increment('stock', $item->quantity);
                            }
                        }
                    });

                    Log::info('Canceled unpaid order successfully', [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'vendor_id' => $order->vendor_id,
                        'total' => $order->total,
                    ]);

                    $canceledCount++;
                } catch (\Throwable $e) {
                    $failedCount++;

                    Log::error('Failed to cancel unpaid order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    $this->error(sprintf('Failed to cancel order #%d: %s', $order->id, $e->getMessage()));
                }
            }

            Log::info('Unpaid orders cancellation job completed', [
                'total_found' => $orders->count(),
                'canceled' => $canceledCount,
                'failed' => $failedCount,
            ]);

            $this->info(sprintf('Canceled %d unpaid orders. %d failed.', $canceledCount, $failedCount));
        } catch (\Throwable $e) {
            Log::critical('Unpaid orders cancellation job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Job failed: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
