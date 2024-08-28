<?php

namespace Novatech\Epp\Response;

class InfoDomainResponse extends \Struzik\EPPClient\Response\Domain\InfoDomainResponse
{
    public function getNameservers(): array
    {
        $nodes = $this->get('//epp:epp/epp:response/epp:resData/domain:infData/domain:ns/domain:hostAttr');
        return array_map(static fn (\DOMNode $node): string => $node->nodeValue, iterator_to_array($nodes));
    }
    public function isAutoRenew(): bool
    {
        $node = $this->getFirst('//epp:epp/epp:response/epp:resData/domain:infData/domain:renew');
        return $node != null && $node->nodeValue == 'y';
    }
}
