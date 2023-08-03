<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            'role' => $this->when(auth()->check() && auth()->user()->role === 'admin', function () {
                return auth()->user()->role;
            }),
            'categories' => $this->when(auth()->check() && auth()->user()->role === 'admin', function () {
                return  $this->categories->map(function ($category) {
                    return [
                        'title' => $category->title,
                        'slug' => $category->slug,

                    ];

                });
            }),
            "articles" => $this->whenLoaded('articles', function () {
                return $this->articles->map(function ($article) {
                return [

                    'title' => $article->title,
                ];
                });
            }),


        ];
    }
}
