<?php

namespace App\Http\Controllers\Teknologi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teknologi\StoreDealRequest;
use App\Http\Requests\Teknologi\UpdateDealRequest;
use App\Models\Deal;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class DealController extends Controller
{
    /**
     * Store a newly created deal for the given technology.
     */
    public function store(StoreDealRequest $request, Technology $technology): RedirectResponse
    {
        $deal = $technology->deals()->create($request->validated());

        $technology->applyStatusFromDeal($deal);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Transaksi berhasil ditambahkan.']);

        return back();
    }

    /**
     * Update the given deal.
     */
    public function update(UpdateDealRequest $request, Technology $technology, Deal $deal): RedirectResponse
    {
        $deal->update($request->validated());

        $technology->applyStatusFromDeal($deal);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Transaksi berhasil diperbarui.']);

        return back();
    }
}
