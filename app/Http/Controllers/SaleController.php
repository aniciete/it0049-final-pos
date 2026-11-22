<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display chronological sales history ledger with optional search and date/staff filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'staff' => $request->integer('staff') ?: null,
            'customer' => $request->integer('customer') ?: null,
            'from' => trim((string) $request->query('from', '')),
            'to' => trim((string) $request->query('to', '')),
        ];

        $sales = Sale::query()
            ->with(['product', 'customer', 'seller'])
            ->filter($filters)
            ->latestLedger()
            ->get();

        $summary = [
            'transaction_count' => $sales->count(),
            'total_units' => (int) $sales->sum('quantity'),
            'total_revenue' => (float) $sales->sum('total_price'),
        ];

        return view('sales.index', [
            'title' => 'Sales History | Bright-Market POS',
            'activePage' => 'sales',
            'sales' => $sales,
            'summary' => $summary,
            'filters' => $filters,
            'staffFilterUser' => $filters['staff'] ? User::query()->find($filters['staff']) : null,
            'customerFilterRecord' => $filters['customer'] ? Customer::query()->find($filters['customer']) : null,
        ]);
    }

    /**
     * Display the checkout register form for recording a new sale.
     */
    public function create(): View
    {
        return view('sales.create', [
            'title' => 'Record Sale | Bright-Market POS',
            'activePage' => 'sales_create',
            'products' => Product::query()->availableForSale()->get(),
            'customers' => Customer::query()->orderBy('full_name')->get(),
        ]);
    }

    /**
     * Validate and atomically record a sale while deducting product inventory.
     */
    public function store(StoreSaleRequest $request, SaleService $saleService): RedirectResponse
    {
        $validated = $request->validated();
        $productId = (int) $validated['product_id'];
        $customerId = isset($validated['customer_id']) ? (int) $validated['customer_id'] : null;
        $quantity = (int) $validated['quantity'];
        $soldBy = (int) $request->user()->id;

        try {
            $result = $saleService->recordSale($productId, $customerId, $soldBy, $quantity);
        } catch (ValidationException $e) {
            $available = Product::query()->whereKey($productId)->value('stock_quantity');

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('available_stock', $available !== null ? (int) $available : null);
        }

        $sale = $result['sale'];
        $product = $result['product'];
        $remaining = $result['remaining_stock'];

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                "Sale #{$sale->id} recorded: {$quantity}x {$product->name} (\${$sale->total_price}). Remaining stock: {$remaining}."
            );
    }

    /**
     * Display an individual receipt record from Sales History.
     */
    public function show(Sale $sale): View
    {
        $sale->load(['product', 'customer', 'seller']);

        return view('sales.show', [
            'title' => "Receipt #{$sale->id} | Bright-Market POS",
            'activePage' => 'sales',
            'sale' => $sale,
        ]);
    }
}
