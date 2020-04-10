<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartItem;
use App\Http\Resources\CartItemCollection as CartItemCollection;
use App\Order;
use App\Product;
use Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{
   


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $userID = auth('api')->user()->getKey();
        }
        
        $cart = Cart::create([
            'id' => md5(uniqid(rand(), true)),
            'key' => md5(uniqid(rand(), true)),
            'userID' => isset($userID) ? $userID : null,

        ]);
        return response()->json([
            'Message' => 'A new cart have been created for you!',
            'cartToken' => $cart->id,
            'cartKey' => $cart->key,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart, Request $request)
    {
        $request->validate([
            'cartKey' => 'required',
        ]);

        if ($cart->key == $request->cartKey) {
            return response()->json([
                'cart' => $cart->id,
                'Items in Cart' => new CartItemCollection($cart->items),
            ], 200);
        }else{
            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }
        
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart, Request $request )
    {
        $request->validate([
            'cartKey' => 'required',
        ]);

        if ($cart->key == $request->cartKey) {
            $cart->delete();
            return response()->json(null, 204);
        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }

    }

    /**
     * Adds Products to the given Cart;
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return void
     */
    public function addProducts(Cart $cart, Request $request)
    {
        $request->validate([
            'cartKey' => 'required',
            'productID' => 'required',
            'quantity' => 'required|numeric|min:1|max:10',
        ]);

        if ($cart->key == $request->cartKey) {

            try {
                $Product = Product::findOrFail($request->productID);
            } catch(ModelNotFoundException $e) {
                return response()->json([
                    'message' => 'The Product you\'re trying to add does not exist.',
                ], 404);
            }

            $cartItem = CartItem::where(['cart_id' => $cart->getKey(), 'product_id' => $request->productID])->first();
            if ($cartItem) {
                $cartItem->quantity = $request->quantity;
                CartItem::where(['cart_id' => $cart->getKey(), 'product_id' => $request->productID])->update(['quantity' => $request->quantity]);
            } else {
                CartItem::create(['cart_id' => $cart->getKey(), 'product_id' => $request->productID, 'quantity' => $request->quantity]);
            }

            return response()->json(['message' => 'The Cart was updated with the given product information successfully'], 200);

        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }
    }

    /**
     * checkout the cart Items and create and order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return void
     */

    public function checkout(Cart $cart, Request $request)
    {
        if (Auth::guard('api')->check()) {
            $userID = auth('api')->user()->getKey();
        }

        $request->validate([
            'cartKey' => 'required',
            'name' => 'required',
            'adress' => 'required',
            'credit_card_number' => 'required',
            'expiration_year' => 'required',
            'expiration_month' => 'required',
            'cvc' => 'required',
        ]);


        if ($cart->key == $request->cartKey) {
            
            $TotalPrice = (float) 0.0;
            $items = $cart->items;

            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                $price = $product->price;
                $inStock = $product->UnitsInStock;

                if ($inStock >= $item->quantity) {
                    $TotalPrice = $TotalPrice + ($price * $item->quantity);

                    $product->UnitsInStock = $product->UnitsInStock - $item->quantity;
                    $product->save();
                } else {
                    return response()->json([
                        'message' => 'The quantity you\'re ordering of ' . $item->Name .
                        ' isn\'t available in stock, only ' . $inStock . ' units are in Stock, please update your cart to proceed',
                    ], 400);
                }
            }


            /**
             * Credit Card information should be sent to a payment gateway for processing and validation,
             
             * just assume that the information is sent and the payment process was done succefully,
             */

            $PaymentGatewayResponse = true;
            $transactionID = md5(uniqid(rand(), true));

            if ($PaymentGatewayResponse) {
                $order = Order::create([
                    'products' => json_encode(new CartItemCollection($items)),
                    'totalPrice' => $TotalPrice,
                    'name' => $request->name,
                    'address' => $request->adress,
                    'userID' => isset($userID) ? $userID : null,
                    'transactionID' => $transactionID,
                ]);

                $cart->delete();

                return response()->json([
                    'message' => 'you\'re order has been completed succefully, thanks for shopping with us!',
                    'orderID' => $order->getKey(),
                ], 200);
            }


        } else {

            return response()->json([
                'message' => 'The CarKey you provided does not match the Cart Key for this Cart.',
            ], 400);
        }
    }
}
