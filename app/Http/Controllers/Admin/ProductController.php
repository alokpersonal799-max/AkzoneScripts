<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * List all products for management.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), fn ($query) => $query->search($request->string('q')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'filters' => $request->only(['q', 'status']),
        ]);
    }

    /**
     * Show the create product form.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(['status' => 'draft', 'version' => '1.0.0', 'is_purchasable' => true, 'use_global_contact' => true]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Persist a new product.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data = $this->handleUploads($request, $data);

        $product = Product::create($data);

        // Announce newly published products to connected Telegram bots.
        if ($product->status === 'published') {
            app(\App\Services\TelegramService::class)->notify('product_added', \App\Support\TelegramMessages::productAdded($product));
            $this->announceNewProduct($product);
        }

        return redirect()->route('admin.products.index')
            ->with('success', '"'.$product->title.'" has been created.');
    }

    /**
     * Auto-create and send a "new product" announcement to all users (once per product).
     */
    protected function announceNewProduct(Product $product): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('announcements')) {
            return;
        }

        // Avoid duplicate announcements for the same product.
        if (\App\Models\Announcement::where('product_id', $product->id)->where('theme', 'new_product')->exists()) {
            return;
        }

        $announcement = \App\Models\Announcement::create([
            'title' => 'New product: '.$product->title,
            'body' => $product->tagline ?: 'A brand new product has just landed in our store. Take a look!',
            'theme' => 'new_product',
            'audience' => 'all',
            'status' => 'draft',
            'allow_reply' => false,
            'reply_types' => [],
            'product_id' => $product->id,
            'action_url' => route('products.show', $product),
            'created_by' => auth()->id(),
        ]);

        $announcement->send();
    }

    /**
     * Show a single product (redirects to edit for convenience).
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the edit form for a product.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'changelogs' => $product->changelogs()->orderByDesc('released_at')->get(),
        ]);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product);
        $data = $this->handleUploads($request, $data, $product);

        $wasPublished = $product->status === 'published';

        $product->update($data);

        // Auto-announce when a product is published for the first time.
        if ($product->status === 'published' && ! $wasPublished) {
            $this->announceNewProduct($product);
        }

        // Create a changelog entry if version and notes are provided.
        if ($request->filled('changelog_version') && $request->filled('changelog_notes')) {
            $product->changelogs()->create([
                'version' => $request->input('changelog_version'),
                'notes' => $request->input('changelog_notes'),
                'released_at' => now()->toDateString(),
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', '"'.$product->title.'" has been updated.');
    }

    /**
     * Delete a product and its associated files.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        if ($product->file_path) {
            Storage::disk('products')->delete($product->file_path);
        }

        foreach ((array) $product->gallery as $image) {
            Storage::disk('public')->delete($image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    /**
     * Validation rules shared between store and update.
     *
     * @return array<string, mixed>
     */
    protected function validateProduct(Request $request, ?Product $product = null): array
    {
        $maxFile = config('marketplace.max_file_size', 204800);
        $allowed = implode(',', config('marketplace.allowed_file_types', ['zip']));

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'is_purchasable' => ['nullable', 'boolean'],
            'use_global_contact' => ['nullable', 'boolean'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],
            'contact_telegram' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array', 'max:6'],
            'gallery.*' => ['image', 'max:4096'],
            'remove_gallery' => ['nullable', 'array'],
            'product_file' => [
                $product && $product->file_path ? 'nullable' : 'nullable',
                'file',
                "mimes:{$allowed}",
                "max:{$maxFile}",
            ],
            'file_type' => ['nullable', 'in:upload,external'],
            'external_url' => ['nullable', 'url', 'max:2000', 'required_if:file_type,external'],
            'download_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'link_expiry_minutes' => ['nullable', 'integer', 'min:0', 'max:525600'],
            'download_message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Normalise the comma separated tag string into an array.
        $validated['tags'] = $request->filled('tags')
            ? collect(explode(',', $request->string('tags')->toString()))
                ->map(fn ($tag) => trim($tag))
                ->filter()
                ->values()
                ->all()
            : [];

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_purchasable'] = $request->boolean('is_purchasable');
        $validated['use_global_contact'] = $request->boolean('use_global_contact');

        // Default delivery type and normalise the optional limits.
        $validated['file_type'] = $validated['file_type'] ?? 'upload';
        $validated['download_limit'] = $validated['download_limit'] ?? null;
        $validated['link_expiry_minutes'] = $validated['link_expiry_minutes'] ?? null;

        // When delivered externally, the link is the source of truth; clear it for uploads.
        if ($validated['file_type'] !== 'external') {
            $validated['external_url'] = null;
        }

        return $validated;
    }

    /**
     * Handle thumbnail and product file uploads, returning the merged data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function handleUploads(Request $request, array $data, ?Product $product = null): array
    {
        if ($request->hasFile('thumbnail')) {
            if ($product && $product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        if ($request->hasFile('product_file') && ($data['file_type'] ?? 'upload') !== 'external') {
            if ($product && $product->file_path) {
                Storage::disk('products')->delete($product->file_path);
            }

            $file = $request->file('product_file');
            $data['file_path'] = $file->store('packages', 'products');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        // Gallery images (max 6 total). Start from the existing set.
        $gallery = $product?->gallery ?? [];

        // Remove any images the admin unchecked.
        if ($request->filled('remove_gallery')) {
            foreach ((array) $request->input('remove_gallery') as $path) {
                Storage::disk('public')->delete($path);
            }
            $gallery = array_values(array_diff($gallery, (array) $request->input('remove_gallery')));
        }

        // Add newly uploaded gallery images.
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                if (count($gallery) >= 6) {
                    break;
                }
                $gallery[] = $image->store('products/gallery', 'public');
            }
        }

        $data['gallery'] = array_slice(array_values($gallery), 0, 6);

        // Remove transient upload fields so they are not mass-assigned.
        unset($data['product_file'], $data['remove_gallery']);

        return $data;
    }
}
