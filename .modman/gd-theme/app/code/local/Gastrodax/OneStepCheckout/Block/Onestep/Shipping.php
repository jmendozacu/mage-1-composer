<?php

class Gastrodax_OneStepCheckout_Block_Onestep_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{

    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('html')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $html = '';
            foreach ($options as $option) {
                $html .= '<div><input type="radio" name="' . $type . '_address_id" value="' . $option['value'] . '"' .
                    ' id="' . $type . '-address-id-' . $option['value'] . '"';
                if ($option['value'] == $addressId) {
                    $html .= ' checked="checked"';
                }
                $html .= '/><label for="' . $type . '-address-id-' . $option['value'] . '">' . $option['label'] . '</label>';
                $html .= '<div class="validation-advice advice-required-entry-shipping-address-id"'
                    . ' style="display:none">' . Mage::helper('checkout')->__('This is a required field.') . '</div>'
                    . '<div style="height: 1px; clear:both"></div></div>' . PHP_EOL;
            }
            $html .= '<div><input type="radio" name="' . $type . '_address_id" value="" id="' . $type . '-address-id-">';
            $html .= '<label for="' . $type . '-address-id-">' . Mage::helper('checkout')->__('New address') . '</label>';
            $html .= '<div class="validation-advice advice-required-entry-shipping-address-id" style="display:none">'
                . Mage::helper('checkout')->__('This is a required field.') . '</div>';
            $html .= PHP_EOL . '<div style="height: 1px; clear:both"></div></div>' . PHP_EOL;

            return $html;

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }
}