<?php

namespace App\Http\Controllers;

use App\Constants\TransactionConstants;
use App\Http\Responses\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

/**
 * @group Authentication
 *
 * APIs for managing user authentication.
 */
class AuthController extends Controller
{
    /**
     * Login user and generate access token.
     *
     * @bodyParam email string required User's email.
     * @bodyParam password string required User's password.
     *
     * @response {
     *   "data": {
     *     "token": "access_token",
     *     "name": "User Name"
     *   },
     *   "message": "User authenticated successfully.",
     *   "code": 200,
     *   "status": "success"
     * }
     *
     * @response 401 {
     *   "message": "Invalid credentials.",
     *   "code": 401,
     *   "status": "failure"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            return Response::create()
                ->setData($success)
                ->setStatusCode(ResponseStatus::HTTP_OK)
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::AUTH_4001]))
                ->setResponseCode(TransactionConstants::AUTH_4001)

                ->success();
        } else
            return Response::create()
                ->setStatusCode(ResponseStatus::HTTP_UNAUTHORIZED)
                ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::AUTH_4002]))
                ->setResponseCode(TransactionConstants::AUTH_4002)
                ->failure();
    }
    /**
     * Logout user and revoke access token.
     *
     * @authenticated
     *
     * security={{"bearerAuth":{}}}

     * @response {
     *   "message": "User logged out successfully.",
     *   "code": 200,
     *   "status": "success"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return Response::create()
            ->setStatusCode(ResponseStatus::HTTP_OK)
            ->setMessage(__(TransactionConstants::RESPONSE_CODES_MESSAGES[TransactionConstants::AUTH_4003]))
            ->setResponseCode(TransactionConstants::AUTH_4003)
            ->success();
    }
}
