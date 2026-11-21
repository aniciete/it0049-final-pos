<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
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
            'title' => 'Customer Accounts | Bright-Market POS',
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
            'title' => 'New Customer Account | Bright-Market POS',
            'activePage' => 'customers',
            'customer' => new Customer,
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
     * Display an individual customer account record and order count.
     */
    public function show(Customer $customer): View
    {
        $customer->loadCount('sales');

        return view('customers.show', [
            'title' => $customer->full_name.' | Customers | Bright-Market POS',
            'activePage' => 'customers',
            'customer' => $customer,
        ]);
    }

    /**
     * Display the form pre-filled with an existing customer's profile data.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'title' => 'Edit Customer Account | Bright-Market POS',
            'activePage' => 'customers',
            'customer' => $customer,
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
