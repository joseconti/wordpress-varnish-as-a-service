<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit();
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
delete_option("wpvarnish_addr_1");
delete_option("wpvarnish_port_1");
delete_option("wpvarnish_secret_1");
delete_option("wpvarnish_timeout_1");
delete_option("wpvarnish_use_adminport_1");
delete_option("wpvarnish_use_version_1");
delete_option("wpvarnish_server_1");
/* SERVER 2 */
delete_option("wpvarnish_addr_2");
delete_option("wpvarnish_port_2");
delete_option("wpvarnish_secret_2");
delete_option("wpvarnish_timeout_2");
delete_option("wpvarnish_use_adminport_2");
delete_option("wpvarnish_use_version_2");
delete_option("wpvarnish_server_2");
/* SERVER 3 */
delete_option("wpvarnish_addr_3");
delete_option("wpvarnish_port_3");
delete_option("wpvarnish_secret_3");
delete_option("wpvarnish_timeout_3");
delete_option("wpvarnish_use_adminport_3");
delete_option("wpvarnish_use_version_3");
delete_option("wpvarnish_server_3");
?>