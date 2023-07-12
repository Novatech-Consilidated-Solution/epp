<?php

namespace Novatech\Epp\Domain;

use Struzik\EPPClient\Request\RequestInterface;

/**
 * Object representation of the <cozadomain:update> node.
 * @package Novatech\Epp\Domain
 */
class DomainListingNode
{
	/**
	 * @param RequestInterface $request
	 * @param \DOMElement $parentNode
	 * @return \DOMElement
	 * @throws \DOMException
	 */
	public static function create(RequestInterface $request, \DOMElement $parentNode): \DOMElement
	{
		$balance = $request->getDocument()->createElement('cozacontact:domainListing', "true");
		$info = $request->getDocument()->createElement('cozacontact:info');
		$info->appendChild($balance);
		$extension = $request->getDocument()->createElement('extension');
		$extension->appendChild($info);
		$parentNode->appendChild($extension);
		return $extension;
	}

}