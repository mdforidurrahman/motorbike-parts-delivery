<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $outlet = auth()->user()->outlet;
        $products = $outlet->products()->with('category')->latest()->paginate(20);
        
        return view('outlet.products.index', compact('products'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('outlet.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $outlet = auth()->user()->outlet;
        
        $product = new Product();
        $product->name = $request->name;
        $product->sku = 'SKU-' . strtoupper(Str::random(8));
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->category_id = $request->category_id;
        $product->outlet_id = $outlet->id;
        $product->is_available = $request->stock_quantity > 0;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/products/' . $filename);
            Image::make($image)->resize(500, 500)->save($path);
            $product->image = 'uploads/products/' . $filename;
        }
        
        $product->save();
        
        return redirect()->route('outlet.products.index')
            ->with('success', 'Product created successfully!');
    }
    
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->authorizeOutlet($product->outlet_id);
        
        $categories = Category::all();
        return view('outlet.products.edit', compact('product', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorizeOutlet($product->outlet_id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'category_id' => $request->category_id,
            'is_available' => $request->stock_quantity > 0
        ]);
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('uploads/products/' . $filename);
            Image::make($image)->resize(500, 500)->save($path);
            $product->image = 'uploads/products/' . $filename;
            $product->save();
        }
        
        return redirect()->route('outlet.products.index')
            ->with('success', 'Product updated successfully!');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->authorizeOutlet($product->outlet_id);
        
        // Delete image
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        
        $product->delete();
        
        return redirect()->route('outlet.products.index')
            ->with('success', 'Product deleted successfully!');
    }
    
    public function toggleAvailability($id)
    {
        $product = Product::findOrFail($id);
        $this->authorizeOutlet($product->outlet_id);
        
        $product->is_available = !$product->is_available;
        $product->save();
        
        return back()->with('success', 'Product availability updated!');
    }
    
    public function inventory()
    {
        $outlet = auth()->user()->outlet;
        $products = $outlet->products()
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get();
            
        return view('outlet.inventory', compact('products'));
    }
    
public function updateStock(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $this->authorizeOutlet($product->outlet_id);
    
    $request->validate([
        'stock_quantity' => 'required|integer|min:0'
    ]);
    
    $product->update([
        'stock_quantity' => $request->stock_quantity,
        'is_available' => $request->stock_quantity > 0
    ]);
    
    // Redirect back with success message instead of JSON
    return redirect()->back()->with('success', 'Stock updated successfully!');
}
    
    public function search(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('is_available', true)
            ->where('stock_quantity', '>', 0)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('outlet')
            ->limit(20)
            ->get();
            
        return response()->json($products);
    }
    
    public function checkAvailability($id)
    {
        $product = Product::findOrFail($id);
        
        return response()->json([
            'available' => $product->is_available && $product->stock_quantity > 0,
            'stock' => $product->stock_quantity,
            'price' => $product->price
        ]);
    }
    
    private function authorizeOutlet($outletId)
    {
        $userOutletId = auth()->user()->outlet->id;
        if ($userOutletId != $outletId) {
            abort(403, 'Unauthorized action.');
        }
    }
}