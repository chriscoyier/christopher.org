<?php
/*
Plugin Name: WP-ContactForm
Plugin URI: http://blog.ftwr.co.uk/wordpress/
Description: WP Contact Form is a drop in form for users to contact you. It can be implemented on a page or a post.
Author: Ryan Duff, Peter Westwood
Author URI: http://blog.ftwr.co.uk
Version: 1.6
*/

load_plugin_textdomain('wpcf', false, dirname( plugin_basename( __FILE__ ) ) );


if ( ! function_exists( 'esc_textarea' ) ) {
	/**
	 * Escaping for textarea values.
	 *
	 * @since 3.1
	 *
	 * @param string $text
	 * @return string
	 */
	function esc_textarea( $text ) {
		$safe_text = htmlspecialchars( $text, ENT_QUOTES );
		return apply_filters( 'esc_textarea', $safe_text, $text );
	}
}

/*
This shows the quicktag on the write pages
Based off Buttonsnap Template
http://redalt.com/downloads
*/
if(get_option('wpcf_show_quicktag') == true) {
	include( plugin_dir_path(__FILE__) . 'buttonsnap.php');

	add_action('init', 'wpcf_button_init');
	add_action('marker_css', 'wpcf_marker_css');

	function wpcf_button_init() {
		$wpcf_button_url = buttonsnap_dirname(__FILE__) . '/wpcf_button.png';

		buttonsnap_textbutton($wpcf_button_url, __('Insert Contact Form', 'wpcf'), '<!--contact form-->');
		buttonsnap_register_marker('contact form', 'wpcf_marker');
	}

	function wpcf_marker_css() {
		$wpcf_marker_url = buttonsnap_dirname(__FILE__) . '/wpcf_marker.gif';
		echo "
			.wpcf_marker {
					display: block;
					height: 15px;
					width: 155px
					margin-top: 5px;
					background-image: url({$wpcf_marker_url});
					background-repeat: no-repeat;
					background-position: center;
			}
		";
	}
}

function wpcf_is_malicious($input) {
	$is_malicious = false;
	$bad_inputs = array("\r", "\n", "mime-version", "content-type", "cc:", "to:");
	foreach($bad_inputs as $bad_input) {
		if(strpos(strtolower($input), strtolower($bad_input)) !== false) {
			$is_malicious = true; break;
		}
	}
	return $is_malicious;
}

/* This function checks for errors on input and changes $wpcf_strings if there are any errors. Shortcircuits if there has not been a submission */
function wpcf_check_input() {
	if(!(isset($_POST['wpcf_stage']))) {return false;} // Shortcircuit.

	$_POST['wpcf_your_name'] = (stripslashes(trim($_POST['wpcf_your_name'])));
	$_POST['wpcf_email'] = (stripslashes(trim($_POST['wpcf_email'])));
	$_POST['wpcf_website'] = (stripslashes(trim($_POST['wpcf_website'])));
	$_POST['wpcf_msg'] = (stripslashes(trim($_POST['wpcf_msg'])));

	global $wpcf_strings;
	$ok = true;

	if(empty($_POST['wpcf_your_name']))
	{
		$ok = false; $reason = 'empty';
		$wpcf_strings['name'] = '<div class="contactright"><input type="text" name="wpcf_your_name" id="wpcf_your_name" size="30" maxlength="50" value="" class="contacterror" /> (' . __('required', 'wpcf') . ')</div>';
	}

	if(!is_email($_POST['wpcf_email']))
	{
		$ok = false; $reason = 'empty';
		$wpcf_strings['email'] = '<div class="contactright"><input type="text" name="wpcf_email" id="wpcf_email" size="30" maxlength="50" value="" class="contacterror" /> (' . __('required', 'wpcf') . ')</div>';
	}

	if(empty($_POST['wpcf_msg']))
	{
		$ok = false; $reason = 'empty';
		$wpcf_strings['msg'] = '<div class="contactright"><textarea name="wpcf_msg" id="wpcf_message" cols="35" rows="8" class="contacterror"></textarea></div>';
	}

	if(wpcf_is_malicious($_POST['wpcf_your_name']) || wpcf_is_malicious($_POST['wpcf_email'])) {
		$ok = false; $reason = 'malicious';
	}

	if($ok == true)
	{
		return true;
	}
	else {
		if($reason == 'malicious') {
			$wpcf_strings['error'] = "<div style='font-weight: bold;'>You can not use any of the following in the Name or Email fields: a linebreak, or the phrases 'mime-version', 'content-type', 'cc:' or 'to:'.</div>";
		} elseif($reason == 'empty') {
			$wpcf_strings['error'] = '<div style="font-weight: bold;">' . stripslashes(get_option('wpcf_error_msg')) . '</div>';
		}
		return false;
	}
}

/*Wrapper function which calls the form.*/
function wpcf_callback( $content ) {
	global $wpcf_strings;

	/* Run the input check. */
	if(false === strpos($content, '<!--contact form-->')) {
		return $content;
	}

	$_POST['wpcf_your_name'] = (stripslashes(trim($_POST['wpcf_your_name'])));
	$_POST['wpcf_email'] = (stripslashes(trim($_POST['wpcf_email'])));
	$_POST['wpcf_website'] = (stripslashes(trim($_POST['wpcf_website'])));
	$_POST['wpcf_msg'] = (stripslashes(trim($_POST['wpcf_msg'])));

	/* Declare strings that change depending on input. This also resets them so errors clear on resubmission. */
	$wpcf_strings = array(
		'name' => '<div class="contactright"><input type="text" name="wpcf_your_name" id="wpcf_your_name" size="30" maxlength="50" value="' . (isset($_POST['wpcf_your_name']) ? esc_attr( $_POST['wpcf_your_name'] ) :'') . '" /> (' . __('required', 'wpcf') . ')</div>',
		'email' => '<div class="contactright"><input type="text" name="wpcf_email" id="wpcf_email" size="30" maxlength="50" value="' . (isset($_POST['wpcf_email']) ? esc_attr( $_POST['wpcf_email'] ) : '') . '" /> (' . __('required', 'wpcf') . ')</div>',
		'msg' => '<div class="contactright"><textarea name="wpcf_msg" id="wpcf_msg" cols="35" rows="8" >' . (isset($_POST['wpcf_msg']) ? esc_textarea( $_POST['wpcf_msg'] ) : '' ) . '</textarea></div>',
		'error' => '');

	if(wpcf_check_input()) // If the input check returns true (ie. there has been a submission & input is ok)
	{
		$recipient = get_option('wpcf_email');
		$subject = get_option('wpcf_subject');
		$success_msg = get_option('wpcf_success_msg');
		$success_msg = stripslashes($success_msg);

		$name = $_POST['wpcf_your_name'];
		$email = $_POST['wpcf_email'];
		$website = $_POST['wpcf_website'];
		$msg = $_POST['wpcf_msg'];

		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $name <$email>\n";
		$headers .= "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

		$fullmsg = "$name wrote:\n";
		$fullmsg .= wordwrap($msg, 80, "\n") . "\n\n";
		$fullmsg .= "Website: " . $website . "\n";
		$fullmsg .= "IP: " . getip();

		wp_mail($recipient, $subject, $fullmsg, $headers);

		$results = '<div style="font-weight: bold;">' . $success_msg . '</div>';
		echo $results;
	}
	else // Else show the form. If there are errors the strings will have updated during running the inputcheck.
	{
		$form = '<div class="contactform">
        ' . $wpcf_strings['error'] . '
        	<form action="' . get_permalink() . '" method="post">
        		<div class="contactleft"><label for="wpcf_your_name">' . __('Your Name: ', 'wpcf') . '</label></div>' . $wpcf_strings['name']  . '
        		<div class="contactleft"><label for="wpcf_email">' . __('Your Email:', 'wpcf') . '</label></div>' . $wpcf_strings['email'] . '
        		<div class="contactleft"><label for="wpcf_website">' . __('Your Website:', 'wpcf') . '</label></div><div class="contactright"><input type="text" name="wpcf_website" id="wpcf_website" size="30" maxlength="100" value="' . (isset($_POST['wpcf_website']) ? esc_attr( $_POST['wpcf_website'] ) : '') . '" /></div>
            	<div class="contactleft"><label for="wpcf_msg">' . __('Your Message: ', 'wpcf') . '</label></div>' . $wpcf_strings['msg'] . '
            	<div class="contactright"><input type="submit" name="Submit" value="' . __('Submit', 'wpcf') . '" id="contactsubmit" /><input type="hidden" name="wpcf_stage" value="process" /></div>
        	</form>
        </div>
        <div style="clear:both; height:1px;">&nbsp;</div>';
		return str_replace('<!--contact form-->', $form, $content);
	}
}

function wpcf_adminpage() {
	if ( !current_user_can( 'manage_options') )
		wp_die( __('You do not have sufficient permissions to access this page.') );

	$location = get_option('siteurl') . '/wp-admin/admin.php?page=wp-contact-form/options-contactform.php'; // Form Action URI
	$location = menu_page_url('wpcf_adminpage',false);
	/*Lets add some default options if they don't exist*/
	add_option('wpcf_email', __('you@example.com', 'wpcf'));
	add_option('wpcf_subject', __('Contact Form Results', 'wpcf'));
	add_option('wpcf_success_msg', __('Thanks for your comments!', 'wpcf'));
	add_option('wpcf_error_msg', __('Please fill in the required fields.', 'wpcf'));
	add_option('wpcf_show_quicktag', TRUE);

	/*check form submission and update options*/
	if (isset ($_POST['stage']) && ( 'process' == $_POST['stage']) )
	{
		update_option('wpcf_email', $_POST['wpcf_email']);
		update_option('wpcf_subject', $_POST['wpcf_subject']);
		update_option('wpcf_success_msg', $_POST['wpcf_success_msg']);
		update_option('wpcf_error_msg', $_POST['wpcf_error_msg']);

		if(isset($_POST['wpcf_show_quicktag'])) // If wpcf_show_quicktag is checked
		{update_option('wpcf_show_quicktag', true);}
		else {update_option('wpcf_show_quicktag', false);}

	}

	/*Get options for form fields*/
	$wpcf_email = stripslashes(get_option('wpcf_email'));
	$wpcf_subject = stripslashes(get_option('wpcf_subject'));
	$wpcf_success_msg = stripslashes(get_option('wpcf_success_msg'));
	$wpcf_error_msg = stripslashes(get_option('wpcf_error_msg'));
	$wpcf_show_quicktag = get_option('wpcf_show_quicktag');
	?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Contact Form Options', 'wpcf') ?></h2>
<form name="form1" method="post"
	action="<?php echo $location ?>&amp;updated=true"><input
	type="hidden" name="stage" value="process" />
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr valign="top">
		<th scope="row"><?php _e('E-mail Address:', 'wpcf') ?></th>
		<td><input name="wpcf_email" type="text" id="wpcf_email"
			value="<?php echo $wpcf_email; ?>" size="40" /> <br />
		<?php _e('This address is where the email will be sent to.', 'wpcf') ?></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Subject:', 'wpcf') ?></th>
		<td><input name="wpcf_subject" type="text" id="wpcf_subject"
			value="<?php echo $wpcf_subject; ?>" size="50" /> <br />
		<?php _e('This will be the subject of the email.', 'wpcf') ?></td>
	</tr>
</table>

<fieldset class="options"><legend><?php _e('Messages', 'wpcf') ?></legend>
<table width="100%" cellspacing="2" cellpadding="5" class="editform">
	<tr valign="top">
		<th scope="row"><?php _e('Success Message:', 'wpcf') ?></th>
		<td><textarea name="wpcf_success_msg" id="wpcf_success_msg"
			style="width: 80%;" rows="4" cols="50"><?php echo $wpcf_success_msg; ?></textarea>
		<br />
		<?php _e('When the form is sucessfully submitted, this is the message the user will see.', 'wpcf') ?></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Error Message:', 'wpcf') ?></th>
		<td><textarea name="wpcf_error_msg" id="wpcf_error_msg"
			style="width: 80%;" rows="4" cols="50"><?php echo $wpcf_error_msg; ?></textarea>
		<br />
		<?php _e('If the user skips a required field, this is the message he will see.', 'wpcf') ?>
		<br />
		<?php _e('You can apply CSS to this text by wrapping it in <code>&lt;p style="[your CSS here]"&gt; &lt;/p&gt;</code>.', 'wpcf') ?><br />
		<?php _e('ie. <code>&lt;p style="color:red;"&gt;Please fill in the required fields.&lt;/p&gt;</code>.', 'wpcf') ?></td>
	</tr>
</table>

</fieldset>

<fieldset class="options"><legend><?php _e('Advanced', 'wpcf') ?></legend>

<table width="100%" cellpadding="5" class="editform">
	<tr valign="top">
		<th width="30%" scope="row" style="text-align: left"><?php _e('Show \'Contact Form\' Quicktag', 'wpcf') ?></th>
		<td><input name="wpcf_show_quicktag" type="checkbox"
			id="wpcf_show_quicktag" value="wpcf_show_quicktag"
			<?php if($wpcf_show_quicktag == TRUE) {?> checked="checked"
			<?php } ?> /></td>
	</tr>
</table>

</fieldset>

<p class="submit"><input type="submit" name="Submit"
	value="<?php _e('Update Options', 'wpcf') ?> &raquo;" /></p>
</form>
</div>
<?php
}

/*Can't use WP's function here, so lets use our own*/
function getip() {
	if (isset($_SERVER))
	{
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))
		{
			$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
		}
		else
		{
			$ip_addr = $_SERVER["REMOTE_ADDR"];
		}
	}
	else
	{
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
		{
			$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
		}
		elseif ( getenv( 'HTTP_CLIENT_IP' ) )
		{
			$ip_addr = getenv( 'HTTP_CLIENT_IP' );
		}
		else
		{
			$ip_addr = getenv( 'REMOTE_ADDR' );
		}
	}
	return $ip_addr;
}

function wpcf_add_options_page() {
	add_options_page(__('Contact Form Options', 'wpcf'), __('Contact Form', 'wpcf'), 'manage_options', 'wpcf_adminpage', 'wpcf_adminpage');
}

function wpcf_enqueue_css() {
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
	wp_enqueue_style( 'wpcf', plugins_url( "wpcf$suffix.css", __FILE__ ), array(), '20110218' );
}

/* Action calls for all functions */

//if(get_option('wpcf_show_quicktag') == true) {add_action('admin_footer', 'wpcf_add_quicktag');}

add_action('admin_menu', 'wpcf_add_options_page');
add_action('wp_enqueue_scripts', 'wpcf_enqueue_css');
add_filter('the_content', 'wpcf_callback', 7);

?>
