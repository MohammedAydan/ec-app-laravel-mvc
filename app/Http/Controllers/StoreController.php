<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display the store page.
     */
    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $limit = min((int) $request->input('limit', 10), 60);
        $search = (string) $request->input('search', '');

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

        return view('store', ['items' => $items]);
    }

    public function show($slug)
    {
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

        return view('show', ['item' => $item, 'featuredItems' => $featuredItems]);
    }
}
