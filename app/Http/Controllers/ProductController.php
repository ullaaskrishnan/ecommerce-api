<?php

namespace App\Http\Controllers;

use App\Product;
use App\Http\Resources\ProductCollection as ProductCollection;
use App\Http\Resources\ProductResource as ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth:api_admin',['except'=>['index','show']]);
               
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        return ProductCollection::collection(Product::all());
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
        ]);

        $product = new Product;
        $product->sku = $request->sku ;
        $product->Name = $request->name ;
        $product->description = $request->description ;
        $product->price = $request->price ;
        $product->UnitsInStock = $request->stock ;
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ],201);
         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        
         
        if ($request->has('name')) {
            $request['Name'] = $request->name;
            unset($request['name']);
        }

        if ($request->has('stock')) {
            $request['UnitsInStock'] = $request->stock;
            unset($request['stock']);
        }

        $product->update($request->all());

        return response([
            'data' => new ProductResource($product)
        ],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response(null,204);
    }
}
