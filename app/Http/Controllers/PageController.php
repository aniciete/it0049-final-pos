<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the public Bright-Market POS overview.
     */
    public function home(): View
    {
        return view('pages.home', [
            'title' => 'Bright-Market POS',
            'activePage' => 'home',
            ...$this->storeMetrics(),
        ]);
    }

    /**
     * Display the system data model and operational safeguards overview.
     */
    public function about(): View
    {
        return view('pages.about', [
            'title' => 'About | Bright-Market POS',
            'activePage' => 'about',
        ]);
    }

    /**
     * Display the authenticated staff operations dashboard.
     */
    public function dashboard(): View
    {
        $todayQuery = Sale::query()->whereDate('created_at', today());

        return view('dashboard', [
            'title' => 'Dashboard | Bright-Market POS',
            'activePage' => 'dashboard',
            ...$this->storeMetrics(),
            'todaySalesCount' => (clone $todayQuery)->count(),
            'todayRevenue' => (float) (clone $todayQuery)->sum('total_price'),
            'recentSales' => Sale::query()
                ->with(['product', 'customer', 'seller'])
                ->latestLedger()
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * Gather shared catalog, directory, and ledger summary counts.
     *
     * @return array<string, int|float>
     */
    private function storeMetrics(): array
    {
        return [
            'productCount' => Product::query()->count(),
            'lowStockCount' => Product::query()->lowStock()->count(),
            'customerCount' => Customer::query()->count(),
            'userCount' => User::query()->count(),
            'salesCount' => Sale::query()->count(),
            'totalRevenue' => (float) Sale::query()->sum('total_price'),
        ];
    }
}
