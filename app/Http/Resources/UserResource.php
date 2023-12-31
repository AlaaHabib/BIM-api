<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($user) {
            return [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'                => $user->email,
                'role'                  => $user->roles->pluck('name')->implode(', '),

            ];
        });
    }
}
