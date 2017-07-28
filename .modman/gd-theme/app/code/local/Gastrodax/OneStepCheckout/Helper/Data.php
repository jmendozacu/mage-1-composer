<?php
class Gastrodax_OneStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check is the one step checkout is enabled
     *
     * @return bool
     */
    public function oneStepCheckoutEnabled()
    {
        return (bool)Mage::getStoreConfig('checkout/options/onestep_checkout_enabled');
    }
}
