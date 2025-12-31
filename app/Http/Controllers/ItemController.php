<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Services\ImageService;
use App\Http\Services\ItemService;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private ItemService $itemService;

    public function __construct()
    {
        $this->itemService = new ItemService();
    }

    public function index(Request $request)
    {
        try {
            $page = (int) $request->input('page', 1);
            $limit = min((int) $request->input('limit', 15), 100);
            $items = $this->itemService->getItems($page, $limit);

            return view('admin.item.index', compact('items'));
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $item = $this->itemService->getItemById($id);
            $featuredItems = $this->itemService->featuredItems($id, 4);

            return view('admin.item.show', compact('item', 'featuredItems'));
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 404, 'message' => $th->getMessage()]);
        }
    }

    public function create()
    {
        return view('admin.item.create');
    }

    public function store(StoreItemRequest $request)
    {
        try {
            $request->merge([
                'tags' => $this->itemService->normalizeTagsInput($request->input('tags')),
            ]);

            $validated = $request->validated();

            $this->itemService->create(
                $validated,
                $request->hasFile('image'),
                $request->file('image')
            );

            return redirect()->route('admin.items.index')->with('success', 'Item created successfully.');
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $item = $this->itemService->getItemById($id);
            return view('admin.item.edit', compact('item'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function update(UpdateItemRequest $request, $id)
    {
        try {
            $request->merge([
                'tags' => $this->itemService->normalizeTagsInput($request->input('tags')),
            ]);
            $validated = $request->validated();

            $this->itemService->update(
                $validated,
                $id,
                $request->hasFile('image'),
                $request->file('image')
            );

            return redirect()->route('admin.items.index')->with('success', 'Item updated successfully.');
        } catch (\Throwable $th) {
            return back()->withInput()->with("error", $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $item = $this->itemService->getItemById($id);
            return view('admin.item.delete', compact('item'));
        } catch (\Throwable $th) {
            return back()->withInput()->with("error", $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->itemService->delete($id);
            return redirect()->route('admin.items.index')->with('success', 'Item deleted successfully.');
        } catch (\Throwable $th) {
            return back()->withInput()->with("error", $th->getMessage());
        }
    }
}
