<?php

declare(strict_types=1);

namespace Novatech\Epp\Response;

use Struzik\EPPClient\Response\Contact\InfoContactResponse;

class BalanceResponse extends InfoContactResponse
{
    public function getBalance(): string
    {
        return $this->getFirst('//epp:epp/epp:response/epp:extension/cozac:infData/cozac:balance')->nodeValue;
    }
}
