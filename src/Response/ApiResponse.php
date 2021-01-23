<?php

namespace FryskeOranjekoeke\Response;

/**
 * Returns a JSON API Response.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class ApiResponse
{
    public function success($data): string
    {
        $this->setHeaders(200);
        return json_encode($data);
    }

    public function created($data): string
    {
        $this->setHeaders(201);
        return json_encode($data);
    }

    public function updated($data): string
    {
        $this->setHeaders(204);
        return json_encode($data);
    }

    public function removed(): string
    {
        $this->setHeaders(204);
        return json_encode();
    }

    public function badRequest($errors = null): string
    {
        return $this->error(400, $errors);
    }

    public function notFound(): string
    {
        return $this->error(404);
    }

    public function methodNotAllowed(string $expectedMethod): string
    {
        $msg = 'Expected ' . strtoupper($expectedMethod);
        return $this->error(405, $msg);
    }

    /**
     * @param mixed|null reason
     */
    public function error(int $statuscode, $errors = null): string
    {
        $response = [
            'errors' => []
        ];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $this->setHeaders($statuscode);
        return json_encode($response);
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
