<?php

namespace Novatech\Epp\Domain;

use DOMElement;
use DOMException;
use Struzik\EPPClient\Request\RequestInterface;

/**
 * Object representation of the <cozadomain:update> node.
 * @package Novatech\Epp\Domain
 */
class PendingNode
{
	/**
	 * @param RequestInterface $request
	 * @param DOMElement $parentNode
	 * @param string $action
	 * @return DOMElement
	 * @throws DOMException
	 */
	public static function create(RequestInterface $request, DOMElement $parentNode, string $action): DOMElement {
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