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
class Plugincompany_Autocomplete_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getMinQuerySortLength(){
        $length = Mage::getStoreConfig('plugincompany_autocomplete/search/sort_min_char');
        if(is_numeric($length)){
            return $length;
        }
        return 0;
    }

}