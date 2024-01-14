<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request){
        logger($request);
        return response()->json(['message'=>$request]);
    }
}
