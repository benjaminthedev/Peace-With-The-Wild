<?php
/**
 * My Points
 *
 * Shows total of user's points account page
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! is_user_logged_in() ) { ?>

	<p><?php _e( 'You must to be logged in to view your points.', 'yith-woocommerce-points-and-rewards' ) ?></p>
	<?php
	return;
}


$points   = get_user_meta( get_current_user_id(), '_ywpar_user_total_points', true );
$points   = ( $points == '' ) ? 0 : $points;
$singular = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
$plural   = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );

$history = YITH_WC_Points_Rewards()->get_history( get_current_user_id() );

//if ( get_option('ywpar_show_point_worth_my_account','yes') == 'yes' ) {
    $toredeem = '';
    $rates = YITH_WC_Points_Rewards()->get_option('rewards_conversion_rate');
    $money = $rates[get_woocommerce_currency()]['money'];
    $toredeem_raw = abs( ($points / $rates[get_woocommerce_currency()]['points']) * $money);
    $toredeem     = wc_price( $toredeem_raw );
	
	
	$need_points_to_redeeming = 0;	
	$minimum_amount_raw       = YITH_WC_Points_Rewards()->get_option('minimum_amount_discount_to_redeem');
						
	if ( $minimum_amount_raw ){
		$minimum_amount           = wc_price( $minimum_amount_raw );	
		$minimum_amount_in_points = ceil( $minimum_amount_raw * $rates[get_woocommerce_currency()]['points'] / $money );
		$need_points_to_redeeming = $minimum_amount_in_points - $points;
	}
//}



?>
<div class="ywpar-wrapper">
	<h2><?php echo apply_filters( 'ywpar_my_account_my_points_title', sprintf( __( 'My %s', 'yith-woocommerce-points-and-rewards' ), $plural ) ); ?></h2>

    <div class="my_points"><?php
		//printf( _n( '<strong>%1$s</strong> %2$s', '<strong>%3$s</strong> %4$s', $points, 'yith-woocommerce-points-and-rewards' ), $points, $singular, $points, $plural );
		
		echo __( 'Your Points:', 'yith-woocommerce-points-and-rewards' ) . ' <b>' . $points . '</b>';
		
		if ( $minimum_amount_raw && $toredeem_raw >= $minimum_amount_raw ){
			echo " ( worth <b>{$toredeem}</b> )";
		}else if( $need_points_to_redeeming ){
			echo "<br><br>\n";
			echo "You are <b>{$need_points_to_redeeming}</b> " . strtolower( _n( $singular, $plural, $need_points_to_redeeming, 'yith-woocommerce-points-and-rewards' ) ) . " away from redeeming <b>{$minimum_amount}</b> off your next order.";
		}		
		
		/*
			if ( get_option('ywpar_show_point_worth_my_account','yes') == 'yes' ) {
				echo '<span>(' . __('worth', 'yith-woocommerce-points-and-rewards') . ' ' . $toredeem . ')</span>';
			}
		*/
		?>
    </div>


	<h2><?php echo apply_filters( 'ywpar_my_account_my_points_history_title', sprintf( __( 'My %s History', 'yith-woocommerce-points-and-rewards' ), $singular ) ); ?></h2>


	<?php if ( $history ) : ?>
		<table class="shop_table ywpar_points_rewards my_account_orders">
			<thead>
			<tr>
				<th class="ywpar_points_rewards-date"><?php _e( 'Date', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-action"><?php _e( 'Action', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-order"><?php _e( 'Order No.', 'yith-woocommerce-points-and-rewards' ); ?></th>
				<th class="ywpar_points_rewards-points"><?php echo $plural; ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $history as $item ) : ?>
				<tr class="ywpar-item">
					<td class="ywpar_points_rewards-date">
						<?php echo date_i18n( wc_date_format(), strtotime( $item->date_earning ) ) ?>
					</td>
					<td class="ywpar_points_rewards-action">
						<?php echo ( $item->description ) ? stripslashes($item->description) : YITH_WC_Points_Rewards()->get_action_label( $item->action ) ?>
					</td>
					<td class="ywpar_points_rewards-order">
						<?php
						if ( $item->order_id != 0 ):
							$order = wc_get_order( $item->order_id );
							if ( $order ) {
								echo '<a href="' . esc_url( $order->get_view_order_url() ) . '">#' . $order->get_order_number() . '</a>';
							} else {
								echo '#' . $item->order_id;
							}
						endif ?>
					</td>
					<td class="ywpar_points_rewards-points" width="1%">
						<?php echo $item->amount ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
