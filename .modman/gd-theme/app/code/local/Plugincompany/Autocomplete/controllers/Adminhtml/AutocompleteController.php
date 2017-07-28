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
class Plugincompany_Autocomplete_Adminhtml_AutocompleteController extends Mage_Adminhtml_Controller_Action
{
    protected $_groupIds;
    /**
     * Controller action to generate the autocomplete JSON files
     * For each storeview 3 separate files are generated
     *
     * {store_id}_1.json for all first character results
     * {store_id}_2.json for all first two character results
     * {store_id}_3.json for all other results
     */
    public function cachesuggestionsAction()
    {
        $stores = Mage::getModel('core/store')->getCollection();
        $error = 0;
        $adminStore = Mage::app()->getStore();
        try {
            if(Mage::app()->isSingleStoreMode()){
                $this->_saveJSON(Mage::app()->getStore());
            }else{
                foreach ($stores as $store) {
                    if ($store->getId() == 0) {
                        continue;
                    }
                    Mage::app()->setCurrentStore($store);
                    $this->_saveJSON($store);
                }
            }

        } catch (Exception $e){
            $error = true;
            Mage::app()->setCurrentStore($adminStore);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        if(!$error){
            Mage::app()->setCurrentStore($adminStore);
            Mage::getSingleton('core/session')->addSuccess('Suggestion cache successfully rebuilt');
        }
        $this->_redirect('adminhtml/cache/index');
    }

    /**
     * Saves the JSON files per store
     * Creates cache directory first
     *
     * @param $store
     * @return $this
     */
    protected function _saveJSON($store)
    {
        $cacheDir = Mage::getBaseDir('media') . DS . 'suggestioncache';
        if(!is_dir($cacheDir)){
            mkdir($cacheDir, 0777, true);
        }

        $json_products = $this->_getProductsJSON($store);
        $parts = $this->_getParts($json_products);

        $cmsPages = $this->_getCMSJSON($store);
        $parts[2] = array_merge($parts[2],$cmsPages);

        $categories = $this->_getCategoriesJSON($store);
        $parts[2] = array_merge($parts[2],$categories);

        $i = 0;
        $storeId = $store->getId();
        if($storeId == 0){
            $storeId = 1;
        }

        foreach ($parts as $part) {
            $i++;
            $data = json_encode(array_values($part),512);
            $fileName = $cacheDir . DS . $storeId . '_' . $i . '.json';
            $fp = fopen($fileName, 'w');
            fwrite($fp, $data);
            fclose($fp);
            chmod($fileName, 0777);
        }
        return $this;
    }

    /**
     * Retrieves all products from store
     * Splits SKU to make searching parts of the SKU possible (is needed due too Twitter Typeahead limitation)
     * Formats the product collection to array suitable for Typeahead JSON
     *
     * @param $store
     * @return array
     */
    protected function _getProductsJSON($store)
    {
        $titleAttr = Mage::getStoreConfig('plugincompany_autocomplete/search/title',$store);
        $descAttr = Mage::getStoreConfig('plugincompany_autocomplete/search/description',$store);
        $keywordAttr = explode(',',Mage::getStoreConfig('plugincompany_autocomplete/search/keyword',$store));
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $attributes = $keywordAttr;
        $attributes[] = $titleAttr;
        $attributes[] = $descAttr;

        $defaultAttributes = array('url_path','url_key','image_url','image','small_image','thumbnail','price','special_price', 'group_price','tax_percent','tax_class_id');
        $attributes = array_merge($attributes,$defaultAttributes);

        $visibility = Mage::getStoreConfig('plugincompany_autocomplete/search/visibility',$store);
        if($visibility){
            $visibility = explode(',',$visibility);
        }


        $_productCollection = Mage::getModel('catalog/product')->setStoreId($store->getId())->getCollection()
            ->addStoreFilter($store->getId())
            ->setStoreId($store->getId())
            ->addAttributeToFilter('status', 1)
            ->setVisibility($visibility)
            ->addAttributeToSelect($attributes)
            ->setPageSize(100)
        ;
//        if($visibility){
//            $_productCollection->addAttributeToFilter('visibility', array('in'=>$visibility));
//        }


        $json_products = array();
        $pages = $_productCollection->getLastPageNumber();
        $filter = Mage::getModel('cms/template_filter');
        for($currentPage = 1; $currentPage <= $pages; $currentPage++){
            $_productCollection->setCurPage($currentPage);
            foreach ($_productCollection as $_product) {
                //add URL path if not exists in product object
                if(!$_product->getUrlPath()){
                    $_product->setUrlPath($_product->getUrlKey() . Mage::getStoreConfig('catalog/seo/product_url_suffix',$store));
                }
                //advanced SKU parsing
                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/search/sku',$store)){
                    $sku = $_product->getSku();
                    //sku split beteen numbers and alpha chars (with spaces)
                    $skuParts = preg_replace('/((?<=[A-Z]{2})(?=[0-9])|(?<=[0-9])(?=[A-Z]{2}))/',' ', $sku);
                    //sku with alphanumeric characters only (other characters replaced with spaces)
                    $skuAlpha = preg_replace("/[^a-zA-Z0-9\s]/", " ", $sku);
                    //split sku with alphanumeric characters only
                    $skuAlphaParts = preg_replace("/[^a-zA-Z0-9\s]/", " ", $skuParts);

                    $skuSearch = $skuParts;
                    $skuSearch .= ' ' . $skuAlpha;
                    $skuSearch .= ' ' . $skuAlphaParts;
                }else{
                    //regular SKU
                    $skuSearch = '';
                }

                //generate keyword data
                $keywordData = array();
                foreach($keywordAttr as $attr){
                    $keywordData[$attr] = $_product->getData($attr);
                }
                $keywordData[] = $skuSearch;
                //add title to keywords also
                $keywordData[$titleAttr] = $_product->getData($titleAttr);

                //make all keywords lowercase and unique
                $keywordData = implode(' ',array_unique(explode(' ', strtolower(strip_tags(implode(' ',$keywordData))))));

                $description = $_product->getData($descAttr);
                if(Mage::getStoreConfigFlag('plugincompany_autocomplete/search/product_cms_variables',$store)){
                    $description = $filter->filter($description);
                }

                $json_products[] = array(
                    'name'          => $_product->getData($titleAttr),
                    'url_path'   => $_product->getUrlPath(),
                    //limit description to 110
                    'description'   => substr(strip_tags($description), 0, $descLimit) . '...',
                    //keep each keyword one time only
                    'keywords'      => $keywordData,
                    'imageurl'      => $this->_getProductImage($_product,$store),
                    'prices'        => $this->getProductPrices($store->getWebsiteId(),$store,$_product)
                );
            }
            $_productCollection->clear();
        }
        return $json_products;
    }

    protected function _getCMSJSON($store){
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $collection = Mage::getModel('cms/page')
            ->getCollection()
            ->addStoreFilter($store)
            ->addFieldToFilter('is_active',1)
        ;
        $json_cms = array();
        $filter = Mage::getModel('cms/template_filter');
        foreach($collection as $page){
            $keywordData = array();
            $keywordData[] = $page->getTitle();
            $keywordData[] = $page->getContent();
            $keywordData = implode(' ',array_unique(explode(' ', strtolower(strip_tags(implode(' ',$keywordData))))));

            $description = preg_replace('/{{(.*?)}}/', '', $page->getContent());
            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/cms_cms_variables',$store)){
                $description = $filter->filter($description);
            }

            $json_cms[] = array(
                'name'          => $page->getTitle(),
                'url_path'      => $page->getIdentifier(),
                //limit description to 110
                'description'   => substr(strip_tags($description), 0, $descLimit) . '...',
                //keep each keyword one time only
                'keywords'      => $keywordData,
                'type'          => 'cms'
            );
        }
        return $json_cms;
    }

    protected function _getCategoriesJSON($store){
        $descLimit = Mage::getStoreConfig('plugincompany_autocomplete/search/description_limit',$store);

        $rootId     = $store->getRootCategoryId();
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addFieldToFilter('path', array('like'=> "1/$rootId/%"))
            ->addAttributeToSelect('*')
        ;

        $categories_json = array();
        $filter = Mage::getModel('cms/template_filter');
        foreach($categories as $category){
            $keywordData = array();
            $keywordData[] = $category->getName();
//            $keywordData[] = $category->getDescription();
            $keywordData = implode(' ',array_unique(explode(' ', strtolower(strip_tags(implode(' ',$keywordData))))));

            $description = $category->getDescription();
            if(Mage::getStoreConfigFlag('plugincompany_autocomplete/general/category_cms_variables',$store)){
                $description = $filter->filter($description);
            }


            $categories_json[] = array(
                'name'          => $category->getName(),
                'url_path'      => $category->getUrlPath(),
                //limit description to 110
                'description'   => substr(strip_tags($description), 0, $descLimit) . '...',
                //keep each keyword one time only
                'keywords'      => $keywordData,
                'type'          => 'cat',
                'imageurl'      => $category->getThumbnail(),
            );
        }
        return $categories_json;
    }

    /**
     * Splits up the array ($json) into 3 parts
     *
     * $part1 has all results for 1 character searches (limited to 5)
     * $part2 has all results for 2 character searches (limited to 5)
     * $part3 has all other results
     *
     * @param $json
     * @return array
     */
    protected function _getParts($json)
    {
        //generates array('A','B','C'... etc
        $alphas = array_merge(range(0,9),range('A', 'Z'));

        //PART1 (first character)

        //prepares $part1 array('A'=>[],'B'=>[] ...
        //array key for each alpha character
        $part1 = array_flip($alphas);
        foreach ($part1 as $k => $v) {
            $part1[$k] = array();
        }

        //loops through all $json array items
        //then loops through all alpha characters to see if a match is found
        //if match is found, the item is added to the $part1[$alph] array
        foreach($json as $item){
            foreach($alphas as $alph){
                //only add up to 5 items per first character
                if(count($part1[$alph]) >= 5){
                    continue;
                }
                //check if $alph is found in keywords text
                $match = preg_grep("/^{$alph}/i", explode(' ',$item['keywords']));
                if (!empty($match)) {
                    $part1[$alph][] = $item;
                }
            }
        }

        //clean up array by keeping only unique items
        $part1 = $this->_makeUnique($part1);

        //PART2 the first two characters

        //prepares array('AA'=>[],'AB'=>[],'AC'=>[]... 'ZA'=>[],ZB'=>[]...'ZZ'=>[])
        $part2 = array();
        foreach($alphas as $a){
            foreach($alphas as $b){
                //two characters
                $t = $a . $b;
                $part2[$t] = array();
            }
        }

        //loops through all $json array items
        //then loops through all $alphas characters to see if a match is found
        //if match is found, the item is added to the $part2[$chars] array
        foreach($json as $item){
            foreach($part2 as $chars => $v){
                //only add up to 5 items
                if(count($part2[$chars]) >= 5){
                    continue;
                }
                $match = preg_grep("/^{$chars}/i", explode(' ',$item['keywords']));
                if (!empty($match)) {
                    $part2[$chars][] = $item;
                }
            }
        }

        //cleans up $part2 by keeping only unique values
        $part2 = $this->_makeUnique($part2);
        //removes values from $part2 that are already present in $part1
        $part2 = $this->_removeDups($part1, $part2);

        //removes values from $json that are already present in $part1
        $json = $this->_removeDups($part1, $json);
        //removes values from $json that are already present in $part2
        $json = $this->_removeDups($part2, $json);

        return array($part1, $part2, $json);
    }

    /**
     * Returns resized product image
     * Strips starting part of URL to make JSON smaller
     *
     * @param $_product
     * @param $store
     * @return mixed|string
     */
    protected function _getProductImage($_product,$store){
        $imageType = Mage::getStoreConfig('plugincompany_autocomplete/general/imagetype',$store);
        if(!$imageType){
            $imageType = 'thumbnail';
        }
        $images = array();
        $images['thumbnail'] = 'thumbnail';
        $images['small_image'] = 'small_image';
        $images['image'] = 'image';
        unset($images[$imageType]);
        array_unshift($images,$imageType);

        foreach($images as $imageType){
            try{
                $img = (string)Mage::helper('catalog/image')->init($_product, $imageType)->resize(Mage::getStoreConfig('plugincompany_autocomplete/general/imagesize',$store));
                $img = str_replace($store->getBaseUrl('media') . 'catalog/product/cache/', '', $img);
                return $img;
            }catch(Exception $e){
                $img = '';
                //do nothing
            }
        }
        if($img == ''){
            try {
                $_product->setSmallImage('no_selection');
                $img = (string)Mage::helper('catalog/image')->init($_product, 'small_image')->resize(Mage::getStoreConfig('plugincompany_autocomplete/general/imagesize', $store));
            }catch(Exception $e) {
                $img = '';
                //do nothing
            }
        }
        return $img;
    }

    /**
     * Get unique values only from array
     *
     * Removes duplicates by url path
     *
     * @param $a
     * @return array|mixed
     */
    protected function _makeUnique($a)
    {
        //only keep unique values
        $a = array_filter($a);
        $a = array_reduce($a, 'array_merge', array());
        $res = array();
        foreach ($a as $k => $v) {
            if (in_array($v['url_path'],$res)) {
                unset($a[$k]);
            }else{
                $res[] = $v['url_path'];
            }
        }
        return $a;
    }

    /**
     * Removes values from $b that are already present in $a
     *
     * @param $a
     * @param $b
     * @return mixed
     */
    protected function _removeDups($a, $b)
    {
        $keys = array();
        foreach ($a as $v) {
            $keys[] = $v['url_path'];
        }
        foreach($b as $k => $v){
            if (in_array($v['url_path'],$keys)) {
                unset($b[$k]);
            }
        }
        return $b;
    }

    protected function _getCustomerGroupIds(){
        if(!$this->_groupIds){
            $this->_groupIds = Mage::getModel('customer/group')
                ->getCollection()
                ->getAllIds();
        }
        return $this->_groupIds;
    }

    public function getProductPrices($websiteId,$store,$_product){
        $now = Mage::getSingleton('core/date')->timestamp( time() );
        $ruleModel = Mage::getResourceModel('catalogrule/rule');
        $groupIDs = $this->_getCustomerGroupIds();
        $showTax = Mage::getStoreConfig('tax/display/type',$store->getId());
        if($showTax == 2 || $showTax == 3){
            $showTax = true;
        }else{
            $showTax = false;
        }
        $rules = $ruleModel->getRulesForProduct($now, $websiteId, $_product->getId());
        foreach($groupIDs as $id){
            $price = false;
            foreach($rules as $rule){
                if($rule['customer_group_id'] == $id){
                    $price = $rule['rule_price'];
                    $price = Mage::helper('tax')
                        ->getPrice($_product, $price,$showTax,null,null,null,$store);
                    break;
                }
            }
            if(!$price){
                $price = Mage::helper('tax')
                    ->getPrice($_product, $_product->getFinalPrice(),$showTax,null,null,null,$store);
            }
            $price = number_format($price,2,'.','');
            $prices[$id] = $price;
        }
        return $prices;
    }
}

