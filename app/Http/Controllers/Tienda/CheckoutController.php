<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('tienda.checkout.index');
    }

    public function confirmacion()
    {
        return view('tienda.checkout.confirmacion');
    }
}