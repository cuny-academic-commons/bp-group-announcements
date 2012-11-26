<?php

/**
 * Bootstrap the BP_Group_Announcements extension
 */
function bpga_bootstrap() {
	bp_register_group_extension( 'BP_Group_Announcements' );
}
add_action( 'bp_init', 'bpga_bootstrap' );


/**
 * Implementation of BP_Group_Extension
 *
 * Registers the group extension and provides a display method
 *
 * @since 1.0
 */
class BP_Group_Announcements extends BP_Group_Extension {

	var $enable_create_step = false;
	var $enable_nav_item = true;
	var $enable_edit_item = false;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * */
	public function __construct() {
		$this->name = __( 'Announcements', 'bpga' );
		$this->slug = bpga_get_slug();

		$this->nav_item_position = 13;

		// Disable the activity update form on the group home page. Props r-a-y
		add_action( 'bp_before_group_activity_post_form', create_function( '', 'ob_start();' ), 9999 );
		add_action( 'bp_after_group_activity_post_form', create_function( '', 'ob_end_clean();' ), 0 );

		// Because this isn't the group home page, we have to ensure
		// that 'groups' is passed as the object type with the post form
		add_action( 'bp_activity_post_form_options', array( $this, 'object_input' ) );
	}

	/**
	 * Display the content of the Announcements tab
	 *
	 * @since 1.0
	 */
	public function display() {

		if ( bp_group_is_admin() || bp_current_user_can( 'bp_moderate' ) ) {
			$group_role = 'an administrator';
		} else if ( bp_group_is_mod() ) {
			$group_role = 'a moderator';
		}

		?>

		<h3><?php _e( 'Announcements', 'bpga' ) ?></h3>

		<p><?php _e( 'On this page, you&#8217;ll see announcements that administrators and moderators have left for the group.', 'bpga' ) ?></p>

		<?php if ( bpga_can_post_group_announcements() ) : ?>

			<?php $group_role = bp_group_is_admin() || bp_current_user_can( 'bp_moderate' ) ? __( 'administrator', 'bpga' ) : __( 'moderator', 'bpga' ) ?>
			<p><?php printf( __( 'As %1$s in %2$s, you can post announcements to the group&#8217;s activity stream.', 'bpga' ), $group_role, bp_get_group_name() ) ?></p>

			<?php locate_template( array( 'activity/post-form.php'), true ) ?>

		<?php endif ?>

		<div class="activity single-group">
			<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
		</div>

		<?php
	}

	/**
	 * The post-form template logic won't provide the necessary -object
	 * -post-in hidden inputs because this is not the group home page. So
	 * we provide them manually
	 *
	 * @since 1.0
	 */
	public static function object_input() {
		if ( bp_is_group() && bp_is_current_action( bpga_get_slug() ) ) {
			echo '<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />';
			echo '<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="' . bp_get_group_id() . '" />';
		}
	}
}

/**
 * Return the Announcements slug
 */
function bpga_get_slug() {
	return apply_filters( 'bpga_get_slug', 'announcements' );
}

/**
 * Can the current user post group announcements?
 *
 * By default, posting is limited to
 *
 * @since 1.0
 */
function bpga_can_post_group_announcements() {
	$can_post = false;

	if ( bp_is_group() ) {
		if ( bp_current_user_can( 'bp_moderate' ) ||
		     bp_group_is_admin() ||
		     bp_group_is_mod()
		   )
		{
			$can_post = true;
		}
	}

	return apply_filters( 'bpga_can_post_group_announcements', $can_post );
}

