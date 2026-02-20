<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Securely download a product.
     */
    public function download(Product $product)
    {
        $user = Auth::user();

        // 1. Check if user has purchased this product (completed order)
        $hasPurchased = Order::where('buyer_id', $user->id)
            ->where('status', \App\Enums\OrderStatus::COMPLETED)
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You must purchase this product before downloading.');
        }

        // 2. Get the latest version of the product
        $version = $product->versions()->latest()->first();
        
        if (!$version || !$version->file_path) {
            return back()->with('error', 'The project file is currently unavailable. Please contact support.');
        }

        // 3. Verify file existence in protected storage
        if (!Storage::disk('local')->exists($version->file_path)) {
            \Log::error("Download failed: File not found at {$version->file_path} for Product ID {$product->id}");
            return back()->with('error', 'Error: Project file not found. We have notified the staff.');
        }

        // 4. Track download
        \App\Models\Download::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'version_id' => $version->id,
            'ip_address' => request()->ip(),
        ]);

        // 5. Serve the file securely
        return Storage::disk('local')->download(
            $version->file_path,
            $product->slug . '-v' . $version->version_number . '.zip'
        );
    }
}
