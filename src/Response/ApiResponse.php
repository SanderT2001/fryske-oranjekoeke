<?php

namespace FryskeOranjekoeke\Response;

/**
 * Returns a JSON API Response.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class ApiResponse
{
    /**
     * The data to return.
     *
     * @var array
     */
    protected $responseData = null;

    public function getResponseData(): array
    {
        $firstElement = $this->responseData[key($this->responseData)];
        if (!is_object($firstElement)) {
            return $this->responseData;
        }
        $reflection = new \ReflectionClass($firstElement);
        $shortNamePlural = ($reflection->getShortName() . 's');
        return [$shortNamePlural => $this->responseData];
    }

    public function setResponseData(array $data): void
    {
        $this->responseData = $data;
    }

    public function __construct(array $data, int $code = 200)
    {
        $this->setResponseData($data);

        $this->setHeaders($code);
        $this->output();
    }

    /**
     * Sets the correct Header in order to return JSON.
     */
    protected function setHeaders(int $statuscode = 200)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, PUT, POST, PATCH, DELETE, OPTIONS');
        http_response_code($statuscode);
    }

    /**
     * Outputs the @var ApiResponse::responseData as JSON.
     */
    protected function output()
    {
        echo json_encode($this->getResponseData());
        die();
    }
}
