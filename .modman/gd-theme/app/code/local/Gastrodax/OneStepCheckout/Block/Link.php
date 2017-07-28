<?php
class Gastrodax_OneStepCheckout_Block_Link extends Mage_Checkout_Block_Onepage_Link
{


    /**
     * Adds a link to the one step checkout
     *
     * @return Solvingmagento_OneStepCheckout_Block_Link
     */
    public function addOnestepCheckoutLink()
    {
        if (!$this->helper('gd_onestepc')->oneStepCheckoutEnabled()) {
            return $this;
        }

        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('core')->isModuleOutputEnabled('Gastrodax_OneStepCheckout')) {
            $text = $this->__('Checkout in One Step');
            $parentBlock->addLink(
                $text,
                'checkout/onestep',
                $text,
                true,
                array('_secure' => true),
                60,
                null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }

    function isPossibleOneStepCheckout()
    {
        return $this->helper('gd_onestepc')->oneStepCheckoutEnabled();
    }

    function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onestep', array('_secure'=>true));
    }
}
