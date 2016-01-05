<?php

namespace Framework\Http;

class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';
    const TRACE = 'TRACE';
    const HTTP = 'HTTP';
    const HTTPS = 'HTTPS';

    private $method;
    private $scheme;
    private $schemeVersion;
    private $path;
    private $headers;
    private $body;

    /**
     * Request constructor.
     * @param string $method        The HTTP verb
     * @param string $path          The resource path on the server
     * @param string $scheme        The protocole name (HTTP or HTTPS)
     * @param string $schemeVersion The scheme version (ie: 1.0, 1.1, or 2.0)
     * @param array $headers        An associative array of headers
     * @param string $body          The request content
     */
    public function __construct($method, $path, $scheme, $schemeVersion, array $headers = [], $body = '')
    {
        $this->setMethod($method);
        $this->path = $path;
        $this->setScheme($scheme);
        $this->schemeVersion = $schemeVersion;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $methods = [
            self::GET,
            self::POST,
            self::PUT,
            self::DELETE,
            self::PATCH,
            self::OPTIONS,
            self::HEAD,
            self::TRACE,
        ];

        if (!in_array($method, $methods))
            throw new \InvalidArgumentException(sprintf(
                'Method %s is not supported and must be one of %s',
                $method,
                implode(', ', $methods)
            ));
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param $scheme
     */
    private function setScheme($scheme)
    {
        $schemes = [ self::HTTP, self::HTTPS ];
        if(!in_array($scheme, $schemes))
            throw new \InvalidArgumentException(sprintf(
                'Scheme %s is not supported and must be one of %s',
                $scheme,
                implode(', ' ,$schemes)
            ));
        $this->scheme = $scheme;
    }

    public function getScheme()
    {
        return $this->scheme;
    }


    public function getSchemeVersion()
    {
        return $this->schemeVersion;
    }


    public function getPath()
    {
        return $this->path;
    }


    public function getHeaders()
    {
        return $this->headers;
    }


    public function getBody()
    {
        return $this->body;
    }
}