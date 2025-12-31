<?php

namespace App\Http\Services;

use App\Models\Item;
use Exception;
use Illuminate\Support\Facades\Log;

class ItemService
{
    private ImageService $imageService;

    public function __construct(?ImageService $imageService = null)
    {
        $this->imageService = $imageService ?? new ImageService();
    }

    public function getItems(int $page = 1, int $limit = 15)
    {
        try {

            $limit = min($limit, 100);
            return Item::latest()->paginate($limit, ['*'], 'page', $page);
        } catch (\Throwable $th) {
            Log::error("Error fetching items: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to fetch items.");
        }
    }

    public function featuredItems($excludeId, $count = 4)
    {
        try {
            return Item::where('id', '!=', $excludeId)->latest()->take($count)->get();
        } catch (\Throwable $th) {
            Log::error("Error fetching featured items: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to fetch featured items.");
        }
    }

    public function getItemById($id)
    {
        try {
            return Item::findOrFail($id);
        } catch (\Throwable $th) {
            Log::error("Error fetching item by ID: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to fetch item by ID.");
        }
    }

    public function create(array $validatedData, bool $hasFile, $file): void
    {
        try {

            if ($validatedData && $hasFile) {
                $imagePath = $this->imageService->saveImage($file);
                $validated['image_url'] = $imagePath['url'];
                $validated['image_preview_url'] = $this->imageService->generatePreview($imagePath['path']);
            }

            $item = new Item();
            $item->image_url = $validatedData['image_url'] ?? '';
            $item->image_preview_url = $validatedData['image_preview_url'] ?? '';
            $item->slug = $this->generateSlug($validatedData['slug']);
            $item->name = $validatedData['name'];
            $item->description = $validatedData['description'] ?? '';
            $item->price = $validatedData['price'];
            $item->sale_price = $validatedData['sale_price'] ?? null;
            $item->currency = strtoupper($validatedData['currency']);
            $item->stock = $validatedData['stock'];
            $item->tags = $validatedData['tags'];
            $item->sales_count = 0;
            $item->save();
        } catch (\Throwable $th) {
            Log::error("Error creating item: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to create item.");
        }
    }

    public function update(array $validatedData, $id, bool $hasFile, $file): void
    {
        try {

            $item = Item::findOrFail($id);

            if (!$item) {
                throw new \Exception("Item not found");
            }

            if ($validatedData && $hasFile) {
                $imagePath = $this->imageService->saveImage($file);
                $validated['image_url'] = $imagePath['url'];
                $validated['image_preview_url'] = $this->imageService->generatePreview($imagePath['path']);
            }

            $item->image_url = $validatedData['image_url'] ?? $item->image_url;
            $item->image_preview_url = $validatedData['image_preview_url'] ?? $item->image_preview_url;
            $item->slug = $this->generateSlug($validatedData['slug']);
            $item->name = $validatedData['name'];
            $item->description = $validatedData['description'] ?? '';
            $item->price = $validatedData['price'];
            $item->sale_price = $validatedData['sale_price'] ?? null;
            $item->currency = strtoupper($validatedData['currency']);
            $item->stock = $validatedData['stock'];
            $item->tags = $validatedData['tags'];
            $item->save();
        } catch (\Throwable $th) {
            Log::error("Error updating item: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to update item.");
        }
    }

    public function delete($id)
    {
        try {

            $item = Item::findOrFail($id);
            if (!$item) {
                throw new \Exception("Item not found");
            }
            $item->delete();
        } catch (\Throwable $th) {
            Log::error("Error deleting item: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to delete item.");
        }
    }

    public function generateSlug($name)
    {
        try {

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $count = Item::where('slug', 'like', "{$slug}%")->count();
            return $count ? "{$slug}-{$count}" : $slug;
        } catch (\Throwable $th) {
            Log::error("Error generating slug: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to generate slug.");
        }
    }

    public function normalizeTagsInput($tags): array
    {
        try {
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
        } catch (\Throwable $th) {
            Log::error("Error normalizing tags input: ", [
                "message" => $th->getMessage(),
            ]);

            throw new \Exception("Failed to normalize tags input.");
        }
    }
}
