<?php

namespace App\Repositories;

use App\Models\Transaction;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\TransactionRepository;

/**
 * Class TransactionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TransactionRepositoryEloquent extends BaseRepository implements TransactionRepository
{
    function search($search)
    {
        return $this->model->search($search);
    }

    function searchByUser($userId)
    {
        return $this->model->byUser($userId);
    }
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Transaction::class;
    }

    public function createTransaction(array $data)
    {
        return $this->create($data);
    }

    public function updateTransaction(array $data, $id)
    {
        $product = $this->find($id);
        if ($product) {
            $this->update($data, $id);
            return $this->find($id);
        }
        return null;
    }

    public function softDeleteTransaction($id)
    {
        $product = $this->find($id);
        if ($product) {
            return $product->delete(); // Soft delete the product
        }
        return false;
    }

    // Retrieve all products with description, amount, and user
    public function getAllTransactions()
    {
        return $this->model->select('description', 'amount', 'user_id');
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
