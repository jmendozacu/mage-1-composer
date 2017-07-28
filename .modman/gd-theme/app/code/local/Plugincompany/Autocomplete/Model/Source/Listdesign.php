<?php
/**
 *
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
 *
 */
class Plugincompany_Autocomplete_Model_Source_Listdesign
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        return array(
                array("value" => "design-list",   "label" => Mage::helper("plugincompany_autocomplete")->__("List")),
                array("value" => "design-grid-2", "label" => Mage::helper("plugincompany_autocomplete")->__("Grid 2 Columns")),
                array("value" => "design-grid-3",   "label" => Mage::helper("plugincompany_autocomplete")->__("Grid 3 Columns"))
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {

        return array(
              "design-list"   => Mage::helper("plugincompany_autocomplete")->__("List"),  
              "design-grid-2" => Mage::helper("plugincompany_autocomplete")->__("Grid 2 Columns"),
              "design-grid-3" => Mage::helper("plugincompany_autocomplete")->__("Grid 3 Columns")
        );
    }

}