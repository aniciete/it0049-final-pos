<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LoadsRecentSales;
use App\Http\Requests\SaveCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use LoadsRecentSales;

    /**
     * Display the customer accounts directory.
     */
    public function index(): View
    {
        $customers = Customer::query()
            ->withCount('sales')
            ->orderBy('id')
            ->get();

        return view('customers.index', [
            'title' => 'Customers | Bright-Market POS',
            'activePage' => 'customers',
            'customers' => $customers,
        ]);
    }

    /**
     * Display the form for creating a new customer account.
     */
    public function create(): View
    {
        return view('customers.create', [
            'title' => 'Add Customer | Bright-Market POS',
            'activePage' => 'customers',
            'customer' => new Customer,
            'recentCustomers' => Customer::query()->latest('id')->limit(5)->get(),
        ]);
    }

    /**
     * Validate and persist a new customer record.
     */
    public function store(SaveCustomerRequest $request): RedirectResponse
    {
        Customer::query()->create($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer account created successfully.');
    }

    /**
     * Display an individual customer account record and linked sales receipts.
     */
    public function show(Customer $customer): View
    {
        return $this->renderCustomerContext('customers.show', $customer->full_name.' | Bright-Market POS', $customer);
    }

    /**
     * Display the form pre-filled with an existing customer's profile data.
     */
    public function edit(Customer $customer): View
    {
        return $this->renderCustomerContext('customers.edit', 'Edit Customer | Bright-Market POS', $customer);
    }

    /**
     * Render a customer detail or edit view with latest linked sales.
     */
    private function renderCustomerContext(string $view, string $title, Customer $customer): View
    {
        $recentSales = $this->loadRecentSalesContext($customer, ['product', 'seller']);

        return view($view, [
            'title' => $title,
            'activePage' => 'customers',
            'customer' => $customer,
            'recentSales' => $recentSales,
        ]);
    }

    /**
     * Validate and update an existing customer record.
     */
    public function update(SaveCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer account updated successfully.');
    }

    /**
     * Remove an existing customer record while preserving historical sales as walk-in.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer account deleted successfully.');
    }
}
