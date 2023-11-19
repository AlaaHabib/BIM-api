<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($transaction) {
            $users[] = $transaction->user;

            return [
                'id'                  => $transaction->id,
                'description'                => $transaction->description,
                'amount'               => $transaction->amount,
                'user'            => new UserResource($users),
            ];
        });
    }
}
