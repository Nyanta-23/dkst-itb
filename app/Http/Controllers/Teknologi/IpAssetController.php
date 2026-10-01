<?php

namespace App\Http\Controllers\Teknologi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teknologi\StoreIpAssetRequest;
use App\Http\Requests\Teknologi\UpdateIpAssetRequest;
use App\Models\IpAsset;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class IpAssetController extends Controller
{
    /**
     * Store a newly created IP asset for the given technology.
     */
    public function store(StoreIpAssetRequest $request, Technology $technology): RedirectResponse
    {
        $technology->ipAssets()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kekayaan intelektual berhasil ditambahkan.']);

        return back();
    }

    /**
     * Update the given IP asset.
     */
    public function update(UpdateIpAssetRequest $request, Technology $technology, IpAsset $ipAsset): RedirectResponse
    {
        $ipAsset->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kekayaan intelektual berhasil diperbarui.']);

        return back();
    }

    /**
     * Remove the given IP asset.
     */
    public function destroy(Technology $technology, IpAsset $ipAsset): RedirectResponse
    {
        Gate::authorize('delete', $ipAsset);

        $ipAsset->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kekayaan intelektual berhasil dihapus.']);

        return back();
    }
}
