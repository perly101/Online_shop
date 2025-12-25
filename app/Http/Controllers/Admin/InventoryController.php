<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')->get();
        return view('admin.inventory', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $validated['image'] = $imageName;
        }

        $product = Product::create($validated);
        
        // Handle variants
        if ($request->has('variants')) {
            foreach ($request->variants as $index => $variant) {
                if (!empty($variant['name'])) {
                    $variantData = [
                        'flavor' => $variant['name'],
                        'price' => $variant['price'] ?? $product->price,
                        'stock' => $variant['stock'] ?? 0,
                    ];
                    
                    // Handle variant image upload
                    if ($request->hasFile("variants.{$index}.image")) {
                        $image = $request->file("variants.{$index}.image");
                        $imageName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                        $image->move(public_path('images'), $imageName);
                        $variantData['image'] = $imageName;
                    }
                    
                    $product->variants()->create($variantData);
                }
            }
        }
        
        return back()->with('success', 'Product added successfully');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.name' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $validated['image'] = $imageName;
        }

        $product->update($validated);

        // Update variants if provided
        if ($request->has('variants') && is_array($request->variants)) {
            // Delete existing variants and their images
            foreach ($product->variants as $variant) {
                if ($variant->image && file_exists(public_path('images/' . $variant->image))) {
                    unlink(public_path('images/' . $variant->image));
                }
            }
            $product->variants()->delete();

            // Create new variants
            foreach ($request->variants as $index => $variant) {
                if (!empty($variant['name'])) {
                    $variantData = [
                        'flavor' => $variant['name'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                    ];
                    
                    // Handle variant image upload
                    if ($request->hasFile("variants.{$index}.image")) {
                        $image = $request->file("variants.{$index}.image");
                        $imageName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                        $image->move(public_path('images'), $imageName);
                        $variantData['image'] = $imageName;
                    }
                    
                    $product->variants()->create($variantData);
                }
            }
        }

        return back()->with('success', 'Product updated successfully');
    }

    public function show($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return back()->with('success', 'Product deleted successfully');
    }
}
