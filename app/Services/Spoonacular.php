<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Spoonacular
{
    protected $client;

    public function __construct()
    {
        $this->client = Http::baseUrl('https://api.spoonacular.com/')->withOptions([
            'query' => [
                'apiKey' => '1e8961e1fc5148a19450c5243daf6407'
            ]
        ]);
    }

    public function fetchFromUrl($url)
    {
        return $this->client->get('recipes/extract', [
            'url' => $url
        ]);
    }

    public function parseText($text)
    {
        return $this->client->asForm()->post('recipes/parseIngredients', [
            'ingredientList' => $text
        ]);
    }
}
