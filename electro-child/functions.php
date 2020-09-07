<?php
/**
 * Electro Child
 *
 * @package electro-child
 */

define( 'ELECTRO_CHILD_ASSETS_VERSION', '1.1.139' );


// Disable automatic updates
add_filter( 'auto_update_theme', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_core_update_send_email', '__return_false' );


// Load custom CSS/JS files
function peace_add_theme_scripts() {
	wp_enqueue_script(
		'theme-custom',
		get_stylesheet_directory_uri() . '/assets/js/custom.js',
		[ 'jquery' ],
		ELECTRO_CHILD_ASSETS_VERSION,
		true
	);
}

add_action( 'wp_enqueue_scripts', 'peace_add_theme_scripts' );


// Shortcode which adds custom icons to the products
add_shortcode( 'product_icons', function ( $atts, $content ) {
	$html          = '';
	$icons_path    = get_stylesheet_directory_uri() . '/images/product_icons';
	$existed_icons = array(
		'vegan-friendly' => array(
			'title'       => 'Vegan',
			'description' => 'Made using 100% vegan friendly and cruelty free ingredients'
		),

		'bio-degradable' => array(
			'title'       => 'Biodegradable',
			'description' => 'Made using ingredients and materials that are biodegradable and naturally compostable'
		),

		'100-natural' => array(
			'title'       => 'Natural',
			'description' => 'Made using 100% natural ingredients and materials with no synthetic chemicals'
		),

		'handmade' => array(
			'title'       => 'Handmade',
			'description' => 'Handcrafted with love'
		),

		'100-organic' => array(
			'title'       => 'Organic',
			'description' => 'Made using only 100% certified organic ingredients and materials'
		),

		'made-in-the-uk' => array(
			'title'       => 'Made In UK',
			'description' => 'This product has been made here in the UK'
		),

		'recyclable' => array(
			'title'       => 'Recyclable',
			'description' => 'Made using materials that are easily recyclable'
		),

		'recycled-materials' => array(
			'title'       => 'Recycled',
			'description' => 'This product has been made from recycled materials'
		),

		'plastic-free' => array(
			'title'       => 'Plastic Free',
			'description' => 'This product has 100% plastic free packaging'
		),

		'sustainable-materials' => array(
			'title'       => 'Sustainable',
			'description' => 'Made using sustainably and ethically harvested materials'
		),

		'palm-oil-free' => array(
			'title'       => 'Palm Oil Free',
			'description' => 'This product is 100% palm oil free'
		)
	);

	$content = trim( $content );

	if ( $content ) {
		$content = mb_strtolower( $content );

		$icons = explode( ',', $content );

		if ( is_array( $icons ) && count( $icons ) ) {
			$icons_per_row = 5;
			$current_icon  = 0;

			foreach ( $icons as $icon ) {
				$icon = trim( $icon );

				if ( in_array( $icon, array_keys( $existed_icons ) ) ) {
					$current_icon ++;

					$img_src         = "{$icons_path}/{$icon}.png";
					$img_title       = $existed_icons[ $icon ]['title'];
					$img_description = $existed_icons[ $icon ]['description'];

					$hide_item = $current_icon > $icons_per_row ? ' style="display: none"' : '';

					$html .= "<div class=\"product_icons_item\"{$hide_item}><img src=\"{$img_src}\" title=\"{$img_description}\" data-toggle=\"tooltip\" data-placement=\"top\"><div class=\"product_icon_title\">{$img_title}</div></div>\n";

					if ( count( $icons ) > $icons_per_row && $current_icon == $icons_per_row ) {
						$html .= "<div class=\"product_icons_show\"><a href=\"#\">View all benefits <i class=\"fas fa-chevron-down\" style=\"margin-left: 3px\"></i></a></div>\n";
					}

					if ( count( $icons ) > $icons_per_row && $current_icon % $icons_per_row == 0 ) {
						$html .= "<div></div>\n";
					}
				}
			}

			if ( count( $icons ) > $icons_per_row ) {
				$html .= "<div class=\"product_icons_hide\"><a href=\"#\">View less benefits <i class=\"fas fa-chevron-up\" style=\"margin-left: 3px\"></i></a></div>\n";
			}
		}
	}

	if ( $html ) {
		$html = "<div class=\"product_icons_container\">{$html}</div>\n";
	}

	return $html;
} );


// Change the breadcrumbs separator
function electro_change_breadcrumb_delimiter( $defaults ) {
	$defaults['delimiter'] = '<span class="delimiter">/</span>';

	return $defaults;
}


// Remove "Home" from breadcrumbs
add_filter( 'woocommerce_breadcrumb_defaults', 'peace_change_breadcrumb_home_text' );
function peace_change_breadcrumb_home_text( $defaults ) {
	unset( $defaults['home'] );

	return $defaults;
}


// Remove categories names from product lists
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_single_product_summary', 'electro_template_loop_categories', 1 );
	remove_action( 'woocommerce_before_shop_loop_item_title', 'electro_template_loop_categories', 20 );
	remove_action( 'woocommerce_shop_loop_item_title', 'electro_template_loop_categories', 50 );
	remove_action( 'electro_product_carousel_alt_content', 'electro_template_loop_categories', 30 );
	remove_action( 'electro_product_card_view_body', 'electro_template_loop_categories', 10 );
} );


// Single product availability
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_single_product_summary', 'electro_template_loop_availability', 10 );
	add_action( 'woocommerce_after_add_to_cart_form', 'electro_template_loop_availability', 10 );
} );

function electro_template_loop_availability() {
//	return;

	$availability = apply_filters( 'electro_get_availability', electro_get_availability() );

	if ( ! empty( $availability['availability'] ) ) : ?>

		<div class="availability !">
            <span class="electro-stock-availability"><p
						class="stock <?php echo esc_attr( $availability['class'] ); ?>"><?php echo esc_html( $availability['availability'] ); ?></p></span>
		</div>

	<?php endif;
}


// Single product wishlist
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_single_product_summary', 'electro_loop_action_buttons', 15 );
} );


// Single product: Remove SKU & Categories
function electro_product_description_tab() {
	echo '<div class="electro-description clearfix">';
	wc_get_template( 'single-product/tabs/description.php' );
	echo '</div>';
	//woocommerce_template_single_meta();
}


// Single product: Remove "Specification" tab from the admin panel
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_product_write_panel_tabs', array(
		'Electro_WC_Helper',
		'add_product_specification_panel_tab'
	) );
	remove_action( 'woocommerce_product_data_panels', array(
		'Electro_WC_Helper',
		'add_product_specification_panel_data'
	) );
} );


// Single product:
// Hide "Specification" tab
// Hide "How To Use" tab
// Hide "Ingredients" tab
// Hide "Materials" tab
// Rename "Description" tab
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	unset( $tabs['specification'] );
	unset( $tabs['ywtm_8763'] );
	unset( $tabs['ywtm_8765'] );
	unset( $tabs['ywtm_9249'] );

	$tabs['description']['title'] = __( 'Overview' );

	return $tabs;
}, 99 );


// Single Product: Display "Overview" tab data according to specification
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	$tabs['description']['callback'] = 'peace_custom_description_tab_content';

	return $tabs;
}, 99 );

function peace_custom_description_tab_content() {
	$product_id = wc_get_product()->get_id();

	if ( $product_id < 1 ) {
		return;
	}

	$description = wc_get_product()->get_description();
	$how_to_use  = get_post_meta( $product_id, '8763_default_editor', true );
	$ingredients = get_post_meta( $product_id, '8765_default_editor', true );
	$materials   = get_post_meta( $product_id, '9249_default_editor', true );

	$brand_description = '';

	$brands = get_the_terms( $product_id, 'yith_product_brand' );

	if ( is_array( $brands ) && count( $brands ) ) {
		$brand_description = $brands[0]->description;
		$brand_name        = $brands[0]->name;
		$brand_link        = get_term_link( $brands[0], 'yith_product_brand' );
	}

	$description       = preg_replace( '/&nbsp;$/', '', $description );
	$how_to_use        = preg_replace( '/&nbsp;$/', '', $how_to_use );
	$ingredients       = preg_replace( '/&nbsp;$/', '', $ingredients );
	$materials         = preg_replace( '/&nbsp;$/', '', $materials );
	$brand_description = preg_replace( '/&nbsp;$/', '', $brand_description );

	$description       = trim( $description );
	$how_to_use        = trim( $how_to_use );
	$ingredients       = trim( $ingredients );
	$materials         = trim( $materials );
	$brand_description = trim( $brand_description );

	$use_second_column = ( $how_to_use || $ingredients || $materials );

	?>
	<div class="description_columns">
		<div class="left-div"<?php if ( ! $use_second_column ) {
			echo ' style="min-width: 100%"';
		} ?>>
			<?php
			if ( $description ) {
				echo "<div class=\"description_block description_1\">\n";
				echo "<h2>Description</h2>\n";
				echo wpautop( $description );
				echo "</div>\n";
			}
			?>
		</div>
		<?php if ( $use_second_column ) { ?>
			<div class="right-div">
				<?php
				if ( $how_to_use ) {
					echo "<div class=\"description_block description_2\">\n";
					echo "<h2>How To Use</h2>\n";
					echo wpautop( $how_to_use );
					echo "</div>\n";
				}
				?>

				<?php
				if ( $ingredients ) {
					echo "<div class=\"description_block description_3\">\n";
					echo "<h2>Ingredients</h2>\n";
					echo wpautop( $ingredients );
					echo "</div>\n";
				}
				?>

				<?php
				if ( $materials ) {
					echo "<div class=\"description_block description_5\">\n";
					echo "<h2>Materials</h2>\n";
					echo wpautop( $materials );
					echo "</div>\n";
				}
				?>
			</div>
		<?php } ?>
		<?php
		if ( $brand_description ) {
			echo "<div class=\"description_block description_4\">\n";
			echo "<h2>Manufacturer Info</h2>\n";
			echo "<a href=\"{$brand_link}\" class=\"description_block_brand_link\">{$brand_name}</a>\n";
			echo wpautop( $brand_description );
			echo "</div>\n";
		}
		?>
	</div>
	<?php
}


// Category page: Change position of elements in products
add_action( 'wp_loaded', function () {
	// Remove actions
	remove_action( 'woocommerce_before_shop_loop', 'electro_product_subcategories', 0 );
	remove_action( 'woocommerce_before_shop_loop', 'electro_shop_control_bar', 11 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 30 );
	remove_action( 'woocommerce_shop_loop_item_title', 'electro_template_loop_product_thumbnail', 40 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'electro_template_loop_product_excerpt', 80 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 60 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'electro_template_loop_product_sku', 90 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 120 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'electro_template_loop_rating', 70 );
	//remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 45 );
	//remove_action( 'woocommerce_after_shop_loop_item', array(YITH_WCBR_Premium::get_instance(), add_loop_brand_template'), 5 );
	
	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );		

	// Add actions
	add_action( 'woocommerce_before_shop_loop', 'electro_product_subcategories', 50 );
	add_action( 'woocommerce_before_shop_loop', 'electro_shop_control_bar', 60 );
	add_action( 'woocommerce_shop_loop_item_title', 'electro_template_loop_product_thumbnail', 30 );

	/*
	add_action( 'woocommerce_shop_loop_item_title', array(
		YITH_WCBR_Premium::get_instance(),
		'add_loop_brand_template'
	), 32 );
	*/

	add_action( 'woocommerce_shop_loop_item_title', 'peace_woocommerce_shop_loop_item_title', 32 );
	add_action( 'woocommerce_after_shop_loop_item', 'electro_template_loop_availability', 40 );
	add_action( 'woocommerce_after_shop_loop_item', 'electro_template_loop_rating', 70 );	
} );

function peace_woocommerce_shop_loop_item_title() {
	global $product;

	if ( is_object( $product ) ) {

		$brands = get_the_terms( get_the_id(), 'yith_product_brand' );

		if ( is_array( $brands ) && count( $brands ) ) {
			$brand_name = $brands[0]->name;

			if ( $brand_name ) {
				echo "<div class=\"yith-wcbr-brands\">\n";
				echo "{$brand_name}\n";
				echo "</div>\n";
			}
		}

		echo '<div class="woocommerce-loop-product_title">' . get_the_title() . '</div>' . "\n";
	}
}


// Remove "Add to Wishlist" in product loop
add_action( 'wp_loaded', function () {
	remove_action( 'electro_loop_action_buttons', 'electro_add_to_wishlist_button' );
} );


// Fix Nginx 499 error
add_filter( 'cron_request', 'peace_cron_request' );
function peace_cron_request( $cron_request ) {
	$cron_request['args']['timeout'] = 5;

	return $cron_request;
}


// Move top jumbotron after category title
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_before_main_content', 'electro_shop_archive_jumbotron', 50 );
	add_action( 'woocommerce_before_shop_loop', 'electro_shop_archive_jumbotron', 10 );
} );


// Add jumbotron support to the YITH brands
require_once get_stylesheet_directory() . '/woocommerce/class-electro-yith-brands.php';

function electro_shop_archive_jumbotron() {
	$static_block_id = '';
	$brands_taxonomy = electro_get_brands_taxonomy();

	if ( is_shop() ) {
		$static_block_id = apply_filters( 'electro_shop_jumbotron_id', '' );
	} else if ( is_product_category() || is_tax( $brands_taxonomy ) || is_tax( 'yith_product_brand' ) ) {
		$term            = get_queried_object();
		$term_id         = $term->term_id;
		$static_block_id = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6', '<' ) ? absint( get_woocommerce_term_meta( $term_id, 'static_block_id', true ) ) : absint( get_term_meta( $term_id, 'static_block_id', true ) );

		if ( ! $static_block_id ) {
			$static_block_id = absint( get_term_meta( $term_id, 'yith_product_brand_static_block_id', true ) );
		}
	}

	if ( ! empty( $static_block_id ) ) {
		if ( is_elementor_activated() ) {
			$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $static_block_id );
		}

		if ( empty( $content ) ) {
			$static_block = get_post( $static_block_id );
			$content      = do_shortcode( $static_block->post_content );
		}

		echo "<div class=\"archive_jumbotron_wrapper\">\n";
		echo $content;
		echo "</div>\n";
	}
}

function electro_shop_bottom_archive_jumbotron() {
	$static_block_id = '';
	$brands_taxonomy = electro_get_brands_taxonomy();

	if ( is_shop() ) {
		$static_block_id = apply_filters( 'electro_shop_bottom_jumbotron_id', '' );
	} else if ( is_product_category() || is_tax( $brands_taxonomy ) || is_tax( 'yith_product_brand' ) ) {
		$term            = get_queried_object();
		$term_id         = $term->term_id;
		$static_block_id = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6', '<' ) ? absint( get_woocommerce_term_meta( $term_id, 'static_block_bottom_id', true ) ) : absint( get_term_meta( $term_id, 'static_block_bottom_id', true ) );

		if ( ! $static_block_id ) {
			$static_block_id = absint( get_term_meta( $term_id, 'yith_product_brand_static_block_bottom_id', true ) );
		}
	}

	if ( ! empty( $static_block_id ) ) {
		if ( is_elementor_activated() ) {
			$content = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $static_block_id );
		}

		if ( empty( $content ) ) {
			$static_block = get_post( $static_block_id );
			$content      = do_shortcode( $static_block->post_content );
		}

		echo '<div class="shop-archive-bottom">' . $content . '</div>';
	}
}


// Product thumbnail in checkout
add_filter( 'woocommerce_cart_item_name', 'product_thumbnail_in_checkout', 20, 3 );
function product_thumbnail_in_checkout( $product_name, $cart_item, $cart_item_key ) {
	if ( is_checkout() ) {
		$thumbnail  = $cart_item['data']->get_image( array( 70, 70 ) );
		$image_html = '<div class="product-item-thumbnail">' . $thumbnail . '</div> ';

		$product_name = $image_html . $product_name;
	}

	return $product_name;
}

// Change Number of Related Products Shown
add_filter( 'woocommerce_output_related_products_args', 'hff_commerce_child_related_products_args', 99, 4 );

function hff_commerce_child_related_products_args( $args ) {

	$args = array(
		'posts_per_page' => 4,
		'columns'        => 4,
		'orderby'        => 'DESC',
	);

	return $args;
}


//Global Partners Soft

add_action( 'electro_after_page', 'electro_child_footer', 15 );

add_action( 'widgets_init', 'electro_child_widgets_init', 11 );

add_action( 'wp_enqueue_scripts', 'electro_child_modify_scripts', 9999 );

add_action( 'wp_enqueue_scripts', 'electro_child_enqueue_scripts', 9999 );

add_action( 'wp_enqueue_scripts', 'electro_child_enqueue_styles' );

add_action( 'electro_mobile_footer_v1', 'electro_child_mobile_footer_v1_modify_actions', - 1 );

add_action( 'electro_mobile_footer_v2', 'electro_child_mobile_footer_v2_modify_actions', - 1 );

add_action( 'init', 'electro_child_init' );

function electro_child_footer() {
	get_template_part( 'templates/footer/blocks' );
}

function electro_child_modify_scripts() {
	global $wp_scripts;

//	$wp_scripts->registered['electro-js']->src =
//		get_stylesheet_directory_uri() . '/assets/js/electro.js';

	$wp_scripts->registered['wc-cart']->src =
		get_stylesheet_directory_uri() . '/assets/plugins/woocommerce/cart.js';
	$wp_scripts->registered['wc-cart']->ver = '3.7.new.1';
}

function electro_child_enqueue_scripts() {
	wp_enqueue_script(
		'electro-child-main',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		[ 'jquery' ],
		ELECTRO_CHILD_ASSETS_VERSION,
		true
	);
}

function electro_child_enqueue_styles() {
	wp_enqueue_style(
		'electro-child-main-style',
		get_stylesheet_directory_uri() . '/assets/css/main.css',
		[],
		ELECTRO_CHILD_ASSETS_VERSION
	);

	if ( is_cart() ) {
		wp_enqueue_style(
			'electro-child-woocommerce-cart',
			get_stylesheet_directory_uri() . '/assets/plugins/woocommerce/cart.css',
			[],
			ELECTRO_CHILD_ASSETS_VERSION
		);
	}
}

function electro_child_widgets_init() {
	register_sidebar(
		[
			'name'          => 'Mobile Footer (Static Blocks)',
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		]
	);
}

function electro_off_canvas_nav() {
	$classes = '';
	if ( apply_filters( 'electro_off_canvas_nav_hide_in_desktop', false ) ) {
		$classes = 'off-canvas-hide-in-desktop';
	}
	?>
	<div class="off-canvas-navigation-wrapper <?php echo esc_attr( $classes ); ?>">
		<div class="echld-el-nav">
			<div class="off-canvas-navbar-toggle-buttons clearfix">
				<button class="navbar-toggler navbar-toggle-hamburger sm-trigger-868" type="button">
					<i class="ec ec-menu"></i>
				</button>
				<button class="navbar-toggler navbar-toggle-close" type="button">
					<i class="ec ec-close-remove"></i>
				</button>
			</div>
			<span class="echld-el-search-icon" data-echld-search-status="collapsed"
					data-echld-search-toggle="echld-mobile-search"></span>
			<div
					data-echld-search-target="echld-mobile-search"
					data-echld-search-status="collapsed"
					class="echld-el-search vc_hidden-lg">
				<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
			</div>
		</div>

		<!--		--><?php //if ( false ) { ?>
		<div class="off-canvas-navigation echld-el-mobile-nav" id="default-oc-header">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'hand-held-nav',
				'container'      => false,
				'menu_class'     => 'nav nav-inline yamm',
				'fallback_cb'    => 'electro_handheld_nav_fallback',
				'walker'         => new wp_bootstrap_navwalker()
			) );
			?>
		</div>
		<!--		--><?php //} ?>
	</div>
	<?php
}

function electro_child_mobile_footer_v1_modify_actions() {
	remove_action( 'electro_mobile_footer_v1', 'electro_footer_social_icons_hh', 40 );
	remove_action( 'electro_mobile_footer_v1', 'electro_footer_v2_handheld_footer_bar_open', 50 );
	remove_action( 'electro_mobile_footer_v1', 'electro_handheld_footer_logo', 60 );
	remove_action( 'electro_mobile_footer_v1', 'electro_footer_call_us_v2', 70 );
	remove_action( 'electro_mobile_footer_v1', 'electro_footer_v2_handheld_footer_bar_close', 80 );
	remove_action( 'electro_mobile_footer_v1', 'electro_footer_v2_wrap_close', 99 );
}

function electro_child_mobile_footer_v2_modify_actions() {
	remove_action( 'electro_mobile_footer_v2', 'electro_footer_social_icons_hh', 40 );
	remove_action( 'electro_mobile_footer_v2', 'electro_footer_v2_handheld_footer_bar_open', 50 );
	remove_action( 'electro_mobile_footer_v2', 'electro_handheld_footer_logo', 60 );
	remove_action( 'electro_mobile_footer_v2', 'electro_footer_call_us_v2', 70 );
	remove_action( 'electro_mobile_footer_v2', 'electro_footer_v2_handheld_footer_bar_close', 80 );
	remove_action( 'electro_mobile_footer_v2', 'electro_footer_v2_wrap_close', 99 );
}

function electro_child_init() {
	add_shortcode(
		'peace-with-the-wild-footer-mobile-blocks',
		'electro_child_shortcode_peace_with_the_wild_footer_mobile_blocks'
	);
}

function electro_child_shortcode_peace_with_the_wild_footer_mobile_blocks( $atts, $content ) {
	ob_start();

	get_template_part( 'templates/shortcodes/footer-mobile-blocks' );

	return ob_get_clean();
}

function electro_child_the_footer_mobile_block_end() {
	$block = get_post( 47430 );
	echo do_shortcode( $block->post_content );
}

function electro_handheld_header_links() {
	$links = array(
		'my-account' => array(
			'priority' => 20,
			'callback' => 'electro_handheld_footer_bar_account_link',
		),
		'cart'       => array(
			'priority' => 30,
			'callback' => 'electro_handheld_footer_bar_cart_link',
		)
	);

	$links['search'] = array(
		'priority' => 10,
		'callback' => 'electro_handheld_header_search_link',
	);

	if ( ! function_exists( 'wc_get_page_id' ) || wc_get_page_id( 'myaccount' ) === - 1 ) {
		unset( $links['my-account'] );
	}

	if ( ! function_exists( 'wc_get_page_id' ) || wc_get_page_id( 'cart' ) === - 1 || electro_get_shop_catalog_mode() == true ) {
		unset( $links['cart'] );
	}

	$links = apply_filters( 'electro_handheld_header_links', $links );


	?>
	<div class="handheld-header-links">
		<ul class="columns-<?php echo count( $links ); ?>">
			<?php foreach ( $links as $key => $link ) : ?>
				<li class="<?php echo esc_attr( $key ); ?>">
					<?php
					if ( $link['callback'] ) {
						call_user_func( $link['callback'], $key, $link );
					}
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}


add_action( 'electro_mobile_header_v1', 'electro_child_header_v1_action', - 1 );

add_action( 'woocommerce_single_product_summary', 'electro_child_woocommerce_product_summary_modify_actions', - 1 );

add_action( 'woocommerce_single_product_summary', 'electro_child_woocommerce_product_summary_desktop', 20 );

add_action( 'woocommerce_after_single_product_summary', 'electro_child_woocommerce_after_product_summary', 9 );

add_action( 'woocommerce_after_add_to_cart_form', 'electro_child_woocommerce_after_add_to_cart_form_head', - 1 );

add_action( 'woocommerce_after_add_to_cart_form', 'electro_child_woocommerce_after_add_to_cart_form_foot', 99 );

add_action( 'wp_head', 'electro_child_head' );

function electro_child_head() {
	get_template_part( 'templates/head' );
}

function electro_child_header_v1_action() {
	if ( wp_is_mobile() ) {
		remove_action(
			'electro_mobile_header_v1',
			'electro_handheld_header_search',
			40
		);
	}
}

function electro_child_woocommerce_product_summary_modify_actions() {
	remove_action(
		'woocommerce_single_product_summary',
		'woocommerce_template_single_excerpt',
		20
	);
}

function electro_child_woocommerce_product_summary_desktop() {
	echo '<div class="echld--extra-hide-on-mobile">';
	woocommerce_template_single_excerpt();
	echo '</div>';
}

function electro_child_woocommerce_after_product_summary() {
	echo '<div class="echld--extra-hide-on-not-mobile">';
	woocommerce_template_single_excerpt();
	echo '</div>';
}

function electro_child_woocommerce_after_add_to_cart_form_head() {
	echo '<div class="echld-el-add-to-cart-extra">';
}

function electro_child_woocommerce_after_add_to_cart_form_foot() {
	echo '</div>';
}


add_filter( 'woocommerce_account_menu_items', 'electro_child_woocommerce_account_menu_items' );
function electro_child_woocommerce_account_menu_items( $items ) {
	$items['wishlist'] = 'Wishlist';

	return $items;
}

/*
add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 2 );
function misha_hook_endpoint( $url, $endpoint ) {

	if ( $endpoint === 'wishlist' ) {
		$url = site_url( 'wishlist' );
	}

	return $url;
}

add_action( 'woocommerce_account_wishlist_endpoint', 'electro_child_wishlist_endpoint_content' );
function electro_child_wishlist_endpoint_content() {
	echo do_shortcode( '[yith_wcwl_wishlist]' );
}
*/



function footer_script()
{
	?>
	<script>
		jQuery('.echld-el-search-icon').click(function(){
			var current_toggle = jQuery(this).attr('data-echld-search-status');
			if(current_toggle == 'expanded')
			{
				jQuery(this).attr('data-echld-search-status','collapsed');
				jQuery(this).next().attr('data-echld-search-status','collapsed');
			}
			else if(current_toggle == 'collapsed')
			{
				jQuery(this).attr('data-echld-search-status','expanded');
				jQuery(this).next().attr('data-echld-search-status','expanded');
			}
		});
	</script>
	<?php
}
add_action('wp_footer','footer_script');



// Hide that green loading bar at the top of website
// Electro - Dequeue pace in child theme
add_action( 'wp_print_scripts', 'ec_child_dequeue_scripts' );

function ec_child_dequeue_scripts() {
	wp_dequeue_script( 'pace' );
}



// Sort Products by Stock Status
add_action( 'woocommerce_product_query', 'bbloomer_sort_by_stock_status_then_alpha', 999 );
 
function bbloomer_sort_by_stock_status_then_alpha( $query ) {
    if ( is_admin() ) return;
    $query->set( 'meta_key', '_stock_status' );
    $query->set( 'orderby', array( 'meta_value' => 'ASC', 'menu_order' => 'ASC' ) );
}

// Sort Products SHORTCODE by Stock Status
/*
add_filter('woocommerce_shortcode_products_query', 'bbloomer_sort_by_stock_status_shortcode', 999, 3);
 
function bbloomer_sort_by_stock_status_shortcode( $args, $atts, $type ) {
    if ( $atts['orderby'] == "stock" ) {
        $args['orderby']  = array( 'meta_value' => 'ASC' );
        $args['meta_key'] = '_stock_status';
    }
    return $args;
}
*/


// Remove out of stock items for for "You may also like…" and "Related products" section
function iconic_enable_hide_out_of_stock_items( $template_name, $template_path, $located, $args ) {
    if( ! in_array( $template_name, array('single-product/related.php', 'single-product/up-sells.php') ) ) {
        return;
    }
 
    add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', function( $option ) { return "yes"; }, 10, 1 );
}

function iconic_disable_hide_out_of_stock_items( $template_name, $template_path, $located, $args ) {
    if( ! in_array( $template_name, array('single-product/related.php', 'single-product/up-sells.php') ) ) {
        return;
    }
 
    add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', function( $option ) { return "no"; }, 10, 1 );
}
 
add_action( 'woocommerce_before_template_part', 'iconic_enable_hide_out_of_stock_items', 10, 4 );
add_action( 'woocommerce_after_template_part', 'iconic_disable_hide_out_of_stock_items', 10, 4 );



// Redirect to the homepage all users trying to access feeds.
function pwtw_disable_feeds() {
	wp_redirect( home_url() );
	die;
}

// Disable global RSS, RDF & Atom feeds.
add_action( 'do_feed',      'pwtw_disable_feeds', -1 );
add_action( 'do_feed_rdf',  'pwtw_disable_feeds', -1 );
add_action( 'do_feed_rss',  'pwtw_disable_feeds', -1 );
add_action( 'do_feed_rss2', 'pwtw_disable_feeds', -1 );
add_action( 'do_feed_atom', 'pwtw_disable_feeds', -1 );

// Disable comment feeds.
add_action( 'do_feed_rss2_comments', 'pwtw_disable_feeds', -1 );
add_action( 'do_feed_atom_comments', 'pwtw_disable_feeds', -1 );

// Prevent feed links from being inserted in the <head> of the page.
add_action( 'feed_links_show_posts_feed',    '__return_false', -1 );
add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
remove_action( 'wp_head', 'feed_links',       2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );


// Disable self-pingback
function pwtw_disable_self_pingbacks( &$links ) {
  foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, get_option( 'home' ) ) )
            unset($links[$l]);
}

add_action( 'pre_ping', 'pwtw_disable_self_pingbacks' );


// Remove Generator and RSD meta tags
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');


// Change H1 to another tag in the blog categories
if ( ! function_exists( 'electro_post_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 * @since 1.0.0
	 */
	function electro_post_header() { ?>
		<header class="entry-header">
		<?php
		if ( is_single() ) {
			$comments_link = '';
			ob_start();
			electro_comment_meta();
			$comments_link = ob_get_clean();

			the_title( '<h1 class="entry-title">', sprintf( '%s</h1>', $comments_link ) );
			electro_post_meta();
		} else {
			the_title( sprintf( '<h4><a class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
			
			if ( 'post' == get_post_type() ) {
				electro_post_meta();
			}
		}
		?>
		</header><!-- .entry-header -->
		<?php
	}
}


// Customize subcategories list
if ( ! function_exists( 'electro_product_subcategories' ) ) {
	/**
	 * Wrapper woocommerce_product_subcategories
	 *
	 */
	function electro_product_subcategories() {

		$columns 	= electro_set_loop_shop_subcategories_columns();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			global $woocommerce_loop;
			$woocommerce_loop[ 'columns' ] = $columns;
		} else {
			$display_type = woocommerce_get_loop_display_mode();
			if( ! in_array( $display_type, array( 'subcategories', 'both' ) ) ) {
				return;
			}
			if( wc_get_loop_prop( 'is_shortcode' ) ) {
				return;
			}

			wc_set_loop_prop( 'columns', $columns );
			if ( ! woocommerce_products_will_display() ) {
				remove_action( 'woocommerce_before_shop_loop', 'electro_product_subcategories', 50 );
			}								
		}

		$class 		= 'woocommerce columns-' . $columns;
		$parent_id	= is_product_category() ? get_queried_object_id() : 0;
		$before 	= '<div class="' .esc_attr( $class ) . '"><ul class="product-loop-categories columns-'. esc_attr( $columns ) . '">';
		$after 		= '</ul></div>';

		if ( ! woocommerce_products_will_display() ) {

			$layout = electro_get_shop_layout();

			if ( 'full-width' == $layout ) {

				add_action( 'electro_after_product_subcategories', 'electro_best_sellers_carousel_in_category' );

			} else {

				add_action( 'electro_after_product_subcategories', 'electro_best_sellers_in_category' );
				add_action( 'electro_after_product_subcategories', 'electro_top_rated_in_category' );

			}
		}

		do_action( 'electro_before_product_subcategories' );

		ob_start();
		if ( ! function_exists( 'woocommerce_output_product_categories' ) ) {
			woocommerce_product_subcategories( array( 'parent_id' => $parent_id, 'before' => $before, 'after' => $after ) );
		} else {
			woocommerce_output_product_categories( array( 'parent_id' => $parent_id, 'before' => $before, 'after' => $after ) );
		}
		$sub_categories_html = ob_get_clean();

		if ( ! empty( $sub_categories_html ) ):

			$woocommerce_page_title = woocommerce_page_title( false );
			$section_product_categories_title = sprintf( esc_html__( '%s Categories', 'electro' ),  $woocommerce_page_title );
			$section_product_categories_title = apply_filters( 'electro_section_product_categories_title', $section_product_categories_title, $woocommerce_page_title );

			?><section class="section-product-categories">
			<?php echo wp_kses_post( $sub_categories_html ); ?>
			</section>
			<?php

		endif;

		do_action( 'electro_after_product_subcategories' );

		$columns 	= electro_set_loop_shop_columns();
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' ) ) {
			$woocommerce_loop[ 'columns' ] = $columns;
		} else {
			wc_set_loop_prop( 'columns', $columns );
			if ( 'subcategories' === $display_type ) {
				wc_set_loop_prop( 'total', 0 );
			}
			if ( ! woocommerce_products_will_display() ) {
				add_action( 'woocommerce_before_shop_loop', 'electro_product_subcategories', 50 );
			}
		}
	}
}


/*
// Move WooCommerce category description after title
add_action( 'wp_loaded', function () {
	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );	
	
	add_action( 'woocommerce_before_shop_loop', 'woocommerce_taxonomy_archive_description', 10 );
	add_action( 'woocommerce_before_shop_loop', 'woocommerce_product_archive_description', 10 );
} );
*/


// Change WooCommerce default password security level
add_filter( 'woocommerce_min_password_strength', 'PWTW_reduce_min_strength_password_requirement' );
function PWTW_reduce_min_strength_password_requirement( $strength ) {
    // 3 => Strong (default) | 2 => Medium | 1 => Weak | 0 => Very Weak (anything).
    return 1; 
}

add_filter( 'password_hint', 'PWTW_smarter_password_hint' );
function PWTW_smarter_password_hint ( $hint ) {
    $hint = 'Hint: longer password is stronger';
    return $hint;
}

add_action( 'wp_print_scripts', 'PWTW_remove_password_strength', 100 );
function PWTW_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}


// Hide Author Info in blog posts
if( ! function_exists( 'electro_author_info' ) ) {
	function electro_author_info() {
	}
}


// Qick fix for ATUM fatal error
if( ! function_exists( 'mb_regex_encoding' ) ) {
	function mb_regex_encoding( $encoding='UTF-8' ){
		return 'UTF-8';
	}
}


// 
// Matomo/Piwik: Add Ecommerce Tracking
//
/*
add_action( 'woocommerce_after_single_product', 'matomo_on_product_view', 99999, $args = 0 );
add_action( 'woocommerce_after_shop_loop', 'matomo_on_category_view', 99999, $args = 0 );


// Tracking a Product Page View
function matomo_on_product_view() {
	global $product;

	if ( ! is_object( $product ) || empty( $product ) ) {
		return;
	}
	
	$sku = esc_js( matomo_get_sku( $product ) );
	
	if ( empty ( $sku ) ) {
		return;
	}
	
	$name       = esc_js( $product->get_title() );
	$categories = matomo_get_product_categories( $product );
	$price      = esc_js( $product->get_price() );
	
	$js_code  = "var _paq = window._paq || [];\n";		
	$js_code .= "_paq.push(['setEcommerceView', '{$sku}', '{$name}', {$categories}, '{$price}']);\n";	
					
	echo "<script>{$js_code}</script>\n";
}


// Tracking a Product Category View
function matomo_on_category_view() {
	if ( ! is_product_category() ) {
		return;
	}
	
	$term = get_queried_object();

	if ( ! is_object( $term ) || empty( $term ) ) {
		return;
	}
				
	$name = esc_js( $term->name );
	
	if ( empty ( $name ) ) {
		return;
	}
	
	$js_code  = "var _paq = window._paq || [];\n";		
	$js_code .= "_paq.push(['setEcommerceView', false, false, '{$name}']);\n";	
					
	echo "<script>{$js_code}</script>\n";
}	


function matomo_get_product_categories( $product ) {
	$product_id = matomo_get_product_id( $product );

	$category_terms = get_the_terms( $product_id, 'product_cat' );

	$categories = array();

	if ( is_wp_error( $category_terms ) ) {
		return $categories;
	}

	if ( ! empty( $category_terms ) ) {
		foreach ( $category_terms as $category ) {
			$categories[] = $category->name;
		}
	}

	$max_num_categories = 5;
	$categories         = array_unique( $categories );
	$categories         = array_slice( $categories, 0, $max_num_categories );

	return json_encode($categories);
}


function matomo_get_sku( $product ) {
	if ( is_object( $product ) ) {
		return $product->get_sku();
	}

	return matomo_get_product_id( $product );
}


function matomo_get_product_id( $product ) {
	if ( ! $product ) {
		return;
	}

	if ( matomo_isWC3() ) {
		return $product->get_id();
	}

	return $product->id;
}


function matomo_isWC3() {
	global $woocommerce;
	$result = version_compare( $woocommerce->version, '3.0', '>=' );

	return $result;
}
*/


// Account sign up form: adds privacy policy accept checkbox
add_action( 'woocommerce_register_form', 'pwtw_add_registration_privacy_policy', 11 );
   
function pwtw_add_registration_privacy_policy() {
 
woocommerce_form_field( 'privacy_policy_reg', array(
   'type'          => 'checkbox',
   'class'         => array('form-row privacy'),
   'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
   'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
   'required'      => true,
   'label'         => 'I\'ve read and accept the <a href="/privacy-policy">Privacy Policy</a>',
));
  
}
  
  
// Show error if user does not tick privacy checkbox  
add_filter( 'woocommerce_registration_errors', 'pwtw_validate_privacy_registration', 10, 3 );
  
function pwtw_validate_privacy_registration( $errors, $username, $email ) {
if ( ! is_checkout() ) {
    if ( ! (int) isset( $_POST['privacy_policy_reg'] ) ) {
        $errors->add( 'privacy_policy_reg_error', __( 'Privacy Policy consent is required!', 'woocommerce' ) );
    }
}
return $errors;
}


// Sorting the shipping options by cost.
add_filter( 'woocommerce_package_rates' , 'pwtw_sort_shipping_services_by_cost', 10, 2 );
function pwtw_sort_shipping_services_by_cost( $rates, $package ) {
	if ( ! $rates )  return;
	
	$rate_cost = array();
	
	foreach( $rates as $rate ) {
		$rate_cost[] = $rate->cost;
	}
	
	// using rate_cost, sort rates.
	array_multisort( $rate_cost, $rates );
	
	return $rates;
}


// Set a particular shipping option as default
add_filter('woocommerce_shipping_chosen_method', 'pwtw_set_default_shipping_method', 10, 2);
function pwtw_set_default_shipping_method( $method, $available_methods ) {
    $default_method = 'flat_rate:3';
	
	// Don't change when "free shipping" is available	
	foreach ( array_keys( $available_methods ) as $available_method ) {
		if ( strpos( $available_method, 'free_shipping' ) !== false ){
			return $available_method;
		}
	}
	
	// Change default shipping method
    if( array_key_exists( $default_method, $available_methods ) ){
    	return $default_method;
	} else {
    	return $method;
	}
}