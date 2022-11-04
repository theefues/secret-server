<?php

/**
 * SecretController Class
 */
class SecretController extends BaseController
{
    /**
     * URI segments.
     * @var array
     */
    private $_uriSegments;

    /**
     * Current request method.
     * @var string
     */
    private $_requestMethod;

    /**
     * Response type.
     * @var string
     */
    private $_responseType;

    public function __construct()
    {
        $this->_uriSegments = $this->getUriSegments();
        $this->_requestMethod = strtoupper($_SERVER["REQUEST_METHOD"]);
        $this->_responseType = $this->getResponseType();
    }

    /**
     * /secret/get/:hash - Get secret by hash
     * @return void
     */
    public function getAction()
    {
        $errorMessage = '';

        if ($this->_requestMethod === 'GET') {

            if(!isset($this->_uriSegments[4])) {
                $this->sendOutput(
                    $this->formatResponseData(['error' => true, 'message' => "A hash must be provided."]),
                    [$this->_responseType, "HTTP/1.1 404 Not Found"]
                );
            }

            try {
                $secretService = new SecretService();
                $secret = $secretService->getSecret($this->_uriSegments[4]);
                $statusCode = $secret['statusCode'];
                unset($secret['statusCode']); 

                $responseData = $this->formatResponseData($secret);
            } catch (Error $e) {
                $errorMessage = $e->getMessage() . ' Something went wrong! Please contact support.';
                $errorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $errorMessage = 'Method not supported for this endpoint.';
            $errorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        if (!$errorMessage) {
            $this->sendOutput(
                $responseData,
                [$this->_responseType, $statusCode]
            );
        } else {
            $this->sendOutput(
                $this->formatResponseData(['error' => true, 'message' => $errorMessage]),
                [$this->_responseType, $errorHeader]
            );
        }
    }

    /**
     * /secret/add/ - Add new secret
     * @return void
     */
    public function addAction()
    {
        $errorMessage = '';

        if ($this->_requestMethod === 'POST') {
            try {
                $secretService = new SecretService();
                $secret = $secretService->addSecret($_POST);
                $responseData = $this->formatResponseData($secret);
            } catch (Error $e) {
                $errorMessage = $e->getMessage() . ' Something went wrong! Please contact support.';
                $errorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $errorMessage = 'Method not supported for this endpoint.';
            $errorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        if (!$errorMessage) {
            $this->sendOutput(
                $responseData,
                [$this->_responseType, 'HTTP/1.1 200 OK']
            );
        } else {
            $this->sendOutput(
                $this->formatResponseData(['error' => true, 'message' => $errorMessage]),
                [$this->_responseType, $errorHeader]
            );
        }
    }
}
