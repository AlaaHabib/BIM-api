<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'description', 'amount'];

    protected $fieldSearchable = [
        'description'
    ];
    protected $fieldFilterable = [
        'amount',
        'user_id'
    ];

    public function scopeSearch($query, $searchParams)
    {
        $searchArray = explode(';', $searchParams);
        $hasValidCriteria = false; // Initialize a flag to track if any valid criteria were provided

        foreach ($searchArray as $search) {
            list($field, $value) = explode(':', $search);
            if (in_array($field, $this->fieldSearchable)) {
                $query->where($field, 'like', '%' . $value . '%');
                $hasValidCriteria = true; // Set the flag to true if at least one valid criteria is found
            }
        }

        // If no valid criteria were found, return an empty query result
        if (!$hasValidCriteria) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function scopeByUser($query, $userId)
    {
        $query->whereHas('user', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopefilter($query, $filterParams)
    {
        $filterArray = explode(';', $filterParams);
        $hasValidCriteria = false; // Initialize a flag to track if any valid criteria were provided

        foreach ($filterArray as $filter) {
            list($field, $value) = explode(':', $filter);
            if (in_array($field, $this->fieldFilterable)) {
                if ($field == 'amount') {
                    $query->where('amount', '>=', $value);
                } else
                    $query->where($field, $value);
                $hasValidCriteria = true; // Set the flag to true if at least one valid criteria is found
            }
        }

        // If no valid criteria were found, return an empty query result
        if (!$hasValidCriteria) {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
