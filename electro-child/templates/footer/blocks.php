<?php $footer_post_id = 9582 ?>

<div class="main_footer">
	<div class="container">
		<div class="ecld-el-footer-general">
			<?php if ( post_type_exists( 'static_block' ) ) { ?>
				<?php $static_block = get_post( $footer_post_id ) ?>
				<?php echo do_shortcode( $static_block->post_content ) ?>
			<?php } ?>
		</div>
	</div>
	<div class="ecld-el-footer-end echld-el-copyright vc_hidden-lg">
		<?php electro_child_the_footer_mobile_block_end() ?>
	</div>
</div>