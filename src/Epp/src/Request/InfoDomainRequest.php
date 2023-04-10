<?php

namespace Novatech\Epp\Request;

use Novatech\Epp\Response\InfoDomainResponse;

class InfoDomainRequest extends \Struzik\EPPClient\Request\Domain\InfoDomainRequest
{
    public function getResponseClass(): string
    {
        return InfoDomainResponse::class;
    }
}
