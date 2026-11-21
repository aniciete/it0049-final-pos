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
     * Display the public Bright-Market POS landing overview.
     */
    public function home(): View
    {
        return view('pages.home', [
            'title' => 'Bright-Market POS | Laravel MVC System',
            'activePage' => 'home',
            'productCount' => Product::query()->count(),
            'lowStockCount' => Product::query()->lowStock()->count(),
            'customerCount' => Customer::query()->count(),
            'userCount' => User::query()->count(),
            'salesCount' => Sale::query()->count(),
            'totalRevenue' => (float) Sale::query()->sum('total_price'),
        ]);
    }

    /**
     * Display the system architecture, 4-table ERD summary, and security overview.
     */
    public function about(): View
    {
        return view('pages.about', [
            'title' => 'Architecture & Schema | Bright-Market POS',
            'activePage' => 'about',
        ]);
    }

    /**
     * Display the authenticated staff operations dashboard.
     */
    public function dashboard(): View
    {
        return view('dashboard', [
            'title' => 'Operations Dashboard | Bright-Market POS',
            'activePage' => 'dashboard',
            'productCount' => Product::query()->count(),
            'lowStockCount' => Product::query()->lowStock()->count(),
            'customerCount' => Customer::query()->count(),
            'userCount' => User::query()->count(),
            'salesCount' => Sale::query()->count(),
            'totalRevenue' => (float) Sale::query()->sum('total_price'),
            'recentSales' => Sale::query()
                ->with(['product', 'customer', 'seller'])
                ->latest('created_at')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
