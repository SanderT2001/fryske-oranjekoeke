<?php

namespace FryskeOranjekoeke\Response;

/**
 * Returns a JSON API Response.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class ApiResponse
{
    protected $responseBodyStruct = [
        'urn'     => null,
        'method'  => null,
        'success' => true,
        'status'  => [
            'code' => 200,
            'msg'  => null
        ],
        /**
         * Based on success or error:
        'reason' => null,
        'data' => []
         */
    ];

    protected $statusMap = [
        200 => 'Success',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict'
    ];

    public function getStatus(int $statuscode): array
    {
        if (empty($this->statusMap[$statuscode]))
            throw new \InvalidArgumentException('The given status code is invalid.');

        return [
            'code' => $statuscode,
            'msg'  => $this->statusMap[$statuscode]
        ];
    }

    public function getResponseBodyStruct(): object
    {
        return (object) $this->responseBodyStruct;
    }

    public function success($data): string
    {
        $response = $this->getBaseOutput(200);
        $response->data = $data;

        $this->setHeaders(200);
        return json_encode($response);
    }

    public function created(): string
    {
        $response = $this->getBaseOutput(201);

        $this->setHeaders(201);
        return json_encode($response);
    }

    public function updated(): string
    {
        $response = $this->getBaseOutput(204);

        $this->setHeaders(204);
        return json_encode($response);
    }

    public function removed(): string
    {
        $response = $this->getBaseOutput(204);

        $this->setHeaders(204);
        return json_encode($response);
    }

    /**
     * @param mixed|null reason
     */
    public function error(int $statuscode, $reason = null): string
    {
        $response = $this->getBaseOutput($statuscode);
        if (!empty($reason))
            $response->reason = $reason;

        $this->setHeaders($statuscode);
        return json_encode($response);
    }

    protected function getBaseOutput(int $statuscode): object
    {
        $response = $this->getResponseBodyStruct();
        $response->urn     = $_SERVER['REQUEST_URI'];
        $response->success = $statuscode === 200;
        $response->method  = $_SERVER['REQUEST_METHOD'];

        $status_struct = $this->getStatus($statuscode);
        $response->status['code'] = $status_struct['code'];
        $response->status['msg'] = $status_struct['msg'];
        return $response;
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
}
