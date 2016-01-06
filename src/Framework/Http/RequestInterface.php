<?php
/**
 * Created by IntelliJ IDEA.
 * User: Flo
 * Date: 06/01/2016
 * Time: 16:50
 */

namespace Framework\Http;


interface RequestInterface extends MessageInterface
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';
    const TRACE = 'TRACE';

    public function getMethod();
    public function getPath();
}