<?php
/**
 * Class to setup Brands attribute
 *
 * @package Electro/WooCommerce
 */

class Electro_YITH_Brands {

	public function __construct() {

		add_action( "yith_product_brand_add_form_fields", array( $this, 'add_category_fields' ), 10 );
		add_action( "yith_product_brand_edit_form_fields", array( $this, 'edit_category_fields' ), 10, 2 );
		add_action( 'create_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

	}


	/**
	 * Product Category static block fields.
	 *
	 * @return void
	 */
	public function add_category_fields() {
		?>
		<div class="form-field">
			<?php 
				if( post_type_exists( 'static_block' ) ) :

					$args = array(
						'posts_per_page'	=> -1,
						'orderby'			=> 'title',
						'post_type'			=> 'static_block',
					);
					$static_blocks = get_posts( $args );
				endif;
			?>
			<div class="form-group">
				<label><?php _e( 'Jumbotron', 'electro' ); ?></label>
				<select id="yith_product_brand_static_block_id" class="form-control" name="yith_product_brand_static_block_id">
					<option><?php echo __( 'Select a Static Block', 'electro' ); ?></option>
				<?php if( !empty( $static_blocks ) ) : ?>
				<?php foreach( $static_blocks as $static_block ) : ?>
					<option value="<?php echo esc_attr( $static_block->ID ); ?>"><?php echo get_the_title( $static_block->ID ); ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
				</select>
			</div>

			<div class="form-group">
				<label><?php _e( 'Bottom Jumbotron', 'electro' ); ?></label>
				<select id="yith_product_brand_static_block_bottom_id" class="form-control" name="yith_product_brand_static_block_bottom_id">
					<option><?php echo __( 'Select a Static Block', 'electro' ); ?></option>
				<?php if( !empty( $static_blocks ) ) : ?>
				<?php foreach( $static_blocks as $static_block ) : ?>
					<option value="<?php echo esc_attr( $static_block->ID ); ?>"><?php echo get_the_title( $static_block->ID ); ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
				</select>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit Category static block fields.
	 *
	 * @param mixed $term Term (yith_product_brand) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_category_fields( $term, $taxonomy ) {

		$static_block_id 		= '';
		$static_block_bottom_id = '';
		$static_block_id 		= defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6', '<' ) ? absint( get_woocommerce_term_meta( $term->term_id, 'yith_product_brand_static_block_id', true ) ) : absint( get_term_meta( $term->term_id, 'yith_product_brand_static_block_id', true ) );
		$static_block_bottom_id = defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.6', '<' ) ? absint( get_woocommerce_term_meta( $term->term_id, 'yith_product_brand_static_block_bottom_id', true ) ) : absint( get_term_meta( $term->term_id, 'yith_product_brand_static_block_bottom_id', true ) );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Top Jumbotron', 'electro' ); ?></label></th>
			<td>
				<?php 
					if( post_type_exists( 'static_block' ) ) :

						$args = array(
							'posts_per_page'	=> -1,
							'orderby'			=> 'title',
							'post_type'			=> 'static_block',
						);
						$static_blocks = get_posts( $args );
					endif;
				?>
				<div class="form-group">
					<select id="yith_product_brand_static_block_id" class="form-control" name="yith_product_brand_static_block_id">
						<option><?php echo __( 'Select a Static Block', 'electro' ); ?></option>
					<?php if( !empty( $static_blocks ) ) : ?>
					<?php foreach( $static_blocks as $static_block ) : ?>
						<option value="<?php echo esc_attr( $static_block->ID ); ?>" <?php echo ( $static_block_id == $static_block->ID  ? 'selected' : '' ); ?>><?php echo get_the_title( $static_block->ID ); ?></option>
					<?php endforeach; ?>
					<?php endif; ?>
					</select>
				</div>
				<div class="clear"></div>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Bottom Jumbotron', 'electro' ); ?></label></th>
			<td>
				<?php 
					if( post_type_exists( 'static_block' ) ) :

						$args = array(
							'posts_per_page'	=> -1,
							'orderby'			=> 'title',
							'post_type'			=> 'static_block',
						);
						$static_blocks = get_posts( $args );
					endif;
				?>

				<div class="form-group">
					<select id="yith_product_brand_static_block_bottom_id" class="form-control" name="yith_product_brand_static_block_bottom_id">
						<option><?php echo __( 'Select a Static Block', 'electro' ); ?></option>
					<?php if( !empty( $static_blocks ) ) : ?>
					<?php foreach( $static_blocks as $static_block ) : ?>
						<option value="<?php echo esc_attr( $static_block->ID ); ?>" <?php echo esc_attr( $static_block_bottom_id == $static_block->ID  ? 'selected' : '' ); ?>><?php echo get_the_title( $static_block->ID ); ?></option>
					<?php endforeach; ?>
					<?php endif; ?>
					</select>
				</div>
				<div class="clear"></div>
			</td>
		</tr>
        <?php
	}

	/**
	 * Save Category static block fields.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_category_fields( $term_id, $tt_id, $taxonomy ) {
				
		if ( isset( $_POST['yith_product_brand_static_block_id'] ) )
			update_woocommerce_term_meta( $term_id, 'yith_product_brand_static_block_id', absint( $_POST['yith_product_brand_static_block_id'] ) );

		if ( isset( $_POST['yith_product_brand_static_block_bottom_id'] ) )
			update_woocommerce_term_meta( $term_id, 'yith_product_brand_static_block_bottom_id', absint( $_POST['yith_product_brand_static_block_bottom_id'] ) );
		
		delete_transient( 'wc_term_counts' );
		
	}

}

new Electro_YITH_Brands;