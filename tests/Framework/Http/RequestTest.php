<?php

namespace Tests\Framework\Http;

use Framework\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testAddSameHttpHeaderTwice() {
        $headers = [
            'Content-Type' => 'text/xml',
            'CONTENT-TYPE' => 'application/json',
        ];
        new Request('GET', '/', 'HTTP', '1.1', $headers);
    }

    /**
     * @param $version
     * @expectedException \InvalidArgumentException
     * @dataProvider provideInvalidHttpSchemeVersion
     */
    public function testUnsupportedHttpSchemeVersion($version) {

        new Request('GET', '/', 'HTTP', $version);
    }

    public function provideInvalidHttpSchemeVersion() {
        return [
            [ '0.1' ],
            [ '0.5' ],
            [ '1.2' ],
            [ '1.5' ],
            [ '2.1' ],
        ];
    }

    /**
     * @dataProvider provideValidHttpSchemeVersion
     */
    public function testSupportedHttpSchemeVersion($version)
    {
        new Request('GET', '/', 'HTTP', $version);
    }
    public function provideValidHttpSchemeVersion()
    {
        return [
            [ Request::VERSION_1_0 ],
            [ Request::VERSION_1_1 ],
            [ Request::VERSION_2_0 ],
        ];
    }

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
        $request = new Request($method, $path, Request::HTTP, '1.1', [
            'Host' => 'http://wikipedia.com',
            'User-Agent' => 'Mozilla/Firefox',
        ]);

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($path, $request->getPath());
        $this->assertSame(Request::HTTP, $request->getScheme());
        $this->assertSame('1.1', $request->getSchemeVersion());
        $this->assertCount(2, $request->getHeaders());
        $this->assertSame(
            [ 'host' => 'http://wikipedia.com', 'user-agent' => 'Mozilla/Firefox' ],
            $request->getHeaders()
        );
        $this->assertSame('http://wikipedia.com', $request->getHeader('Host'));
        $this->assertSame('Mozilla/Firefox', $request->getHeader('User-Agent'));
        $this->assertEmpty($request->getBody());
    }

    public function providerRequestParameters() {
        return [
            [request::GET, '/'],
            [request::POST, '/home'],
            [request::PUT, '/foo'],
            [request::DELETE, '/bar'],
            [request::PATCH, '/option'],
            [request::OPTIONS, '/lol'],
            [request::HEAD, '/contact'],
            [request::TRACE, '/fr/article/42'],
        ];
    }

}
