<?php

namespace Novatech\Epp\Domain;

use Struzik\EPPClient\Request\RequestInterface;

/**
 * Object representation of the <domain:autorenew> node.
 *
 * @package Novatech\Epp\Domain
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Bona Philippe Lukengu <lukengup@aim.com>
 *
 */
class AutoRenewNode
{
	/**
	 * @param RequestInterface $request
	 * @param \DOMElement $parentNode
	 * @param bool $autoRenew
	 * @return \DOMElement
	 * @throws \DOMException
	 */
	public static function create(RequestInterface $request, \DOMElement $parentNode, bool $autoRenew): \DOMElement
	{
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
}