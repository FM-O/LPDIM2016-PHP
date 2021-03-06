<?php

namespace Framework\Http;

class Request extends AbstractMessage implements RequestInterface
{
    private $method;
    private $path;

    /**
     * Request constructor.
     * @param string $method The HTTP verb
     * @param string $path The resource path on the server
     * @param string $scheme The protocole name (HTTP or HTTPS)
     * @param string $schemeVersion The scheme version (ie: 1.0, 1.1, or 2.0)
     * @param array $headers An associative array of headers
     * @param string $body The request content
     */
    public function __construct($method, $path, $scheme, $schemeVersion, array $headers = [], $body = '')
    {
        parent::__construct($scheme, $schemeVersion, $headers, $body);

        $this->setMethod($method);
        $this->path = $path;
    }

    private static function parsePrologue($message)
    {
        $lines = explode("\n", $message);
        $result = preg_match('#^(?P<method>[A-Z]{3,7}) (?P<path>.+) (?P<scheme>HTTPS?)\/(?P<version>[1-2]\.[0-2])$#', $lines[0], $matches);
        if (!$result) {
            throw new MalformedHttpMessageException($message, 'HTTP message prologue is malformed.');
        }

        return $matches;
    }

    public static function createFromMessage($message)
    {
        if (!is_string($message) || empty($message)) {
            throw new MalformedHttpMessageException($message, 'HTTP message is not valid.');
        }

        // 1. Parse Prologue (first required line)
        $prologue = self::parsePrologue($message);

        // 4. Construct new instance of Request class with atomic data
        return new self(
            $prologue['method'],
            $prologue['path'],
            $prologue['scheme'],
            $prologue['version'],
            static::parseHeaders($message),
            static::parseBody($message)
        );
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

    public function getPath()
    {
        return $this->path;
    }

    protected function createPrologue() {
        return sprintf('%s %s %s/%s', $this->method, $this->path, $this->scheme, $this->schemeVersion);
    }
}