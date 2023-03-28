<?php

namespace Core\Http;

class Request
{
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getContentType(): string
    {
        return strpos($this->getPath(), '/api') === 0 ? 'application/json' : 'text/html';
    }

    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function isSecure(): bool
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }
}
