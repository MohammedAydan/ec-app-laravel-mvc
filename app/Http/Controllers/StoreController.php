<?php

namespace App\Http\Controllers;

use App\Http\Services\StoreService;
use App\Models\Item;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    private StoreService $storeService;

    public function __construct()
    {
        $this->storeService = new StoreService();
    }
    /**
     * Display the store page.
     */
    public function index(Request $request)
    {
        try {
            $page = (int) $request->input('page', 1);
            $limit = min((int) $request->input('limit', 12), 60);
            $search = (string) $request->input('search', '');

            $items = $this->storeService->getItems($page, $limit, $search);
            return view('store', ['items' => $items]);
        } catch (\Throwable $th) {
            return view('store', ['items' => collect(), 'error' => 'Could not load items at this time.'])
                ->with('error', $th->getMessage());
        }
    }

    public function show($slug)
    {
        try {
            $response = $this->storeService->getItemBySlug($slug);
            return view('show', $response);
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 404, 'message' => $th->getMessage()]);
        }
    }
}
