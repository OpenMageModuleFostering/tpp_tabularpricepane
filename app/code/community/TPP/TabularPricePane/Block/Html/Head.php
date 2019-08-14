<?php
/**
 *
 * Tabular Price Pane
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to helpdesk@ikhodal.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package     TPP_TabularPricePane
 * @copyright   Copyright (c) 2016-2017 ikhodal (http://www.ikhodal.com).
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TPP_TabularPricePane_Block_Html_Head extends Mage_Page_Block_Html_Head
{
	public function getCssJsHtml()
	{   
		$__html = "<script type='text/javascript'> var site_url = '".$this->getUrl('')."'; var js_url = '".$this->getJsUrl('')."'; var skin_url = '".$this->getSkinUrl('')."'; </script>
<!--[if IE 8]><script type='text/javascript'> document.getElementsByClassName = Element.prototype.getElementsByClassName = function(cn) { return this.querySelectorAll((' ' + cn).replace(/[~!@$%^&*()_+\-=,./';:\"?><[\]{}|`#]/g, '\\$&').replace(/\s*(\s|$)/g, '$1').replace(/\s/g, '.'));};</script><![endif]-->
";
		$__html .= parent::getCssJsHtml();
		return $__html;
	}
}
?>