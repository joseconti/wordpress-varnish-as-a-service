<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit();
delete_option("wpvarnish_addr");
delete_option("wpvarnish_port");
delete_option("wpvarnish_secret");
delete_option("wpvarnish_timeout");
delete_option("wpvarnish_use_adminport");
delete_option("wpvarnish_use_version");
delete_option("wpvarnish_update_pagenavi");
delete_option("wpvarnish_update_commentnavi");
?>