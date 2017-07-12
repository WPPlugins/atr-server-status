<?php
	$servers = [];
	$atts = $GLOBALS["ass_shortcode_atts"];

	if($atts["id"] == null) {
		$servers = ass_get_all_servers();
	} else if(strpos($atts["id"], ',') !== false) {
		$server_ids = explode(",", $atts["id"]);
		foreach($server_ids as $ID) {
			$servers[] = ass_get_server($ID);
		}
	} else {
		$servers[0] = ass_get_server($atts["id"]);
	}
?>

<?php foreach($servers as $server) { ?>
	<div class="server-box checking" id="server-<?php print $server->ID; ?>">
		<strong><?php print htmlentities($server->humanname); ?></strong>
		<div class="server-box-status"><p class="server-loading"><?php print __("Checking server status", "atr-server-status"); ?> <span>.</span><span>.</span><span>.</span></p></div>
	</div>
	<script type="text/javascript">
		check_server("#server-<?php print $server->ID; ?>", <?php print $server->ID; ?>);
	</script>
<?php }; ?>