<?php

namespace HayBTech\Payment\Model;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\ConfigInterface;

class HayBTech extends AbstractMethod
{
    protected $_code = 'haybtech';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;

    /**
     * Get checkout redirect URL.
     */
    public function getOrderPlaceRedirectUrl()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\UrlInterface')
            ->getUrl('haybtech/payment/redirect');
    }

    /**
     * Initialize SDK and create transaction.
     */
    public function initialize($paymentAction, $stateObject)
    {
        // Load internal SDK
        require_once dirname(__DIR__) . '/lib/sdk/HayBTech.php';
        
        $order = $this->getInfoInstance()->getOrder();
        $secretKey = $this->getConfigData('secret_key');

        try {
            \HayBTech\HayBTech::configure($secretKey);

            $response = \HayBTech\HayBTech::payments()->create([
                'amount' => $order->getGrandTotal(),
                'currency' => $order->getOrderCurrencyCode(),
                'merchant_ref' => $order->getIncrementId()
            ]);

            $order->setExtOrderId($response->get('data')['id']);
            return $this;
        } catch (\Exception $e) {
            // Log error and throw localized exception for Magento checkout
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
