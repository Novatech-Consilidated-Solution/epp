<?php

namespace Novatech\Epp\Request;

use Novatech\Epp\Response\CancelPendingResponse;
use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Common\UpdateNode;
use Struzik\EPPClient\Node\Domain\DomainNameNode;
use Struzik\EPPClient\Node\Domain\DomainUpdateNode;
use Struzik\EPPClient\Request\AbstractRequest;
use Struzik\EPPClient\Request\RequestInterface;

class CancelPendingRequest extends AbstractRequest
{
    private string $domain = '';
    private string $action = '';

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }


    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getResponseClass(): string
    {
        return CancelPendingResponse::class;
    }

    protected function handleParameters(): void
    {
        $eppNode = EppNode::create($this);
        $commandNode = CommandNode::create($this, $eppNode);
        $updateNode = UpdateNode::create($this, $commandNode);
        $domainUpdateNode = DomainUpdateNode::create($this, $updateNode);
        DomainNameNode::create($this, $domainUpdateNode, $this->domain);
        CancelPendingRequest::createPendingActionNode($this, $commandNode, $this->action);
        TransactionIdNode::create($this, $commandNode);
    }

    /**
     * @throws \DOMException
     */
    public static function createPendingActionNode(
        RequestInterface $request,
        \DOMElement $parentNode,
        string $action
    ): \DOMElement {
        $updateNode = $request->getDocument()->createElement('cozadomain:update');
        $updateNode->setAttribute(
            'xsi:schemaLocation',
            'http://co.za/epp/extensions/cozadomain-1-0 coza-domain-1.0.xsd'
        );
        $updateNode->setAttribute('cancelPendingAction', $action);
        $extension = $request->getDocument()->createElement('extension');
        $extension->appendChild($updateNode);
        $parentNode->appendChild($extension);
        return $extension;
    }
}
