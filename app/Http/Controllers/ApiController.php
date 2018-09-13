<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index() {
        return "OK";
    }

    public function getAllCollections() {
        return response()->json(['collectionId' => "All", 'status' => 'Connected']);;
    }

    public function getCollection($id) {

    }

    public function getAnime($id) {

    }

    
}
