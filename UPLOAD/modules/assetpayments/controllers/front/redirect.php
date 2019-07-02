<?php
require_once(dirname(__FILE__) . '../../../assetpayments.php');
require_once(dirname(__FILE__) . '../../../assetpayments.cls.php');

class AssetPaymentsRedirectModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        global $cookie, $link;

        $language = Language::getIsoById(intval($cookie->id_lang));
        $language = (!in_array($language, array('ua', 'en', 'ru'))) ? 'ru' : $language;
        $language = strtoupper($language);

        $cart = $this->context->cart;

        $currency = new CurrencyCore($cart->id_currency);
        $payCurrency = $currency->iso_code;
        $asset = new AssetPayments();
        $assetCls = new AssetPaymentsCls();
        $total = $cart->getOrderTotal();
		$ip = getenv('HTTP_CLIENT_IP')?:
			  getenv('HTTP_X_FORWARDED_FOR')?:
			  getenv('HTTP_X_FORWARDED')?:
			  getenv('HTTP_FORWARDED_FOR')?:
			  getenv('HTTP_FORWARDED')?:
			  getenv('REMOTE_ADDR');

        $option = array();
        
		//****Required variables****//	
		//$option['TemplateId'] = $asset->getOption('template_id');
		$option['TemplateId'] = 19;
		$option['CustomMerchantInfo'] = 'PrestaShop '.(defined('_PS_VERSION_')?_PS_VERSION_:'');
		$option['MerchantInternalOrderId'] = $cart->id;
		$option['StatusURL'] = $link->getModuleLink('assetpayments', 'callback');
		$option['ReturnURL'] = $link->getModuleLink('assetpayments', 'result');
		$option['IpAddress'] = $ip;
		$option['AssetPaymentsKey'] = $asset->getOption('merchant');
		$option['Amount'] = $total;
		$option['Currency'] = $payCurrency;

        //****Customer data and address****//
		
		$address = new AddressCore($cart->id_address_invoice);
        if ($address) {
            $customer = new CustomerCore($address->id_customer);
			$country_iso = Country::getIsoById($address->id_country);
			if ($country_iso == '' || strlen($country) > 3) {
				$country_iso = 'USA';
			}
            
            $option['FirstName'] = $address->firstname .' ' . $address->lastname;
            $option['LastName'] = $address->lastname;
            $option['Email'] = $customer->email;
            $option['Phone'] = $address->phone;
            $option['City'] = $address->city;
            $option['Address'] = $address->address1 . ', ' . $address->address2 . ', ' . $address->city . ', ' . $country_iso;
			$option['CountryISO'] = $country_iso;
        }
		
		//****Adding cart details****//
		foreach ($cart->getProducts() as $product) {
			
			$anyproduct = new Product($product['id_product'], true, $this->context->language->id, $this->context->shop->id);
			$images = $anyproduct->getImages($this->context->language->id); 
			$list_image = array();
				foreach ($images as $img) {
					$image['cover'] = (bool)$img['cover'];
					$image['url'] = $this->context->link->getImageLink($anyproduct->link_rewrite, $img['id_image'], 'home_default');
					$image['position'] = $img['position'];
					array_push($list_image,$image);
				}			
			
		$option['Products'][] = array(
				'ProductId' => $product['id_product'],
				'ProductName' => str_replace(["'", '"', '&#39;'], ['', '', ''], htmlspecialchars_decode($product['name'])),
				'ProductPrice' => $product['total_wt'],
				'ProductItemsNum' => $product['quantity'],
				'ImageUrl' => $this->context->link->getImageLink($anyproduct->link_rewrite, $img['id_image'], 'home_default'),
			);
			$order_total += $product['total_wt'] * $product['quantity'];
		}	
			
		//****Adding shipping method****//
		$shipping_price = $total - $order_total;
	
		$option['Products'][] = array(
				"ProductId" => '00000',
				"ProductName" => 'Delivery',
				"ProductPrice" => $shipping_price,
				"ImageUrl" => 'https://assetpayments.com/dist/css/images/delivery.png',
				"ProductItemsNum" => 1,
			);		
		
        $asset->validateOrder(intval($cart->id), _PS_OS_PREPARATION_, $total, $asset->displayName);
        $url = AssetPaymentsCls::URL;
		$data = base64_encode( json_encode($option) );
		
		//var_dump ($option);

        $this->context->smarty->assign(array('fields' => $data, 'url' => $url));
        $this->setTemplate('redirect.tpl');
    }
}