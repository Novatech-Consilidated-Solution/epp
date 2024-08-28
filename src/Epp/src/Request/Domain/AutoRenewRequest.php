<?php

declare(strict_types=1);

namespace Novatech\Epp\Request;

use DOMException;
use Novatech\Epp\Domain\AutoRenewNode;
use Novatech\Epp\Response\AutoRenewResponse;
use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Common\UpdateNode;
use Struzik\EPPClient\Node\Domain\DomainNameNode;
use Struzik\EPPClient\Node\Domain\DomainUpdateNode;
use Struzik\EPPClient\Request\Domain\UpdateDomainRequest;

class AutoRenewRequest extends UpdateDomainRequest
{
    /**
     * @var bool
     */
    private $autoRenew;

    /**
     * @var  string
     */
    private $domain;

    /**
     * {@inheritdoc}
     */
    public function getResponseClass(): string
    {
        return AutoRenewResponse::class;
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
        AutoRenewNode::create($this, $commandNode, $this->autoRenew);
        TransactionIdNode::create($this, $commandNode);
    }

    /**
     * @return bool
     */
    public function isAutoRenew(): bool
    {
        return $this->autoRenew;
    }

	/**
	 * @param bool $autoRenew
	 * @return $this
	 */
    public function setAutoRenew(bool $autoRenew): self
    {
        $this->autoRenew = $autoRenew;
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
	 * @return $this
	 */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }
}
