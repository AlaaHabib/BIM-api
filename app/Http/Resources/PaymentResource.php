<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return $this->collection->map(function ($payment) {
            $users[] = $payment->user;
            $transactions[] = $payment->transaction;

            return [
                'id'                  => $payment->id,
                'transaction'         => new TransactionResource($transactions),
                'amount_paid'              => $payment->amount_paid,
                'user'                => new UserResource($users),
            ];
        });
    }
}
