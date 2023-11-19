<?php

namespace App\Http\Controllers;

use App\Constants\TransactionConstants;
use App\Http\Requests\RecordPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Responses\Response;
use App\Repositories\PaymentRepositoryEloquent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * @group Payments
 * APIs for managing payments
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class PaymentController extends Controller
{
    public PaymentRepositoryEloquent $paymentRepository;


    public function __construct(PaymentRepositoryEloquent $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Get all payments
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/v1/payments",
     *     summary="Get all payments",
     *     security={{"bearerAuth":{}}},
     *     tags={"Payments"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', null);

        $query = $this->paymentRepository->getAllPayments();

        // Paginate the results
        $result = $query->orderBy('created_at', 'asc')->paginate($limit);

        $result = new PaymentResource($result);
        $result = $result->response()->getData(true);

        return Response::create()
            ->setData($result)
            ->setStatusCode(ResponseStatus::HTTP_OK)
            ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::PAYMENT_2002]))
            ->setResponseCode(TransactionConstants::PAYMENT_2002)
            ->success();
    }

    /**
     * Create a new payment
     *
     * @param \App\Http\Requests\RecordPaymentRequest $request
     * @return \Illuminate\Http\Response
     *
     * @OA\Post(
     *     path="/api/v1/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="string"),
     *             @OA\Property(property="amount_paid", type="number"),
     *             @OA\Property(property="transaction_id", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created"
     *     )
     * )
     */
    public function store(RecordPaymentRequest $request)
    {
        $data = $request->only(
            [
                'transaction_id',
                'amount_paid',
                'user_id',
            ]
        );
        $this->paymentRepository->create($data);

        return Response::create()
            ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::PAYMENT_2001]))
            ->setStatusCode(ResponseStatus::HTTP_CREATED)
            ->setResponseCode(TransactionConstants::PAYMENT_2001)
            ->success();
    }
}
