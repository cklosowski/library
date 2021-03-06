<?php
/**
 * Plugin Name: Easy Digital Downloads - Unlimited User Downloads for EDD Recurring
 * Plugin URI: http://easydigitaldownloads.com/
 * Description: Allows certain users to be allowed to bypass the active account requirement in EDD Recurring Payments
 * Author: Pippin Williamson
 * Author URI: http://pippinsplugins.com
 * Contributors: mordauk
 * Version: 1.0
 */

class EDD_Recurring_Unlimited_User_Downloads {

 	function __construct() {
 		if( ! class_exists( 'EDD_Recurring' ) )
 			return;

 		add_filter( 'edd_recurring_download_has_access', array( $this, 'process_download' ), 10, 4 );
 		add_action( 'show_user_profile',                 array( $this, 'user_checkbox'    )        );
 		add_action( 'edit_user_profile',                 array( $this, 'user_checkbox'    )        );
		add_action( 'personal_options_update',           array( $this, 'update_access'    )        );
 	}

 	// Check if the user has been granted unlimited access
 	public function process_download( $has_access = false, $user_id = 0, $download_id = 0, $price_id = null ) {

 		if( ! $has_access )
 			return false;

 		if( get_user_meta( $user_id, '_edd_recurring_unlimited', true ) )
 			return true;

 		return $has_access;

 	}

 	public function user_checkbox( $user ) {
		global $edd_options;

		$unlimited = get_user_meta( $user->ID, '_edd_recurring_unlimited', true )
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="edd_recurring_unlimited"><?php _e( 'Unlimited Recurring Access', 'edd' ); ?></label>
					</th>
					<td>
						<input name="edd_recurring_unlimited" type="checkbox" id="edd_recurring_unlimited" value="0"<?php checked( true, $unlimited ); ?>/>
						<span class="description"><?php _e( 'Grant user unlimited access for recurring downloads. Checking this means a user will not need a subscription to download files.', 'edd' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	public function update_access( $user_id = 0 ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['edd_recurring_unlimited'] ) ) {

			update_user_meta( $user_id, '_edd_recurring_unlimited', '1' );

		} elseif( !  isset( $_POST['edd_recurring_unlimited'] ) ) {

			delete_user_meta( $user_id, '_edd_recurring_unlimited' );

		}
	}

 }

function edd_recurring_unlimited_init() {
	new EDD_Recurring_Unlimited_User_Downloads;
}
add_action( 'plugins_loaded', 'edd_recurring_unlimited_init', 999 );