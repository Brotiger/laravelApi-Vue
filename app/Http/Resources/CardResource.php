<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'desk_list_id' => $this->desk_list_id,
            'created_at' => $this->created_at,
            'tasks' => TaskResource::collection($this->tasks)
        ];
    }
}
