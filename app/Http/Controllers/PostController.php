<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index() {
     return response()->json( [
       "success" => true,
       "message" => "We are here"
     ] ); 
   }
}
