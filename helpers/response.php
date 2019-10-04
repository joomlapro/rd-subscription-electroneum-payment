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

class plgRDMedia_PaymentElectroneumHelperResponse
{
	private $plugin = 'electroneum';
	private $params;
	private $config;
	private $helper;

	public function __construct($params)
	{
		$this->params = $params;
		
	}

	public function response()
	{
		$config = RDSubsConfig::get();
		
		$jinput = JFactory::getApplication()->input;

		## Including required paths
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/payment.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/message.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/date.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/amount.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/invoice.php';
		
		
		$ordercode = JFactory::getSession()->get('ordercode');

		// Obtain price for this order.
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/amount.php';
		$amount    = new RDSubsAmount();
		$net_price = $amount->netprice();
		
		JHtml::_('jquery.framework');
		JHtml::script(Juri::base().'plugins/rdmedia_payment/electroneum/src/electroneum.js'); 
		JHtml::stylesheet(Juri::base().'plugins/rdmedia_payment/electroneum/src/electroneum.css'); 
		
		
		$ajaxtask = JRequest::getVar("ajaxtask");
	
		
		
		if($ajaxtask == 'getresponse')
		{
			require_once("plugins/rdmedia_payment/electroneum/src/Vendor.php");
			require_once("plugins/rdmedia_payment/electroneum/src/Exception/VendorException.php");
			

			$apikey = $this->params->get('apikey');
			$secret = $this->params->get('secret');
			$outlet = $this->params->get('outlet');
			$currency = $this->params->get('currency');
			
			 $etn = JRequest::getVar("etn"); 
			 $paymentid = JRequest::getVar("paymentid"); 
			 
			 $vendor = new \Electroneum\Vendor\Vendor($apikey, $secret);
			 
			 $payload = array();
			 $payload['payment_id'] = $paymentid;
 	         $payload['vendor_address'] = 'etn-it-'.$outlet;
			 

			 $result = $vendor->checkPaymentPoll(json_encode($payload));
			 
			 $return = array();
	 	     if($result['status'] == 1) 
			 {
				 $return['success'] = 1;
				 $return['amount'] = $result['amount'];
				 $result['message'] = '';
			 }
			 else if (!empty($result['message']))  
			 {
				 $return['success'] = 0;
				 $return['message'] = $result['message'];
			 }
			 else
			 {
				  $return['success'] = 0;
				  $return['message'] = 'Unknown Error was found';
			 }
		
			echo json_encode($return);
			exit;
		}
		
		


		$paytask = JRequest::getVar("paytask");
		
		if($paytask == "getpayment")
		{
			require_once("plugins/rdmedia_payment/electroneum/src/Vendor.php");
			require_once("plugins/rdmedia_payment/electroneum/src/Exception/VendorException.php");
			
			$apikey = $this->params->get('apikey');
			$secret = $this->params->get('secret');
			$outlet = $this->params->get('outlet');
			$currency = $this->params->get('currency');
			
			
			if($apikey == '' || $currency == '' || $outlet == '' || $secret == '')
			{
				echo "<h3>Please fill all vendor settings in Plugin Configurations</h3>";
				return;
				
			}
			
			
			$vendor = new \Electroneum\Vendor\Vendor($apikey, $secret);
			$qrImgUrl = $vendor->getQr($net_price, $currency, $outlet);
			
			
			$formurl = JRoute::_('index.php?option=com_rdsubs&view=payment&processor=electroneum&task=return&paytask=thankpage');
			$returnurl = JRoute::_(JURI::Base().'index.php?option=com_rdsubs&view=payment&processor=electroneum&task=return&paytask=thankpage&etnvalue=');


			$html = "";
			$html .= '<div id="firstdiv" style="">';
			$html .= '<form class="uk-form uk-form-horizontal uk-text-center" style=" text-align:center;" id="electronium_payform" method="post" action="'.$formurl.'">';
			$html .= '<div class="uk-form-row">';
			$html .= '<div id="error_div"></div>';
			$html .= '</div>';
			$html .= '<div id="paymentqr_div">';
				$html .= '<div class="uk-form-row">';
				$html .= "<p class='uk-text-primary uk-text-large uk-text-bold'>Payment for " . $vendor->getEtn(). " ETN to outlet</p>";
				
				$html .= '<div class="uk-text-center uk-margin-bottom" style="background-color: rgb(255, 255, 255); margin:0 auto; padding-bottom: 5px; border-color: rgb(255, 255, 255); border-style: solid; border-width: 12px 12px 6px; border-image: none 100% / 1 / 0 stretch; border-radius: 8px; box-shadow: rgba(50, 50, 50, 0.2) 0px 2px 8px 0px; width: 240px; text-decoration: none; color: rgb(51, 51, 51); text-align: center; cursor: pointer;">';
	
					$html .= '<div style="position: relative; box-sizing: content-box; border: 1px solid #24aaca;">';
						 $html .= "<img id='qrimage' src=\"$qrImgUrl\" style='box-sizing: border-box; border: 8px solid rgb(255, 255, 255); margin-bottom: 10px; width: 100%;' />"; 
					 	 $html .= '<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIyLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAxMTg1LjQgMjYwLjMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDExODUuNCAyNjAuMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsOiMwQzM1NDg7fQoJLnN0MXtmaWxsOiMyQUIxRjM7fQo8L3N0eWxlPgo8dGl0bGU+YWx0LWNvbG91cnM8L3RpdGxlPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMzk5LjcsMTE5LjJ2MjYuNGgtNjMuOXYxNi4xYzAuMSwyLjcsMi4yLDQuOCw0LjksNC45aDU5djEwLjNoLTU5Yy04LjQsMC0xNS4yLTYuOC0xNS4yLTE1LjJjMCwwLDAsMCwwLDAKCXYtNDIuNWMwLTguNCw2LjgtMTUuMiwxNS4yLTE1LjJjMCwwLDAsMCwwLDBoNDMuN0MzOTIuOCwxMDMuOSwzOTkuNiwxMTAuNywzOTkuNywxMTkuMkMzOTkuNywxMTkuMSwzOTkuNywxMTkuMSwzOTkuNywxMTkuMnoKCSBNMzg5LjIsMTM1LjN2LTE2LjFjMC0yLjctMi4yLTQuOS00LjktNC45aC00My43Yy0yLjcsMC4xLTQuOCwyLjItNC45LDQuOXYxNi4xSDM4OS4yeiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNDIwLjIsODAuMnY4MS41YzAuMSwyLjcsMi4yLDQuOCw0LjksNC45aDEyLjN2MTAuM2gtMTIuM2MtOC40LDAtMTUuMi02LjgtMTUuMi0xNS4yYzAsMCwwLDAsMCwwVjgwLjJINDIwLjJ6IgoJLz4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTUyMCwxMTkuMnYyNi40aC02My45djE2LjFjMC4xLDIuNywyLjIsNC44LDQuOSw0LjloNTl2MTAuM2gtNTljLTguNCwwLTE1LjItNi44LTE1LjItMTUuMmMwLDAsMCwwLDAsMHYtNDIuNQoJYzAtOC40LDYuOC0xNS4yLDE1LjItMTUuMmMwLDAsMCwwLDAsMGg0My43QzUxMy4xLDEwMy45LDUyMCwxMTAuNyw1MjAsMTE5LjJDNTIwLDExOS4xLDUyMCwxMTkuMSw1MjAsMTE5LjJ6IE01MDkuNywxMzUuM3YtMTYuMQoJYzAtMi43LTIuMi00LjktNC45LTQuOWgtNDMuN2MtMi43LDAuMS00LjgsMi4yLTQuOSw0Ljl2MTYuMUg1MDkuN3oiLz4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTYwNC40LDE2Ni42djEwLjNoLTU5Yy04LjQsMC0xNS4yLTYuOC0xNS4yLTE1LjJjMCwwLDAsMCwwLDB2LTQyLjVjMC04LjQsNi44LTE1LjIsMTUuMi0xNS4yYzAsMCwwLDAsMCwwaDU4LjgKCXYxMC4zaC01OC44Yy0yLjcsMC4xLTQuOCwyLjItNC45LDQuOXY0Mi41YzAuMSwyLjcsMi4yLDQuOCw0LjksNC45TDYwNC40LDE2Ni42eiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNjI0LjksMTE0LjN2NDcuM2MwLjEsMi43LDIuMiw0LjgsNC45LDQuOWgyNi42djEwLjNoLTI2LjZjLTguNCwwLTE1LjEtNi44LTE1LjItMTUuMWMwLDAsMC0wLjEsMC0wLjFWODAuMQoJSDYyNVYxMDRoMzEuNXYxMC4zTDYyNC45LDExNC4zeiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzIyLjEsMTA0djEwLjNoLTQwLjljLTIuNywwLjEtNC44LDIuMi00LjksNC45djU3LjZINjY2di01Ny42YzAtOC40LDYuOC0xNS4yLDE1LjItMTUuMmMwLDAsMCwwLDAsMEg3MjIuMXoiCgkvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzg4LjQsMTA0YzguNCwwLDE1LjMsNi43LDE1LjMsMTUuMWMwLDAsMCwwLDAsMC4xdjQyLjVjMCw4LjQtNi44LDE1LjItMTUuMiwxNS4yYzAsMCwwLDAtMC4xLDBoLTQzLjcKCWMtOC40LDAtMTUuMi02LjgtMTUuMi0xNS4yYzAsMCwwLDAsMCwwdi00Mi41YzAtOC40LDYuOC0xNS4yLDE1LjItMTUuMmMwLDAsMCwwLDAsMEg3ODguNHogTTc0NC43LDExNC4zYy0yLjcsMC4xLTQuOCwyLjItNC45LDQuOQoJdjQyLjVjMC4xLDIuNywyLjIsNC44LDQuOSw0LjloNDMuN2MyLjcsMCw0LjktMi4yLDQuOS00Ljl2LTQyLjVjMC0yLjctMi4yLTQuOS00LjktNC45TDc0NC43LDExNC4zeiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNODg5LjEsMTE5LjJ2NTcuNmgtMTAuM3YtNTcuNmMtMC4xLTIuNy0yLjItNC44LTQuOS00LjloLTQzLjdjLTIuNywwLTQuOSwyLjItNSw0Ljl2NTcuNmgtMTAuM1YxMDRoNTkKCWM4LjQsMCwxNS4yLDYuNywxNS4yLDE1LjFDODg5LjEsMTE5LjEsODg5LjEsMTE5LjEsODg5LjEsMTE5LjJ6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik05NzYuMiwxMTkuMnYyNi40aC02My45djE2LjFjMC4xLDIuNywyLjIsNC44LDQuOSw0LjloNTl2MTAuM2gtNTljLTguNCwwLTE1LjItNi44LTE1LjItMTUuMmMwLDAsMCwwLDAsMAoJdi00Mi41YzAtOC40LDYuOC0xNS4yLDE1LjItMTUuMmMwLDAsMCwwLDAsMGg0My43Qzk2OS4zLDEwMy45LDk3Ni4yLDExMC43LDk3Ni4yLDExOS4yQzk3Ni4yLDExOS4xLDk3Ni4yLDExOS4xLDk3Ni4yLDExOS4yegoJIE05NjUuOCwxMzUuM3YtMTYuMWMwLTIuNy0yLjItNC45LTQuOS00LjloLTQzLjdjLTIuNywwLjEtNC44LDIuMi00LjksNC45djE2LjFIOTY1Ljh6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xMDYzLjMsMTA0djU3LjZjMCw4LjQtNi44LDE1LjItMTUuMiwxNS4yYzAsMCwwLDAtMC4xLDBoLTQzLjdjLTguNCwwLTE1LjItNi44LTE1LjItMTUuMmMwLDAsMCwwLDAsMFYxMDQKCWgxMC4zdjU3LjZjMC4xLDIuNywyLjIsNC44LDQuOSw0LjloNDMuN2MyLjcsMCw0LjktMi4yLDUtNC45VjEwNEgxMDYzLjN6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xMTg1LjQsMTE5LjJ2NTcuNmgtMTAuMnYtNTcuNmMtMC4xLTIuNy0yLjMtNC45LTUtNC45aC0zMC4zYy0yLjcsMC00LjksMi4yLTQuOSw0Ljl2NTcuNmgtMTAuM3YtNTcuNgoJYy0wLjEtMi43LTIuMi00LjgtNC45LTQuOWgtMzAuNGMtMi43LDAuMS00LjgsMi4yLTQuOSw0Ljl2NTcuNmgtMTAuNFYxMDRoOTYuMmM4LjMsMCwxNS4xLDYuNywxNS4yLDE1CglDMTE4NS40LDExOSwxMTg1LjQsMTE5LjEsMTE4NS40LDExOS4yeiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTc1LjksMTAwLjNsMjEuNiwxNi40bDEzLjcsMTAuM2wtMTUuMiw3LjhsLTMwLjcsMTUuN2wxMC41LDcuM2wxNC43LDEwLjJsLTE1LjksOC4ybC05Ny41LDUwLjMKCWM1My4xLDI5LjIsMTE5LjksOS45LDE0OS4xLTQzLjNjMjEuOC0zOS42LDE3LjEtODguNS0xMS45LTEyMy4yTDE3NS45LDEwMC4zeiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNODQuMSwxNjUuOGwtMjEuNi0xNi40bC0xMy43LTEwLjNsMTUuMi03LjhsMzAuNy0xNS43bC0xMC41LTcuM0w2OS41LDk4LjFsMTUuOS04LjJsMTAyLjctNTMKCUMxMzYuNyw0LjgsNjguOSwyMC41LDM2LjksNzEuOUMxMSwxMTMuNCwxNS43LDE2Nyw0OC4zLDIwMy40TDg0LjEsMTY1Ljh6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00MS43LDIxMC42Qy0yLjgsMTYxLjgsMC44LDg2LjIsNDkuNSw0MS44QzkwLjcsNC4zLDE1Mi4zLDAuMiwxOTguMSwzMmwxMC43LTUuNUMxNTEuNS0xNyw2OS45LTUuOCwyNi40LDUxLjUKCWMtMzguMSw1MC4yLTM0LjcsMTIwLjUsOCwxNjYuOUw0MS43LDIxMC42eiIvPgo8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMjIxLDUyLjhjNDIuOCw1MC4yLDM2LjgsMTI1LjYtMTMuNCwxNjguNGMtMzkuNiwzMy43LTk2LjUsMzgtMTQwLjcsMTAuNEw1NiwyMzcuMgoJYzU5LjEsNDAuOSwxNDAuMSwyNi4yLDE4MS4xLTMyLjljMzMuOC00OC44LDMwLjMtMTE0LjQtOC43LTE1OS4zTDIyMSw1Mi44eiIvPgo8cG9seWdvbiBjbGFzcz0ic3QxIiBwb2ludHM9IjY4LjksMTQwLjkgMTAwLjEsMTY0LjUgMjkuNiwyMzguOCAxNjkuNywxNjYuNSAxNDQuNCwxNDkuMSAxOTEuMSwxMjUuMiAxNTkuOCwxMDEuNiAyMzAuMywyNy40IAoJOTAuMyw5OS42IDExNS42LDExNy4xICIvPgo8L3N2Zz4K" style="width: 110px; box-sizing: content-box; background-color: rgb(255, 255, 255); padding: 0px 8px; position: absolute; bottom: -13px; left: 50%; transform: translateX(-50%);">';
					$html .= '</div>';
	
			      $html .= '<img src="'.JURI::Base().'plugins/rdmedia_payment/electroneum/src/loading.gif" style="height:55px; margin-top:10px;" />';
				  $html .= '<div>Scan with the app or click to pay</div>';
	
 	           $html .= '</div>';

				
			$html .= '</div>';
			
			$html .= '<div class="uk-form-row">';
			$html .= '<button type="button" onclick="check_rdsubs_electroneumresponse()" class="uk-button uk-button-primary">'.JText::_("Confirm").'</button>';
			$html .= '</div>';	
			
			$html .= '<input type="hidden" name="sitebaseurl" id="sitebaseurl" value="'.JURI::getInstance().'" />';
			$html .= '<input type="hidden" name="etn" id="etn" value="'.$vendor->getEtn().'" />'; 
			$html .= '<input type="hidden" name="paymentid" id="paymentid" value="'.$vendor->getPaymentId().'" />'; 
			$html .= '<input type="hidden" name="apikey" id="apikey" value="'.$apikey.'" />';
			$html .= '<input type="hidden" name="secret" id="secret" value="'.$secret.'" />';
			$html .= '<input type="hidden" name="outlet" id="outlet" value="'.$outlet.'" />';
			$html .= '<input type="hidden" name="returnurl" id="returnurl" value="'.$returnurl.'" />';

			$html .= '<input type="hidden" name="ajaxtask" id="ajaxtask" value="getresponse" />';
			$html .= '<input type="submit" name="submit_btn" value="submitbtn" style="display:none;" />';
			$html .= '</div>';	
			$html .= '</form>'."\n";
			
			$html .= '</div>';	
			
			$html .= '<div id="thirddiv" style="display:none;">';
			$html .= '<svg id="checkmark_svg" style="display:none;" class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>';
			$html .= '</div>';
			
			echo $html;
		    return;
			
		}
		
		
		
		
		$etnvalue = JRequest::getVar("etnvalue");
		
        $db = JFactory::getDBO(); 
		$query = "UPDATE #__rd_subs_temp_transaction SET processed = 2 , transaction_id = '".uniqid()."' WHERE ordercode ='".$ordercode."'";

		$db->setQuery($query);
		$db->query();
		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__rd_subs_temp_transaction WHERE ordercode ='".$ordercode."'";
		$db->setQuery($query);
		$result = $db->LoadObject();
		
		
	    $etnvalue = JRequest::getVar("etnvalue");

		$result->etnvalue = $etnvalue;
		
		

    
		
		$this->ipn($result);
		

		$trans_id 	= $result->transaction_number;

		

		$paymenthelper = new RDSubsPayment;
		$result  = $paymenthelper->getTransactionState($trans_id);
		

		

		// Removing the session, it's not needed anymore.
		$session = JFactory::getSession();
		$session->clear('coupon');
		$session->clear('ordercode');

		if ($result->processed == 1)
		{
			// Receive the amount to be paid:
			$amount = new RDSubsAmount($result->ordercode);
			$netto  = $amount->netprice();

			// Variables for the message:
			$variables = array(
				'transaction_id' => $result->transaction_id,
				'ordercode'      => $result->ordercode,
				'orderlist'      => $paymenthelper->showOrderedItems((int) $result->ordercode),
				'date'           => RDSubsDate::_(),
				'userid'         => $result->userid,
				'price'          => RDSubsPrice::_($netto, false, false),
				'date'             => 'NOW',
			);
			

	
			$message = new RDSubsMessage;

			$body = $message->id('thanks-for-ordering-page')
				->user($result->userid)
				->variables($variables)
				->getBody();

			$subject = $message->getSubject();

			echo '<h1>' . $subject . '</h1>';
			echo $body;
			
			// Saving & Storing the invoice
			if ($config->use_invoicing)
			{
				$data = array(
					'userid'    => $result->userid,
					'ordercode' => $result->ordercode,
					'provider'  => $this->plugin,
					'trans_id'  => $result->transaction_id,
				);

				$invoice = new RDSubsInvoice;

				if ($invoice->store($data) == true)
				{
					$invoiceid = $paymenthelper->sendInvoice((int) $result->ordercode);
					$invoicing = $invoice->getInvoiceInformationById($invoiceid);
				}

				if (file_exists(JPATH_ADMINISTRATOR . '/components/com_rdsubs/invoices/' . $invoicing->invoice_no . '.pdf'))
				{
					$attachment = JPATH_ADMINISTRATOR . '/components/com_rdsubs/invoices/' . $invoicing->invoice_no . '.pdf';
				}

				$message = new RDSubsMessage;
				$message->id('send-invoice')
					->invoice($invoiceid)
					->variables($variables)
					->attachment($attachment)
					->send();
			}
			
			return;
		}
		
	}

	public function ipn($data)
	{
	

		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/payment.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/amount.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/message.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/price.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/config.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_rdsubs/helpers/invoice.php';

		$config = RDSubsConfig::get();
		
		
	
		
		## Check temporary transaction:
		$paymenthelper = new RDSubsPayment;
		$result        = $paymenthelper->getTempTransaction($data->transaction_id);
		

		if (!$result)
		{
			
		}
		else
		{
		

				$amount = new RDSubsAmount($result->ordercode);

			
				// Get user data for this payment:
				$results = $paymenthelper->getUserData($data->transaction_id);
				
				$prices = json_decode($data->amounts);
				


				// Store the transaction in the database now:
				$transaction_data                 = new stdClass();
				$transaction_data->transaction_id = $data->transaction_id;
				$transaction_data->userid         = $results->userid;
				$transaction_data->details        = $data->etnvalue;
				$transaction_data->amount         = $prices->net_price;
				$transaction_data->type           = 'Electroneum';
				$transaction_data->plugin         = 'electroneum';
				$transaction_data->ordercode      = $result->ordercode;
		
				

				// Store the transaction in the table:
				$store = $paymenthelper->store($transaction_data, '#__rd_subs_transactions');

				// Get the transaction_id for the current session.
				$transaction = $paymenthelper->getTransactionID($data->transaction_id);

				// Process ordered items:
				$products = $paymenthelper->processOrderedItems((int) $result->ordercode, (int) $transaction->id, (int) $results->userid);

				// Update the status of the temporary transaction:
				$paymenthelper->processTempTransaction((int) $result->ordercode, 1);

				

				// Receive the amount to be paid:
				$amount = new RDSubsAmount($result->ordercode);
				$netto  = $amount->netprice();
				
			

				$variables = array(
					'transaction_id' => $data->transaction_id,
					'ordercode'      => $result->ordercode,
					'date'           => RDSubsDate::_(),
					'price'          => RDSubsPrice::_($netto),
					'orderlist'      => $paymenthelper->getOrderedItemsList(),
				);

				
				if (!$config->use_invoicing)
				{
					$message = new RDSubsMessage;
					$message->id('thanks-for-ordering')
						->user($results->userid)
						->variables($variables)
						->send();
				}

			
		}
	}
}
