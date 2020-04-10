<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Http\Resources\OrderCollection as OrderCollection;
use App\Http\Resources\OrderResource as OrderResource;
use App\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userOrders = Order::all();
        return new OrderCollection($userOrders);
    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

   
}
