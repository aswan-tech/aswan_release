<?php


class Custom_Common_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function setPromotionAction(){
		try{
			$happy_start_time = Mage::getStoreConfig('promotion/happy_start/time');
			$MagebaseDir = Mage::getBaseDir();
			$start = explode(',',$happy_start_time);
			$phppath = shell_exec('which php');
			
			$start_hour = (int)$start[0];
			$start_min = (int)$start[1];

			$happy_end_time = Mage::getStoreConfig('promotion/happy_end/time');

			$end = explode(',',$happy_end_time);
			$end_hour = (int)$end[0];
			$end_min = (int)$end[1];

			$output = shell_exec('crontab -l');

			Mage::getModel('common/common')->sendNotificationEmail("Old Cron Tab : ".$output);
			
			echo "Pulling out old crontab values....";

			$output = explode("\n",$output);
			
			/* removing empty values in array */
			foreach($output as $key=>$value){
				if($value != ''){
					$tempoutput[$key] = $value;
				}
			}
			$newoutput = array();
			$startfound = false;
			$endfound = false;
			foreach($tempoutput as $key=>$value){
				if(preg_match("/promostart/",$value)){
					$startfound = true;
					$value=$start_min.' '.$start_hour.' * * * '.$phppath.' '.$MagebaseDir.'/promostart.php';
					$newoutput[$key] = $value;
				}
				elseif(preg_match("/promoend/",$value)){
					$endfound = true;
					$value=$end_min.' '.$end_hour.' * * * '.$phppath.' '.$MagebaseDir.'/promoend.php';
					$newoutput[$key] = $value;
				}
				else{
					$newoutput[$key] = $value;
				}
			}
			
			if(!$startfound){
				$newoutput[]=$start_min.' '.$start_hour.' * * * '.$phppath.' '.$MagebaseDir.'/promostart.php';
			}
			if(!$startfound){
				$newoutput[]=$end_min.' '.$end_hour.' * * * '.$phppath.' '.$MagebaseDir.'/promoend.php';
			}
			$crontabnew = implode("\n", $newoutput);

			file_put_contents($MagebaseDir.'/var/crontab.txt', $crontabnew.PHP_EOL);
			echo exec('crontab '.$MagebaseDir.'/var/crontab.txt');
			
			echo "Setting new crontab values....";

			$output = shell_exec('crontab -l');

			Mage::getModel('common/common')->sendNotificationEmail('New Cron Tab : '.$output);
			
			echo "New Crontab values set....";
			
		}catch(Exception $e){
			Mage::getModel('common/common')->sendNotificationEmail('Cron Tab not set for Happy Hour Promotions');
		}
	}
	
	public function moreImagesAction()
	{
		$id = Mage::app()->getRequest()->getParam('id');
		$countImage = Mage::app()->getRequest()->getParam('countimg');
		$prodPage = Mage::app()->getRequest()->getParam('page');
		
		if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
			$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
		} else {
			$main_image_resize_width = 395;	
	   	}
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
			$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
		} else {
			$main_image_resize_height = 413;	
	   	}
	   
		if(Mage::getStoreConfig('progallery/progalleryconfig/thumbs_border')) {
			$thumbs_border = Mage::getStoreConfig('progallery/progalleryconfig/thumbs_border');
		} else {
			$thumbs_border = '0px solid #DDDDDD;';
		}
		
		if(Mage::getStoreConfig('progallery/progalleryconfig/thumbs_width')) {
			$thumbs_width = Mage::getStoreConfig('progallery/progalleryconfig/thumbs_width');
		} else {
			$thumbs_width = '60';
		}
		
		if(Mage::getStoreConfig('progallery/progalleryconfig/thumbs_height')) {
			$thumbs_height = Mage::getStoreConfig('progallery/progalleryconfig/thumbs_height');
		} else {
			$thumbs_height = '60';
		}
	   
		if(!empty($id)){
			$product = Mage::getModel('catalog/product')->load($id);
			$gallery = $product->getMediaGalleryImages();
			$count_images = count($product->getMediaGalleryImages());
			if (Mage::getStoreConfig('progallery/thumbscarouselconfig/scrollitems')):
				$items = Mage::getStoreConfig('progallery/thumbscarouselconfig/scrollitems');
			else:
				$items = '1';
			endif;
			$html = '';
			$i = 1;
			?>
			<style text='text/css'>
				#more-views-list {left:0px !important;}
				#more-views-list {margin-top:0px !important;}
				#thumbs-overflower .jcarousel-prev {display:none !important;}
				#thumbs-overflower .jcarousel-next {display:none !important;}
			</style>
			<?php
				$paging = Mage::getStoreConfig('progallery/progalleryconfig/has_after_pagination');	
				if(Mage::getStoreConfig('progallery/thumbscarouselconfig/has_prevandnext')) {
					if(Mage::getStoreConfig('progallery/thumbscarouselconfig/has_ajax_prevandnext')){ 
						if($count_images > $paging) {
			?>
							<style text='text/css'>				
								#thumbs-prev-and-next { display:none !important; }
								#lightbox-carousel-controls { display:none; }
								#thumbs-overflower .jcarousel-prev {display:block !important;}
								#thumbs-overflower .jcarousel-next {display:block !important;}
							</style>
			<?php				
						}
					}
				}
				$html .= "<div class='caroufredsel_wrapper'>";
				$html .= "<ul class='product-more jcarousel-skin-tango more-views-list' id='more-views-list-ajax'>";
	  
			foreach($gallery as $item) {
				$class="jcarousel-item jcarousel-item-horizontal jcarousel-item-".$i." 
				jcarousel-item-".$i."-horizontal";
				$style="float: left; list-style: none outside none;";
				
				if($prodPage == 1) {
					$html .= "<li class=".$class." style=".$style." jcarouselindex=".$i.">
					
					<a href='javascript:void(0);' style='border:".$thumbs_border." width:".$thumbs_width."px; 
					height:".$thumbs_height."px;'><img width='".$thumbs_width."' height='".$thumbs_height."' 
					alt='".$item->getLabel()."' src='".Mage::helper('catalog/image')->init($product, 'image', 
					$item->getFile())->resize($thumbs_width, $thumbs_height)."'></a>
					
					<div class='i-i' style='display:none;' id='".$item->getValueId()."'>".$item->getValueId	
					()."</div></li>";
				} else {
					$html .= "<li><a href='#' class='MB_focusable' onclick='replaceThumb(";
					$html .= '"'.Mage::helper('catalog/image')->init($product, 'image', $item->getFile())->resize
					($main_image_resize_width , $main_image_resize_height)->keepFrame(false).'"';
					$html .= "); return false;'>";
					$html .= "<img width='".$thumbs_width."' height='".$thumbs_height."' alt='".$item->getLabel
					()."' src='".Mage::helper('catalog/image')->init($product, 'image', $item->getFile())->
					resize($thumbs_width, $thumbs_height)."'></a></li>";
				}
				$i++;
			}
			$html .="</ul>";
			$html .="</div>";
			
			$html .="<script type='text/javascript'>
			jQuery('#more-views-list-ajax').jcarousel({ horizontal: true, scroll: ".$items." });
			jQuery('.thumbs-carousel-navig').bind('click', function() {
			var seen = {};
			jQuery('#more-views-list-ajax li img').each(function() {
			var txt = jQuery(this).attr('src');		
			if (seen[txt]) {
				jQuery(this).parent().parent().remove();
			} else {
				seen[txt] = true;
			}
			});
			});</script>";
			
			if($prodPage == 1){
				$html .= "<script type='text/javascript'>jQuery('div.more-views ul li img').progalleryThumbs({'product_id': ".$id.",'mage_baseurl': mage_baseurl,'mage_zoom': mage_zoom,'resize_width': resize_width,'resize_height': resize_height,'carouselCircular': carouselCircular,'carouselInfinite': carouselInfinite,'carouselDirection': carouselDirection,'carouselItems': carouselItems,'carouselScrollItems': carouselScrollItems,'carouselScrollEffect': carouselScrollEffect,'carouselScrollDuration': carouselScrollDuration,'carouselPauseOnHover': carouselPauseOnHover,'carouselPlay': carouselPlay,'carouselPlayDelay': carouselPlayDelay,'carouselHasPrevAndNext': carouselHasPrevAndNext,'carouselHasPagination': carouselHasPagination,'carouselPrevButton': carouselPrevButton,'carouselNextButton': carouselNextButton,'carouselPagination': carouselPagination});</script>";
			}
			$this->getResponse()->setBody($html);
		} else {
			return false;
		}
	}
	
	
	public function shortListMoreImagesAction()
	{
		$id = Mage::app()->getRequest()->getParam('id');
		$prodPage = Mage::app()->getRequest()->getParam('page');
		
		$main_image_resize_width = 395;
		$main_image_resize_height = 413;
		$thumbs_border = '0px solid #DDDDDD;';
		$thumbs_width = '190';
		$thumbs_height = '193';
	   
		if(!empty($id)){
			$product = Mage::getModel('catalog/product')->load($id);
			$gallery = $product->getMediaGalleryImages();
			$count_images = count($product->getMediaGalleryImages());
			
			$items = '1';
			$html = '';
			$i = 1;
			?>
			<style text='text/css'>
				#more-views-list {left:0px !important;}
				#more-views-list {margin-top:0px !important;}
				#thumbs-overflower .jcarousel-prev {display:none !important;}
				#thumbs-overflower .jcarousel-next {display:none !important;}
			</style>
			<?php
				$html .= "<div class='caroufredsel_wrapper'>";
				$html .= "<ul class='product-more jcarousel-skin-tango more-views-list' id='more-views-list-".$id."'>";
	  
			foreach($gallery as $item) {
				$class="jcarousel-item jcarousel-item-horizontal jcarousel-item-".$i." jcarousel-item-".$i."-horizontal";
				$style="float: left; list-style: none outside none;";
				
				$html .= "<li class=".$class." style=".$style." jcarouselindex=".$i.">
							<a href='javascript:void(0);' style='border:".$thumbs_border." width:".$thumbs_width."px; 
							height:".$thumbs_height."px;'><img width='".$thumbs_width."' height='".$thumbs_height."' 
							alt='".$item->getLabel()."' src='".Mage::helper('catalog/image')->init($product, 'image', 
							$item->getFile())->resize($thumbs_width, $thumbs_height)."'></a>
							<div class='i-i' style='display:none;' id='".$item->getValueId()."'>".$item->getValueId	
							()."</div></li>";
				
				$i++;
			}
			$html .="</ul>";
			$html .="</div>";
			
			$html .="<script type='text/javascript'>
					jQuery('#more-views-list-".$id."').jcarousel({ horizontal: true, scroll: 1 });
					jQuery('.thumbs-carousel-navig').bind('click', function() {
						var seen = {};
						jQuery('#more-views-list-".$id." li img').each(function() {
							var txt = jQuery(this).attr('src');		
							if (seen[txt]) {
								jQuery(this).parent().parent().remove();
							} else {
								seen[txt] = true;
							}
						});
					});</script>";
			$this->getResponse()->setBody($html);
		} else {
			return false;
		}
	}
	
	public function moreImagesZoomAction()
	{
		$id = Mage::app()->getRequest()->getParam('id');
		$prodPage = Mage::app()->getRequest()->getParam('page');
		
		if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
			$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
		} else {
			$main_image_resize_width = 395;	
	   }
	   
	   if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
			$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
		} else {
			$main_image_resize_height = 413;	
	   }
	   
		if(Mage::getStoreConfig('progallery/lightboxconfig/thumbs_border')) {
			$thumbs_border = Mage::getStoreConfig('progallery/lightboxconfig/thumbs_border');
		} else {
			$thumbs_border = '1px solid #DDDDDD;';
		}
		
		if(Mage::getStoreConfig('progallery/lightboxconfig/thumbs_width')) {
			$thumbs_width = Mage::getStoreConfig('progallery/lightboxconfig/thumbs_width');
		} else {
			$thumbs_width = '90';
		}
		
		if(Mage::getStoreConfig('progallery/lightboxconfig/thumbs_height')) {
			$thumbs_height = Mage::getStoreConfig('progallery/lightboxconfig/thumbs_height');
		} else {
			$thumbs_height = '100';
		}
		
		if(Mage::getStoreConfig('progallery/lightboxthumbscarouselconfig/items')) {
			$limit_items = Mage::getStoreConfig('progallery/lightboxthumbscarouselconfig/items');
		} else {
			$limit_items = '5';
		}
	   
		if(!empty($id)){
			$product = Mage::getModel('catalog/product')->load($id);
			$gallery = $product->getMediaGalleryImages();
			$html = '';
			$i = 1;
			$j = 0;
			$pagingajax = Mage::getStoreConfig('progallery/thumbscarouselconfig/has_ajax_prevandnext');
			
			foreach($gallery as $item) {
				if($i == 6){
					// break;
				}
					$html .= "<li>
					<a onclick='showZoomImage(".$item->getValueId().",".$id.");' href='javascript:void(0);' 
					style='border:".$thumbs_border."' width:".$thumbs_width."px; height:".$thumbs_height.
					"px;'><img width='".$thumbs_width."' height='".$thumbs_height."' alt='".$item->getLabel().
					"' src='".Mage::helper('catalog/image')->init($product, 'image', $item->getFile())->resize(
					$thumbs_width, $thumbs_height)."'></a><div class='i-i' 
					style='display:none;' id='".$item->getValueId()."'>".$item->getValueId()."</div></li>";					$i++; $j++;
			}
			
			
			
			$html .= "<script type='text/javascript'>
			jQuery('#lightbox-more-views-list li img').each(function() {
			if(jQuery(this).attr('src').split('/').pop() == 
				jQuery('#lightbox-image').attr('src').split('/').pop()) {
					jQuery(this).addClass('viewable');
				}
			});
			
		jQuery('.lightbox-thumbs-prev, .lightbox-thumbs-next, .jcarousel-prev, .jcarousel-next').bind('click',function() {
		var seenlightbox = {};
		jQuery('#lightbox-more-views-list li img').each(function() {
		var txtlightbox = jQuery(this).attr('src');		
		if (seenlightbox[txtlightbox]) {		
        jQuery(this).parent().parent().remove();
		} else {
        seenlightbox[txtlightbox] = true;
		}
		});
		});
			
			var len  = ".$j.";
			var limit = ".$limit_items.";
			var ajaxpaging =".$pagingajax.";
			
			if(ajaxpaging) {
				jQuery('#lightbox-carousel-controls').css('display','block');				
			}
			
			if(len < limit) {			
				jQuery('#lightbox-carousel-controls').css('display','none !important');
			}
			
			function showZoomImage(itemid ,pid) {
			var formData = 'clicked_thumb_index='+itemid+'&pid='+pid;		
			jQuery.ajax({
			url:'".Mage::getBaseUrl()."progallery/ajax/get_product_view_zoom',
			data: formData,
			type: 'POST',
			beforeSend: function() {
				jQuery('#lightbox-overlay').show();
				jQuery('#lightbox-image-spinner').show();
			},
			success:function(result){
				jQuery('#lightbox-overlay').hide();
				jQuery('#lightbox-image-spinner').hide();
				jQuery('#lightbox-image').attr('src', result);
				
			jQuery('#lightbox-more-views-list li img').each(function() {
				if(jQuery('#lightbox-more-views-list li img').hasClass('viewable')) {
					jQuery(this).removeClass('viewable');
				}			
			if(jQuery(this).attr('src').split('/').pop() ==
				jQuery('#lightbox-image').attr('src').split('/').pop()) {
					jQuery(this).addClass('viewable');
			}
			});
			}
			});
			
			}
			</script>";
			
			$this->getResponse()->setBody($html);
		}
		else{
			return false;
		}
	}
	
	public function swatchImageCustomAction(){
		if(isset($_GET['prodid'])){
			$_productId = $_GET['prodid'];
		}
		if(isset($_GET['colorCode'])){
			$_color = $_GET['colorCode'];
		}
		
		if(isset($_productId) && isset($_color)){
			$_product = Mage::getModel('catalog/product')->load($_productId);
			if (Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE == $_product->getTypeId()) {
				$_childProducts = $_product->getTypeInstance()->getUsedProducts();
				if(count($_childProducts) > 0){
					$_colorArray = array(); 
					$i = 0;
					foreach($_childProducts as $_child){
						$_colorcode = $_child->getColor();
						
						if(isset($_colorcode) && $_colorcode == $_color){
							$_colorArray[$i] = $_child->getId();
						}
						$i++;	
					}
					
					foreach($_colorArray as $_productChildCustom){
							$_productChildCustom = Mage::getModel('catalog/product')->load($_productChildCustom);
							
							$_mainImageCustom = $_productChildCustom->getImage();
							
							$_mainImagesrc = Mage::helper('catalog/image')->init($_productChildCustom,'image')->resize(230,230);
							
							$pos = strpos($_mainImageCustom,'no_image');
							
							if(!empty($_mainImageCustom) && ($pos === false)) {
							
								$_mainImagesrc = Mage::helper('catalog/image')->init($_productChildCustom,'image')->resize(230,230);
															
								break;
							}	
						unset($_productChildCustom);
						unset($_mainImageCustom);
					}
				}
			}
			echo($_mainImagesrc);
		}
	}
	
	public function favpanelloadAction(){
		$obj = new Mage_Catalog_Block_Navigation();
		$activeCategories = array();
		
		foreach ($obj->getStoreCategories() as $child) {
			if ($child->getIsActive()) {
				$activeCategories[] = $child;
			}
		}
		
		if (count($activeCategories) < 1) {
			return '';
		}
		$tempcounter=1;
		
		foreach ($activeCategories as $category) {
			if(strtolower($category->getName()) != 'clearance'){
				if($tempcounter<=3){
					$even[]=$category->getId(); 	
				}else{
					$odd[]= $category->getId();
				}
				$tempcounter++;
			}
		}
		
		/* bestseller collection picked */
		
		$storeId = Mage::app()->getStore()->getId();
		
		//$bestseller_products_parent = Mage::getResourceModel('productreports/product_collection')->addOrderedQtyCustom()->addAttributeToSelect('*')->setStoreId($storeId)->addAttributeToFilter('type_id','configurable')->addStoreFilter($storeId)->setOrder('ordered_qty', 'desc'); // most best sellers on top
		
	
		/* bestseller collection picked */
		
		$_bestSellertobeDisplayed = (int)Mage::getStoreConfig('bannerlist/fav_settings/num_to_display');
		
		$_tobedisplayedfirst = (int)Mage::getStoreConfig('bannerlist/fav_settings/display_order');
				
		$ctr=1; 
		$html = '';
		$html .= '<div class="top-panel">';
		foreach ($even as $category_id) {
			
			$category = Mage::getModel("catalog/category")->load($category_id);
			$subCats = $category->getChildren();//show only the enabled childs (dont show "Get the Look" childs)
			$subCatsArr = explode(",", $subCats);
			$bestseller_products_ids = array();
			$bestseller_products = Mage::getResourceModel('productreports/product_collection')->addOrderedQtyCustom()->addAttributeToSelect('*')->setStoreId($storeId)->addAttributeToFilter('type_id','configurable')->addStoreFilter($storeId)->addCategoryFilter($category)->addViewsCount()->setPageSize($_bestSellertobeDisplayed);
			
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($bestseller_products);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($bestseller_products);
			
			
			foreach($bestseller_products as $bestproducts){
				$bestseller_products_ids[] = $bestproducts->getEntityId();
			}
			
			unset($bestseller_products);
			unset($bestproducts);
			$cat_name = strtolower($category->getName()); 
			$catID = $category->getId(); 
				$html .= '<div class="b-category-item">
							<div class="cat-div">
							<div class="cat-name">'.$cat_name.'</div></div>';
							$product_collection = $category->getProductCollection()->addFieldToFilter("status", 1)->addFieldToFilter("visibility", 4)->addAttributeToFilter("inchoo_featured_product",1);
							
							if($_tobedisplayedfirst == 0){
								$ids_merged = array_unique(array_merge($bestseller_products_ids, $product_collection->getAllIds()));
							}elseif($_tobedisplayedfirst == 1){
								$ids_merged = array_unique(array_merge($product_collection->getAllIds(), $bestseller_products_ids));
							}else{
								$ids_merged = array_unique(array_merge($bestseller_products_ids, $product_collection->getAllIds()));
							}
							
							if(sizeof($ids_merged) > 0) {
								$i = sizeof($ids_merged);
								$counter = 0; 
								$flag = false;
								foreach($ids_merged as $product_ids){
									$_product = Mage::getModel('catalog/product')->load($product_ids);
									$commArr = array();
									$prod_cats = $_product->getCategoryIds();
									$commArr = array_intersect($prod_cats, $subCatsArr);
									if(is_array($commArr) && count($commArr)){
										if($_product != ""){
											if($_product->isSaleable()) {
												if($counter == 0){
													$html .= '<ul id="favcarousel'.$ctr.'" class="jcarousel jcarousel-skin-tango cat-products-'.$category->getId().' main-cat-ul">';
													$flag = true;
												}
												
												$html .= '<li class="prod-name">
															<div class="prod-image">
																<a href="'.$_product->getProductUrl().'?catId='.$catID.'&name='.$_product->getName().'">
																	<img src="'.Mage::helper("catalog/image")->init($_product, "small_image")->resize(110,110).'" alt="'.$_product->getName().'" title="'.$_product->getName().'" />
																</a>
															</div>
															<div class="prod-container">
																<div class="prod-name">
																	<a href="'.$_product->getProductUrl().'?catId='.$catID.'&name='.$_product->getName().'">';
																	$name = $_product->getName();
																	if(strlen($name) > 15){
																		$html .= substr($name, 0, 15)."..."; 
																	} else {
																		$html .= $name; 
																	}
																$html .='</a>
																</div>
															<div class="prod-price">'; 
															$defPrice = $_product->getPrice();
															$specialprice = 0;
															$_taxHelper  = Mage::helper('tax');
															$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
															$currentDate = date("Y-m-d h:m:s", $currentDate);
					
											
															$specialToDate = $_product->getSpecialToDate();
															$specialFromDate = $_product->getSpecialFromDate();
						
															if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $_product->getSpecialPrice() != 0 ){
																$price = $_product->getFinalPrice();
																$specialprice = $_product->getSpecialPrice();
															} else {
																$price = $_product->getFinalPrice();
															}
											
											
															$price = $_taxHelper->getPrice($_product, $price, true);
															if ($specialprice != 0 && (int)$specialprice <= (int)$price) { 
																$specialprice = $_taxHelper->getPrice($_product, $_product->getSpecialPrice(), true);
																$price = $specialprice;
															}
															
															if(!((int)$price < (int)$defPrice)){
																$html .= Mage::helper('common')->currency($price);
															}else{
																// Discount percents output start
																$_savePercent = 100 - round(($price / $defPrice)*100);
																$html .= '<div class="strike">'.Mage::helper('common')->currency($defPrice).'</div><div class="red">'.Mage::helper('common')->currency($price).'</div><div class="price-discount-flag-fav"><span>'.$_savePercent.'% OFF</span></div>';
															}
														$html .='</div>
														<button type="submit" onClick="setLocation(\''.$this->getAddToCartUrl($_product).'\')" class="add-to-basket">Add to Basket</button>
													</div>
												</li>';
												$counter++;
											}
										}
									}
									unset($_product);
								}
								
								if($counter > 0 && $flag){
									$html .= '</ul>';		
								}
						unset($product_collection);
						unset($ids_merged);
							} 
					$html .= '</div>';
			unset($category);
			unset($bestseller_products_ids);
			$ctr++;
		}
		$html .= '</div>';
		$html .= '<div class="bottom-panel">';
		foreach ($odd as $category_id) {
			$category = Mage::getModel("catalog/category")->load($category_id);
			$subCats = $category->getChildren();//show only the enabled childs (dont show "Get the Look" childs)
			$subCatsArr = explode(",", $subCats);
			
			$bestseller_products = Mage::getResourceModel('productreports/product_collection')->addOrderedQtyCustom()->addAttributeToSelect('*')->setStoreId($storeId)->addAttributeToFilter('type_id','configurable')->addStoreFilter($storeId)->addCategoryFilter($category)->addViewsCount()->setPageSize($_bestSellertobeDisplayed);
			
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($bestseller_products);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($bestseller_products);
			
			$bestseller_products_ids=array();
			
			foreach($bestseller_products as $bestproducts){
				$bestseller_products_ids[] = $bestproducts->getEntityId();
			}
			
			unset($bestseller_products);
			unset($bestproducts);
			$cat_name = strtolower($category->getName()); 
			$catID = $category->getId();
				$html .= '<div class="b-category-item">
							<div class="cat-div">
								<div class="cat-name">'.$cat_name.'</div>
							</div>';
			$product_collection = $category->getProductCollection()->addFieldToFilter("status", 1)->addFieldToFilter("visibility", 4)->addAttributeToFilter("inchoo_featured_product",1);
			
			if($_tobedisplayedfirst == 0){
				$ids_merged = array_unique(array_merge($bestseller_products_ids, $product_collection->getAllIds()));
			}elseif($_tobedisplayedfirst == 1){
				$ids_merged = array_unique(array_merge($product_collection->getAllIds(), $bestseller_products_ids));
			}else{
				$ids_merged = array_unique(array_merge($bestseller_products_ids, $product_collection->getAllIds()));
			}
			
			if(sizeof($ids_merged) > 0) {
				$i = sizeof($ids_merged);
				$counter = 0; 
				$flag = false;
				foreach($ids_merged as $product_id){
					$_product = Mage::getModel('catalog/product')->load($product_id);
					$commArr = array();
					$prod_cats = $_product->getCategoryIds();
					$commArr = array_intersect($prod_cats, $subCatsArr);
					if(is_array($commArr) && count($commArr)){
						if($_product != ""){
							if($_product->isSaleable()) {
								if($counter == 0){
									$html .= '<ul id="favcarousel'.$ctr.'" class="jcarousel jcarousel-skin-tango cat-products-'.$category->getId().' main-cat-ul">';
									$flag = true;
								}
								$html .= '<li class="prod-name">
											<div class="prod-image">
												<a href="'.$_product->getProductUrl().'?catId='.$catID.'&name='.$_product->getName().'">
													<img src="'.Mage::helper("catalog/image")->init($_product, "small_image")->resize(110,110).'" alt="'.$_product->getName().'" title="'.$_product->getName().'" />
												</a>
											</div>
											<div class="prod-container">
												<div class="prod-name">
													<a href="'.$_product->getProductUrl().'?catId='.$catID.'&name='.$_product->getName().'">';
													$name = $_product->getName();
													if(strlen($name) > 15){
														$html .= substr($name, 0, 15)."..."; 
													} else {
														$html .= $name; 
													} 
													$html .='</a>
												</div>
												<div class="prod-price">'; 
												$defPrice = $_product->getPrice();
												$specialprice = 0;
												$_taxHelper  = Mage::helper('tax');
												$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
												$currentDate = date("Y-m-d h:m:s", $currentDate);
					
											
												$specialToDate = $_product->getSpecialToDate();
												$specialFromDate = $_product->getSpecialFromDate();
						
												if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $_product->getSpecialPrice() != 0 ){
													$price = $_product->getFinalPrice();
													$specialprice = $_product->getSpecialPrice();
												} else {
													$price = $_product->getFinalPrice();
												}
												$price = $_taxHelper->getPrice($_product, $price, true);
												if ($specialprice != 0 && (int)$specialprice <= (int)$price) { 
													$specialprice = $_taxHelper->getPrice($_product, $_product->getSpecialPrice(), true);
													$price = $specialprice;
												}
												if(!((int)$price < (int)$defPrice)){
													$html .= Mage::helper('common')->currency($price);
												}else{
													// Discount percents output start
															$_savePercent = 100 - round(($price / $defPrice)*100);
															$html .= '<div class="strike">'.Mage::helper('common')->currency($defPrice).'</div><div class="red">'.Mage::helper('common')->currency($price).'</div><div class="price-discount-flag-fav"><span >'.$_savePercent.'% OFF</span></div>';
													}
												
											$html .= '</div>
													<button type="submit" onClick="setLocation(\''.$this->getAddToCartUrl($_product).'\')" class="add-to-basket">Add to Basket</button>
											</div>
										</li>';
									$counter++; 
							} 	
						}
					}
					unset($_product);
				} 
				if($counter > 0 && $flag){
					$html .= '</ul>';		
				}
				unset($product_collection);
				unset($ids_merged);
			}
			$html .= '</div>';
			unset($category);
			unset($counter);
			unset($bestseller_products_ids);
			$ctr++;
		}
		$html .= '</div>';
		$html .= '<script>jQuery("#favcarousel1,#favcarousel2,#favcarousel3,#favcarousel4,#favcarousel5,#favcarousel6").jcarousel({ vertical: true, scroll: 2 });</script>';
		$this->getResponse()->setBody($html);
		//print $html;
	}
	
	 public function getAddToCartUrl($product, $additional = array()) {
        if (Mage::helper('icart')->isEnabled()) {
            return Mage::helper('icart')->getAddUrl($product, $additional);
        } else {
            return parent::getAddToCartUrl($product, $additional);
        }
    }
	
	public function mainImagesAction()
	{
	    $id = Mage::app()->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($id);
		
		$product_video = Mage::getModel('productvideo/videos')->load($id,'product_id');
		
		if(is_object($product_video)){
			if(sizeof($product_video->getData()) > 0){
				$video_code = $product_video->getVideoCode();
				$video_title = $product_video->getVideoTitle();
			}else{
				$video_code = null;
				$video_title = null;
			}
		}else{
			$video_code = null;
			$video_title = null;
		}
		
		$baseImageUrl = Mage::helper('catalog/image')->init($product, 'image');
		
			if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
				$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
			} else {
				$main_image_resize_width = 395;	
			}
			   
			if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
				$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
			} else {
				$main_image_resize_height = 413;	
			}
			   
			if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width')) {
			   $lightbox_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width');
			} else {
			   $lightbox_image_resize_width = 1100;	
			}
			   
			if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height')) {
			  $lightbox_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height');
			} else {
			  $lightbox_image_resize_height = 1150;	
			}
			$data = array();
			
			//$data['bigImg'] = $baseImageUrl->resize($main_image_resize_width,$main_image_resize_height)->keepFrame(false)->__toString();
			//$data['uribig'] = $baseImageUrl->resize($lightbox_image_resize_width, $lightbox_image_resize_height)->keepFrame(false)->__toString();
			$data['bigImg'] = $baseImageUrl->resize($main_image_resize_width,$main_image_resize_height)->__toString();
			$data['uribig'] = $baseImageUrl->resize($lightbox_image_resize_width, $lightbox_image_resize_height)->__toString();
			$data['videocode'] = $video_code;
			$data['videotitle'] = $video_title;
			echo json_encode($data);
	}
	
	
	public function getRecommendedCategoryProductsAction() 
	{
		$id = $this->getRequest()->getParam('id');
		$menuHtml = "";
		$Category = Mage::getModel('catalog/category')->load($id); //where $category_id is the id of the category
		//echo $Category->getName();exit;
		$pCollection = Mage::getModel('catalog/product')->getCollection();
		$pCollection->addCategoryFilter($Category); 
		$pCollection->addAttributeToSelect(array('name', 'url_path', 'url_key',  'price', 'image', 'special_from_date', 'special_to_date', 'special_price', 'thumbnail', 'small_image')); 
		$pCollection->addFieldToFilter('status', 1)->addFieldToFilter('visibility', 4)->addAttributeToFilter('show_in_megamenu',1);
		$pCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
		$pCollection->setPage(1, 4)->load();
		
		if(count($pCollection->getData()) > 0){
			$menuHtml .='<div class="recomended_band">We Recommend</div>';
			$menuHtml .='<div class="recomendedProduct"> <ul>';
			
			foreach($pCollection as $p) {
				$product = Mage::getModel('catalog/product')->load($p->getId());
				/* $url = (!is_null( $p->getUrlPath($Category))) ?  Mage::getBaseUrl() . $p->getUrlPath($Category) : $p->getProductUrl(); */
				
				$url = $p->getProductUrl();
				
				//echo $url = Mage::getBaseUrl() . $p->getUrlPath($Category);
				//$name = $this->htmlEscape($p->getName());
				$name = $p->getName();
				if(strlen($name) > 15){
					$name = substr($name, 0, 15).'...'; 
				}

				$defPrice = $p->getPrice();
				$specialprice = 0;
				$_taxHelper  = Mage::helper('tax');
				$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
				$currentDate = date("Y-m-d h:m:s", $currentDate);

				$specialToDate = $p->getSpecialToDate();
				$specialFromDate = $p->getSpecialFromDate();

				if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $p->getSpecialPrice() != 0 ){
					$price = $p->getFinalPrice();
					$specialprice = $p->getSpecialPrice();
				} else {
					$price = $p->getFinalPrice();
				}
				
				$price = $_taxHelper->getPrice($p, $price, true);
				if ($specialprice != 0 && (int)$specialprice <= (int)$price) { 
					$specialprice = $_taxHelper->getPrice($p, $p->getSpecialPrice(), true);
					$price = $specialprice;
				}
									
				$menuHtml .='<li>';
				//$menuHtml .='<a href="'.$url .'"><img src="'.$p->getImageUrl().'" width="112" height="117"></a>';
				
				$menuHtml .='<a href="'.$url .'">
				<img data-original="'.Mage::helper('catalog/image')->init($product, 'image',
				$p->getFile())->resize(112,112)->keepFrame(false).'" src="'.Mage::helper('catalog/image')->init($product, 'image',
				$p->getFile())->resize(112,112)->keepFrame(false).'" width="112" 
				height="112"></a>';
				
				$menuHtml .='<p><a href="'.$url .'">'.$name.'</a><br>';
				
				if(!((int)$price < (int)$defPrice)){
					//$menuHtml .='<span class="new_price"><span class="WebRupee">`</span>'. number_format($price,2) .'</span>';
					$menuHtml .='<span class="new_price">'. Mage::helper('common')->currency($price) .'</span>';
				}else{
					//$menuHtml .='<span class="strike"><span class="WebRupee">`</span>'. number_format($defPrice,2). '</span>';
					//$menuHtml .='&nbsp;<span class="new_price"><span class="WebRupee">`</span>'.number_format($price,2) .'</span>';
					$menuHtml .='<span class="strike">'. Mage::helper('common')->currency($defPrice). '</span>';
					$menuHtml .='&nbsp;<span class="new_price">'.Mage::helper('common')->currency($price) .'</span>';
				}
				
				$menuHtml .='</p>';
				$menuHtml .='</li>';
			}
		
			$menuHtml .='</ul></div>';
		/* More link + recommended products */
		}
		
		$this->getResponse()->setBody($menuHtml);
	}

	public function swatchesHtmlAction(){
		
		$obj = new Mage_Catalog_Block_Product_View_Type_Configurable;
		
		$_smpproductId = 0;
		
		$_product_id = Mage::app()->getRequest()->getParam('product');
		
		$_product = Mage::getModel('catalog/product')->load($_product_id);
		
		if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width')) {
			$main_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_width');
		} else {
			$main_image_resize_width = 395;	
		}
 	   
		if(Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height')) {
			$main_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/main_image_resize_height');
		} else {
			$main_image_resize_height = 413;	
		}
		   
		if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width')) {
		   $lightbox_image_resize_width = Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_width');
		} else {
		   $lightbox_image_resize_width = 1100;	
		}
		   
		if(Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height')) {
		  $lightbox_image_resize_height = Mage::getStoreConfig('progallery/resizeImage/lightbox_image_resize_height');
		} else {
		  $lightbox_image_resize_height = 1150;	
		}
		
		$confProd = Mage::getModel('catalog/product_type_configurable');
		// Get all attributes of configurable product
		$confAtts = $confProd->getConfigurableAttributesAsArray($_product);

		$isColorAvailable = false;
		$isSizeAvailable = false;
		
		$confCategory = Mage::getModel('catalog/category');
		
		$returnArray = $confCategory->getSwatchColorSizeDataArray($confAtts);
		
		$color = $returnArray['color'];
		//$colorData = $returnArray['colorData']; 
		$colorDataRootPath = $returnArray['colorDataRootPath'];
		$superAtt = $returnArray['superAtt'];
		$size = $returnArray['size']; 
		$sortedSize = $returnArray['sortedSize']; 
		$sortedSizeAbbr = $returnArray['sortedSizeAbbr'];
		$isColorAvailable = $returnArray['isColorAvailable'];
		$isSizeAvailable =  $returnArray['isSizeAvailable'];	

		// get all associated product ids
		$childIds = $confProd->getUsedProductIds($_product);
		$price = $_product->getPrice();
		$finalPrice = $_product->getFinalPrice();
		$priceDiff=0;
		
		if($finalPrice!=$price){
			$priceDiff = $price-$finalPrice;
		}
		
		$optData = $confCategory->getSwatchOptData($childIds,$size,$priceDiff);
		
		foreach($color['color'] as $ck=>$cv)
		{
			foreach($sortedSize as $sk=>$sv)
			 {
			   if(!isset($optData[$ck][$sk]))
			   {
				 $sortedOptData[$ck][$sk]['image'] = 0;
				 $sortedOptData[$ck][$sk]['small_image'] = 0;
				 $sortedOptData[$ck][$sk]['size_label'] = $sv;
				 $sortedOptData[$ck][$sk]['stock'] = 0;
			   }
			   else
			   {
				 $sortedOptData[$ck][$sk] = $optData[$ck][$sk];
			   }
			   
			 }
			
		}
		
		$swatchHtml = '';
				
		$cats = $_product->getCategoryIds();

                $page_var = $this->getRequest()->getParam('page');

                if(isset($page_var) && $page_var == "shortlist"){
                    $swatchHtml .= "";
                }else{
                    if(!$confCategory->removeSizeGuide($cats)){
			$swatchHtml .= '<span class="size-guide-new"><a class="show_popup" href="javascript:void(0);">Size Guide</a></span>';
                    }
                }
		
		
		$i = 0;
		
		if ($_product->isSaleable()){
			if($isColorAvailable && $isSizeAvailable) {
				$swatchHtml .= '<div class="sizeColor"><span>Select colour and size</span></div>';
				foreach($superAtt as $sAttKey=>$sAttVal)
				{
					$swatchHtml .='<input type="hidden" id="'.$_product->getId().'_'.$sAttKey.'" name="super_attribute['.$sAttVal.']" value="" >';
				}
				
				$swatchHtml .= '<div class="color-swatch-table"><table id="swatches"><tr><td>&nbsp;</td>';
				
				foreach($sortedSize as $sk=>$sz) 
				{
					$swatchHtml .= '<td style="padding-right:10px;" title="'.$sz.'"><span style="cursor: help;">'.$sortedSizeAbbr[$sk].'</span></td>';
				}
				
				$swatchHtml .= '</tr>';
				
				foreach($sortedOptData as $optKey=>$optVal) 
				{
					$swatchHtml .= '<tr>';
					
					$swatchNotAvailable = true;
					$colorSwatchImageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/NA.jpg'; 
					
					foreach($optVal as $optionKey=>$optionVal) {
						$thumbProduct = Mage::getModel('catalog/product')->load($optionVal['simple_prod_id']);
						if($swatchNotAvailable)
						{
						  $colorSwatchImage = $thumbProduct->getColorSwatchImage();
						  if (!empty($colorSwatchImage) and ($colorSwatchImage != "no_selection") and file_exists(Mage :: getBaseDir('media') . DS . 'catalog/product' . $colorSwatchImage)) {
							  $colorSwatchImageUrl = Mage::helper('catalog/image')->init($thumbProduct, 'color_swatch_image')->__toString();
							  $swatchNotAvailable = false;
							  break;
							}
						}
					}
					
					$swatchHtml .= '<td style="padding-right:10px; padding-top:10px;"><a href="javascript:void(0);" id="'.$_product->getId().'_'.$optKey.'" title="'.$color['color'][$optKey].'" class="colorswatchProd" onmouseover="javascript:changeImageHovercolor(this);" onmouseout="javascript:restoreImagecolor();" ><div style="width:20px;height:20px;"><img style="width:20px;height:20px;cursor:pointer;" src="'.$colorSwatchImageUrl.'"></div></a></td> ';
					
					foreach($optVal as $optionKey=>$optionVal) 
					{
						$thumbProduct = Mage::getModel('catalog/product')->load($optionVal['simple_prod_id']);
						$baseImageUrl = Mage::helper('catalog/image')->init($thumbProduct, 'image');
						
						$prod_fprice = Mage::helper('common')->currency($optionVal['finalPrice']);
						$prod_fprice = str_replace('class="WebRupee"', 'class=WebRupee', $prod_fprice);
						
						$prod_price = Mage::helper('common')->currency($optionVal['price']);
						$prod_price = str_replace('class="WebRupee"', 'class=WebRupee', $prod_price);
						
						$swatchHtml .= '<td style="padding-right:10px; padding-top:10px;" id="'.$optionVal['simple_prod_id'].'">';
						
						if($optionVal['stock'])
						{
							$url_image_1 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images';
							
							$url_image_2 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/available_1.jpg';

                                                        if(isset($page_var) && $page_var == "shortlist"){
                                                            //$swatchHtml .= '<a href="javascript:void(0);" class="swatchProd" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'" onmouseover="javascript:changeImageHover(this);" onmouseout="javascript:restoreImage(this);" onclick="selectOption(\''.$_product->getId().'\',\''.$optKey.'\',\''.$optionKey.'\',\''.$url_image_1.'\');changeImage(\''.$_product->getId().'\',\''.$optionVal['simple_prod_id'].'\',\''.number_format($optionVal['finalPrice'],2).'\',\''.number_format($optionVal['price'],2).'\');"><img width="20" height="20" alt="Available" title="Available" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'_opt" src="'.$url_image_2.'" /></a>';
															$swatchHtml .= '<a href="javascript:void(0);" class="swatchProd" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'" onclick="selectOption(\''.$_product->getId().'\',\''.$optKey.'\',\''.$optionKey.'\',\''.$url_image_1.'\');changeImage(\''.$_product->getId().'\',\''.$optionVal['simple_prod_id'].'\',\''.number_format($optionVal['finalPrice'],2).'\',\''.number_format($optionVal['price'],2).'\',\''.$prod_fprice.'\',\''.$prod_price.'\');changeMoreView(\''.$optionVal['simple_prod_id'].'\',\''.$_product_id.'\');"><img width="20" height="20" alt="Available" title="Available" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'_opt" src="'.$url_image_2.'" /></a>';
                                                        }else{
                                                            $swatchHtml .= '<a href="javascript:void(0);" class="swatchProd" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'" onmouseover="javascript:changeImageHover(this);" onmouseout="javascript:restoreImage(this);" onclick="selectOption(\''.$_product->getId().'\',\''.$optKey.'\',\''.$optionKey.'\',\''.$url_image_1.'\');changeImage(\''.$_product->getId().'\',\''.$optionVal['simple_prod_id'].'\',\''.number_format($optionVal['finalPrice'],2).'\',\''.number_format($optionVal['price'],2).'\');changeMoreView(\''.$optionVal['simple_prod_id'].'\',\'\');"><img width="20" height="20" alt="Available" title="Available" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'_opt" src="'.$url_image_2.'" /></a>';
                                                        }
							
							
						}
						else
						{
							$swatchHtml .= '<img width="20" height="20" alt="Out of stock" title="Out of stock" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/not_available.jpg'.'" />';
						}
						
						if($_smpproductId == $optionVal['simple_prod_id'])
						{
							$url_image_3 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images';
							// Select the same simple product on PDP when coming from wishlist
							$swatchHtml .= '<script type="text/javascript">selectOption(\''.$_product->getId().'\',\''.$optKey.'\',\''.$optionKey.'\',\''.$url_image_3.'\');changeImage(\''.$_product->getId().'\',\''.$optionVal['simple_prod_id'].'\',\''.number_format($optionVal['finalPrice'],2).'\',\''.number_format($optionVal['price'],2).'\');changeMoreView(\''.$optionVal['simple_prod_id'].'\',\'\');</script>';
						}
						
						$assigned = true;
						if($optionVal['image'] && $optionVal['small_image'] && $assigned)
						{
							$assigned = false;
							
							$swatchHtml .= '<input type="hidden" class="main-image-changed" id="'.$_product->getId().'_'.$optKey.'_'.$optionKey.'_image" rel="'.$optionVal['image'].'" value="'.Mage::helper('catalog/image')->init($_product,'image')->resize(395,413)->__toString().'" />';
							
							$swatchHtml .= '<input type="hidden" id="'.$_product->getId().'_'.$optKey.'_color_img_main_image" name="'.$optKey.'_color" value="'.$optionVal['simple_prod_id'].'" rel="'.$optionVal['image'].'" />';
							
							$swatchHtml .= '<input type="hidden" id="'.$_product->getId().'_'.$optKey.'_color_img_lightbox_image" name="'.$optKey.'_color_lightbox" value="'.$baseImageUrl->resize($lightbox_image_resize_width, $lightbox_image_resize_height)->__toString().'" />';
							
							$swatchHtml .= '<input type="hidden" id="'.$_product->getId().'_'.$optKey.'_color_small_img" name="'.$optKey.'_small_color" value="'.$optionVal['small_image'].'" />';
							
							$swatchHtml .= '<input type="hidden" id="'.$_product->getId().'_'.$optKey.'_moreviewId" name="moreviewId" value="'.$optionVal['simple_prod_id'].'" />';
							
							$swatchHtml .= '</td>';
						}
					}
					
					$swatchHtml .= '</tr>';
				}
				
				$url_image_4 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/selected.jpg';
				$url_image_5 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/available_1.jpg';
				$url_image_6 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/not_available.jpg';
				
				$swatchHtml .='</table></div><div class="helper-images"><span><img width="15" height="15" src="'.$url_image_4.'" /> Selected   </span><img width="15" height="15" src="'.$url_image_5.'" /> In stock   </span><img width="15" height="15" src="'.$url_image_6.'" /> Out of stock   </span></div>';
			}
			else
			{
				// Default view
				$swatchHtml .='<div class="sizeColor">Select colour and size</div><dl>';
				/*
				foreach($_attributes as $_attribute){
					if ($_attribute->decoratedIsLast){
						$swatchHtml .= '<dd id="'.$_product->getId().'_item_'.++$i.'" class="last">';
					}else{
						$swatchHtml .= '<dd id="'.$_product->getId().'_item_'.++$i.'">';
					}
					
					$swatchHtml .= '<div class="input-box" style="background:none !important;">';
					$swatchHtml .= '<select  name="super_attribute['.$_attribute->getAttributeId().']" id="attribute'.$_attribute->getAttributeId().'" class="super-attribute-select_'.$_product->getId().'" onchange="resetOption('.$_product->getId().','.$i.');"><option>';
					      if(stristr($_attribute->getLabel(),"color")) 
						    $swatchHtml .= 'Select Colour'; 
						  elseif(stristr($_attribute->getLabel(), "size")) 
						    $swatchHtml .= 'Select Size'; 
						  else
						    $swatchHtml .= 'Select Option';
					$swatchHtml .= '</option></select>';
					
					if ($_attribute->decoratedIsLast){
						$cats = $_product->getCategoryIds();
						if(!$confCategory->removeSizeGuide($cats)){			 
							$swatchHtml .= '<a class="show_popup" href="javascript:void(0);">Size Guide</a>';
						} 
					}
					
					$swatchHtml .='</div></dd>';
				}
				*/
				
				$swatchHtml .= '</dl>';

				$swatchHtml .='<script type="text/javascript">var spConfig_'.$_product->getId().' = new FCM_Product.Config('.$obj->getJsonConfig().');var selectedAssocProducts_'.$_product->getId().' = {};</script>';
			}
			$swatchHtml .= '<script type="text/javascript">jQuery(".show_popup").click(function() {jQuery("#fancybox-outer1").css({top:20}).show(); jQuery("#fancybox-overlay1")
jQuery("#fancybox-wrap1,#fancybox-overlay1").fadeIn(300);
});
</script>';
		}
		$this->getResponse()->setBody($swatchHtml);
	}
	
	public function weRecommendAction() {
	$product_contro = new Mage_Catalog_Block_Product;
	$html = '';
	$limit= (int) Mage::getStoreConfig('inchoo_notes_general/feed/related_count'); 
	$collection = Mage::getSingleton('catalog/product')->getCollection();
	$collection->addFieldToFilter('status',1);
	$collection->addAttributeToFilter('visibility', 4);
	$collection->addAttributeToFilter('we_recommend_cart', 1);
	$collection->getSelect()->limit($limit);
	$count = count($collection);	
	if($count) {
	$html .=	"<section id='weRecom'>
					<div class='asLook'>
						<ul id='mycarousel-weRecom' class='jcarousel-skin-tango'>";
				
					foreach($collection as $product) {	
						$_product = Mage::getModel('catalog/product')->load($product->getEntityId());
						$_productnameWe = $_product->getName();
						if(strlen($_productnameWe) > 27): $_productnameWe = substr($_productnameWe,0,24).'...';  endif;	
						$defPrice = $_product->getPrice();
						$specialprice = 0;
						$_taxHelper  = Mage::helper('tax');
						$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
						$currentDate = date("Y-m-d h:m:s", $currentDate);
						if (!$_product->isConfigurable()){
						$specialToDate = $_product->getSpecialToDate();
						$specialFromDate = $_product->getSpecialFromDate();
						
						if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $_product->getSpecialPrice() != 0 ){
						$price = $_product->getResource()->getAttribute('price')->getFrontend()->getValue($_product);
						$price = $_product->getSpecialPrice();
						} else {
						$price = $_product->getFinalPrice();
						}
						} else {
						$price = $_product->getPrice();
						}					
						$priceFor = $product_contro->getPriceHtml($_product, true);
				
						$html .= "<li><div class='BestPro'>
						<a href='".$_product->getProductUrl()."'>
						<img src='".Mage::helper('catalog/image')->init($_product, 'small_image')->resize(238, 249)."' width='238' height='249' alt='".$_product->getName()."' title='".$_product->getName()."' />
						</a> 
						<div class='BestProDetails'>".$_productnameWe."</div>
						<div class='BestProDetailsPrice'>".$priceFor."</div> 
						</div></li>";
					}				
				$html .= "</ul>
			</div>
		</section>";
		
		$scroll_items = (int) Mage::getStoreConfig('inchoo_notes_general/feed/related_scroll');
			if($scroll_items <=0 ) {
				$items = 1;
			} else {
				$items = $scroll_items;
			}	

		$html .=	"<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery('#mycarousel-weRecom').jcarousel({ horizontal: true, scroll: ".$items."});
		});
	</script>";
		}	
		$this->getResponse()->setBody($html);
	}

public function recentlyViewedAction() {
	$product_contro_re = new Mage_Catalog_Block_Product;
	$html = '';	
	if ($_products = Mage::getSingleton('Mage_Reports_Block_Product_Viewed')->getItemsCollection()):
	$count = count($_products);
	$limit= (int) Mage::getStoreConfig('inchoo_notes_general/feed/recent_count');
		$html .= "<section id='recView'>
					<div class='asLook'>
						<ul id='mycarousel-recView' class='jcarousel-skin-tango'>";
						$i=0;
						foreach ($_products as $_product):
						$product = Mage::getModel('catalog/product')->load($_product->getId());
						$_productname = $product->getName();
						if(strlen($_productname) > 27): $_productname = substr($_productname,0,24).'...';  endif;
						$defPrice = $product->getPrice();
						$specialprice = 0;
						$_taxHelper  = Mage::helper('tax');
						$currentDate = mktime(0,0,0,date("m"),date("d"),date("Y"));
						$currentDate = date("Y-m-d h:m:s", $currentDate);
						if (!$product->isConfigurable()){
						$specialToDate = $product->getSpecialToDate();
						$specialFromDate = $product->getSpecialFromDate();						
						if ( ($currentDate >= $specialFromDate && $currentDate < $specialToDate || $specialToDate == "") && $product->getSpecialPrice() != 0 ){
						$price = $product->getResource()->getAttribute('price')->getFrontend()->getValue($product);
						$price = $product->getSpecialPrice();
						} else {
						$price = $product->getFinalPrice();
						}
						} else {
						$price = $product->getPrice();
						}	
						$priceRe = $product_contro_re->getPriceHtml($_product, true);			
							if($i < $limit):
				
			$html	.=	"<li><div class='BestPro'>
							<a href='".$_product->getProductUrl()."' title='".$_productname."' class='product-image'>
							<img src='".Mage::helper('catalog/image')->init($_product, 'small_image')->
							resize(238, 249)."' width='238' height='249' alt='".$_product->getName()."' /></a>
							<div class='BestProDetails'>".$_product->getName()."</div>
							<div class='BestProDetailsPrice'>".$priceRe."</div></div></li>";

						endif;
						$i++;
						endforeach;
			
			$html	.=	"</ul></div></section>";
			 $scroll_items = (int) Mage::getStoreConfig('inchoo_notes_general/feed/recent_scroll');
				if($scroll_items <=0 ) {
					$items = 1;
				} else {
					$items = $scroll_items;
				}
		$html 	.= "<script type='text/javascript'>
		jQuery(document).scroll(function() {
			jQuery('#mycarousel-recView').jcarousel({ horizontal: true, scroll: ".$items."});
		});
		</script>";		
		endif;	
		$this->getResponse()->setBody($html);
	}
	
	
	public function generateVedioSectionAction(){
		$id = Mage::app()->getRequest()->getParam('id');
		$product_video = Mage::getModel('productvideo/videos')->load($id,'product_id');
		
		$video_code = $product_video->getVideoCode();
		$video_title = $product_video->getVideoTitle();
		
		if($video_title == ""){
			$video_title = Mage::getModel('catalog/product')->load($id)->getName();
		}
		
		$video_width = $product_video->getVideoWidth();
        $video_height = $product_video->getVideoHeight();
		
		$_helper = Mage::helper('productvideo/data');
		
		$html = '';
		
		$html .= '<div class="catwalk"><div class="video-views"><ul><li><a class="rw-product-video" title="'.$video_title.'" href="http://www.youtube.com/v/'.$video_code.'?fs=1&amp;hl=en_US">';
		
		$html .= '<img width="101" height="18" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/enterprise/lecom/images/view_catwalk_img.png" alt="'.$video_title.'" /></a></li></ul>';
		
		$html .= '	 <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".rw-product-video").click(function() {
			jQuery.fancybox({
					"padding"              : '.((int) $_helper->getPadding()).',
					"margin"               : '.((int) $_helper->getMargin()).',
					"opacity"              : '.($_helper->getOpacity() ? "true" : "false").',
					"modal"                : '.($_helper->getModal() ? "true" : "false").',
					"cyclic"               : '.($_helper->getCyclic() ? "true" : "false").',
					"scrolling"            : "'.$_helper->getScrolling().'",
					"width"                : '.$video_width.',
					"height"               : '.$video_height.',
					"autoScale"            : '.($_helper->getAutoScale() ? "true" : "false").',
					"autoDimensions"       : '.($_helper->getAutoDimensions() ? "true" : "false").',
					"centerOnScroll"       : '.($_helper->getCenterOnScroll() ? "true" : "false").',
					"hideOnOverlayClick"   : '.($_helper->getHideOnOverlayClick() ? "true" : "false").',
					"hideOnContentClick"   : '.($_helper->getHideOnContentClick() ? "true" : "false").',
					"overlayShow"          : '.($_helper->getOverlayShow() ? "true" : "false").',
					"overlayOpacity"       : '.$_helper->getOverlayOpacity().',
					"overlayColor"         : "'.$_helper->getOverlayColor().'",
					"titleShow"            : '.($_helper->getTitleShow() ? "true" : "false").',
					"titleFormat"          : '.($_helper->getTitleFormat() == "" ? "null" : $_helper->getTitleFormat()).',
					"transitionIn"         : "'.$_helper->getTransitionIn().'",
					"transitionOut"        : "'.$_helper->getTransitionOut().'",
					"speedIn"              : '.$_helper->getSpeedIn().',
					"speedOut"             : '.$_helper->getSpeedOut().',
					"changeSpeed"          : '.$_helper->getChangeSpeed().',
					"changeFade"           : "'.$_helper->getChangeFade().'",
					"easingIn"             : "'.$_helper->getEasingIn().'",
					"easingOut"            : "'.$_helper->getEasingOut().'",
					"showCloseButton"      : '.($_helper->getShowCloseButton() ? "true" : "false").',
					"showNavArrows"        : '.($_helper->getShowNavArrows() ? "true" : "false").',
					"enableEscapeButton"   : '.($_helper->getEnableEscapeButton() ? "true" : "false").',
					
					"title"			: this.title,
					"href"			: this.href.replace(new RegExp("watch\\?v=", "i"), "v/"),
					"type"			: "swf",
					"swf"			: {
					 	"wmode"		: "transparent",
						"allowfullscreen"	: '.($_helper->getAllowFullScreen() ? "true" : "false").'
					}
				});
			return false;
		});
	});
</script>';

	$html .= '</div></div>';
	
	$this->getResponse()->setBody($html);
	}
	
	public function callReviewAction() {
		$productId  = (int) $this->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($productId);
		Mage::register('current_product', $product);
		Mage::register('product', $product);
		
		$html = '';
		$html .= $this->getLayout()->createBlock('core/template')->setTemplate('review/product/view/count.phtml')->toHtml();
		$html .= $this->getLayout()->createBlock('review/form')->setTemplate('review/form.phtml')->toHtml();
		$html .= $this->getLayout()->createBlock('review/product_view_list')->setTemplate('review/product/view/list.phtml')->toHtml();
		
		$this->getResponse()->setBody($html);
	}
	
	public function ajaxCartUpdateAction(){
		//$block = $this->getLayout()->createBlock('checkout/cart_sidebar')->setTemplate('checkout/cart/cartheader.phtml');
		
		//echo $block->toHtml();
		$qty = (int)Mage::getModel('checkout/cart')->getQuote()->getItemsQty();
		if($qty == NULL) {
			echo 0;
		}
		else{
			echo $qty;
		}
	}
	
	public function fileDownloadAction(){
		$_filepath = Mage::getBaseDir('var').'/content_feed_errorlog.csv';
		
		if (file_exists($_filepath)) {
			header("Content-type: text/csv");  
			header("Cache-Control: no-store, no-cache");  
			header('Content-Disposition: attachment; filename=content_feed_errorlog.csv');
			
			readfile($_filepath);
			exit;
		} else {
			echo "The file content_feed_errorlog.csv does not exist";
		}
	}
	
	public function generateRegistrationPopupAction(){
		$cookieTime = Mage::getStoreConfig('shortlist/popupcookiesetting/cookieval');
		
		$cookiePath = Mage::getStoreConfig('web/cookie/cookie_path');
		
		$cookieDomain = Mage::getStoreConfig('web/cookie/cookie_domain');
		
		if (isset($_COOKIE["registerpopup"])){
			//setcookie("registerpopup", "", time()-$cookieTime);
		}else{
			$session = Mage::getSingleton('customer/session');
			if (!$session->isLoggedIn()) {
				if($cookiePath == ''){
					$cookiePath = '/';
				}
				
				if($cookieDomain == ''){
					$cookieDomain = 'americanswan.com';
				}
				
				if($cookieTime != '0' && $cookieTime != ''){
					Mage::getModel('core/cookie')->set('registerpopup', "Test Cookie", 86400 * 30);	
					//setcookie("registerpopup", "Test Cookie", time()+$cookieTime, $cookiePath, $cookieDomain);
				}else{					
					Mage::getModel('core/cookie')->set('registerpopup', "Test Cookie", 86400 * 30);
					//setcookie("registerpopup", "Test Cookie", time()+2592000, $cookiePath, $cookieDomain);
				}
				
				$html = $this->getLayout()->createBlock('common/popup')->setTemplate('shortlist/register_popup.phtml')->toHtml();
	
				$this->getResponse()->setBody($html);
				
			}
		}
	}
	
	public function myDalaPixelAction(){
		$paramname = $this->getRequest()->getParam('paramname');
		
		$_affiliateArray = array("mydala","icubes","vcommission","trootrac","networkplay","OMG");
		if(in_array($paramname, $_affiliateArray)){
			if($paramname == "mydala"){
				if (isset($_COOKIE["myDalaCookie"])){
					echo '0';
				}else{
					$cookiePath = '/';
			
					$cookieDomain = 'americanswan.com';
					$randomnumber = rand(100,999);
			
					//setting session based cookie
					setcookie("myDalaCookie", $randomnumber, 0, $cookiePath, $cookieDomain);			
			
					echo '<img src="http://www.mydala.com/alliance/pixel/pixserverlead/0/114/0/0/'.$randomnumber.'" />';
				}
			}else{
				if (isset($_COOKIE["pixelAffiliateCookie"])){
					$cookiePath = '/';
			
					$cookieDomain = 'americanswan.com';
					//unset cookie and set it again as per affiliate recieved
					setcookie("pixelAffiliateCookie", '', time()-3600, $cookiePath, $cookieDomain);
					
					//setting cookie again
					setcookie("pixelAffiliateCookie", $paramname, 0, $cookiePath, $cookieDomain);
					
					echo '0';
					
				}else{
					$cookiePath = '/';
			
					$cookieDomain = 'americanswan.com';
								
					//setting session based cookie
					setcookie("pixelAffiliateCookie", $paramname, 0, $cookiePath, $cookieDomain);
					
					echo '0';
				}
			}
		}else{
			echo '0';
		}		
	}
	
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	
	/**
     * Create customer account action
     */
    public function createPostCutomAction()
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
			
			/* custom code to copy email to first name since firstname is not provided through popup*/
			
			if($customerData['firstname'] == ''){
				$customerData['firstname'] = $customerData['email'];
			}
			
			/* custom code to copy email to first name since firstname is not provided through popup*/
			
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                    ->setEntity($address);

                $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                        ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                        ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) {
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        $this->_redirectSuccess(Mage::getUrl('customer/account/index', array('_secure'=>true)));
                        return;
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $url = $this->_welcomeCustomer($customer);
                        $this->_redirectSuccess($url);
                        return;
                    }
                } else {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the customer.'));
            }
        }
		
        $this->_redirectError(Mage::getUrl('customer/account/create/', array('_secure' => true)));
    }
	
	/**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @return string
     */
    protected function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );
        if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
            }
            $this->_getSession()->addSuccess($userPrompt);
        }

        $customer->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId()
        );

        /*$successUrl = Mage::getUrl('', array('_secure'=>true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }*/
		$successUrl = Mage::getUrl('common/index/success', array('_secure'=>true));

        return $successUrl;
    }
	
	/**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    protected function _isVatValidationEnabled($store = null)
    {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }
	public function successAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function switchcurrencyAction()
    {
        if ($curency = (string) $this->getRequest()->getParam('currency')) {
            $currencyArr = explode("_", $curency);
			if (count($currencyArr) > 1) {
				Mage::app()->getStore()->setCurrentCurrencyCode($currencyArr[0],$currencyArr[1]);
				$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
				$session->setData("country_code", $currencyArr[1]);
			} else {
				Mage::app()->getStore()->setCurrentCurrencyCode($curency);
			}
        }
		
		
        $this->_redirectReferer(Mage::getBaseUrl());
    }
	/*
	 * ajaxloginAction() method is used to customer login
	 * @param Null
	 * @return Null
	 */ 
	public function ajaxloginAction() {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['username']) && !empty($login['password'])) {
				try {
                    $session->login($login['username'], $login['password']);
                    $custData = $session->getCustomer()->getData(); 
					if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                    // remember me
                    if($login['is_remember'] == true) {
						$rememberVal = base64_encode($login['username'])."|".base64_encode($login['password']);
						$this->remember($rememberVal);
					}                                 
                    echo 1;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }                    
                    $session->setUsername($login['username']);
                    die($message);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
				die($this->__('Login and password are required.'));
            }
        }
		
		die();
	}
	
	/*
	 * ajaxsignupAction() method is used to create new customer account
	 * @param Null
	 * @return Null
	 */ 
	public function ajaxsignupAction() {
		  
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $helper = Mage::helper('common');
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        
        if ($this->getRequest()->isPost()) {
			$errors = array();
            
            // validate data
            
            $password = $this->getRequest()->getPost('password');
			$email = $this->getRequest()->getPost('email');
			$gender = $this->getRequest()->getPost('gender');
			$mobile = $this->getRequest()->getPost('mobile');
			
			$gaCookies = Mage::getModel( 'nosql/parse_ga' )->getCookies();
			$source = strtolower($gaCookies['campaign']['source']);
			$campaign = strtolower($gaCookies['campaign']['name']);
			
			$filter = new Zend_Filter_Alnum();
            $password = $filter->filter($password);
            $emailArr = explode('@', $email);
            
            if(!Zend_Validate::is($email, 'EmailAddress')) {
				$errors[] = 'Please enter valid email address.';
			}
			else if(!Zend_Validate::is($password, 'NotEmpty')) {
				$errors[] = 'Please enter password.';
			}
			else if (!Zend_Validate::is($password, 'StringLength', array(6))) {
					$errors[] = 'The minimum password length is 6.';
			}
            if(count($errors) == 0) {				
				try {
							$websiteId = Mage::app()->getWebsite()->getId();
							$store = Mage::app()->getStore();
							$customer = Mage::getModel('customer/customer')->setId(null);
							$customer->setWebsiteId($websiteId);
							$customer->loadByEmail($email);
							$mobcheck  = $helper->checkMob($mobile);
							if(!$customer->getId() && !$mobcheck) {
								if($this->getRequest()->getPost('is_subscribed'))
									$data = array('email'=>$email,'password'=>$password,'gender'=>$gender,'mobile'=>$mobile,'is_subscribed'=>true,'source'=>$source,'campaign'=>$campaign);
								else
									$data = array('email'=>$email,'password'=>$password,'gender'=>$gender,'mobile'=>$mobile,'source'=>$source,'campaign'=>$campaign);	
								Mage::getSingleton('core/session')->setNewCustData($data);
								$result = $helper->generateOtp($mobile,$email);
								if($result==true)
									echo 1;
								else
									echo $result;
							}
							
							else if($customer->getId()){
								echo 'You have an existing account using this email id.';

							}

							else if($mobcheck){
								echo 'You have an existing account using this phone no.';

							}else 
								echo 'Try Again !';

				}
				catch(Exception $ex) {
					echo $ex->getMessage();
				}
			}
			else {
				echo implode($errors, '<br>');
			}
        }
        else{
			echo 'Invalid data';
		}
        die();
	}

	public function activateNewRegAction(){
		$otp = $this->getRequest()->getPost('register_otp');
		$session_otp = Mage::getSingleton('core/session')->getRegOtp();
		if($otp == $session_otp){
			$data = Mage::getSingleton('core/session')->getNewCustData();
			$email = $data['email'];
			$password = $data['password'];
			$mobile = $data['mobile'];
			$gender = $data['gender'];
			$source = $data['source'];
			$campaign = $data['campaign'];
			$emailArr = explode('@', $email);
			if($data){
				$websiteId = Mage::app()->getWebsite()->getId();
				$store = Mage::app()->getStore();
				$customer = Mage::getModel('customer/customer')->setId(null);
				$customer->setWebsiteId($websiteId);
				$customer = Mage::getModel('customer/customer')->load($cust_session_id);
				$customer->setWebsiteId($websiteId);
				$customer->setStore($store);
				$customer->setEmail($email);
				$customer->setFirstname($emailArr[0]);
				$customer->setPassword($password);
				$customer->setGender($gender);
				$customer->setSource($source);
				$customer->setCampaign($campaign);
				//new lines added for otp generation
				$customer->setTelephone($mobile);
				$customer->setIsActive(1);
				if (isset($data['is_subscribed'])) {
					$customer->setIsSubscribed(1);
				}
				$customer->setConfirmation(null);
				$customer->save();
				// send email to customer
				Mage::dispatchEvent('customer_register_success',
					array('account_controller' => $this, 'customer' => $customer)
				);
				if ($customer->isConfirmationRequired()) {
					$customer->sendNewAccountEmail(
						'confirmation',
						$session->getBeforeAuthUrl(),
						Mage::app()->getStore()->getId()
					);
				}
				$this->_getSession()->loginById($customer->getId());
				$this->_welcomeCustomer($customer);
				if($this->getRequest()->getPost('redirect_url')){
					$result['url'] = Mage::getBaseUrl();
					$result['message'] = 1;
				}
				else
					$result['message'] =1;
			}else
				$result['message'] =  "Your session has been expired.Try again!";
			
		}else
				$result['message']=  "Please enter the correct OTP.";

		if($this->getRequest()->getPost('redirect_url'))
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		else
			echo $result['message'];		
	}

	public function regenerateOtpAction(){
		$helper = Mage::helper('common');
		$data = Mage::getSingleton('core/session')->getNewCustData();
		if(isset($data['mobile'])){
			$result = $helper->generateOtp($data['mobile']);
			if($result==true)
				echo 1;
			else
				echo "Please Try Again!";
		}
		else echo "Your session has been expired.Try again!";
		

	}
    public function isUserLoggedInAction(){
    	$helper = Mage::helper('common');
       	$customerinfo = Mage::getSingleton('customer/session');
       	if($customerinfo->isLoggedIn()){
       		$configValue = Mage::getStoreConfig('registercoupens/general/ruleid');
       		$arr = explode(',',trim($configValue));
       		$rule_id = $arr[5];
       		$customerId= $customerinfo->getCustomerId();
       		$couponcode = $helper->getNewUserCouponCode($rule_id,$customerId);
       		if($couponcode){
       			$imgurl =Mage::getDesign()->getSkinUrl('images-v3/signup-am-logo.jpg'); 
       			echo '<div style="" class="signup-wrap-box">
                        <div class="signup-cont-box">
                                        <a href="javascript:void(0)"  id="gtm-signin-signup-popup-cross" class="cl-btn gtm-track">x</a>

                                        <div class="tp-logo-w">
                                                <div class="lgowrap">
                                                        <img alt="" src="'.$imgurl.'">
                                                </div>
                                        </div>
                                        <div class="clear"></div>';
                                        $html = $this->getLayout()->createBlock('cms/block')->setBlockId('coupon-new-registration-v3')->toHtml();
                                        $html = $newphrase = str_replace('{COUPON_CODE}', $couponcode,  $html);
                                        echo $html;
                echo  '</div></div>';
       		}
       			//echo $couponcode;
       		else
       			echo 0;
		}
        else
            echo 0;
    }
    public function unsetAction(){
    	Mage::getModel('core/cookie')->set('nw_user_reg','ap567no',3600,'/',null,null,false);
    	echo 1;
    }
    
    public function remember($value){
		Mage::getModel('core/cookie')->set('urme',$value,(3600*24*30),'/',null,null,false);
	}	
} // END of class