jQuery(document).ready(function () {
    // Append brand link to the product title
    jQuery('.single-product-wrapper .yith-wcbr-brands').appendTo('.product_title').show();
	

    // Single product: Product icons – repositioned below main image
    jQuery('.single-product-wrapper .product_icons_container').appendTo('.product-images-wrapper').show();

    if (jQuery('.single-product .woocommerce-product-gallery').length && jQuery('.single-product .electro-wc-product-gallery').length < 1) {
        jQuery('.product_icons_container').css('margin-left', '0px');
    }


    // Single product: Show all icons on click
    jQuery('.product_icons_container .product_icons_show').click(function (e) {
        e.preventDefault();

        jQuery('.product_icons_container .product_icons_item').slideDown('medium', function () {
            jQuery('.product_icons_container .product_icons_show').hide();
            jQuery('.product_icons_container .product_icons_hide').show();
        });

    });

    jQuery('.product_icons_container .product_icons_hide').click(function (e) {
        e.preventDefault();

        jQuery('.product_icons_container .product_icons_show ~ .product_icons_item').slideUp('medium', function () {
            jQuery('.product_icons_container .product_icons_hide').hide();
            jQuery('.product_icons_container .product_icons_show').show();
        });
    });


    // Single product: Set "Add to wishlist" before "Points"
    jQuery('.single-product-wrapper .yith-wcwl-add-button').insertAfter('.single-product-wrapper .availability');


    // Single product: Gallery floated to top left of image vertical aligned
    jQuery('.single-product-wrapper .electro-wc-product-gallery').prependTo('.single-product .product-images-wrapper');


    // Single product: Hide thumbnails bar if only one image
    if (jQuery('.single-product .woocommerce-product-gallery').length && jQuery('.single-product .electro-wc-product-gallery').length < 1) {
        jQuery('.single-product .woocommerce-product-gallery').css('margin-left', '0px');
    }


    // Slightly darken everything else other than the nav bar
    if (jQuery('#menu-navbar-primary').length) {
        jQuery('<div class="custom_overlay"></div>').insertAfter('#content');
        jQuery('.custom_overlay').css('top', jQuery('#menu-navbar-primary').offset().top + 45 + 'px');

        jQuery('#menu-navbar-primary').hover(
            function () {
                jQuery('.custom_overlay').show();
            },
            function () {
                jQuery('.custom_overlay').hide();
            }
        );
    }


    // Single product: Reviews
    if (jQuery('.entry-summary .woocommerce-product-rating').length) {
        jQuery('<div class="reviews_in_tab">' + jQuery('.entry-summary .woocommerce-product-rating').first().html() + '</div>').insertAfter('.wc-tabs');
        jQuery('.reviews_tab').remove();
    }

    jQuery('#tab-reviews').insertAfter('.wc-tabs-wrapper').show();

    if (jQuery('.single-product .woocommerce-noreviews').length) {
        jQuery('.reviews_bar').hide();
        jQuery('#reviews_header').hide();
        jQuery('#reviews_order').hide();
    }
});


jQuery(window).load(function () {
    // Fix YITH Filters "Show more" issue
    if (jQuery('#sidebar .yith-wcan-list').length) {
        jQuery('#sidebar .widget-title.with-dropdown').each(function () {
            if (jQuery(this).hasClass('open')) {
                jQuery(this).parent().find('.maxlist-more').show();
            } else {
                jQuery(this).parent().find('.maxlist-more').hide();
            }
        });

        jQuery('#sidebar .widget-title.with-dropdown').click(function () {
            if (jQuery(this).hasClass('open')) {
                jQuery(this).parent().find('.maxlist-more').show();
            } else {
                jQuery(this).parent().find('.maxlist-more').hide();
            }
        });
    }


    // Fix YITH Waiting List box is displayed twice for out of stock variations
    //
    // if (jQuery('.single-product').length) {
    //     setInterval(function () {
    //         if (jQuery('.single-product #yith-wcwtl-output').length > 1) {
    //             jQuery('.single-product #yith-wcwtl-output').hide();
    //             jQuery('.single-product #yith-wcwtl-output').last().show();
    //         }
    //     }, 200);
    // }


    // Replace the default price of the product (x.xx - x.xx) with a price of selected option
    if (jQuery('.single-product .variations_form').length) {
        var original_price = jQuery('.entry-summary .electro-price').first().html();

        setInterval(function () {
            if (jQuery('.woocommerce-variation-price .electro-price').is(':visible')) {
                jQuery('.entry-summary .electro-price').first().html(jQuery('.woocommerce-variation-price .electro-price').html());
            } else {
                jQuery('.entry-summary .electro-price').first().html(original_price);
            }

            if (jQuery('.entry-summary .variations select').val()) {
                jQuery('.entry-summary .single_variation_wrap').css('margin-top', '-18px');
            } else {
                jQuery('.entry-summary .single_variation_wrap').css('margin-top', '0px');
            }
        }, 200);
    }


    // Remove extra link to the product from brand name
    jQuery('.yith-wcbr-brands .woocommerce-LoopProduct-link').remove();
	
	
	// Expand YITH Live Chat on some pages
	if (jQuery('#YLC').length) {
		var trigger_on_pages = ['/cart/', '/checkout/'];
		
		if (trigger_on_pages.indexOf(jQuery(location).attr('pathname')) !== -1){
			setTimeout(function(){
				jQuery('a[href="#yith-live-chat"]').first().click();
			}, 3000);			
		}
	}
});