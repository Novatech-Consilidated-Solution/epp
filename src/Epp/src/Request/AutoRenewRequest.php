<?php

declare(strict_types=1);

namespace Novatech\Epp\Request;

use Novatech\Epp\Response\AutoRenewResponse;
use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Common\UpdateNode;
use Struzik\EPPClient\Node\Domain\DomainNameNode;
use Struzik\EPPClient\Node\Domain\DomainUpdateNode;
use Struzik\EPPClient\Request\Domain\UpdateDomainRequest;
use Struzik\EPPClient\Request\RequestInterface;

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
     * @throws \DOMException
     */
    protected function handleParameters(): void
    {
        $eppNode = EppNode::create($this);
        $commandNode = CommandNode::create($this, $eppNode);
        $updateNode = UpdateNode::create($this, $commandNode);
        $domainUpdateNode = DomainUpdateNode::create($this, $updateNode);
        DomainNameNode::create($this, $domainUpdateNode, $this->domain);
        AutoRenewRequest::createAutRenew($this, $commandNode, $this->autoRenew);
        TransactionIdNode::create($this, $commandNode);
    }

    /**
     * @throws \DOMException
     */
    public static function createAutRenew(
        RequestInterface $request,
        \DOMElement $parentNode,
        bool $autoRenew
    ): \DOMElement {
        $renewNode  = $request->getDocument()->createElement('cozadomain:autorenew', $autoRenew ? "true" : "false");
        $chg = $request->getDocument()->createElement('cozadomain:chg');
        $chg->appendChild($renewNode);
        $updateNode = $request->getDocument()->createElement('cozadomain:update');
        $updateNode->setAttribute(
            'xsi:schemaLocation',
            'http://co.za/epp/extensions/cozadomain-1-0 coza-domain-1.0.xsd'
        );
        $updateNode->appendChild($chg);
        $extension = $request->getDocument()->createElement('extension');
        $extension->appendChild($updateNode);
        $parentNode->appendChild($extension);
        return $extension;
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
     */
    public function setAutoRenew(bool $autoRenew): void
    {
        $this->autoRenew = $autoRenew;
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
    public function setDomain(string $domain): AutoRenewRequest
    {
        $this->domain = $domain;
        return $this;
    }
}
