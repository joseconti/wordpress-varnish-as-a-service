<?php
/*
Plugin Name: WordPress Varnish as a Service
Version: 1.0.1
Author: Javier Casares
Author URI: http://javiercasares.com/
Plugin URI: http://javiercasares.com/wp-varnish-aas/
Description: A plugin for purging Varnish cache when content is published or edited. It works with HTTP purge and Admin Port purge. Works with Varnish 2 (PURGE) and Varnish 3 (BAN) versions. Based on WordPress Varnish and Plugin Varnish Purges.
*/
class WPVarnish {
	public $wpv_addr_optname;
	public $wpv_port_optname;
	public $wpv_secret_optname;
	public $wpv_timeout_optname;
	public $wpv_use_adminport_optname;
	public $wpv_use_version_optname;
	public $wpv_update_pagenavi_optname;
	public $wpv_update_commentnavi_optname;
	public $commenter;
	function WPVarnish() {
		global $post;
		$this->wpv_addr_optname = "wpvarnish_addr";
		$this->wpv_port_optname = "wpvarnish_port";
		$this->wpv_secret_optname = "wpvarnish_secret";
		$this->wpv_timeout_optname = "wpvarnish_timeout";
		$this->wpv_use_adminport_optname = "wpvarnish_use_adminport";
		$this->wpv_use_version_optname = "wpvarnish_use_version";
		$this->wpv_update_pagenavi_optname = "wpvarnish_update_pagenavi";
		$this->wpv_update_commentnavi_optname = "wpvarnish_update_commentnavi";
		$wpv_addr_optval = array ("127.0.0.1");
		$wpv_port_optval = array (80);
		$wpv_secret_optval = array ("");
		$wpv_timeout_optval = 5;
		$wpv_use_adminport_optval = 0;
		$wpv_update_pagenavi_optval = 0;
		$wpv_update_commentnavi_optval = 0;
		if ( (get_option($this->wpv_addr_optname) == FALSE) ) {
			add_option($this->wpv_addr_optname, $wpv_addr_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_port_optname) == FALSE) ) {
			add_option($this->wpv_port_optname, $wpv_port_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_secret_optname) == FALSE) ) {
			add_option($this->wpv_secret_optname, $wpv_secret_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_timeout_optname) == FALSE) ) {
			add_option($this->wpv_timeout_optname, $wpv_timeout_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_use_version_optname) == FALSE) ) {
			add_option($this->wpv_use_version_optname, $wpv_use_version_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_update_pagenavi_optname) == FALSE) ) {
			add_option($this->wpv_update_pagenavi_optname, $wpv_update_pagenavi_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_update_commentnavi_optname) == FALSE) ) {
			add_option($this->wpv_update_commentnavi_optname, $wpv_update_commentnavi_optval, '', 'yes');
		}
		if ( (get_option($this->wpv_use_adminport_optname) == FALSE) ) {
			add_option($this->wpv_use_adminport_optname, $wpv_use_adminport_optval, '', 'yes');
		}
		// Localization init
		add_action('init', array(&$this, 'WPVarnishLocalization'));
		// Add Administration Interface
		add_action('admin_menu', array(&$this, 'WPVarnishAdminMenu'));
		// When posts/pages are published, edited or deleted
		add_action('edit_post', array(&$this, 'WPVarnishPurgePost'), 99);
		add_action('edit_post', array(&$this, 'WPVarnishPurgeCommonObjects'), 99);
		// When comments are made, edited or deleted
		add_action('comment_post', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('edit_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('trashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('untrashed_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		add_action('deleted_comment', array(&$this, 'WPVarnishPurgePostComments'),99);
		// When posts or pages are deleted
		add_action('deleted_post', array(&$this, 'WPVarnishPurgePost'), 99);
		add_action('deleted_post', array(&$this, 'WPVarnishPurgeCommonObjects'), 99);
		add_filter('wp_get_current_commenter', array(&$this, "wp_get_current_commenter_varnish"));
	}
	function wp_get_current_commenter_varnish($commenter) {
		if (get_query_var($this->query)) {
			return $commenter;
		} else {
			return array(
				'comment_author'       => '',
				'comment_author_email' => '',
				'comment_author_url'   => '',
			);
		}
	}
	function WPVarnishLocalization() {
		load_plugin_textdomain('wp-varnish-aas',false,dirname(plugin_basename(__FILE__)).'/lang/');
	}
	function WPVarnishPurgeCommonObjects() {
		$this->WPVarnishPurgeObject("/");
		$this->WPVarnishPurgeObject("/feed/");
		$this->WPVarnishPurgeObject("/feed/atom/");
		// Also purges page navigation
		if (get_option($this->wpv_update_pagenavi_optname) == 1) {
			$this->WPVarnishPurgeObject("/page/(.*)");
		}
	}
	// WPVarnishPurgeAll - Using a regex, clear all blog cache. Use carefully.
	function WPVarnishPurgeAll() {
		$this->WPVarnishPurgeObject('/(.*)');
	}
	// WPVarnishPurgePost - Takes a post id (number) as an argument and generates
	// the location path to the object that will be purged based on the permalink.
	function WPVarnishPurgePost($wpv_postid) {
		$wpv_url = get_permalink($wpv_postid);
		$wpv_permalink = str_replace(get_bloginfo('wpurl'),"",$wpv_url);
		$this->WPVarnishPurgeObject($wpv_permalink);
	}
	// WPVarnishPurgePostComments - Purge all comments pages from a post
	function WPVarnishPurgePostComments($wpv_commentid) {
		$comment = get_comment($wpv_commentid);
		$wpv_commentapproved = $comment->comment_approved;
		// If approved or deleting...
		if ($wpv_commentapproved == 1 || $wpv_commentapproved == 'trash') {
			$wpv_postid = $comment->comment_post_ID;
			// Popup comments
			$this->WPVarnishPurgeObject('/\\\?comments_popup=' . $wpv_postid);
			// Also purges comments navigation
			if (get_option($this->wpv_update_commentnavi_optname) == 1) {
					$this->WPVarnishPurgeObject('/\\\?comments_popup=' . $wpv_postid . '&(.*)');
			}
		}
	}
	function WPVarnishAdminMenu() {
		add_options_page(__('Varnish as a Service Configuration','wp-varnish-aas'), 'Varnish aaS', 1, 'WPVarnish', array(&$this, 'WPVarnishAdmin'));
	}
	function WPVarnishAdmin() {
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if (current_user_can('administrator')) {
				if (isset($_POST['wpvarnish_admin'])) {
					if (!empty($_POST["$this->wpv_addr_optname"])) {
						$wpv_addr_optval = $_POST["$this->wpv_addr_optname"];
							update_option($this->wpv_addr_optname, $wpv_addr_optval);
					}
					if (!empty($_POST["$this->wpv_port_optname"])) {
						$wpv_port_optval = $_POST["$this->wpv_port_optname"];
						update_option($this->wpv_port_optname, $wpv_port_optval);
					}
					if (!empty($_POST["$this->wpv_secret_optname"])) {
						$wpv_secret_optval = $_POST["$this->wpv_secret_optname"];
						update_site_option($this->wpv_secret_optname, $wpv_secret_optval);
					}
					if (!empty($_POST["$this->wpv_timeout_optname"])) {
						$wpv_timeout_optval = $_POST["$this->wpv_timeout_optname"];
						update_option($this->wpv_timeout_optname, $wpv_timeout_optval);
					}
					if (!empty($_POST["$this->wpv_use_adminport_optname"])) {
						update_option($this->wpv_use_adminport_optname, 1);
					} else {
						update_option($this->wpv_use_adminport_optname, 0);
					}
					if (!empty($_POST["$this->wpv_use_version_optname"])) {
						$wpv_use_version_optval = $_POST["$this->wpv_use_version_optname"];
						update_option($this->wpv_use_version_optname, $wpv_use_version_optval);
					}
					if (!empty($_POST["$this->wpv_update_pagenavi_optname"])) {
						update_option($this->wpv_update_pagenavi_optname, 1);
					} else {
						update_option($this->wpv_update_pagenavi_optname, 0);
					}
					if (!empty($_POST["$this->wpv_update_commentnavi_optname"])) {
						update_option($this->wpv_update_commentnavi_optname, 1);
					} else {
						update_option($this->wpv_update_commentnavi_optname, 0);
					}
				}
				if (isset($_POST['wpvarnish_clear_blog_cache']))
					$this->WPVarnishPurgeAll();
?>
		<div class="updated"><p><?php echo __('Settings Saved!','wp-varnish-aas' ); ?></p></div>
<?php
			} else {
?>
		<div class="updated"><p><?php echo __('You do not have the privileges.','wp-varnish-aas' ); ?></p></div>
<?php
			}
		}
		$wpv_timeout_optval = get_option($this->wpv_timeout_optname);
		$wpv_use_adminport_optval = get_option($this->wpv_use_adminport_optname);
		$wpv_update_pagenavi_optval = get_option($this->wpv_update_pagenavi_optname);
		$wpv_update_commentnavi_optval = get_option($this->wpv_update_commentnavi_optname);
?>
    <div class="wrap">
      <h2><?php echo __("Varnish as a Service Administration",'wp-varnish-aas'); ?></h2>
      <h3><?php echo __("Varnish Server configuration",'wp-varnish-aas'); ?></h3>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<table class="form-table" id="form-table">
			<tr valign="top">
				<th scope="row"><?php echo __("Varnish Administration IP Address",'wp-varnish-aas'); ?></th>
				<th scope="row"><?php echo __("Varnish Administration Port",'wp-varnish-aas'); ?></th>
				<th scope="row"><?php echo __("Varnish Secret",'wp-varnish-aas'); ?></th>
				<th scope="row"><?php echo __("Varnish Version",'wp-varnish-aas'); ?></th>
			</tr>
<?php
			$addrs = get_option($this->wpv_addr_optname);
			$ports = get_option($this->wpv_port_optname);
			$secrets = get_option($this->wpv_secret_optname);
			$versions = get_option($this->wpv_use_version_optname);
?>
			<tr valign="top">
				<td><input type="text" name="wpvarnish_addr" value="<?= $addrs; ?>" style="width: 120px;"></td>
				<td><input type="text" name="wpvarnish_port" value="<?= $ports; ?>" style="width: 50px;"></td>
				<td><input type="text" name="wpvarnish_secret" value="<?= $secrets; ?>" style="width: 260px;"></td>
				<td><select name="wpvarnish_use_version">
					<option value="2"<?php if($versions == 2) echo " selected"; ?>>v2 - PURGE</option>
					<option value="3"<?php if($versions == 3) echo " selected"; ?>>v3 - BAN</option>
				</select></td>
			</tr>
		</table>
			<p><?php echo __("Timeout",'wp-varnish-aas'); ?>: <input class="small-text" type="text" name="wpvarnish_timeout" value="<?php echo $wpv_timeout_optval; ?>" /> <?php echo __("seconds",'wp-varnish-aas'); ?></p>
			<p><input type="checkbox" name="wpvarnish_use_adminport" value="1" <?php if ($wpv_use_adminport_optval == 1) echo 'checked '?>/> <?php echo __("Use admin port",'wp-varnish-aas'); ?></p>
			<p><input type="checkbox" name="wpvarnish_update_pagenavi" value="1" <?php if ($wpv_update_pagenavi_optval == 1) echo 'checked '?>/> <?php echo __("Also purge all page navigation",'wp-varnish-aas'); ?></p>
			<p><input type="checkbox" name="wpvarnish_update_commentnavi" value="1" <?php if ($wpv_update_commentnavi_optval == 1) echo 'checked '?>/> <?php echo __("Also purge all comment navigation",'wp-varnish-aas'); ?></p>
			<p class="submit"><input type="submit" class="button-primary" name="wpvarnish_admin" value="<?php echo __("Save Changes",'wp-varnish-aas'); ?>" /></p>
			<p class="submit"><input type="submit" class="button-primary" name="wpvarnish_clear_blog_cache" value="<?php echo __("Purge All Blog Cache",'wp-varnish-aas'); ?>" /> <?php echo __("(Use only if necessary)",'wp-varnish-aas'); ?></p>
			</form>
		</div>
<?php
	}
	function WPAuth($challenge, $secret) {
		$ctx = hash_init('sha256');
		hash_update($ctx, $challenge);
		hash_update($ctx, "\n");
		hash_update($ctx, $secret . "\n");
		hash_update($ctx, $challenge);
		hash_update($ctx, "\n");
		$sha256 = hash_final($ctx);
		return $sha256;
	}
	// WPVarnishPurgeObject - Takes a location as an argument and purges this object
	// from the varnish cache.
	function WPVarnishPurgeObject($wpv_url) {
		global $varnish_servers;
		$wpv_purgeaddr = get_option($this->wpv_addr_optname);
		$wpv_purgeport = get_option($this->wpv_port_optname);
		$wpv_secret = get_option($this->wpv_secret_optname);
		$wpv_timeout = get_option($this->wpv_timeout_optname);
    $wpv_use_adminport = get_option($this->wpv_use_adminport_optname);
    $wpv_use_version = get_option($this->wpv_use_version_optname);
		$wpv_wpurl = get_bloginfo('wpurl');
		$wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
		$wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
		$wpv_blogaddr = preg_replace($wpv_replace_wpurl, "$2", $wpv_wpurl);
		$wpv_url = $wpv_blogaddr . $wpv_url;
		$varnish_sock = fsockopen($wpv_purgeaddr, $wpv_purgeport, $errno, $errstr, $wpv_timeout);
		if($varnish_sock) {
			if (!$varnish_sock) {
				error_log("wp-varnish-aas error: $errstr ($errno)");
			}
			if ($wpv_use_adminport) {
				$buf = fread($varnish_sock, 1024);
				if(preg_match('/(\w+)\s+Authentication required./', $buf, &$matches)) {
					$secret = $wpv_secret;
					$auth = $this->WPAuth($matches[1], $secret);
					fwrite($varnish_sock, "auth ".$auth."\n");
					$buf = fread($varnish_sock, 1024);
					if(!preg_match('/^200/', $buf)) {
						error_log("wp-varnish error: authentication failed using admin port");
						fclose($varnish_sock);
						return;
					}
				}
				if ($wpv_use_version == 2) {
					// VARNISH 2
					$out = "purge req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
				} elseif ($wpv_use_version == 3) {
					// VARNISH 3
					$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
				} else {
					// VARNISH 3
					$out = "ban req.url ~ ^$wpv_url && req.http.host == $wpv_host\n";
				}
				fwrite($varnish_sock, $out."\n");
				$buf = fread($varnish_sock, 1024);
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
$wpvarnish = & new WPVarnish();
?>