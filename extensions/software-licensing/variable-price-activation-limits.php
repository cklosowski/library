<?php
/*
Plugin Name: Easy Digital Downloads - Variable Pricing License Activation Limits
Plugin URL: http://easydigitaldownloads.com/extension/
Description: Limit the number of license activations permitted based on variable prices
Version: 1.0
Author: Pippin Williamson
Author URI: http://pippinsplugins.com
Contributors: mordauk
*/

function pw_edd_sl_license_at_limit( $ret = false, $license_id = 0, $limit = 0, $download_id = 0 ) {

  $purchase_id   = get_post_meta( $license_id, '_edd_sl_payment_id', true );
	$purchase_date = new DateTime( get_post_field( 'post_date', $purchase_id ) );
	$limit_date    = new DateTime( '2013-01-01' );

	if( $purchase_date < $limit_date ) {
		// licenses purchased before January 1, 2013 are unlimited
		return false;
	}

	$purchase_details = edd_get_payment_meta_cart_details( $purchase_id );

	$price_id = false;

	foreach( $purchase_details as $item ) {
		if( $item['id'] == $download_id ) {
			if( ! empty( $item['item_number']['options'] ) ) {
				foreach( $item['item_number']['options'] as $option ) {
					$price_id = (int) $option['price_id'];
				}
			}
		}
	}

	if( $price_id !== false ) {

		switch( $price_id ) {

			case 0:
				$limit = 1; // single site license
				break;
			case 1:
				$limit = 5; // up to 5 sites
				break;
			case 2:
				$limit = 0; // unlimited
				break;
		}

		$site_count = absint( get_post_meta( $license_id, '_edd_sl_site_count', true ) );

		// check to make sure a limit is in place
		if( $limit > 0 ) {
			if( $site_count >= absint( $limit ) ) {
				$ret = true; // license is at limit
			}
		}
	}

	return $ret;

}
add_filter( 'edd_sl_license_at_limit', 'pw_edd_sl_license_at_limit', 10, 4 );