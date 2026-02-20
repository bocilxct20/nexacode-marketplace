<?php

namespace App\Livewire\Author;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Flux;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price = '';
    public $category_id = '';
    public $thumbnail;
    public $demo_url = '';
    public $video_url = '';
    public $project_file;
    public $changelog = '';
    public $is_active = true;
    public $is_featured = false;
    public $is_elite_marketed = false;

    public $existingThumbnail = null;
    public $screenshot_uploads = []; // For new uploads
    public $existingScreenshots = []; // For display/deletion
    public $version_number = '1.0.0';
    public $readyToLoad = false;

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->editingId,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:product_tags,id',
            'thumbnail' => $this->editingId ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'demo_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'project_file' => $this->editingId ? 'nullable|file|max:102400' : 'required|file|max:102400', // 100MB max
            'is_featured' => 'boolean',
            'is_elite_marketed' => 'boolean',
            'version_number' => 'nullable|string|max:20',
            'screenshot_uploads.*' => 'image|max:2048',
        ];
    }
    
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedName($value)
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $product = Product::where('author_id', Auth::id())->findOrFail($id);
        
        $this->editingId = $id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->category_id = $product->tags()->first()?->id;
        $this->demo_url = $product->demo_url;
        $this->video_url = $product->video_url;
        $this->is_active = $product->status === 'approved';
        $this->is_featured = (bool) $product->is_featured;
        $this->is_elite_marketed = (bool) $product->is_elite_marketed;
        $this->existingThumbnail = $product->thumbnail;
        $this->existingScreenshots = $product->screenshots ?? [];
        
        $latestVersion = $product->versions()->latest()->first();
        $this->version_number = $latestVersion ? $latestVersion->version_number : '1.0.0';
        
        $this->showModal = true;
    }


    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'demo_url' => $this->demo_url,
            'video_url' => $this->video_url,
            'status' => $this->is_active ? (Auth::user()->isElite() || \App\Models\PlatformSetting::get('auto_approve_products', false) ? 'approved' : 'pending') : 'draft',
            'author_id' => Auth::id(),
            'is_featured' => $this->is_featured,
            'is_elite_marketed' => Auth::user()->isElite() ? $this->is_elite_marketed : false,
        ];

        // Enforcement of Featured Slot Limits
        $user = Auth::user();
        if ($this->is_featured) {
            $limit = $user->isElite() ? -1 : ($user->isPro() ? 3 : 0);
            
            if ($limit === 0) {
                $data['is_featured'] = false;
                $this->is_featured = false;
            } elseif ($limit > 0) {
                $currentFeaturedCount = Product::where('author_id', $user->id)
                    ->where('is_featured', true)
                    ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
                    ->count();

                if ($currentFeaturedCount >= $limit) {
                    $this->addError('is_featured', "Kamu telah menggunakan semua slot spotlight ($limit/3). Nonaktifkan produk lain terlebih dahulu.");
                    return;
                }
            }
        }

        if ($this->thumbnail) {
            if ($this->editingId && $this->existingThumbnail) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->existingThumbnail);
            }
            
            // Convert to WebP before storing
            $filename = 'products/' . \Illuminate\Support\Str::random(40) . '.webp';
            $webpContent = $this->convertToWebP($this->thumbnail);
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $webpContent);
            
            $data['thumbnail'] = $filename;
        }

        // Process Multiple Screenshots
        if (!empty($this->screenshot_uploads)) {
            $screenshots = $this->editingId ? $this->existingScreenshots : [];
            
            foreach ($this->screenshot_uploads as $file) {
                // Convert to WebP
                $filename = 'products/screenshots/' . \Illuminate\Support\Str::random(40) . '.webp';
                $webpContent = $this->convertToWebP($file);
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $webpContent);
                $screenshots[] = $filename;
            }
            
            $data['screenshots'] = $screenshots;
        }

        if ($this->editingId) {
            $product = Product::where('author_id', Auth::id())->findOrFail($this->editingId);
            $product->update($data);
            $product->tags()->sync([$this->category_id]);
            
            if ($this->project_file) {
                // Create a new version for every new file upload during edit
                $product->versions()->create([
                    'version_number' => $this->version_number ?: '1.0.1',
                    'changelog' => $this->changelog ?: 'Updated item files.',
                    'file_path' => $this->project_file->store('protected/products', 'local'), // Protected storage
                    'is_active' => true,
                ]);
            }
            
            $message = 'Product updated successfully';
        } else {
            $product = Product::create($data);

            // Notify Admin
            \Illuminate\Support\Facades\Mail::to(config('mail.aliases.admin'))->queue(new \App\Mail\AdminNewProductSubmission($product));
            
            // Database Notification for Admins
            if ($product->status === 'pending') {
                $admins = \App\Models\User::admins();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\SystemNotification([
                        'title' => 'Product Pending Review 📦',
                        'message' => 'Author ' . Auth::user()->name . ' has submitted a new product: "' . $product->name . '".',
                        'type' => 'warning',
                        'action_text' => 'Moderate Product',
                        'action_url' => route('admin.moderation'),
                    ]));
                }
            }

            $product->tags()->sync([$this->category_id]);
            
            // Create initial version
            $product->versions()->create([
                'version_number' => $this->version_number ?: '1.0.0',
                'changelog' => $this->changelog ?: 'Initial release.',
                'file_path' => $this->project_file->store('protected/products', 'local'), // Protected storage
                'is_active' => true,
            ]);
            
            $message = 'Product created successfully';
        }

        Flux::toast(
            variant: 'success',
            heading: 'Success',
            text: $message,
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->price = '';
        $this->category_id = '';
        $this->thumbnail = null;
        $this->project_file = null;
        $this->changelog = '';
        $this->demo_url = '';
        $this->video_url = '';
        $this->is_active = true;
        $this->is_featured = false;
        $this->is_elite_marketed = false;
        $this->existingThumbnail = null;
        $this->screenshot_uploads = [];
        $this->existingScreenshots = [];
        $this->version_number = '1.0.0';
        $this->editingId = null;
        $this->resetValidation();
    }

    public function deleteThumbnail($id)
    {
        $product = Product::where('author_id', Auth::id())->findOrFail($id);
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
            $product->update(['thumbnail' => null]);
            $this->existingThumbnail = null;
        }
        $this->dispatch('product-updated', 'Thumbnail removed');
        Flux::toast(variant: 'success', text: 'Thumbnail removed successfully.');
    }

    public function removeScreenshot($index)
    {
        if (isset($this->existingScreenshots[$index])) {
            $path = $this->existingScreenshots[$index];
            Storage::disk('public')->delete($path);
            
            unset($this->existingScreenshots[$index]);
            $this->existingScreenshots = array_values($this->existingScreenshots);
            
            if ($this->editingId) {
                $product = Product::where('author_id', Auth::id())->findOrFail($this->editingId);
                $product->update(['screenshots' => $this->existingScreenshots]);
            }
            
            Flux::toast(variant: 'success', text: 'Screenshot removed.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updateStatus($productId, $status)
    {
        $product = Product::where('author_id', Auth::id())->findOrFail($productId);
        
        // Security Guard: Prevent non-Elite from bypassing moderation
        $allowedStatuses = ['draft', 'pending'];
        if (Auth::user()->isElite()) {
            $allowedStatuses[] = 'approved';
        }

        if (!in_array($status, $allowedStatuses)) {
            Flux::toast(variant: 'danger', text: 'Invalid status transition.');
            return;
        }

        $product->update(['status' => $status]);

        $this->dispatch('product-updated', 'Status updated successfully');
        Flux::toast(variant: 'success', text: 'Product status updated to ' . $status);
    }

    public function deleteProduct($productId)
    {
        $product = Product::where('author_id', Auth::id())->findOrFail($productId);
        $product->delete();

        $this->dispatch('product-deleted', 'Product removed successfully');
        Flux::toast(variant: 'success', text: 'Product has been permanently removed.');
    }

    private function convertToWebP($file)
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($path);
            // Handle transparency for PNG
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        } elseif ($mime == 'image/webp') {
            $image = imagecreatefromwebp($path);
        } else {
            // Fallback for other formats if any
            return file_get_contents($path);
        }

        // Resize if too large (Max 1200px width/height for thumbnails)
        $width = imagesx($image);
        $height = imagesy($image);
        $maxSize = 1200;

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $newWidth = $maxSize;
                $newHeight = $maxSize / $ratio;
            } else {
                $newHeight = $maxSize;
                $newWidth = $maxSize * $ratio;
            }
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for resized image
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }

        ob_start();
        imagewebp($image, null, 80); // quality 80
        $content = ob_get_clean();
        imagedestroy($image);

        return $content;
    }

    public function render()
    {
        $user = Auth::user();

        $query = Product::where('author_id', $user->id)
            ->with(['versions', 'tags', 'orderItems']);

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Gate data fetching
        if ($this->readyToLoad) {
            $products = $query->paginate(15);

            // Calculate stats
            $stats = [
                'total' => Product::where('author_id', $user->id)->count(),
                'approved' => Product::where('author_id', $user->id)->where('status', 'approved')->count(),
                'pending' => Product::where('author_id', $user->id)->where('status', 'pending')->count(),
                'total_sales' => Product::where('author_id', $user->id)->sum('sales_count'),
                'featured_count' => Product::where('author_id', $user->id)->where('is_featured', true)->count(),
                'featured_limit' => $user->isElite() ? -1 : ($user->isPro() ? 3 : 0),
            ];
        } else {
            $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = [
                'total' => 0, 'approved' => 0, 'pending' => 0, 
                'total_sales' => 0, 'featured_count' => 0, 'featured_limit' => 0
            ];
        }

        return view('livewire.author.product-manager', [
            'products' => $products,
            'stats' => $stats
        ]);
    }
}
