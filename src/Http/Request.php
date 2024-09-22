<?php

namespace App\Http;

use App\Exception\Http\HttpException;

class Request
{
    public function __construct(
        private array $get,
        private array $server
    ){
    }

    /**
     * В суперглобальном массиве $_SERVER
     * значение URI хранится под ключом REQUEST_URI
     * @throws HttpException
     */
    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException('Cannot get path from the request');
        }

        $components = parse_url($this->server['REQUEST_URI']);

        if (!is_array($components) || !array_key_exists('path', $components)) {
            // Если мы не можем получить путь - бросаем исключение
            throw new HttpException('Cannot get path from the request');
        }

        return $components['path'];
    }

    /**
     * Метод получает значение параметра
     * строки запроса
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
            throw new HttpException(
                "No such query param in the request: $param"
            );
        }

        $value = trim($this->get[$param]);

        if (empty($value)) {
            throw new HttpException(
                "Empty query param in the request: $param"
            );
        }

        return $value;
    }

    /**
     * Метод получает значение заголовка запроса
     * @throws HttpException
     */
    public function header(string $header): string
    {
        $headerName = mb_strtoupper("http_" . str_replace('-', '_', $header));

        if (!array_key_exists($headerName, $this->server)) {
            throw new HttpException(
                "No such header in the request: $header"
            );
        }

        $value = trim($this->server[$headerName]);

        if (empty($value)) {
            throw new HttpException(
                "Empty header in the request: $header"
            );
        }

        return $value;
    }
}