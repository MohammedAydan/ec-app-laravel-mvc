<?php

namespace App\Http\Controllers;

use App\Http\Services\ImageService;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private ImageService $imageService;

    public function __construct()
    {
        $this->imageService = new ImageService();
    }

    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $limit = min((int) $request->input('limit', 15), 100);
        $items = Item::latest()->paginate($limit, ['*'], 'page', $page);

        return view('admin.item.index', compact('items'));
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        $featuredItems = Item::where('id', '!=', $id)->latest()->take(4)->get();

        return view('admin.item.show', compact('item', 'featuredItems'));
    }

    public function create()
    {
        return view('admin.item.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'tags' => $this->normalizeTagsInput($request->input('tags')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'stock' => 'required|integer|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'slug' => 'required|string|max:255|unique:items,slug',
            'image_url' => 'nullable|url',
        ]);

        if ($validated && $request->hasFile('image')) {
            $imagePath = $this->imageService->saveImage($request->file('image'));
            $validated['image_url'] = $imagePath['url'];
            $validated['image_preview_url'] = $this->imageService->generatePreview($imagePath['path']);
        }

        $item = new Item();
        $item->image_url = $validated['image_url'] ?? '';
        $item->image_preview_url = $validated['image_preview_url'] ?? '';
        $item->slug = $this->generateSlug($validated['slug']);
        $item->name = $validated['name'];
        $item->description = $validated['description'] ?? '';
        $item->price = $validated['price'];
        $item->sale_price = $validated['sale_price'] ?? null;
        $item->currency = strtoupper($validated['currency']);
        $item->stock = $validated['stock'];
        $item->tags = $validated['tags'];
        $item->sales_count = 0;
        $item->save();

        return redirect()->route('admin.items.index')->with('success', 'Item created successfully.');
    }

    private function normalizeTagsInput($tags): array
    {
        if (is_null($tags)) {
            return [];
        }

        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        if (!is_array($tags)) {
            return [];
        }

        return collect($tags)
            ->map(fn($tag) => trim((string) $tag))
            ->filter(fn($tag) => $tag !== '')
            ->values()
            ->all();
    }

    private function generateSlug($name)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $count = Item::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('admin.item.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->merge([
            'tags' => $this->normalizeTagsInput($request->input('tags')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'stock' => 'required|integer|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'slug' => "required|string|max:255|unique:items,slug,{$item->id}",
            'image_url' => 'nullable|url',
        ]);

        if ($validated && $request->hasFile('image')) {
            $imagePath = $this->imageService->saveImage($request->file('image'));
            $validated['image_url'] = $imagePath['url'];
            $validated['image_preview_url'] = $this->imageService->generatePreview($imagePath['path']);
        }

        $item->image_url = $validated['image_url'] ?? $item->image_url;
        $item->image_preview_url = $validated['image_preview_url'] ?? $item->image_preview_url;
        $item->slug = $this->generateSlug($validated['slug']);
        $item->name = $validated['name'];
        $item->description = $validated['description'] ?? '';
        $item->price = $validated['price'];
        $item->sale_price = $validated['sale_price'] ?? null;
        $item->currency = strtoupper($validated['currency']);
        $item->stock = $validated['stock'];
        $item->tags = $validated['tags'];
        $item->save();

        return redirect()->route('admin.items.index')->with('success', 'Item updated successfully.');
    }

    public function delete($id)
    {
        $item = Item::findOrFail($id);
        return view('admin.item.delete', compact('item'));
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.items.index')->with('success', 'Item deleted successfully.');
    }
}
