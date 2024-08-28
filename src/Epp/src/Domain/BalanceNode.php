<?php

namespace Novatech\Epp\Domain;

use DOMElement;
use DOMException;
use Struzik\EPPClient\Request\RequestInterface;

/**
 * Object representation of the <balance> node.
 * @package Novatech\Epp\Domain
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Bona Philippe Lukengu<lukengup@aim.com>
 */
class BalanceNode
{
	/**
	 * @param RequestInterface $request
	 * @param DOMElement $parentNode
	 * @return DOMElement
	 * @throws DOMException
	 */
	public static function create(RequestInterface $request, DOMElement $parentNode): DOMElement
	{
		$balance = $request->getDocument()->createElement('cozacontact:balance', "true");
		$info = $request->getDocument()->createElement('cozacontact:info');
		$info->appendChild($balance);
		$extension = $request->getDocument()->createElement('extension');
		$extension->appendChild($info);
		$parentNode->appendChild($extension);
		return $extension;
	}

}