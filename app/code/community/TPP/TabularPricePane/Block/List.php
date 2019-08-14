<?php
/**
 *
 * Tabular Price Pane
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to helpdesk@ikhodal.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package     TPP_TabularPricePane
 * @copyright   Copyright (c) 2016-2017 ikhodal (http://www.ikhodal.com).
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class TPP_TabularPricePane_Block_List extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface {

	protected $_serializer = null; 
	protected $_products_per_row;
	protected $_productCollection; 
	public $_config = array();

	/**
	 * Initialization
	 */
	protected function _construct() {
		$this->_serializer = new Varien_Object(); 
		parent::_construct();
	} 
	
	/**
    * get category id by path
    *
    * @param string $cidPath
    *
    * @return int
    */
    public function getCategoryIdByPath($cidPath)
    {
		if(trim($cidPath)=="") return 0;
		
        $store_id = Mage::app()->getStore()->getId();
        $ob = Mage::getResourceModel('catalog/url')->getRewriteByIdPath($cidPath, $store_id);
		if(!$ob){
			$categoryId = explode("/",$cidPath);
			$categoryId = $categoryId[1];
		}else{
			$categoryId = $ob->getCategoryId();
		}  
        return $categoryId;
    }
	
	/**
	 * Produce list rendered as default html with backend configuration.
	 * @return string
	*/
	protected function _toHtml() {
		$category_id = 0;
		if($this->getData('cat_path')) 
            $category_id = $this->getCategoryIdByPath($this->getData('cat_path'));
			
		$this->_config = array(
			'widget_title'=>$this->getData('widget_title'),
			'price_from'=>$this->getData('price_from'),
			'price_to'=>$this->getData('price_to'),
			'price_difference'=>$this->getData('price_difference'),
			'number_of_product_display'=>$this->getData('number_of_product_display'),
			'price_text_color'=>$this->getData('price_text_color'),
			'title_text_color'=>$this->getData('title_text_color'),
			'price_tab_text_color'=>$this->getData('price_tab_text_color'),
			'price_tab_background_color'=>$this->getData('price_tab_background_color'),
			'header_text_color'=>$this->getData('header_text_color'),
			'header_background_color'=>$this->getData('header_background_color'),
			'display_title_price_over_image'=>$this->getData('display_title_price_over_image'), 
			'hide_widget_title'=>$this->getData('hide_widget_title'), 
			'hide_product_title'=>$this->getData('hide_product_title'),
			'hide_product_price'=>$this->getData('hide_product_price'),
			'block_id'=>$this->getData('block_id'), 
			'category_id'=>$category_id,
			'tp_widget_width'=>$this->getData('tp_widget_width')
		); 
		
		return $this->renderView();
	} 
	
	/**
	 * Load the filtered product collections from database.
	 * @return collection
	*/
	public function getAllProducts() {
	 	$collection = Mage::getModel('catalog/product')->getCollection(); 
		$collection->addStoreFilter(); 
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection); 
		$this->_addProductAttributesAndPrices($collection);   
		$collection->setPageSize((int)$this->_config["number_of_product_display"]);  
		$this->_productCollection = $collection;
		return $collection;  
	}
	
	/**
	 * Configured price list calculated based on price from, price to and difference between two prices.
	 * @return array
	*/	
	public function getPriceTabArray(){
		$price_from = $this->getData('price_from'); 
		$collection = Mage::getModel('catalog/product')->getCollection(); 
		$collection->addStoreFilter();
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection); 
		$this->_addProductAttributesAndPrices($collection); 
		
		if(trim($this->getData('price_to'))!="" && intval($this->getData('price_to'))>0)
			$price_to = $this->getData('price_to');
		else	
			$price_to = $collection->getMaxPrice();
			
		$price_difference = $this->getData('price_difference');
		if(trim($price_difference) == "" || intval($price_difference)<=0)
			$price_difference = $price_to-$price_from; 
		
		$_arr_price_list = array();
		for($i=$price_from+$price_difference;$i<=$price_to;$i=$i+$price_difference){
			if((($i+$price_difference)>$price_to)){
				$_arr_price_list[] = array('from'=>($i-$price_difference+1),'to'=>$i); 
				if($i!=$price_to)
					$_arr_price_list[] = array('from'=>$i,'to'=>$price_to); 
			}
			else { 
				if($i==$price_from+$price_difference)
					$_arr_price_list[] = array('from'=>($i-$price_difference),'to'=>$i);  
				else	
					$_arr_price_list[] = array('from'=>($i-$price_difference+1),'to'=>$i);  
			}	
		}
		
		return $_arr_price_list; 
	}
	 
	/**
	 * Load the more products on ajax based filteration.
	 * @return collection
	*/
	public function loadMoreFilteredProducts(){
		$params = $this->getRequest()->getParams();  
		$_from = (isset($params["from"])?intval($params["from"]):0);
		$_to =(isset($params["to"])?intval($params["to"]):0); 
		$_total =(isset($params["total"])?intval($params["total"]):0);
		$category_id =(isset($params["category_id"])?intval($params["category_id"]):0); 
		 
		$_limit_start =(isset($params["limit_start"])?intval($params["limit_start"]):0);
		$_limit_end = intval($params["number_of_product_display"]); 
				 
		$collection = Mage::getModel('catalog/product')->getCollection(); 
		$collection->addAttributeToSelect('image'); 
		$collection->addStoreFilter();
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection); 
		$this->addProductAttributesAndPrices($collection); 
		
		if($category_id>0){
			 $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addAttributeToFilter('category_id', array('in' => array($category_id)));
		}else{
				$category_res = $this->_getCategories();
				$_all_active_category = array();
				foreach($category_res as $_category){
					$_all_active_category[] =	$_category->getId();
				}
				$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
					->addAttributeToFilter('category_id', array('in' => $_all_active_category));
				
		}
		 
	 
	 	$collection->getSelect()->where('price_index.min_price <= '.$_to.' and price_index.min_price >= '.$_from)->limit($_limit_end,$_limit_start)->group('e.entity_id')->order('price_index.min_price', 'asc');
		$collection->load();
		return $collection;
	}
	
	/**
	 * Load the product collection on ajax based filteration.
	 * @return collection
	*/
	public function loadFilteredProducts(){
	    $params = $this->getRequest()->getParams();  
		$_from = (isset($params["from"])?intval($params["from"]):0);
		$_to =(isset($params["to"])?intval($params["to"]):0); 
		$category_id =(isset($params["category_id"])?intval($params["category_id"]):0); 
		$_limit_start =(isset($params["limit_start"])?intval($params["limit_start"]):0);
		$flg_pr =(isset($params["flg_pr"])?intval($params["flg_pr"]):0);
		$_limit_end = intval($params["number_of_product_display"]);  
		 
		$collection = Mage::getModel('catalog/product')->getCollection(); 
		$collection->addAttributeToSelect('image'); 
		$collection->addStoreFilter();
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection); 
		$this->addProductAttributesAndPrices($collection);   
		 
		if($category_id>0){
			 $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
				->addAttributeToFilter('category_id', array('in' => array($category_id)));
		}else{
				$category_res = $this->_getCategories();
				$_all_active_category = array();
				foreach($category_res as $_category){
					$_all_active_category[] =	$_category->getId();
				}
				$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
					->addAttributeToFilter('category_id', array('in' => $_all_active_category)); 
		} 
		 
		$collection->getSelect()->where('price_index.min_price <= '.$_to.' and price_index.min_price >= '.$_from)->limit($_limit_end,$_limit_start)->group('e.entity_id')->order('price_index.min_price', 'asc');
		$collection->load();
		return $collection;
	}
	
	/**
	 * Returns the count of searched products.
	 * @return int
	*/
	function getTotalProducts($_from=0,$_to=0,$category_id,$c_flg,$is_default_category_with_hidden) {  
		$collection = Mage::getModel('catalog/product')->getCollection(); 
		$collection->addAttributeToSelect('image'); 
		$collection->addStoreFilter();
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection); 
		$this->addProductAttributesAndPrices($collection);  
		
		$_category_filter_query = "";
		if($category_id>0 && ($c_flg==1 || $is_default_category_with_hidden==1)){
				$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
					->addAttributeToFilter('category_id', array('in' => array($category_id)));
		}else{
				$category_res = $this->_getCategories();
				$_all_active_category = array();
				foreach($category_res as $_category){
					$_all_active_category[] =	$_category->getId();
				}
				$collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'inner')
					->addAttributeToFilter('category_id', array('in' => $_all_active_category));
				
		}
		 
		$collection->getSelect()->where('price_index.min_price <= '.$_to.' and price_index.min_price >= '.$_from)->group('e.entity_id'); 
		return $collection->count(); 
	}	
	
	/**
	 * Define the fields to select/filter from database.
	 * @return collection
	*/
	protected function addProductAttributesAndPrices(Mage_Catalog_Model_Resource_Product_Collection $collection)
    { 
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		$collection->addAttributeToFilter('status', array('eq' => 1));  
			 	
	    return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
			->addStoreFilter()
            ->addTaxPercents() 
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite();
    }
  
	/**
	 * Returns a collection of all the active categories
	 * @return collection
	*/
	protected function _getCategories() {
		return Mage::getModel('catalog/category')->getCollection()->addAttributeToFilter('is_active',array('1'))->load();
	}
 
	/**
	 * Returns number of products that are displayed per row
	 * @return int
	*/
	public function getProductsPerRow() {
		if (is_null($this->_products_per_row))
			$this->_products_per_row = (int) $this->getData('products_per_row') ? (int) $this->getData('products_per_row') : 3;
		return $this->_products_per_row;
	}  
	
	/**
	 * Returns unique code to widget
	*/
	public function getUCode() { 
		return $this->_config["block_id"];
	}

} 