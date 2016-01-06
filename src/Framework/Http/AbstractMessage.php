<?php
/**
 * Created by IntelliJ IDEA.
 * User: Flo
 * Date: 06/01/2016
 * Time: 11:41
 */

namespace Framework\Http;


abstract class AbstractMessage implements MessageInterface
{

    protected $scheme;
    protected $schemeVersion;
    protected $headers;
    protected $body;

    public function __construct($scheme, $schemeVersion, array $headers = [], $body = '')
    {
        $this->setScheme($scheme);
        $this->setSchemeVersion($schemeVersion);
        $this->setHeaders($headers);
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $headers
     * @return array
     */
    private function setHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->addHeader($header, $value);
        }
    }

    public function getHeader($name)
    {
        $name = strtolower($name);
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds new normalized header value to the list of all headers
     * @param string $header
     * @param string $value
     * @throws \RuntimeException
     */
    private function addHeader($header, $value)
    {
        $header = strtolower($header);
        if (isset($this->headers[$header]))
            throw new \RuntimeException(sprintf(
                'Header %s is already defined and cannot be set twice',
                $header
            ));
        $this->headers[$header] = $value;
    }

    /**
     * @param $scheme
     */
    private function setScheme($scheme)
    {
        $schemes = [self::HTTP, self::HTTPS];
        if (!in_array($scheme, $schemes))
            throw new \InvalidArgumentException(sprintf(
                'Scheme %s is not supported and must be one of %s',
                $scheme,
                implode(', ', $schemes)
            ));
        $this->scheme = $scheme;
    }

    private function setSchemeVersion($schemeVersion)
    {
        $schemeVersions = [self::VERSION_1_0, self::VERSION_1_1, self::VERSION_2_0];
        if (!in_array($schemeVersion, $schemeVersions)) {
            throw new \InvalidArgumentException(sprintf(
                'Scheme version %s is not supported and must be one of %s.',
                $schemeVersion,
                implode(', ', $schemeVersions)
            ));
        }
        $this->schemeVersion = $schemeVersion;
    }

    public function getScheme()
    {
        return $this->scheme;
    }


    public function getSchemeVersion()
    {
        return $this->schemeVersion;
    }

    protected abstract function createPrologue();

    final public function getMessage()
    {
        $message = $this->createPrologue();

        if (count($this->headers)) {
            $message .= "\n";
            foreach ($this->headers as $header => $value) {
                $message .= sprintf("%s: %s", $header, $value)."\n";
            }
        }

        $message .= "\n";
        if ($this->body) {
            $message .= $this->body;
        }
        return $message;
    }

    /**
     * String representation of a Request instance
     *
     * Alias of getMesssage()
     *
     * @return string
     */
    public function __toString() {
        return $this->getMessage();
    }

    // Parse content (if any)
    protected static function parseBody($message)
    {
        $pos = strpos($message, "\n"."\n");

        return (string) substr($message, $pos+2);
    }

    // Parse list of headers (if any)
    protected static function parseHeaders($message)
    {
        $start = strpos($message, "\n") + 1;
        $end = strpos($message, "\n"."\n");
        $length = $end - $start;
        $lines = explode("\n", substr($message, $start, $length));

        $i = 0;
        $headers = [];
        while (!empty($lines[$i])) {
            $line = $lines[$i];
            $result = preg_match('#^([a-z][a-z0-9-]+)\: (.+)$#i', $line, $header);
            if (!$result) {
                throw new MalformedHttpHeaderException(sprintf('Invalid header line at position %u: %s',$i+2, $line));
            }
            list(, $name, $value) = $header;

            $headers[$name] = $value;
            $i++;
        }
        return $headers;
    }
}