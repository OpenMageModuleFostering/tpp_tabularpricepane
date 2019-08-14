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
if ( (typeof jQuery === 'undefined') && !window.jQuery ) { 
		document.write(("<script type='text/javascript' src='"+js_url+"tpp/jquery-1.10.2.min.js'></script>"));
} else {
	if((typeof jQuery === 'undefined') && window.jQuery) {
		jQuery = window.jQuery;
	} else if((typeof jQuery !== 'undefined') && !window.jQuery) {
		window.jQuery = jQuery;
	}
}
var flg_v1 = 0; 	
function loadMoreProducts(from,to,limit,elementId,total,request_obj){
	if(flg_v1==1) return;
	jQuery(document).ready(function($){ 
			var root_element = $("#"+elementId).parent();
			if($("#"+elementId).parent().parent().hasClass("lt-tab"))
				root_element = $("#"+elementId).parent().parent(); 
			
			var category_id = $(root_element).find(".item-products").find(".ik-drp-product-category").val();
			var product_search_text = $(root_element).find(".item-products").find(".ik-product-search-text").val();
			if((category_id==='undefined')) category_id = 0;
			if((product_search_text==='undefined')) product_search_text = ""; 
 			$(root_element).find(".item-products").find(".ik-product-load-more").html("<div align='center'>"+$(".wp-load-icon").html()+"</div>");
			flg_v1 = 1;
			$.ajax({
				url: site_url+'tabularpricepane/index/loadmore',
				data: {'from' : from,'to' : to,'limit_start' : limit,'hide_categorybox':request_obj.hide_categorybox,'total' : total,'category_id' : category_id,'product_search_text' : product_search_text,'hide_searchbox' : request_obj.hide_searchbox,'price_difference' : request_obj.price_difference,'hide_product_price' : request_obj.hide_product_price,'hide_product_title' : request_obj.hide_product_title,'product_price_color' : request_obj.product_price_color,'product_title_color' : request_obj.product_title_color,'price_tab_text_color' : request_obj.price_tab_text_color,'price_tab_background_color' : request_obj.price_tab_background_color,'header_text_color' : request_obj.header_text_color,'header_background_color' : request_obj.header_background_color,'display_title_price_over_image' : request_obj.display_title_price_over_image,'number_of_product_display' : request_obj.number_of_product_display,'vcode' : request_obj.vcode	},
				success:function(data) {     
					printData(elementId,data,"loadmore");
				},error: function(errorThrown){ console.log(errorThrown);}
			});
	});
}
function fillProducts(elementId,from,to,request_obj,flg_pr){
	if(flg_v1==1) return;
 	jQuery(document).ready(function($){   
			var root_element = $("#"+elementId).parent();
			if($("#"+elementId).parent().parent().hasClass("lt-tab"))
				root_element = $("#"+elementId).parent().parent();  
				
			$(root_element).parent().find(".price-item").each(function(){ 
				$(this).removeClass("pn-active");
			});
			
			$("#"+elementId).addClass("pn-active");	
			 
			if(flg_pr==2){
				var category_id =  $(root_element).find(".item-products").find(".ik-drp-product-category").val(); 
				$(root_element).find(".ik-search-button").html("<br />"+$(".wp-load-icon").html()); 
			}
			else{
				if($("#"+elementId).parent().parent().hasClass("lt-grid")){
					$(root_element).find(".item-products").hide();
					$(root_element).parent().find(".item-products").hide();
				}
				var category_id = eval("default_category_id_"+request_obj.vcode); 
				$("#"+elementId).find(".ik-load-content,.ik-product-no-items").remove();
				$("#"+elementId).find(".ld-price-item-text").html("<div class='ik-load-content'>"+$(".wp-load-icon").html()+"</div>");
			}	
			var product_search_text = $(root_element).find(".item-products").find(".ik-product-search-text").val();
			if((category_id==='undefined')) category_id = 0;
			if((product_search_text==='undefined')) product_search_text = "";
 			flg_v1 = 1;
		 	$.ajax({
				url: site_url+"tabularpricepane/index",
				data: {'action':'getProducts1','from' : from,'to' : to,flg_pr:flg_pr,'hide_categorybox':request_obj.hide_categorybox,'category_id' : category_id,'product_search_text' : product_search_text,'hide_searchbox' : request_obj.hide_searchbox,'price_difference' : request_obj.price_difference,'hide_product_price' : request_obj.hide_product_price,'hide_product_title' : request_obj.hide_product_title,'product_price_color' : request_obj.product_price_color,'product_title_color' : request_obj.product_title_color,'price_tab_text_color' : request_obj.price_tab_text_color,'price_tab_background_color' : request_obj.price_tab_background_color,'header_text_color' : request_obj.header_text_color,'header_background_color' : request_obj.header_background_color,'display_title_price_over_image' : request_obj.display_title_price_over_image,'number_of_product_display' : request_obj.number_of_product_display,'vcode' : request_obj.vcode},
				success:function(data) { 
					printData(elementId,data,"fillproduct"); 
				},error: function(errorThrown){ console.log(errorThrown);}
			});   
	});		

	;(function($){
		$(window).resize(function(){
			$(".wea_content .item-products").each(function(){
				var root_element = $(this).parent();
				var cnt_width = $(this).parent().width();
				$(this).find(".ik-product-item").each(function(){
					if(cnt_width > 1024)		
						$(this).css("width","230px");
					else if(cnt_width <= 1024 && cnt_width > 768)	
						$(this).css("width","19%");
					else if(cnt_width <= 858 && cnt_width > 640)	
						$(this).css("width","24%");
					else if(cnt_width <= 640 && cnt_width > 480)	
						$(this).css("width","32%"); 
					else if(cnt_width <= 480 && cnt_width > 260)	
						$(this).css("width","49%");  
					else if(cnt_width <= 260)	
						$(this).css("width","99%");     
				});
				if(cnt_width<=390 && cnt_width > 280){
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").css("width","82%");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("width","82%");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("padding-top","10px");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-button").css("padding-top","14px"); 
				}else if(cnt_width<=280){
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").css("width","82%");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("width","82%");
				}else{
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").removeAttr("style");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").removeAttr("style");
					$(root_element).find(".item-products").find(".ik-product-category .ik-search-button").removeAttr("style");
				}
			});
		});
	})(jQuery);	
}
function printData(elementId,data,flg){
	jQuery(document).ready(function($){
		
	  	var root_element = $("#"+elementId).parent();
		if($("#"+elementId).parent().parent().hasClass("lt-tab"))
			root_element = $("#"+elementId).parent().parent(); 
		 
		if(flg=="loadmore"){
			$(root_element).find(".item-products").find(".wp-load-icon").remove();
			$(root_element).find(".item-products").find(".clr").remove();
			$(root_element).find(".item-products").find(".ik-product-load-more").remove(); 
			$(root_element).find(".item-products").append(data).fadeIn(400); 
			$(root_element).find(".item-products").append("<div class='clr'></div>");
		}else{ 
			$("#"+elementId).find(".ik-load-content,.ik-product-no-items").remove();
			//$(root_element).find(".item-products").fadeOut(1);
			//$(root_element).parent().find(".item-products").fadeOut(1);
			$(root_element).find(".item-products").html(data).fadeIn(400);  
		}
		
		var cnt_width = $("#"+elementId).parent().parent().width();
		var prod_item_height = [];
		$(root_element).find(".item-products").find(".ik-product-item").each(function(){		
			
			if(cnt_width > 1024)		
				$(this).css("width","230px");
			else if(cnt_width <= 1024 && cnt_width > 768)	
				$(this).css("width","19%");
			else if(cnt_width <= 858 && cnt_width > 640)	
				$(this).css("width","24%");
			else if(cnt_width <= 640 && cnt_width > 480)	
				$(this).css("width","32%"); 
			else if(cnt_width <= 480 && cnt_width > 260)	
				$(this).css("width","49%");  
			else if(cnt_width <= 260)	
				$(this).css("width","99%");  	 
				
			prod_item_height.push($(this).find(".ik-product-name").height()); 
		});
		
		if(cnt_width<=390 && cnt_width > 280){
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").css("width","82%");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("width","82%");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("padding-top","10px");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-button").css("padding-top","14px"); 
		}else if(cnt_width<=280){
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").css("width","82%");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").css("width","82%");
		}else{
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").removeAttr("style");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-category").removeAttr("style");
				$(root_element).find(".item-products").find(".ik-product-category .ik-search-button").removeAttr("style");
		}
		if($(root_element).find(".item-products").find(".ik-product-category .ik-search-category").length<=0){
			$(root_element).find(".item-products").find(".ik-product-category .ik-search-title").css("margin-right",0);
		}
		
		if(cnt_width > 260)
		$(root_element).find(".item-products").find(".ik-product-item").find(".ik-product-name").css("height",(Math.max.apply(Math,prod_item_height))+"px");
		
		flg_v1 = 0;	
	});	  
}
var flg_ms_hover = 0;
function pr_item_image_mousehover(ob_pii){ 
	if(flg_ms_hover == 1) return;
	jQuery(document).ready(function($){
		$(ob_pii).find(".ov-layer").show();  
		$(ob_pii).find(".ov-layer").css("visibility","visible"); 
		$(ob_pii).find(".ov-layer").css("top","40");  
		flg_ms_hover = 1;
		if($.trim($(ob_pii).find(".ov-layer").html())!="")
			$(ob_pii).find(".ov-layer").animate({opacity:0.9,top:0},0); 
		else
			$(ob_pii).find(".ov-layer").animate({opacity:0.5,top:0},0); 
	});
} 
function pr_item_image_mouseout(ob_pii){
	jQuery(document).ready(function($){ 
		$(ob_pii).find(".ov-layer").animate({opacity:0,top:40},0);
		flg_ms_hover = 0;
		$(ob_pii).find(".ov-layer").hide();
		$(ob_pii).find(".ov-layer").css("visibility","hidden");  
	});
}

function price_tab_ms_out(ob_ms_eff){
	jQuery(document).ready(function($){ 
		$(ob_ms_eff).removeClass("pn-active-bg"); 	
	});
}
function price_tab_ms_hover(ob_ms_eff){
	jQuery(document).ready(function($){ 
		$(ob_ms_eff).addClass("pn-active-bg"); 	
	});
}