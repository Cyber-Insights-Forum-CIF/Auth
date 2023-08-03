<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


            return [
            'id' => $this->id,
            'author' => $this->user->name,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'title' => $this->category->title,
                ];
            }),
            'short_date' => $this->created_at->format('d m Y g:i A'),
            'long_date' => $this->created_at->format('d F Y'),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('d-F-Y g:i A'),
            'Carbon Time' =>  Carbon::now()->toDateTimeString(),

        ];

        $data =  parent::toArray($request);
        $data['user'] = $this->whenLoaded('user', function () {
            return [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'membership' => $this->user->membership
            ];
        });
        $data["category"] =$this->whenLoaded('category', function () {
            return [
                'title' => $this->category->title,
            ];
        });




        return $data;


    }
}
