<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentRepositoryEloquent extends BaseRepository implements PaymentRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Payment::class;
    }

    // Retrieve all payments with amount_paid , user, and transaction
    public function getAllPayments()
    {
        return $this->model->select('id','amount_paid' , 'user_id','transaction_id');
    }
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
