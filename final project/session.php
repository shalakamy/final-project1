<?php
// Use Laravel Framework: routes, controllers, blade templates, models

// Route definitions: web.php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// ProductController.php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }
}

// CartController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $cart = session()->get('cart', []);
        $cart[$request->id] = [
            'name' => $request->name,
            'price' => $request->price,
            'quantity' => ($cart[$request->id]['quantity'] ?? 0) + $request->quantity,
        ];
        session(['cart' => $cart]);
        return redirect()->route('cart.view');
    }

    public function view()
    {
        $cart = session('cart', []);
        return view('cart.view', compact('cart'));
    }
}

// CheckoutController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $cart = session('cart', []);
        // Example: Store Order in DB or Process Payment
        session()->forget('cart');
        return redirect('/')->with('success', 'Order placed successfully!');
    }
}

// Product model: Product.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description'];
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> final project</title>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>

// products/index.blade.php
@extends('layouts.app')
@section('content')
<h1>Product List</h1>
<div class="products">
    @foreach ($products as $product)
        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>{{ $product->description }}</p>
            <p>Price: ${{ $product->price }}</p>
            <a href="{{ route('products.show', $product->id) }}">View</a>
        </div>
    @endforeach
</div>
@endsection

// products/show.blade.php
@extends('layouts.app')
@section('content')
<h1>{{ $product->name }}</h1>
<p>{{ $product->description }}</p>
<p>Price: ${{ $product->price }}</p>
<form method="POST" action="{{ route('cart.add') }}">
    @csrf
    <input type="hidden" name="id" value="{{ $product->id }}">
    <input type="hidden" name="name" value="{{ $product->name }}">
    <input type="hidden" name="price" value="{{ $product->price }}">
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" value="1">
    <button type="submit">Add to Cart</button>
</form>
@endsection

// cart/view.blade.php
@extends('layouts.app')
@section('content')
<h1>Shopping Cart</h1>
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
    </tr>
    @foreach ($cart as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td>${{ $item['price'] }}</td>
            <td>{{ $item['quantity'] }}</td>
        </tr>
    @endforeach
</table>
<a href="{{ route('checkout.process') }}">Checkout</a>
@endsection

