<?php
/*
Plugin Name: WordPress Varnish as a Service
Version: 1.3.0-Alpha1
Author: Javier Casares
Author URI: http://javiercasares.com/
Plugin URI: http://javiercasares.com/wp-varnish-aas/
Description: A plugin for purging Varnish cache when content is published or edited. It works with HTTP purge and Admin Port purge. Works with Varnish 2 (PURGE) and Varnish 3 (BAN) versions. Based on WordPress Varnish and Plugin Varnish Purges.
*/

//Lets go to define something. We want to know which plugin version it is, so here we say it.
	define('WORDPRESS_VARNISH_AS_A_SERVICE_VERSION', '1.3-Alpha1');


//If Plugin exist
function wordpress_varnish_as_a_service_activate() {
	if ( get_site_option("wpvarnish_addr_1") ) {
		/* OLDEST + DEPRECATED */
		delete_option("wpvarnish_update_pagenavi");
		delete_option("wpvarnish_update_commentnavi");
		/* SERVER OLD */
		delete_option("wpvarnish_addr");
		delete_option("wpvarnish_port");
		delete_option("wpvarnish_secret");
		delete_option("wpvarnish_timeout");
		delete_option("wpvarnish_use_adminport");
		delete_option("wpvarnish_use_version");
		delete_option("wpvarnish_server");
		/* SERVER 1 */
		update_site_option("wpvarnish_addr_1", get_option("wpvarnish_addr_1"));
		update_site_option("wpvarnish_port_1",get_option("wpvarnish_port_1"));
		update_site_option("wpvarnish_secret_1",get_option("wpvarnish_secret_1"));
		update_site_option("wpvarnish_timeout_1",get_option("wpvarnish_timeout_1"));
		update_site_option("wpvarnish_use_adminport_1",get_option("wpvarnish_use_adminport_1"));
		update_site_option("wpvarnish_use_version_1",get_option("wpvarnish_use_version_1"));
		update_site_option("wpvarnish_server_1",get_option("wpvarnish_server_1"));
		/* SERVER 2 */
		update_site_option("wpvarnish_addr_2",get_option("wpvarnish_addr_2"));
		update_site_option("wpvarnish_port_2",get_option("wpvarnish_port_2"));
		update_site_option("wpvarnish_secret_2",get_option("wpvarnish_secret_2"));
		update_site_option("wpvarnish_timeout_2",get_option("wpvarnish_timeout_2"));
		update_site_option("wpvarnish_use_adminport_2",get_option("wpvarnish_use_adminport_2"));
		update_site_option("wpvarnish_use_version_2",get_option("wpvarnish_use_version_2"));
		update_site_option("wpvarnish_server_2",get_option("wpvarnish_server_2"));
		/* SERVER 3 */
		update_site_option("wpvarnish_addr_3",get_option("wpvarnish_addr_3"));
		update_site_option("wpvarnish_port_3",get_option("wpvarnish_port_3"));
		update_site_option("wpvarnish_secret_3",get_option("wpvarnish_secret_3"));
		update_site_option("wpvarnish_timeout_3",get_option("wpvarnish_timeout_3"));
		update_site_option("wpvarnish_use_adminport_3",get_option("wpvarnish_use_adminport_3"));
		update_site_option("wpvarnish_use_version_3",get_option("wpvarnish_use_version_3"));
		update_site_option("wpvarnish_server_3",get_option("wpvarnish_server_3"));
	}
	
}
				
register_activation_hook( 'wordpress-varnish-as-a-service/wordpress-varnish-as-a-service.php', 'wordpress_varnish_as_a_service_activate' );

class WPVarnish {
	public $commenter;
	function WPVarnish() {
		global $post;

		$wpv_addr_optval_1 = "127.0.0.1";
		$wpv_port_optval_1 = "80";
		$wpv_secret_optval_1 = "";
		$wpv_timeout_optval_1 = 5;
		$wpv_use_adminport_optval_1 = 0;
		$wpv_use_version_optval_1 = 3;
		$wpv_server_optval_1 = 0;

		$wpv_addr_optval_2 = "127.0.0.1";
		$wpv_port_optval_2 = "80";
		$wpv_secret_optval_2 = "";
		$wpv_timeout_optval_2 = 5;
		$wpv_use_adminport_optval_2 = 0;
		$wpv_use_version_optval_2 = 3;
		$wpv_server_optval_2 = 0;

		$wpv_addr_optval_3 = "127.0.0.1";
		$wpv_port_optval_3 = "80";
		$wpv_secret_optval_3 = "";
		$wpv_timeout_optval_3 = 5;
		$wpv_use_adminport_optval_3 = 0;
		$wpv_use_version_optval_3 = 3;
		$wpv_server_optval_3 = 0;

		if(!get_site_option("wpvarnish_addr_1"))
			add_site_option("wpvarnish_addr_1", $wpv_addr_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_port_1"))
			add_site_option("wpvarnish_port_1", $wpv_port_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_secret_1"))
			add_site_option("wpvarnish_secret_1", $wpv_secret_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_timeout_1"))
			add_site_option("wpvarnish_timeout_1", $wpv_timeout_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_use_version_1"))
			add_site_option("wpvarnish_use_version_1", $wpv_use_version_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_use_adminport_1"))
			add_site_option("wpvarnish_use_adminport_1", $wpv_use_adminport_optval_1, '', 'yes');
		if(!get_site_option("wpvarnish_server_1"))
			add_site_option("wpvarnish_server_1", $wpv_server_optval_1, '', 'yes');

		if(!get_site_option("wpvarnish_addr_2"))
			add_site_option("wpvarnish_addr_2", $wpv_addr_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_port_2"))
			add_site_option("wpvarnish_port_2", $wpv_port_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_secret_2"))
			add_site_option("wpvarnish_secret_2", $wpv_secret_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_timeout_2"))
			add_site_option("wpvarnish_timeout_2", $wpv_timeout_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_use_version_2"))
			add_site_option("wpvarnish_use_version_2", $wpv_use_version_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_use_adminport_2"))
			add_site_option("wpvarnish_use_adminport_2", $wpv_use_adminport_optval_2, '', 'yes');
		if(!get_site_option("wpvarnish_server_2"))
			add_site_option("wpvarnish_server_2", $wpv_server_optval_2, '', 'yes');

		if(!get_site_option("wpvarnish_addr_3"))
			add_site_option("wpvarnish_addr_3", $wpv_addr_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_port_3"))
			add_site_option("wpvarnish_port_3", $wpv_port_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_secret_3"))
			add_site_option("wpvarnish_secret_3", $wpv_secret_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_timeout_3"))
			add_site_option("wpvarnish_timeout_3", $wpv_timeout_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_use_version_3"))
			add_site_option("wpvarnish_use_version_3", $wpv_use_version_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_use_adminport_3"))
			add_site_option("wpvarnish_use_adminport_3", $wpv_use_adminport_optval_3, '', 'yes');
		if(!get_site_option("wpvarnish_server_3"))
			add_site_option("wpvarnish_server_3", $wpv_server_optval_3, '', 'yes');
			
		// Plugin version, very important for future release
		
		if (!get_site_option("wpvarnish_version"))
			add_site_option("wpvarnish_version", WORDPRESS_VARNISH_AS_A_SERVICE_VERSION);
		
			
		

		add_action('init', array(&$this, 'WPVarnishLocalization'));
		if (!is_multisite()){
		add_action('admin_menu', array(&$this, 'WPVarnishAdminMenu')); // Here we Add the Varnish options page to WP Admin
		} else {
		add_action('network_admin_menu', array(&$this, 'WPVarnishNetworkAdminMenu'));  // Here we Add the Varnish options page to WP Network Setting page
		}
		add_action('edit_post', array(&$this, 'WPVarnishPurgePost'), 99);
		add_action('edit_post', array(&$this, 'WPVarnishPurgeCommonObjects'), 99);
		add_action('comment_post', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('edit_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('trashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('untrashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('deleted_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('deleted_post', array(&$this, 'WPVarnishPurgePost'), 99);
		add_action('deleted_post', array(&$this, 'WPVarnishPurgeCommonObjects'), 99);
		add_filter('wp_get_current_commenter', array(&$this, "wp_get_current_commenter_varnish"));
	}
	function wp_get_current_commenter_varnish($commenter) {
		if (get_query_var($this->query)) {
			return $commenter;
		} else {
			return array('comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '');
		}
	}
	function WPVarnishLocalization() {
		load_plugin_textdomain('wp-varnish-aas',false,dirname(plugin_basename(__FILE__)).'/lang/');
	}
	function WPVarnishPurgeCommonObjects() {
		$this->WPVarnishPurgeObject("/");
		$this->WPVarnishPurgeObject("(.*)/feed/(.*)");
		$this->WPVarnishPurgeObject("(.*)/trackback/(.*)");
		$this->WPVarnishPurgeObject("/page/(.*)");
	}
	function WPVarnishPurgeAll() {
		$this->WPVarnishPurgeObject("(.*)");
	}
	function WPVarnishPurgePost($wpv_postid) {
		$wpv_url = get_permalink($wpv_postid);
		$wpv_permalink = str_replace(get_bloginfo("wpurl"),"",$wpv_url);
		$this->WPVarnishPurgeObject($wpv_permalink);
		$this->WPVarnishPurgeObject($wpv_permalink."page/(.*)");
	}
	function WPVarnishPurgePostComments($wpv_commentid) {
		$comment = get_comment($wpv_commentid);
		$wpv_commentapproved = $comment->comment_approved;
		if ($wpv_commentapproved == 1 || $wpv_commentapproved == 'trash') {
			$wpv_postid = $comment->comment_post_ID;
			$this->WPVarnishPurgeObject('comments_popup='.$wpv_postid);
		}
	}
	function WPVarnishAdminMenu() {
		add_options_page(__('Varnish as a Service Configuration','wp-varnish-aas'), 'Varnish aaS', 1, 'WPVarnish', array(&$this, 'WPVarnishAdmin'));
	}
	function WPVarnishNetworkAdminMenu() {
		add_submenu_page('settings.php', __('Varnish as a Service Configuration','wp-varnish-aas'), 'Varnish aaS', 1, 'WPVarnish', array(&$this, 'WPVarnishAdmin'));
	}
	function WPVarnishAdmin() {
		if(current_user_can('administrator')) {
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				if(isset($_POST['wpvarnish_admin'])) {
					if(isset($_POST["wpvarnish_addr_1"]))
						update_site_option("wpvarnish_addr_1", trim(strip_tags($_POST["wpvarnish_addr_1"])));
					if(isset($_POST["wpvarnish_port_1"]))
						update_site_option("wpvarnish_port_1", (int)trim(strip_tags($_POST["wpvarnish_port_1"])));
					if(isset($_POST["wpvarnish_secret_1"]))
						update_site_option("wpvarnish_secret_1", trim(strip_tags($_POST["wpvarnish_secret_1"])));
					if(isset($_POST["wpvarnish_timeout_1"]))
						update_site_option("wpvarnish_timeout_1", (int)trim(strip_tags($_POST["wpvarnish_timeout_1"])));
					if(isset($_POST["wpvarnish_use_adminport_1"]))
						update_site_option("wpvarnish_use_adminport_1", 1);
					else
						update_site_option("wpvarnish_use_adminport_1", 0);
					if(isset($_POST["wpvarnish_use_version_1"]))
						update_site_option("wpvarnish_use_version_1", $_POST["wpvarnish_use_version_1"]);
					if(isset($_POST["wpvarnish_server_1"]))
						update_site_option("wpvarnish_server_1", 1);
					else
						update_site_option("wpvarnish_server_1", 0);
					if(isset($_POST["wpvarnish_addr_2"]))
						update_site_option("wpvarnish_addr_2", trim(strip_tags($_POST["wpvarnish_addr_2"])));
					if(isset($_POST["wpvarnish_port_2"]))
						update_site_option("wpvarnish_port_2", (int)trim(strip_tags($_POST["wpvarnish_port_2"])));
					if(isset($_POST["wpvarnish_secret_2"]))
						update_site_option("wpvarnish_secret_2", trim(strip_tags($_POST["wpvarnish_secret_2"])));
					if(isset($_POST["wpvarnish_timeout_2"]))
						update_site_option("wpvarnish_timeout_2", (int)trim(strip_tags($_POST["wpvarnish_timeout_2"])));
					if(isset($_POST["wpvarnish_use_adminport_2"]))
						update_site_option("wpvarnish_use_adminport_2", 1);
					else
						update_site_option("wpvarnish_use_adminport_2", 0);
					if(isset($_POST["wpvarnish_use_version_2"]))
						update_site_option("wpvarnish_use_version_2", $_POST["wpvarnish_use_version_2"]);
					if(isset($_POST["wpvarnish_server_2"]))
						update_site_option("wpvarnish_server_2", 1);
					else
						update_site_option("wpvarnish_server_2", 0);
					if(isset($_POST["wpvarnish_addr_3"]))
						update_site_option("wpvarnish_addr_3", trim(strip_tags($_POST["wpvarnish_addr_3"])));
					if(isset($_POST["wpvarnish_port_3"]))
						update_site_option("wpvarnish_port_3", (int)trim(strip_tags($_POST["wpvarnish_port_3"])));
					if(isset($_POST["wpvarnish_secret_3"]))
						update_site_option("wpvarnish_secret_3", trim(strip_tags($_POST["wpvarnish_secret_3"])));
					if(isset($_POST["wpvarnish_timeout_3"]))
						update_site_option("wpvarnish_timeout_3", (int)trim(strip_tags($_POST["wpvarnish_timeout_3"])));
					if(isset($_POST["wpvarnish_use_adminport_3"]))
						update_site_option("wpvarnish_use_adminport_3", 1);
					else
						update_site_option("wpvarnish_use_adminport_3", 0);
					if(isset($_POST["wpvarnish_use_version_3"]))
						update_site_option("wpvarnish_use_version_3", $_POST["wpvarnish_use_version_3"]);
					if(isset($_POST["wpvarnish_server_3"]))
						update_site_option("wpvarnish_server_3", 1);
					else
						update_site_option("wpvarnish_server_3", 0);
?>
	<div class="updated"><p><?php echo __('Settings Saved!','wp-varnish-aas'); ?></p></div>
<?php
				}
				if(isset($_POST['wpvarnish_clear_blog_cache'])) {
?>
	<div class="updated"><p><?php echo __('Purging Everything!','wp-varnish-aas'); ?></p></div>
<?php
					$this->WPVarnishPurgeAll();
				}
				if (isset($_POST['wpvarnish_test_blog_cache_1'])) {
?>
	<div class="updated"><p><?php echo __('Testing Connection to Varnish Server','wp-varnish-aas'); ?> 1</p></div>
<?php
					$this->WPVarnishTestConnect(1);
				}
				if (isset($_POST['wpvarnish_test_blog_cache_2'])) {
?>
	<div class="updated"><p><?php echo __('Testing Connection to Varnish Server','wp-varnish-aas'); ?> 2</p></div>
<?php
					$this->WPVarnishTestConnect(2);
				}
				if (isset($_POST['wpvarnish_test_blog_cache_3'])) {
?>
	<div class="updated"><p><?php echo __('Testing Connection to Varnish Server','wp-varnish-aas'); ?> 3</p></div>
<?php
					$this->WPVarnishTestConnect(3);
				}
			}
?>
	<div class="wrap">
		<h2><?php echo __("Varnish as a Service Administration",'wp-varnish-aas'); ?></h2>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<table width="100%">
			<tr valign="top">
				<td>
					<dl>
						<dt><label for="varactive1"><?php echo __("Server Activated",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varactive1" type="checkbox" name="wpvarnish_server_1" value="1"<?php if(get_site_option("wpvarnish_server_1") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varipaddress1"><?php echo __("Server IP Address",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varipaddress1" type="text" name="wpvarnish_addr_1" value="<?php echo get_site_option("wpvarnish_addr_1"); ?>" style="width: 120px;"></dd>
						<dt><label for="varport1"><?php echo __("Server Port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varport1" type="text" name="wpvarnish_port_1" value="<?php echo get_site_option("wpvarnish_port_1"); ?>" style="width: 50px;"></dd>
						<dt><label for="varuseadmin1"><?php echo __("Use Admin port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varuseadmin1" type="checkbox" name="wpvarnish_use_adminport_1" value="1"<?php if(get_site_option("wpvarnish_use_adminport_1") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varsecret1"><?php echo __("Secret Key",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varsecret1" type="text" name="wpvarnish_secret_1" value="<?php echo get_site_option("wpvarnish_secret_1"); ?>" style="width: 260px;"></dd>
						<dt><label for="varversion1"><?php echo __("Version",'wp-varnish-aas'); ?></label></dt>
						<dd><select id="varversion1" name="wpvarnish_use_version_1">
							<option value="2"<?php if(get_site_option("wpvarnish_use_version_1") == 2) echo " selected"; ?>>v2 - PURGE</option>
							<option value="3"<?php if(get_site_option("wpvarnish_use_version_1") == 3) echo " selected"; ?>>v3 - BAN</option>
						</select></dd>
						<dt><label for="vartimeout1"><?php echo __("Timeout",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="vartimeout1" class="small-text" type="text" name="wpvarnish_timeout_1" value="<?php echo get_site_option("wpvarnish_timeout_1"); ?>"> <?php echo __("seconds",'wp-varnish-aas'); ?></dd>
						<dt><?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?></dt>
						<dd><input type="submit" class="button-secondary" name="wpvarnish_test_blog_cache_1" value="<?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?>"></dd>
					</dl>
				</td>
				<td>
					<dl>
						<dt><label for="varactive2"><?php echo __("Server Activated",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varactive2" type="checkbox" name="wpvarnish_server_2" value="1"<?php if(get_site_option("wpvarnish_server_2") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varipaddress2"><?php echo __("Server IP Address",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varipaddress2" type="text" name="wpvarnish_addr_2" value="<?php echo get_site_option("wpvarnish_addr_2"); ?>" style="width: 120px;"></dd>
						<dt><label for="varport2"><?php echo __("Server Port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varport2" type="text" name="wpvarnish_port_2" value="<?php echo get_site_option("wpvarnish_port_2"); ?>" style="width: 50px;"></dd>
						<dt><label for="varuseadmin2"><?php echo __("Use Admin port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varuseadmin2" type="checkbox" name="wpvarnish_use_adminport_2" value="1"<?php if(get_site_option("wpvarnish_use_adminport_2") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varsecret2"><?php echo __("Secret Key",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varsecret2" type="text" name="wpvarnish_secret_2" value="<?php echo get_site_option("wpvarnish_secret_2"); ?>" style="width: 260px;"></dd>
						<dt><label for="varversion2"><?php echo __("Version",'wp-varnish-aas'); ?></label></dt>
						<dd><select id="varversion2" name="wpvarnish_use_version_2">
							<option value="2"<?php if(get_site_option("wpvarnish_use_version_2") == 2) echo " selected"; ?>>v2 - PURGE</option>
							<option value="3"<?php if(get_site_option("wpvarnish_use_version_2") == 3) echo " selected"; ?>>v3 - BAN</option>
						</select></dd>
						<dt><label for="vartimeout2"><?php echo __("Timeout",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="vartimeout2" class="small-text" type="text" name="wpvarnish_timeout_2" value="<?php echo get_site_option("wpvarnish_timeout_2"); ?>"> <?php echo __("seconds",'wp-varnish-aas'); ?></dd>
						<dt><?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?></dt>
						<dd><input type="submit" class="button-secondary" name="wpvarnish_test_blog_cache_2" value="<?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?>"></dd>
					</dl>
				</td>
				<td>
					<dl>
						<dt><label for="varactive3"><?php echo __("Server Activated",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varactive3" type="checkbox" name="wpvarnish_server_3" value="1"<?php if(get_site_option("wpvarnish_server_3") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varipaddress3"><?php echo __("Server IP Address",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varipaddress3" type="text" name="wpvarnish_addr_3" value="<?php echo get_site_option("wpvarnish_addr_3"); ?>" style="width: 120px;"></dd>
						<dt><label for="varport3"><?php echo __("Server Port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varport3" type="text" name="wpvarnish_port_3" value="<?php echo get_site_option("wpvarnish_port_3"); ?>" style="width: 50px;"></dd>
						<dt><label for="varuseadmin3"><?php echo __("Use Admin port",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varuseadmin3" type="checkbox" name="wpvarnish_use_adminport_3" value="1"<?php if(get_site_option("wpvarnish_use_adminport_3") == 1) echo ' checked'; ?>></dd>
						<dt><label for="varsecret3"><?php echo __("Secret Key",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="varsecret3" type="text" name="wpvarnish_secret_3" value="<?php echo get_site_option("wpvarnish_secret_3"); ?>" style="width: 260px;"></dd>
						<dt><label for="varversion3"><?php echo __("Version",'wp-varnish-aas'); ?></label></dt>
						<dd><select id="varversion3" name="wpvarnish_use_version_3">
							<option value="2"<?php if(get_site_option("wpvarnish_use_version_3") == 2) echo " selected"; ?>>v2 - PURGE</option>
							<option value="3"<?php if(get_site_option("wpvarnish_use_version_3") == 3) echo " selected"; ?>>v3 - BAN</option>
						</select></dd>
						<dt><label for="vartimeout3"><?php echo __("Timeout",'wp-varnish-aas'); ?></label></dt>
						<dd><input id="vartimeout3" class="small-text" type="text" name="wpvarnish_timeout_3" value="<?php echo get_site_option("wpvarnish_timeout_3"); ?>"> <?php echo __("seconds",'wp-varnish-aas'); ?></dd>
						<dt><?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?></dt>
						<dd><input type="submit" class="button-secondary" name="wpvarnish_test_blog_cache_3" value="<?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?>"></dd>
					</dl>
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" class="button-primary" name="wpvarnish_admin" value="<?php echo __("Save Changes",'wp-varnish-aas'); ?>"> <input type="submit" class="button-secondary" name="wpvarnish_clear_blog_cache" value="<?php echo __("Purge All Blog Cache",'wp-varnish-aas'); ?>"></p>
		</form>
	</div>
<?php
		}
	}
	function WPAuth($challenge, $secret) {
		$ctx = hash_init('sha256');
		hash_update($ctx, $challenge);
		hash_update($ctx, "\n");
		hash_update($ctx, $secret."\n");
		hash_update($ctx, $challenge);
		hash_update($ctx, "\n");
		$sha256 = hash_final($ctx);
		return $sha256;
	}
	function WPVarnishPurgeObject($wpv_url) {
		global $varnish_servers;
		$j=0;
		if(get_site_option("wpvarnish_server")) {
			$array_wpv_purgeaddr[$j] = get_site_option("wpvarnish_addr_1");
			$array_wpv_purgeport[$j] = get_site_option("wpvarnish_port_1");
			$array_wpv_secret[$j] = get_site_option("wpvarnish_secret_1");
			$array_wpv_timeout[$j] = get_site_option("wpvarnish_timeout_1");
			$array_wpv_use_adminport[$j] = get_site_option("wpvarnish_use_adminport_1");
			$array_wpv_use_version[$j] = get_site_option("wpvarnish_use_version_1");
			$j++;
		}
		if(get_site_option("wpvarnish_server_2")) {
			$array_wpv_purgeaddr[$j] = get_site_option("wpvarnish_addr_2");
			$array_wpv_purgeport[$j] = get_site_option("wpvarnish_port_2");
			$array_wpv_secret[$j] = get_site_option("wpvarnish_secret_2");
			$array_wpv_timeout[$j] = get_site_option("wpvarnish_timeout_2");
			$array_wpv_use_adminport[$j] = get_site_option("wpvarnish_use_adminport_2");
			$array_wpv_use_version[$j] = get_site_option("wpvarnish_use_version_2");
			$j++;
		}
		if(get_site_option("wpvarnish_server_3")) {
			$array_wpv_purgeaddr[$j] = get_site_option("wpvarnish_addr_3");
			$array_wpv_purgeport[$j] = get_site_option("wpvarnish_port_3");
			$array_wpv_secret[$j] = get_site_option("wpvarnish_secret_3");
			$array_wpv_timeout[$j] = get_site_option("wpvarnish_timeout_3");
			$array_wpv_use_adminport[$j] = get_site_option("wpvarnish_use_adminport_3");
			$array_wpv_use_version[$j] = get_site_option("wpvarnish_use_version_3");
			$j++;
		}
		for($i=0; $i<$j; $i++) {
			$wpv_purgeaddr = $array_wpv_purgeaddr[$i];
			$wpv_purgeport = $array_wpv_purgeport[$i];
			$wpv_secret = $array_wpv_secret[$i];
			$wpv_timeout = $array_wpv_timeout[$i];
			$wpv_use_adminport = $array_wpv_use_adminport[$i];
			$wpv_use_version = $array_wpv_use_version[$i];
			$wpv_wpurl = get_bloginfo('wpurl');
			$wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
			$wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
			$wpv_blogaddr = preg_replace($wpv_replace_wpurl, "$2", $wpv_wpurl);
			$wpv_url = $wpv_blogaddr.$wpv_url;
			$varnish_sock = fsockopen($wpv_purgeaddr, $wpv_purgeport, $errno, $errstr, $wpv_timeout);
			if($varnish_sock) {
				if($wpv_use_adminport) {
					$buf = fread($varnish_sock, 1024);
					if(preg_match('/(\w+)\s+Authentication required./', $buf, $matches)) {
						$auth = $this->WPAuth($matches[1], $wpv_secret);
						fwrite($varnish_sock, "auth ".$auth."\n");
						$buf = fread($varnish_sock, 1024);
						if(preg_match('/^200/', $buf)) {
							if ($wpv_use_version == 2) {
								$out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
							} elseif ($wpv_use_version == 3) {
								$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
							} else {
								$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
							}
							fwrite($varnish_sock, $out."\n");
						}
					} else {
						if ($wpv_use_version == 2) {
							$out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						} elseif ($wpv_use_version == 3) {
							$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						} else {
							$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						}
						fwrite($varnish_sock, $out."\n");
					}
				} else {
					$out = "PURGE $wpv_url HTTP/1.0\r\n";
					$out .= "Host: $wpv_host\r\n";
					$out .= "Connection: Close\r\n\r\n";
					fwrite($varnish_sock, $out);
				}
				fclose($varnish_sock);
			}
		}
	}
	function WPVarnishTestConnect($servernum) {
		global $varnish_servers;
		$varnish_test_conn = "";
		if($servernum == 1) {
			$wpv_purgeaddr = get_site_option("wpvarnish_addr_1");
			$wpv_purgeport = get_site_option("wpvarnish_port_1");
			$wpv_secret = get_site_option("wpvarnish_secret_1");
			$wpv_timeout = get_site_option("wpvarnish_timeout_1");
			$wpv_use_adminport = get_site_option("wpvarnish_use_adminport_1");
			$wpv_use_version = get_site_option("wpvarnish_use_version_1");
		} elseif($servernum == 2) {
			$wpv_purgeaddr = get_site_option("wpvarnish_addr_2");
			$wpv_purgeport = get_site_option("wpvarnish_port_2");
			$wpv_secret = get_site_option("wpvarnish_secret_2");
			$wpv_timeout = get_site_option("wpvarnish_timeout_2");
			$wpv_use_adminport = get_site_option("wpvarnish_use_adminport_2");
			$wpv_use_version = get_site_option("wpvarnish_use_version_2");
		} elseif($servernum == 3) {
			$wpv_purgeaddr = get_site_option("wpvarnish_addr_3");
			$wpv_purgeport = get_site_option("wpvarnish_port_3");
			$wpv_secret = get_site_option("wpvarnish_secret_3");
			$wpv_timeout = get_site_option("wpvarnish_timeout_3");
			$wpv_use_adminport = get_site_option("wpvarnish_use_adminport_3");
			$wpv_use_version = get_site_option("wpvarnish_use_version_3");
		}
		$wpv_wpurl = get_bloginfo("wpurl");
		$wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
		$wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
		$wpv_url = $wpv_blogaddr."/";
		$varnish_test_conn .= "<ul>\n";
		$varnish_test_conn .= "<li><span style=\"color: blue;\">".__("INFO - Testing Server",'wp-varnish-aas')." ".$servernum."</span></li>\n";
		$varnish_sock = fsockopen($wpv_purgeaddr, $wpv_purgeport, $errno, $errstr, $wpv_timeout);
		if($varnish_sock) {
			$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Connection to Server",'wp-varnish-aas')."</span></li>\n";
			if ($wpv_use_adminport) {
				$varnish_test_conn .= "<li><span style=\"color: blue;\">".__("INFO - Using Admin Port",'wp-varnish-aas')."</span></li>\n";
				$buf = fread($varnish_sock, 1024);
				if(preg_match('/(\w+)\s+Authentication required./', $buf, $matches)) {
					$auth = $this->WPAuth($matches[1], $wpv_secret);
					fwrite($varnish_sock, "auth ".$auth."\n");
					$buf = fread($varnish_sock, 1024);
					if(preg_match('/^200/', $buf)) {
						$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Authentication",'wp-varnish-aas')."</span></li>\n";
						if ($wpv_use_version == 2) {
							$out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						} elseif ($wpv_use_version == 3) {
							$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						} else {
							$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
						}
						fwrite($varnish_sock, $out."\n");
						$buf = fread($varnish_sock, 256);
						if(preg_match('/^200/', $buf)) {
							$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Cache flush",'wp-varnish-aas')."</span></li>\n";
						} else {
							$varnish_test_conn .= "<li><span style=\"color: red;\">".__("KO - Cache flush",'wp-varnish-aas')."</span><br><small>".__("Verify your Varnish version",'wp-varnish-aas')."</small></li>\n";
						}
					} else {
						$varnish_test_conn .= "<li><span style=\"color: red;\">".__("KO - Invalid Secret Key",'wp-varnish-aas')."</span></li>\n";
					}
				} else {
					$varnish_test_conn .= "<li><span style=\"color: blue;\">".__("INFO - Authentication not required",'wp-varnish-aas')."</span></li>\n";
					if ($wpv_use_version == 2) {
						$out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
					} elseif ($wpv_use_version == 3) {
						$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
					} else {
						$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
					}
					fwrite($varnish_sock, $out."\n");
					$buf = fread($varnish_sock, 256);
					if(preg_match('/^200/', $buf)) {
						$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Cache flush",'wp-varnish-aas')."</span></li>\n";
					} else {
						$varnish_test_conn .= "<li><span style=\"color: red;\">".__("KO - Cache flush",'wp-varnish-aas')."</span><br><small>".__("Verify your Varnish version",'wp-varnish-aas')."</small></li>\n";
					}
				}
			} else {
				$varnish_test_conn .= "<li><span style=\"color: blue;\">".__("INFO - HTTP Purge",'wp-varnish-aas')."</span></li>\n";
				$out = "PURGE $wpv_url HTTP/1.0\r\n";
				$out .= "Host: $wpv_host\r\n";
				$out .= "Connection: Close\r\n\r\n";
				fwrite($varnish_sock, $out);
				$buf = fread($varnish_sock, 256);
				if(preg_match('/200 OK/', $buf)) {
					$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Request",'wp-varnish-aas')."</span></li>\n";
				} else {
					$varnish_test_conn .= "<li><span style=\"color: red;\">".__("KO - Request",'wp-varnish-aas')."</span></li>\n";
				}
			}
			fclose($varnish_sock);
		} else {
			$varnish_test_conn .= "<li><span style=\"color: red;\">".__("KO - Connection to Server",'wp-varnish-aas')."</span><br><small>".__("IP address or port closed. Verify your firewall or iptables.",'wp-varnish-aas')."</small></li>\n";
		}
		$varnish_test_conn .= "</ul>\n";
?>
	<div class="updated"><?php echo $varnish_test_conn; ?></div>
<?php
	}
}
$wpvarnish = & new WPVarnish();
?>