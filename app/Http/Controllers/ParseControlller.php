<?php

namespace App\Http\Controllers;

use App\Http\Resources\ParsedItemResource;
use App\Http\Resources\ParsedUrlResource;
use App\Services\Spoonacular;
use Illuminate\Http\Request;

class ParseControlller extends Controller
{
    public function url(Request $request)
    {
        $response = app(Spoonacular::class)->fetchFromUrl($request->input('url'))->object();

        return new ParsedUrlResource($response);
    }

    public function text(Request $request)
    {
        $response = app(Spoonacular::class)->parseText($request->input('text'))->object();

        return ParsedItemResource::collection($response);
    }
}
