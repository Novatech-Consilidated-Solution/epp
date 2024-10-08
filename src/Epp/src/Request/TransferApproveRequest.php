<?php

namespace Novatech\Epp\Request;

use Struzik\EPPClient\Node\Common\CommandNode;
use Struzik\EPPClient\Node\Common\EppNode;
use Struzik\EPPClient\Node\Common\TransactionIdNode;
use Struzik\EPPClient\Node\Common\TransferNode;
use Struzik\EPPClient\Node\Domain\DomainNameNode;
use Struzik\EPPClient\Node\Domain\DomainTransferNode;
use Struzik\EPPClient\Request\AbstractRequest;
use Struzik\EPPClient\Response\CommonResponse;

/**
 * Object representation of the request of the domain transferring with operation 'approve'.
 *
 * @package Novatech\Epp\Request
 * @author Bona Philippe<lukengup@aim.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 *
 */

class TransferApproveRequest extends AbstractRequest
{
	/**
	 * @var string $domain
	 */
	private string $domain = '';

	/**
	 * {@inheritdoc}
	 */
	public function getResponseClass(): string
	{
		return CommonResponse::class;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handleParameters(): void
	{
		$eppNode = EppNode::create($this);
		$commandNode = CommandNode::create($this, $eppNode);
		$transferNode = TransferNode::create($this, $commandNode, TransferNode::OPERATION_APPROVE);
		$domainTransferNode = DomainTransferNode::create($this, $transferNode);
		DomainNameNode::create($this, $domainTransferNode, $this->domain);
		TransactionIdNode::create($this, $commandNode);
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
	 * @return TransferApproveRequest
	 */
	public function setDomain(string $domain): self
	{
		$this->domain = $domain;
		return $this;
	}


}