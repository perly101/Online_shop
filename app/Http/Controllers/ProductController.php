<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return view('products.show', compact('product'));
    }
    
    public function similar($id)
    {
        $product = Product::findOrFail($id);
        // Get 4 random products excluding the current one
        $similarProducts = Product::where('id', '!=', $id)
            ->inRandomOrder()
            ->limit(4)
            ->get(['id', 'name', 'price', 'image']);
        
        return response()->json($similarProducts);
    }
}
