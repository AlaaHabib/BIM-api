<?php

namespace App\Http\Controllers;

use App\Constants\TransactionConstants;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Responses\Response;
use App\Repositories\TransactionRepositoryEloquent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * @group Translations
 * APIs for managing translations
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class TransactionController extends Controller
{
    public TransactionRepositoryEloquent $transactionRepository;


    public function __construct(TransactionRepositoryEloquent $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
    /**
     * @OA\Get(
     *     path="/api/v1/transactions",
     *     summary="Get all transactions",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit the number of results",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *          name="page",
     *          description="page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter criteria",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sortOrder",
     *         in="query",
     *         description="Sort order (asc/desc)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transactions not found"
     *     )
     * )
     */
    // Show all transactions with description, amount, and user
    public function index(Request $request)
    {
        $limit = $request->query('limit', null);
        $query = $this->transactionRepository;
        if (!Auth::user()->hasRole('admin')) {
            $query = $query->searchByUser(Auth::user()->id);
        }
        if ($request->has('search')) {
            $query = $query->search($request->search);
        }
        // filter transactions by amount, and user
        if ($request->has('filter')) {
            $query = $query->filter($request->filter);
        }
        if (!$request->has('search') && !$request->has('filter')) {
            $query = $this->transactionRepository->getAllTransactions();
        }

        // Paginate the results
        // Sort transactions by description, amount, and user
        if ($request->has('sortBy') || $request->has('sortOrder'))
            $result = $query->orderBy($request->sortBy ?? 'created_at', $request->sortOrder ?? 'asc')->paginate($limit);
        else
            $result = $query->orderBy('created_at', 'asc')->paginate($limit);

        $result = new TransactionResource($result);
        $result = $result->response()->getData(true);

        return Response::create()
            ->setData($result)
            ->setStatusCode(ResponseStatus::HTTP_OK)
            ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1001]))
            ->setResponseCode(TransactionConstants::TRANSACTION_1001)
            ->success();
    }
    /**
     * @OA\Post(
     *     path="/api/v1/transactions",
     *     summary="Create a new transaction",
     *     security={{"bearerAuth":{}}},
     *     tags={"Transactions"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction created"
     *     )
     * )
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $data = $request->only(
                [
                    'description',
                    'amount',
                    'user_id',
                ]
            );
            $this->transactionRepository->create($data);

            return Response::create()
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1003]))
                ->setStatusCode(ResponseStatus::HTTP_CREATED)
                ->setResponseCode(TransactionConstants::TRANSACTION_1003)
                ->success();
        } catch (\Throwable $th) {
            return Response::create()
                ->setMessage($th)
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->failure();
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/transactions/{id}",
     *     summary="Get a transaction by ID",
     *      tags={"Transactions"},
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transaction",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $transaction = $this->transactionRepository->find($id);

            if (!Auth::user()->hasRole('admin') && $transaction->user_id !== Auth::id()) {
                return Response::create()
                    ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::AUTHOR_3001]))
                    ->setResponseCode(TransactionConstants::AUTHOR_3001)
                    ->setStatusCode(ResponseStatus::HTTP_UNAUTHORIZED)
                    ->failure();
            }
            $transactions[] = $transaction;
            $result = new TransactionResource($transactions);

            $result = $result->response()->getData(true);

            return Response::create()
                ->setData($result)
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1001]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1001)
                ->setStatusCode(ResponseStatus::HTTP_OK)
                ->success();
        } catch (\Throwable $th) {
            return Response::create()
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1004]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
    /**
     * @OA\Put(
     *     path="/api/v1/transactions/{id}",
     *     summary="Update a transaction by ID",
     *      tags={"Transactions"},
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transaction",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="category_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction updated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found"
     *     )
     * )
     */
    public function update(UpdateTransactionRequest $request, $id)
    {
        try {
            $transaction = $this->transactionRepository->update($request->all(), $id);

            $transactions[] = $transaction;
            $result = new TransactionResource($transactions);

            $result = $result->response()->getData(true);

            return Response::create()
                ->setData($result)
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1002]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1002)
                ->setStatusCode(ResponseStatus::HTTP_CREATED)
                ->success();
        } catch (\Exception $th) {
            return Response::create()
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1004]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
    /**
     * @OA\Delete(
     *     path="/api/v1/transactions/{id}",
     *     summary="Delete a transaction by ID",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the transaction",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->transactionRepository->softDeleteTransaction($id);

            return Response::create()
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1005]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1005)
                ->setStatusCode(ResponseStatus::HTTP_OK)
                ->success();
        } catch (\Exception $th) {
            return Response::create()
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::TRANSACTION_1004]))
                ->setResponseCode(TransactionConstants::TRANSACTION_1004)
                ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                ->failure();
        }
    }
}
