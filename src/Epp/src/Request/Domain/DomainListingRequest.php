<?php

declare(strict_types=1);

namespace Novatech\Epp\Request;

use DOMException;
use Novatech\Epp\Domain\DomainListingNode;
use Novatech\Epp\Response\DomainListingResponse;
use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\InfoNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Contact\ContactIdentifierNode;
use Struzik\EPPClient\Node\Contact\ContactInfoNode;
use Struzik\EPPClient\Request\AbstractRequest;

class DomainListingRequest extends AbstractRequest
{
    private string $identifier = '';
    /**
     * {@inheritdoc}
     */
    public function getResponseClass(): string
    {
        return DomainListingResponse::class;
    }

    /**
     * Setting the identifier of the contact. REQUIRED.
     *
     * @param string $identifier contact identifier
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Getting the identifier of the contact.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @throws DOMException
     */
    protected function handleParameters(): void
    {
        $eppNode = EppNode::create($this);
        $eppNode->setAttribute('xmlns:cozacontact', 'http://co.za/epp/extensions/cozacontact-1-0');
        $commandNode = CommandNode::create($this, $eppNode);
        $infoNode = InfoNode::create($this, $commandNode);
        $contactInfoNode = ContactInfoNode::create($this, $infoNode);
        ContactIdentifierNode::create($this, $contactInfoNode, $this->identifier);
        DomainListingNode::create($this, $commandNode);
        TransactionIdNode::create($this, $commandNode);
    }



}
