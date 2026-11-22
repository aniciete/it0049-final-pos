<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait LoadsRecentSales
{
    /**
     * Load the total sales count onto the model and return up to five latest sales.
     *
     * @param  array<int, string>  $relations
     * @return Collection<int, Sale>
     */
    private function loadRecentSalesContext(Model $model, array $relations): Collection
    {
        $model->loadCount('sales');

        return $model->sales()
            ->with($relations)
            ->latestLedger()
            ->limit(5)
            ->get();
    }
}
