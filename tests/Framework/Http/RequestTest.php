<?php

namespace Tests\Framework\Http;

use Framework\Http\Request;
include '/src/Framework/Http/Request.php';

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $scheme
     * @expectedException \InvalidArgumentException
     * @dataProvider provideInvalidHttpScheme
     */
    public function testUnsupportedHttpScheme($scheme) {
        new Request('GET', '/', $scheme, '1.1');
    }

    public function provideValidHttpScheme() {

        return [
            [Request::HTTP],
            [Request::HTTPS],
        ];
    }

    public function provideInvalidHttpScheme() {

        return [
            [ 'FTP' ],
            [ 'SFTP' ],
            [ 'SSH' ],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider provideInvalidHttpMethod
     */
    public function testUnsupportedHttpMethod($method) {
        new Request($method, '/', 'HTTP', '1.1');
    }

    public function provideInvalidHttpMethod() {
        return [
            [ 'FOO' ],
            [ 'BAR' ],
            [ 'BAZ' ],
            [ 'PURGE' ],
            [ 'TOTO' ]
        ];
    }

    /**
     * @dataProvider providerRequestParameters
     */
    public function testCreateRequestInstance($method, $path){
        $request = new Request($method, $path, Request::HTTP, '1.1');

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($path, $request->getPath());
        $this->assertSame(Request::HTTP, $request->getScheme());
        $this->assertSame('1.1', $request->getSchemeVersion());
        $this->assertEmpty($request->getHeaders());
        $this->assertEmpty($request->getBody());
    }

    public function providerRequestParameters() {
        return [
            [request::GET, '/'],
            [request::POST, '/'],
            [request::PUT, '/'],
            [request::DELETE, '/'],
            [request::PATCH, '/'],
            [request::OPTIONS, '/'],
            [request::HEAD, '/'],
            [request::TRACE, '/'],
        ];
    }

}
