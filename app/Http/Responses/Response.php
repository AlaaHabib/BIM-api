<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response as SystemResponse;

class Response
{
    /**
    * Status code of the responses.
    *
    * @var int
    */
    public $statusCode;

    /**
    * Determine if the response is successful or failed.
    *
    * @var bool
    */
    public $isSuccessful;

    /**
    * Data of the response.
    *
    * @var array
    */
    public $data;

    /**
    * Mssage of the response.
    *
    * @var string
    */
    public $message;

    /**
    * Errors of the response.
    *
    * @var array
    */
    public $errors;

    /**
    * Response code of the response.
    *
    * @var string
    */
    public $responseCode;

    public function __construct()
    {
        $this->statusCode = SystemResponse::HTTP_OK;
        $this->isSuccessful = true;
        $this->data = (object)[];
        $this->message = "Successful Response";
        $this->errors = (object)[];
    }

    /**
    * Create object of the response
    */
    public static function create()
    {
        return new Response();
    }

    /**
    * Set statusCode field of the response
    */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
    * Set message field of the response
    */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
    * Set data field of the response
    */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
    * Set errors field of the response
    */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
    * Set responseCode field of the response
    */
    public function setResponseCode(string $responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
    * return json format of the response object in case success status
    */
    public function success()
    {
        return response()->json([
            'is_successful'     => true,
            'data'              => $this->data,
            'message'           => $this->message,
            'errors'            => $this->errors,
            'response_code'     => $this->responseCode
        ] , $this->statusCode);
    }

    /**
    * return json format of the response object in case failure status
    */
    public function failure()
    {
        return response()->json([
            'is_successful'     => false,
            'data'              => $this->data,
            'message'           => $this->message,
            'errors'            => $this->errors,
            'response_code'     => $this->responseCode
        ] , $this->statusCode);
    }
}
