<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    /**
     * Display active or archived products catalog with stock badges and image thumbnails.
     */
    public function index(Request $request): View
    {
        $viewArchived = $request->query('view') === 'archived';

        $products = ($viewArchived
            ? Product::onlyTrashed()->latest('deleted_at')->orderBy('id')
            : Product::query()->orderBy('id'))
            ->withCount('sales')
            ->get();

        return view('products.index', [
            'title' => 'Products Catalog | Bright-Market POS',
            'activePage' => 'products',
            'products' => $products,
            'viewArchived' => $viewArchived,
            'activeCount' => Product::query()->count(),
            'archivedCount' => Product::onlyTrashed()->count(),
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('products.create', [
            'title' => 'New Product | Bright-Market POS',
            'activePage' => 'products',
            'product' => new Product(['stock_quantity' => 0]),
        ]);
    }

    /**
     * Validate and store a newly created product with optional image upload.
     */
    public function store(SaveProductRequest $request, ImageUploadService $imageService): RedirectResponse
    {
        $validated = $request->validated();
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $imageService->storeProductImage($request->file('image'));
        }

        try {
            Product::query()->create([
                'name' => $validated['name'],
                'price' => number_format((float) $validated['price'], 2, '.', ''),
                'stock_quantity' => (int) $validated['stock_quantity'],
                'image' => $imagePath,
            ]);
        } catch (Throwable $e) {
            if ($imagePath !== null) {
                $imageService->delete($imagePath);
            }

            throw $e;
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product added to inventory.');
    }

    /**
     * Display details for an active or archived product.
     */
    public function show(int $product): View
    {
        $record = Product::withTrashed()->withCount('sales')->findOrFail($product);

        return view('products.show', [
            'title' => $record->name.' | Products | Bright-Market POS',
            'activePage' => 'products',
            'product' => $record,
        ]);
    }

    /**
     * Show the form for editing an existing active product.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'title' => 'Edit Product | Bright-Market POS',
            'activePage' => 'products',
            'product' => $product,
        ]);
    }

    /**
     * Validate and update an existing product and optionally replace or remove its image.
     */
    public function update(
        SaveProductRequest $request,
        Product $product,
        ImageUploadService $imageService,
    ): RedirectResponse {
        $validated = $request->validated();
        $oldImage = $product->image;
        $newImagePath = null;
        $removeRequested = $request->boolean('remove_image');

        if ($request->hasFile('image')) {
            $newImagePath = $imageService->storeProductImage($request->file('image'));
        }

        $imageToPersist = $newImagePath ?? ($removeRequested ? null : $oldImage);

        try {
            $product->update([
                'name' => $validated['name'],
                'price' => number_format((float) $validated['price'], 2, '.', ''),
                'stock_quantity' => (int) $validated['stock_quantity'],
                'image' => $imageToPersist,
            ]);
        } catch (Throwable $e) {
            if ($newImagePath !== null) {
                $imageService->delete($newImagePath);
            }

            throw $e;
        }

        if ($oldImage !== null && ($newImagePath !== null || $removeRequested)) {
            $imageService->delete($oldImage);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product details updated.');
    }

    /**
     * Archive (soft-delete) an active product, or permanently purge an unsold product.
     */
    public function destroy(Request $request, int $product, ImageUploadService $imageService): RedirectResponse
    {
        $record = Product::withTrashed()->findOrFail($product);
        $wantsPurge = $request->boolean('purge') || $record->trashed();

        if ($wantsPurge) {
            if ($record->sales()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'Products with recorded sales cannot be permanently deleted and must stay archived.');
            }

            $imagePath = $record->image;
            $recordName = $record->name;
            $wasTrashed = $record->trashed();

            $record->forceDelete();

            if ($imagePath !== null) {
                $imageService->delete($imagePath);
            }

            $routeParams = $wasTrashed ? ['view' => 'archived'] : [];

            return redirect()
                ->route('products.index', $routeParams)
                ->with('success', "Product \"{$recordName}\" permanently deleted.");
        }

        $record->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$record->name}\" archived. Existing sales ledger entries remain intact.");
    }

    /**
     * Restore an archived product back to active inventory.
     */
    public function restore(int $product): RedirectResponse
    {
        $record = Product::onlyTrashed()->findOrFail($product);
        $record->restore();

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$record->name}\" restored to catalog.");
    }
}
