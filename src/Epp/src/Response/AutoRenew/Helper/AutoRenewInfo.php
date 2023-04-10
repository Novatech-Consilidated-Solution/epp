<?php

declare(strict_types=1);

namespace Novatech\Epp\Response\AutoRenew\Helper;

use DOMElement;
use Struzik\EPPClient\Response\ResponseInterface;
use UnexpectedValueException;

class AutoRenewInfo
{
    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;
    /**
     * @var \DOMNode
     */
    private \DOMNode $node;

    public function __construct(ResponseInterface $response, \DOMNode $node)
    {
        if ($node->nodeName !== 'cozad:cozaData') {
            throw new UnexpectedValueException(
                sprintf(
                    'The name of the passed node must be "contact:postalInfo", "%s" given.',
                    $node->nodeName
                )
            );
        }

        $this->response = $response;
        $this->node = $node;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->response->getFirst('cozad:detail', $this->node)->nodeValue;
    }

    public function getResult(): string
    {
        /* @var DOMElement $element */
        $element = $this->response->get('cozad:detail', $this->node)->item(0);
        return $element->getAttribute("result");
    }
}
