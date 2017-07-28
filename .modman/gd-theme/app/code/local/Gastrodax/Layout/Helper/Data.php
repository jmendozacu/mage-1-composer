<?php
/**
 * @project gastrodax
 *
 * @author pbreitsprecher
 * @copyright 2016
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @last_modified 08.09.16 08:38
 * @file Data.php
 *
 */

class Gastrodax_Layout_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getIsActive(){
        return Mage::getStoreConfig('layoutsection/layoutgroup/theme');
    }
}