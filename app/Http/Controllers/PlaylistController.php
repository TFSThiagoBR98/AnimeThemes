<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index() {
        return view('playlist_index');
    }

    public function play() {
        return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
    }

    public function menu() {
        return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
    }

    public function create_pl(Request $request) {
        return response()->json(['message' => 'Jobs API', 'status' => 'Connected']);;
    }
}
