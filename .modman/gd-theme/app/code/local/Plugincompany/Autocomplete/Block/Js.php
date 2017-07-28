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
class Plugincompany_Autocomplete_Block_Js extends Mage_Core_Block_Template {

    public function getLastCacheRefresh()
    {
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'suggestioncache' . DS . Mage::app()->getStore()->getStoreId() . '_1.json';
        $time = filemtime($fileName);
        if (!$time) {
            return 'false';
        }
        return $time;
    }

    public function getAttributePriorities(){
        $attributes = array_filter(array_map('trim',explode(',',Mage::getStoreConfig('plugincompany_autocomplete/search/attribute_priority'))));
        $attributes = json_encode($attributes);
        return $attributes;
    }

    public function getCategoryLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/categorylimit');
        if(!$limit) $limit = 2;
        return $limit;
    }

    public function getCMSPageLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/cmslimit');
        if(!$limit) $limit = 2;
        return $limit;
    }

    public function getProductLimit(){
        $limit = Mage::getStoreConfig('plugincompany_autocomplete/general/productlimit');
        if(!$limit) $limit = 3;
        return $limit;

    }
}