<?php

namespace Novatech\Epp\Request;

use DOMException;
use Novatech\Epp\Domain\PendingNode;
use Novatech\Epp\Response\CancelPendingResponse;
use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Common\UpdateNode;
use Struzik\EPPClient\Node\Domain\DomainNameNode;
use Struzik\EPPClient\Node\Domain\DomainUpdateNode;
use Struzik\EPPClient\Request\AbstractRequest;

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
	 * @return CancelPendingRequest
	 */
    public function setAction(string $action): self
    {
        $this->action = $action;
		return $this;
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
	 * @return CancelPendingRequest
	 */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
		return $this;
    }

    public function getResponseClass(): string
    {
        return CancelPendingResponse::class;
    }

	/**
	 * @throws DOMException
	 */
	protected function handleParameters(): void
    {
        $eppNode = EppNode::create($this);
        $commandNode = CommandNode::create($this, $eppNode);
        $updateNode = UpdateNode::create($this, $commandNode);
        $domainUpdateNode = DomainUpdateNode::create($this, $updateNode);
        DomainNameNode::create($this, $domainUpdateNode, $this->domain);
        PendingNode::create($this, $commandNode, $this->action);
        TransactionIdNode::create($this, $commandNode);
    }



}
