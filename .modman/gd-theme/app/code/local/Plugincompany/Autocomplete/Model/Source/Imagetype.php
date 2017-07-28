<?php
/*
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
/**
 *
 * @category    Plugincompany
 * @package     Plugincompany_Autocomplete
 * @author      Milan Simek
 */
class Plugincompany_Autocomplete_Model_Source_Imagetype
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     * get possible values
     * @access public
     * @return array
     * @author Milan Simek
     */
    public function toOptionArray(){
        return array(
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Thumbnail'),
                'value' => 'thumbnail'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Small Image'),
                'value' => 'small_image'
            ),
            array(
                'label' => Mage::helper('plugincompany_autocomplete')->__('Base Image'),
                'value' => 'image'
            ),
        );
    }
    /**
     * Get list of all available values
     * @access public
     * @return array
     * @author Milan Simek
     */
    public function getAllOptions() {
        return $this->toOptionArray();
    }
}