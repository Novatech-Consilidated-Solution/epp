<?php

declare(strict_types=1);

namespace Novatech\Epp\Response;

use Application\Infrastructure\Epp\Socket;
use DOMNode;
use Struzik\EPPClient\Response\Contact\InfoContactResponse;

class DomainListingResponse extends InfoContactResponse
{
    /**
     * @return array
     */
    public function getLatestDomains(): array
    {
	    $nodes = $this->get('//epp:epp/epp:response/epp:extension/cozac:infData/cozac:domain');
	    return array_map(static fn (DOMNode $node): string => $node->nodeValue, iterator_to_array($nodes));

    }
}
