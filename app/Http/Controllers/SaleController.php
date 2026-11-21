<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * Display chronological sales history ledger with transaction summary metrics.
     */
    public function index(): View
    {
        $sales = Sale::query()
            ->with(['product', 'customer', 'seller'])
            ->latest('created_at')
            ->latest('id')
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
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->flatten()->first() ?? 'Unable to record sale.');
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
}
