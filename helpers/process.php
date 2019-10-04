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

class plgRDMedia_PaymentElectroneumHelperProcess
{
	private $plugin = 'electroneum';
	private $params;
	private $config;
	private $helper;

	public function __construct($params)
	{
		$this->params = $params;
	
	}

	public function process($task)
	{
		$app = JFactory::getApplication();
		// Including required paths to calculator.
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/payment.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/config.php';

		

		// Get the ordercode if there is one.
		$ordercode = JFactory::getSession()->get('ordercode');

		// Obtain price for this order.
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/amount.php';
		$amount    = new RDSubsAmount();
		$net_price = $amount->netprice();


		
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/orders.php';
		$item_name = RDSubsOrders::getProductsList($ordercode);
		



		$return_url = JUri::root() . 'index.php?option=com_rdsubs&view=payment&processor=electroneum&task=return&paytask=getpayment';
	
		$app->redirect($return_url);
	
		
		return true;
	}
}
