<?php

declare(strict_types=1);

namespace Novatech\Epp\Response;

use Application\Infrastructure\Epp\Socket;
use Struzik\EPPClient\Response\Contact\InfoContactResponse;

class DomainListingResponse extends InfoContactResponse
{
    /**
     * @return \DOMNodeList
     */
    public function getLatestDomains(): \DOMNodeList
    {
        return $this->get('//epp:epp/epp:response/epp:extension/cozac:infData/cozac:domain');
    }
}
