<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryDetailResource extends JsonResource
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
        $data['articles'] = $this->articles;
        return $data;
        return parent::toArray($request);
    }
}
