<?php

namespace App\Http\Controllers;

use Dusterio\LumenPassport\Http\Controllers\AccessTokenController as BaseController;
use Psr\Http\Message\ServerRequestInterface;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Encoding\JoseEncoder;

class AccessTokenController extends BaseController
{
    public function issueToken(ServerRequestInterface $request)
    {
        $this->jwt = new Parser(new JoseEncoder());

        return parent::issueToken($request);
    }
}