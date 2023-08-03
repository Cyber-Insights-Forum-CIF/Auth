<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data =  parent::toArray($request);
        $data['user'] = $this->whenLoaded('user', function () {
            return [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ];
        });
        $data["articles"] = $this->whenLoaded('articles', function () {
            return $this->articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
            ];
            });
        });
        return $data;


        return parent::toArray($request);


    }
}
