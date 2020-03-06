<?php

namespace FryskeOranjekoeke\Response;

/**
 * Returns a JSON API Response.
 *
 * @TODO Moet netter
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 *
 * @return application/json Returns an JSON-Object containing the data to output.
 *         {
 *             "Success": @var $string,
 *             "URN": @var string,
 *             "Method": @var string,
 *             "Status": {
 *                 "Code": @var int,
 *                 "Msg": @var string
 *             }
 *             "Data": @var array
 *         }
 */
class ApiResponse
{
    /**
     * @var array
     */
    protected $baseResponseBody = [
        'Success' => true,
        'URN'     => null,
        'Method'  => null,
        'Status'  => [
            'Code' => 200,
            'Msg'  => null
        ],
        'Data'    => [
        ]
    ];

    /**
     * @var int
     */
    protected $status = 200;

    /**
     * @var array
     */
    protected $statusMap = [
        200 => 'Success',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed'
    ];

    /**
     * The data to return.
     *
     * @var array
     */
    protected $responseData = null;

    /**
     * Gets the Array containing the Status Request information.
     */
    public function getStatusArray(int $status = 200): array
    {
        if (empty($this->statusMap[$status])) {
            throw new \InvalidArgumentException('The given status code is invalid.');
        }

        return [
            'Code' => $status,
            'Msg'  => $this->statusMap[$status]
        ];
    }

    public function setStatus(int $code): void
    {
        $this->status = $code;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Gets the Response Body that will be returned with this Response.
     */
    public function getResponseBody(): array
    {
        $response          = (object) $this->baseResponseBody;
        $response->Success = ($this->getStatus() === 200);
        $response->URN     = $_SERVER['REQUEST_URI'];
        $response->Method  = $_SERVER['REQUEST_METHOD'];
        $response->Status  = $this->getStatusArray($this->getStatus());
        $response->Data    = $this->getResponseData();

        return (array) $response;
    }

    public function getResponseData(): array
    {
        $firstElement = ($this->responseData[key($this->responseData)] ?? null);
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
        $this->setStatus($code);

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
        echo json_encode($this->getResponseBody());
        die();
    }
}
