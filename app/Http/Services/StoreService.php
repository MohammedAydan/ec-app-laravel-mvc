<?php

namespace App\Http\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class StoreService
{
    public function getItems($page, $limit, $search)
    {
        try {
            if ($search !== '') {
                // Escape special characters for LIKE clauses to prevent unexpected pattern matching
                $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);

                $items = Item::where(function ($query) use ($escapedSearch) {
                    $query->where('name', 'like', '%' . $escapedSearch . '%')
                        ->orWhere('description', 'like', '%' . $escapedSearch . '%')
                        ->orWhere('tags', 'like', '%' . $escapedSearch . '%');
                })
                    ->latest()
                    ->paginate($limit, ['*'], 'page', $page);
            } else {
                $items = Item::query()->latest()->paginate($limit, ['*'], 'page', $page);
            }

            return $items;
        } catch (\Throwable $th) {
            Log::error('Failed get items', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception("Failed get items");
        }
    }

    public function getItemBySlug($slug): ?array
    {
        try {
            $item = Item::where('slug', $slug)->firstOrFail();

            $featuredItems = Item::where('id', '!=', $item->id)
                // tags overlap
                ->where(function ($query) use ($item) {
                    $tags = is_iterable($item->tags) ? $item->tags : [];
                    foreach ($tags as $tag) {
                        $query->orWhere('tags', 'like', '%' . $tag . '%');
                    }
                })
                ->inRandomOrder()
                ->limit(4)
                ->get();

            return  ['item' => $item, 'featuredItems' => $featuredItems];
        } catch (\Throwable $th) {
            Log::error('Failed get item by slug', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception("Failed get item by slug");
        }
    }
}
