<?php

namespace App\Models;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'sold_by',
        'quantity',
        'total_price',
    ];

    /**
     * Product sold in this transaction (includes archived products for history integrity).
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Optional customer linked to this transaction (null represents a walk-in sale).
     *
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Authenticated staff user who recorded this transaction.
     *
     * @return BelongsTo<User, $this>
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    /**
     * Order sales chronologically from newest to oldest.
     *
     * @param  Builder<Sale>  $query
     * @return Builder<Sale>
     */
    public function scopeLatestLedger(Builder $query): Builder
    {
        return $query->latest('created_at')->latest('id');
    }

    /**
     * Filter sales by search text, staff user, customer, and YYYY-MM-DD date bounds.
     *
     * @param  Builder<Sale>  $query
     * @param  array{q?: string|null, staff?: int|null, customer?: int|null, from?: string|null, to?: string|null}  $filters
     * @return Builder<Sale>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $numericId = ltrim($search, '#');
            $query->where(function (Builder $q) use ($search, $numericId): void {
                if (ctype_digit($numericId)) {
                    $q->orWhere('id', (int) $numericId);
                }
                $q->orWhereHas('product', fn ($pq) => $pq->withTrashed()->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('customer', fn ($cq) => $cq->where('full_name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('seller', fn ($uq) => $uq->where('full_name', 'like', '%'.$search.'%')->orWhere('username', 'like', '%'.$search.'%'));
            });
        }

        if (! empty($filters['staff'])) {
            $query->where('sold_by', (int) $filters['staff']);
        }

        if (! empty($filters['customer'])) {
            $query->where('customer_id', (int) $filters['customer']);
        }

        $fromDate = self::parseDateFilter((string) ($filters['from'] ?? ''));
        if ($fromDate !== null) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        $toDate = self::parseDateFilter((string) ($filters['to'] ?? ''));
        if ($toDate !== null) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        return $query;
    }

    /**
     * Validate a strict YYYY-MM-DD date string before applying it to a filter.
     */
    private static function parseDateFilter(string $value): ?string
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return null;
        }

        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($parsed === false || $parsed->format('Y-m-d') !== $value) {
            return null;
        }

        return $parsed->format('Y-m-d');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'total_price' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
