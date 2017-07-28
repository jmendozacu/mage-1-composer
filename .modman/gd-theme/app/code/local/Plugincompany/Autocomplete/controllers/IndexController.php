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
class Plugincompany_Autocomplete_IndexController extends Mage_Core_Controller_Front_Action
{
    public function getjsonAction(){
        $part = $this->getRequest()->getParam('part');
        $fileName = Mage::getBaseDir() . DS . 'media' . DS . 'suggestioncache' . DS . Mage::app()->getStore()->getStoreId() . '_' . $part . '.json';
        $seconds_to_cache = 3600000;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        header('Content-type: application/json');
        ob_start('ob_gzhandler');
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(file_get_contents($fileName));
    }
}
