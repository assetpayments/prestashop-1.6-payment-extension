<?php

class AssetPayments extends PaymentModule
{
    private $settingsList = array(
        'ASSET_MERCHANT',
        'ASSET_SECRET_KEY'
    );

    public function __construct()
    {
        $this->name = 'assetpayments';
        $this->tab = 'payments_gateways';
        $this->version = '1.6';
        $this->author = 'https://assetpayments.com';

        parent::__construct();
        $this->displayName = $this->l('AssetPayments');
        $this->description = $this->l('Оплата через AssetPayments');
        $this->confirmUninstall = $this->l('Вы действительно хотите удалить модуль?');
    }

    public function install()
    {
        if (!parent::install() OR !$this->registerHook('payment')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        foreach ($this->settingsList as $val) {
            if (!Configuration::deleteByName($val)) {
                return false;
            }
        }
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function getOption($name)
    {
        return trim(Configuration::get("ASSET_" . strtoupper($name)));
    }

    private function _displayForm()
    {
        $this->_html .=
            '<form action="' . Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']) . '" method="post">
			<fieldset>
			<legend><img src="../img/admin/AdminTools.gif" />' . $this->l('Merchant Settings') . '</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr>
						<td colspan="3">' . $this->l('Please specify the AssetPayments account details for customers') . '.<br /><br /></td>
					</tr>					
					<tr>
						<td width="130" style="height: 35px;">' . $this->l('Merchant ID') . '</td>
						<td><input type="text" name="merchant" value="' . $this->getOption("merchant") . '" style="width: 300px;" /></td>
					</tr>
					<tr>
						<td width="130" style="height: 35px;">' . $this->l('Secret key') . '</td>
						<td><input type="text" name="secret_key" value="' . $this->getOption("secret_key") . '" style="width: 300px;" /></td>
					</tr>
					<tr><td colspan="3" align="center"><input class="button" name="btnSubmit" value="' . $this->l('Update settings') . '" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
    }

    private function _displayAssetPayments()
    {
        $this->_html .= '<img src="../modules/assetpayments/img/ap.png" style="float:left; margin-right:15px;"><b>' .
            $this->l('This module allows you to accept payments by AssetPayments.') . '</b><br /><br />' .
            $this->l('If the client chooses this payment mode, the order will change its status into a \'Waiting for payment\' status.') .
            '<br /><br /><br />';
    }

    public function getContent()
    {
        $this->_html = '<h2>' . $this->displayName . '</h2>';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!sizeof($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors AS $err) {
                    $this->_html .= '<div class="alert error">' . $err . '</div>';
                }
            }
        } else {
            $this->_html .= '<br />';
        }
        $this->_displayAssetPayments();
        $this->_displayForm();
        return $this->_html;
    }


    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            /*$this->_postErrors[] = $this->l('Account details are required.');*/
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('ASSET_MERCHANT', Tools::getValue('merchant'));
            Configuration::updateValue('ASSET_SECRET_KEY', Tools::getValue('secret_key'));
        }
        $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('ok') . '" /> ' . $this->l('Settings updated') . '</div>';
    }

    # Display

    public function hookPayment($params)
    {
        if (!$this->active) return;
        if (!$this->_checkCurrency($params['cart'])) return;

        global $smarty;
        $smarty->assign(array(
            'this_path' => $this->_path,
            'id' => (int)$params['cart']->id,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
            'this_description' => 'Оплата через систему AssetPayments'
        ));

        return $this->display(__FILE__, 'assetpayments.tpl');
    }

    private function _checkCurrency($cart)
    {
        $currency_order = new Currency((int)($cart->id_currency));
        $currencies_module = $this->getCurrency((int)$cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module AS $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
}
