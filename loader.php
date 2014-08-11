<?php
/*
Plugin Name: BP Group Announcements
Description: Disables activity updates on group home pages in favor of an admins-only Announcements tab
Author: Boone B Gorges
Author URI: http://boone.gorg.es
Version: 1.0.4
*/

function bpga_loader() {
	if ( bp_is_active( 'groups' ) && bp_is_active( 'activity' ) ) {
		require( dirname(__FILE__) . '/bp-group-announcements.php' );
	}
}
add_action( 'bp_include', 'bpga_loader' );
