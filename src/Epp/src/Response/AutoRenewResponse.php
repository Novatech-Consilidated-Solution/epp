<?php

namespace Novatech\Epp\Response;


use Novatech\Epp\Response\AutoRenew\Helper\AutoRenewInfo;
use Struzik\EPPClient\Response\Domain\UpdateDomainResponse;

class AutoRenewResponse extends UpdateDomainResponse
{
    /**
     * @return AutoRenewInfo|null
     */
    public function getInfo(): AutoRenewInfo|null
    {
        $node = $this->getFirst('//epp:epp/epp:response/epp:extension/cozad:cozaData');
        if ($node === null) {
            return null;
        }
        return new AutoRenewInfo($this, $node);
    }
}
