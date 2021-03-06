<?php
/**
 * Created by IntelliJ IDEA.
 * User: Flo
 * Date: 06/01/2016
 * Time: 15:33
 */

namespace Framework\Http;


class Response extends AbstractMessage
{
    private $statusCode;

    private static $reasonPhrases = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Page Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        418 => 'I\'m a teapot',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    public function __construct($statusCode, $scheme, $schemeVersion, array $headers, $body)
    {
        parent::__construct($scheme, $schemeVersion, $headers, $body);

        $this->setStatusCode($statusCode);
    }

    public function getReasonPhrase()
    {
        return isset(self::$reasonPhrases[$this->statusCode]) ? self::$reasonPhrases[$this->statusCode] : '';
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    private function setStatusCode($statusCode)
    {
        $statusCode = (int) $statusCode;
        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException('Invalid status code');
        }
    }
    protected function createPrologue()
    {
        return sprintf('%s/%s %u %s', $this->scheme, $this->schemeVersion, $this->statusCode, $this->getReasonPhrase());
    }

    final public static function createFromMessage($message)
    {
        if (!is_string($message) || empty($message)) {
            throw new MalformedHttpMessageException($message, 'HTTP message is not valid.');
        }
        // 1. Parse prologue (first required line)
        $prologue = static::parsePrologue($message);
        // 4. Construct new instance of Request class with atomic data
        return new self(
            $prologue['status'],
            $prologue['scheme'],
            $prologue['version'],
            static::parseHeaders($message),
            static::parseBody($message)
        );
    }

    private static function parsePrologue($message)
    {
        $lines = explode("\n", $message);
        $result = preg_match('#^(?P<scheme>HTTPS?)\/(?P<version>[1-2]\.[0-2]) (?P<status>[1-5][0-9]{2})#', $lines[0], $matches);
        if (!$result) {
            throw new MalformedHttpMessageException($message, 'HTTP message prologue is malformed.');
        }

        return $matches;
    }
}