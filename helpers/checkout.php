<?php
/**
 * @package		Joomla
 * @subpackage  Bitcoin coinbase Checkout Plugin
 * @version	    1.0.0  
 * @release	    2016-11-15 
 *
 * @copyright	2016 Weppsol || Weppsol All rights reserved.
 * @author	    Weppsol
 * @license		GNU GENERAL PUBLIC LICENSE V2
 * @license	    https://weppsol.com/license
 */


defined('_JEXEC') or die;

class plgRDMedia_PaymentElectroneumHelperCheckout
{
	private $plugin = 'electroneum';
	private $params;
	private $config;

	public function __construct($params)
	{
		$this->params = $params;
		$this->config = new stdClass;
	}

	public function display($layout)
	{

		$html = $layout->render(array(
			'name'  => $this->plugin,
			'image' => 'plugins/rdmedia_payment/electroneum/src/electroneum.png',
		));

		return $html;
	}
}
