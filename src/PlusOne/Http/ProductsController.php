<?php

declare(strict_types=1);

namespace Francken\PlusOne\Http;

use Francken\Treasurer\Product;
use Illuminate\Support\Collection;

final class ProductsController
{
    public function index() : Collection
    {
        $products = Product::query()
            ->where('beschikbaar', true)
            ->with(['extra'])
            ->get()
            ->map(fn (Product $product) : array => [
                'id' => $product->id,
                'naam' => $product->name,
                'prijs' => $product->price / 100,
                'categorie' => $product->categorie,
                "positie" => $product->position,
                "beschikbaar" => $product->available,
                "afbeelding" => $product->photo_url,
                "btw" => $product->btw,
                "eenheden" => $product->eenheden,
                "created_at" => $product->created_at,
                "updated_at" => $product->updated_at,
                "product_id" => $product->id,
                "splash_afbeelding" => $product->splash_url,
                "kleur" => $product->color,
            ]);

        return collect(['products' => $products]);
    }
}
