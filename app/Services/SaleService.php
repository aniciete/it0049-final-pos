<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    /**
     * Atomically verify active product stock, decrement inventory, compute total_price
     * server-side, and persist the completed sale inside a database transaction.
     *
     * @return array{sale: Sale, product: Product, remaining_stock: int}
     *
     * @throws ValidationException
     */
    public function recordSale(int $productId, ?int $customerId, int $soldBy, int $quantity): array
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be at least 1 unit.',
            ]);
        }

        return DB::transaction(function () use ($productId, $customerId, $soldBy, $quantity): array {
            $product = Product::query()->find($productId);

            if ($product === null) {
                throw ValidationException::withMessages([
                    'product_id' => 'Selected product was not found or has been archived.',
                ]);
            }

            if (! User::query()->whereKey($soldBy)->exists()) {
                throw ValidationException::withMessages([
                    'sold_by' => 'Authenticated staff account could not be verified.',
                ]);
            }

            if ($customerId !== null && ! Customer::query()->whereKey($customerId)->exists()) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Selected customer account does not exist.',
                ]);
            }

            if ($product->stock_quantity < $quantity) {
                throw $this->insufficientStockException($product->name, $product->stock_quantity);
            }

            // Conditional UPDATE guards against concurrent overselling.
            $affected = Product::query()
                ->whereKey($productId)
                ->whereNull('deleted_at')
                ->where('stock_quantity', '>=', $quantity)
                ->decrement('stock_quantity', $quantity);

            if ($affected !== 1) {
                $latest = Product::withTrashed()->find($productId);

                if ($latest === null || $latest->trashed()) {
                    throw ValidationException::withMessages([
                        'product_id' => 'Selected product was not found or has been archived.',
                    ]);
                }

                throw $this->insufficientStockException($latest->name, $latest->stock_quantity);
            }

            // Re-read authoritative post-UPDATE stock inside the transaction.
            $remainingStock = (int) Product::query()
                ->whereKey($productId)
                ->value('stock_quantity');

            $totalPrice = number_format(round((float) $product->price * $quantity, 2), 2, '.', '');

            $sale = Sale::query()->create([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'sold_by' => $soldBy,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
            ]);

            return [
                'sale' => $sale,
                'product' => $product,
                'remaining_stock' => $remainingStock,
            ];
        });
    }

    /**
     * Build a consistent validation exception when requested quantity exceeds stock.
     */
    private function insufficientStockException(string $productName, int $available): ValidationException
    {
        $unitLabel = $available === 1 ? 'unit' : 'units';

        return ValidationException::withMessages([
            'quantity' => "Insufficient stock for {$productName}. Only {$available} {$unitLabel} available.",
        ]);
    }
}
