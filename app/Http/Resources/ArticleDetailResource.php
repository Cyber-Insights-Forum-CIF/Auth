<?php

namespace App\Http\Resources;
use App\Http\Resources\CategoryDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


            return  parent::toArray($request);
            $data =  parent::toArray($request);
            $data['user'] = $this->user;
            $data['category'] = $this->category;
            return $data;


    }
}

