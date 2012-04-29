<?php
/*
Plugin Name: WordPress Varnish as a Service
Version: 1.1.0
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
	public $commenter;
	function WPVarnish() {
		global $post;
		$wpv_addr_optval = "127.0.0.1";
		$wpv_port_optval = "80";
		$wpv_secret_optval = "";
		$wpv_timeout_optval = 5;
		$wpv_use_adminport_optval = 0;
		$wpv_use_version_optval = 3;
		if((get_option("wpvarnish_addr") == FALSE)) {
			add_option("wpvarnish_addr", $wpv_addr_optval, '', 'yes');
		}
		if((get_option("wpvarnish_port") == FALSE)) {
			add_option("wpvarnish_port", $wpv_port_optval, '', 'yes');
		}
		if((get_option("wpvarnish_secret") == FALSE)) {
			add_option("wpvarnish_secret", $wpv_secret_optval, '', 'yes');
		}
		if((get_option("wpvarnish_timeout") == FALSE)) {
			add_option("wpvarnish_timeout", $wpv_timeout_optval, '', 'yes');
		}
		if((get_option("wpvarnish_use_version") == FALSE)) {
			add_option("wpvarnish_use_version", $wpv_use_version_optval, '', 'yes');
		}
		if((get_option("wpvarnish_use_adminport") == FALSE)) {
			add_option("wpvarnish_use_adminport", $wpv_use_adminport_optval, '', 'yes');
		}
		add_action('init', array(&$this, 'WPVarnishLocalization'));
		add_action('admin_menu', array(&$this, 'WPVarnishAdminMenu'));
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
			$this->WPVarnishPurgeObject('/\\\?comments_popup='.$wpv_postid);
			$this->WPVarnishPurgeObject('/\\\?comments_popup='.$wpv_postid.'&(.*)');
		}
	}
	function WPVarnishAdminMenu() {
		add_options_page(__('Varnish as a Service Configuration','wp-varnish-aas'), 'Varnish aaS', 1, 'WPVarnish', array(&$this, 'WPVarnishAdmin'));
	}
	function WPVarnishAdmin() {
		if(current_user_can('administrator')) {
			if($_SERVER["REQUEST_METHOD"] == "POST") {
				if(isset($_POST['wpvarnish_admin'])) {
					if(isset($_POST["wpvarnish_addr"])) {
						$wpv_addr_optval = trim(strip_tags($_POST["wpvarnish_addr"]));
						update_option("wpvarnish_addr", $wpv_addr_optval);
					}
					if(isset($_POST["wpvarnish_port"])) {
						$wpv_port_optval = (int)trim(strip_tags($_POST["wpvarnish_port"]));
						update_option("wpvarnish_port", $wpv_port_optval);
					}
					if(isset($_POST["wpvarnish_secret"])) {
						$wpv_secret_optval = trim(strip_tags($_POST["wpvarnish_secret"]));
						update_option("wpvarnish_secret", $wpv_secret_optval);
					}
					if(isset($_POST["wpvarnish_timeout"])) {
						$wpv_timeout_optval = (int)trim(strip_tags($_POST["wpvarnish_timeout"]));
						update_option("wpvarnish_timeout", $wpv_timeout_optval);
					}
					if(isset($_POST["wpvarnish_use_adminport"])) {
						update_option("wpvarnish_use_adminport", 1);
					} else {
						update_option("wpvarnish_use_adminport", 0);
					}
					if(isset($_POST["wpvarnish_use_version"])) {
						$wpv_use_version_optval = $_POST["wpvarnish_use_version"];
						update_option("wpvarnish_use_version", $wpv_use_version_optval);
					}
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
				if (isset($_POST['wpvarnish_test_blog_cache'])) {
?>
	<div class="updated"><p><?php echo __('Testing Connection to Varnish Server','wp-varnish-aas'); ?></p></div>
<?php
					$this->WPVarnishTestConnect();
				}
			}
			$wpv_timeout_optval = get_option("wpvarnish_timeout");
			$wpv_use_adminport_optval = get_option("wpvarnish_use_adminport");
			$addrs = get_option("wpvarnish_addr");
			$ports = get_option("wpvarnish_port");
			$secrets = get_option("wpvarnish_secret");
			$versions = get_option("wpvarnish_use_version");
?>
	<div class="wrap">
		<h2><?php echo __("Varnish as a Service Administration",'wp-varnish-aas'); ?></h2>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<dl>
				<dt><label for="varipaddress"><?php echo __("Server IP Address",'wp-varnish-aas'); ?></label></dt>
				<dd><input id="varipaddress" type="text" name="wpvarnish_addr" value="<?= $addrs; ?>" style="width: 120px;"></dd>
				<dt><label for="varport"><?php echo __("Server Port",'wp-varnish-aas'); ?></label></dt>
				<dd><input id="varport" type="text" name="wpvarnish_port" value="<?= $ports; ?>" style="width: 50px;"></dd>
				<dt><label for="varuseadmin"><?php echo __("Use Admin port",'wp-varnish-aas'); ?></label></dt>
				<dd><input id="varuseadmin" type="checkbox" name="wpvarnish_use_adminport" value="1"<?php if($wpv_use_adminport_optval == 1) echo ' checked'; ?>></dd>
				<dt><label for="varsecret"><?php echo __("Secret Key",'wp-varnish-aas'); ?></label></dt>
				<dd><input id="varsecret" type="text" name="wpvarnish_secret" value="<?= $secrets; ?>" style="width: 260px;"></dd>
				<dt><label for="varversion"><?php echo __("Version",'wp-varnish-aas'); ?></label></dt>
				<dd><select id="varversion" name="wpvarnish_use_version">
					<option value="2"<?php if($versions == 2) echo " selected"; ?>>v2 - PURGE</option>
					<option value="3"<?php if($versions == 3) echo " selected"; ?>>v3 - BAN</option>
				</select></dd>
				<dt><label for="vartimeout"><?php echo __("Timeout",'wp-varnish-aas'); ?></label></dt>
				<dd><input id="vartimeout" class="small-text" type="text" name="wpvarnish_timeout" value="<?php echo $wpv_timeout_optval; ?>"> <?php echo __("seconds",'wp-varnish-aas'); ?></dd>
			</dl>
			<p class="submit"><input type="submit" class="button-primary" name="wpvarnish_admin" value="<?php echo __("Save Changes",'wp-varnish-aas'); ?>"> <input type="submit" class="button-secondary" name="wpvarnish_clear_blog_cache" value="<?php echo __("Purge All Blog Cache",'wp-varnish-aas'); ?>"> <input type="submit" class="button-secondary" name="wpvarnish_test_blog_cache" value="<?php echo __("Test Connection to Varnish",'wp-varnish-aas'); ?>"></p>
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
		$varnish_test_conn = "";
		$wpv_purgeaddr = get_option("wpvarnish_addr");
		$wpv_purgeport = get_option("wpvarnish_port");
		$wpv_secret = get_option("wpvarnish_secret");
		$wpv_timeout = get_option("wpvarnish_timeout");
		$wpv_use_adminport = get_option("wpvarnish_use_adminport");
		$wpv_use_version = get_option("wpvarnish_use_version");
		$wpv_wpurl = get_bloginfo('wpurl');
		$wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
		$wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
		$wpv_blogaddr = preg_replace($wpv_replace_wpurl, "$2", $wpv_wpurl);
		$wpv_url = $wpv_blogaddr.$wpv_url;
		$varnish_sock = fsockopen($wpv_purgeaddr, $wpv_purgeport, $errno, $errstr, $wpv_timeout);
		if($varnish_sock) {
			if($wpv_use_adminport) {
				$buf = fread($varnish_sock, 1024);
				if(preg_match('/(\w+)\s+Authentication required./', $buf, &$matches)) {
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
	function WPVarnishTestConnect() {
		global $varnish_servers;
		$varnish_test_conn = "";
		$wpv_purgeaddr = get_option("wpvarnish_addr");
		$wpv_purgeport = get_option("wpvarnish_port");
		$wpv_secret = get_option("wpvarnish_secret");
		$wpv_timeout = get_option("wpvarnish_timeout");
		$wpv_use_adminport = get_option("wpvarnish_use_adminport");
		$wpv_use_version = get_option("wpvarnish_use_version");
		$wpv_wpurl = get_bloginfo("wpurl");
		$wpv_replace_wpurl = '/^http:\/\/([^\/]+)(.*)/i';
		$wpv_host = preg_replace($wpv_replace_wpurl, "$1", $wpv_wpurl);
		$wpv_url = $wpv_blogaddr."/";
		$varnish_test_conn .= "<ul>\n";
		$varnish_sock = fsockopen($wpv_purgeaddr, $wpv_purgeport, $errno, $errstr, $wpv_timeout);
		if($varnish_sock) {
			$varnish_test_conn .= "<li><span style=\"color: green;\">".__("OK - Connection to Server",'wp-varnish-aas')."</span></li>\n";
			if ($wpv_use_adminport) {
				$varnish_test_conn .= "<li><span style=\"color: blue;\">".__("INFO - Using Admin Port",'wp-varnish-aas')."</span></li>\n";
				$buf = fread($varnish_sock, 1024);
				if(preg_match('/(\w+)\s+Authentication required./', $buf, &$matches)) {
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