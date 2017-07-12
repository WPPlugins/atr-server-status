<?php
	$servers = ass_get_all_servers();
	if( isset($_GET["edit"]) && is_numeric($_GET["edit"]) ) {
		$edit = ass_get_server( $_GET["edit"] );
	}
?>
<form action="<?php print esc_url( admin_url('admin-post.php') ); ?>" method="POST">
	<div class="ass-admin-page-padding">
		<h1><?php print __("Server status management", "atr-server-status"); ?></h1>
		<div class="ass-shortcode-display">
			<span class="ass-shortcodes-label">Shortcodes: </span><pre id="ass-shortcode" onclick="select_text(this.id)">[<?php print ASS_SERVER_STATUS_SHORTCODE; ?>]</pre> <pre id="ass-shortcode-single" onclick="select_text(this.id)">[<?php print ASS_SERVER_STATUS_SHORTCODE; ?> id="X"]</pre> <pre id="ass-shortcode-commas" onclick="select_text(this.id)">[<?php print ASS_SERVER_STATUS_SHORTCODE; ?> id="X,X,X"]</pre>
		</div>
		<div class="ass-table-responsive">
			<table class="wp-list-table widefat fixed striped ass-server-status" id="ass-admin-servers-table">
				<thead>
					<tr class="ass-server-row">
						<th class="moveit" style="width:2%"></th>
						<th style="width:18%">
							<strong><?php print __("Server Name", "atr-server-status"); ?></strong>
							<span class="ass-cell-description"><?php print __("A human friendly name.", "atr-server-status"); ?></span>
						</th>
						<th style="width:20%;">
							<strong><?php print __("Hostname", "atr-server-status"); ?></strong>
							<span class="ass-cell-description"><?php print __("An IP address or FQDN to be requested.", "atr-server-status"); ?></span>
						</th>
						<th style="width:20%;">
							<strong><?php print __("Port", "atr-server-status"); ?></strong>
							<span class="ass-cell-description"><?php print __("Port number to be reached.", "atr-server-status"); ?></span>
						</th>
						<th style="width:20%;">
							<strong><?php print __("Timeout", "atr-server-status"); ?></strong>
							<span class="ass-cell-description"><?php print __("Seconds until server is considered unavailable.", "atr-server-status"); ?></span>
						</th>
						<th style="width:15%;">
							<strong><?php print __("Protocol", "atr-server-status"); ?></strong>
							<span class="ass-cell-description"><?php print __("The protocol to use.", "atr-server-status"); ?></span>
						</th>
						<th style="width:5%;"></th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($servers)) { ?>
						<?php foreach($servers as $server) { ?>
							<?php
								$rowclass = '';
								if(isset($_GET["server"]) && $_GET["server"] == $server->ID) {
									$rowclass = "saved";
								}
								if(isset($_GET["edit"]) && $_GET["edit"] == $server->ID) continue;
							?>
							<tr class="ass-server-row <?php print $rowclass; ?>" data-server-id="<?php print $server->ID; ?>">
								<td class="moveit"><span class="ass-drag-handle dashicons dashicons-move"></span></td>
								<td class="humanname"><?php print $server->humanname; ?> (id: <span style="cursor:pointer;" id="ass-server-row-id-<?php print $server->ID; ?>" onclick="select_text(this.id); "><?php print $server->ID; ?></span>)</td>
								<td class="hostname"><?php print $server->hostname; ?></td>
								<td class="port"><?php print $server->port; ?></td>
								<td class="timeout"><?php print $server->timeout; ?></td>
								<td class="protocol ass-upper"><?php print $server->protocol ?>://</td>
								<td class="ass-center">
									<a href="<?php menu_page_url("ass-admin-servers") ?>&edit=<?php print $server->ID; ?>"><span class="dashicons dashicons-edit"></span></a>
									<a href="<?php menu_page_url("ass-admin-servers") ?>" class="ass-remove-server" data-server-id="<?php print $server->ID; ?>"><span class="dashicons dashicons-trash"></span></a>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>
					<tr class="ass-server-row" id="ass-save-server-row">
						<td class="moveit"><span class="ass-drag-handle dashicons dashicons-move"></span></td>
						<td>
							<input type="text" name="server[humanname]" value="<?php if(isset($edit)) print $edit->humanname; ?>" required />
						</td>
						<td>
							<input type="text" name="server[hostname]" value="<?php if(isset($edit)) print $edit->hostname; ?>" required />
						</td>
						<td>
							<input type="number" name="server[port]" value="<?php if(isset($edit)) print $edit->port; ?>" required />
						</td>
						<td>
							<input type="number" name="server[timeout]" value="<?php if(isset($edit)) print $edit->timeout; ?>" required />
						</td>
						<td>
							<select name="server[protocol]" id="ass-server-protocol">
								<?php foreach(ass_get_supported_protocols() as $protocol) { ?>
									<option value="<?php print $protocol; ?>" <?php if(isset($edit) && $edit->protocol == $protocol) print 'selected="selected"' ?>>
										<?php print strtoupper($protocol); ?>
									</option>
								<?php } ?>
							</select>
						</td>
						<td>
							<?php if(isset($edit)) { ?>
								<?php wp_nonce_field("ass-edit-server"); ?>
								<input type="hidden" name="server[ID]" value="<?php print $edit->ID; ?>" />
								<input type="hidden" name="action" value="ass_edit_server" />
								<input type="submit" class="button button-primary" value="Edit" name="ass-edit-server" />
							<?php } else { ?>
								<?php wp_nonce_field("ass-add-server"); ?>
								<input type="hidden" name="server[weight]" class="ass-server-weight" value="">
								<input type="hidden" name="action" value="ass_add_server" />
								<input type="submit" class="button button-primary" value="Add" name="ass-add-server" />
							<?php } ?> 
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ass-admin-message"></div>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#ass-server-protocol").change(function() {
			var protocol = $(this).val();
			$("#ass-admin-message").slideUp("fast");
			if(protocol == "udp") {
				$("#ass-admin-message").html('<?php print __("<strong>Heads up!</strong> UDP sockets will in some instances appear as opened without an error, even if the remote host is unreachable. UDP is a connectionless protocol, the servers operating system will only attempt to establish a link when it is needed for reading/writing, this can produce false positives.", "atr-server-status"); ?>');
				$("#ass-admin-message").slideDown("fast");
			} else if(protocol == "http" || protocol == "https") {
				$("#ass-admin-message").html("<?php print __("<strong>Stay awake!</strong> This server must respond with a HTTP code less than 400. Otherwise the server will be considered down.", "atr-server-status"); ?>" );
				$("#ass-admin-message").slideDown("fast");
			}
		});

		$("#ass-admin-servers-table").sortable({
			items:".ass-server-row",
			handle:".ass-drag-handle",
			axis: 'y',
			helper: function(e, tr) {
				var $originals = tr.children();
				var $helper = tr.clone();
				$helper.children().each(function(index) {
					// Set helper cell sizes to match the original sizes
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			update: function(event, ui) {
				$(".ass-server-row").each(function() {
					if(typeof $(this).data("server-id") !== "undefined") {
						var server_obj = {
							ID: $(this).data("server-id"),
							weight: $(this).index(),
							action: "ass_sort_server"
						}

						$.post(ajaxurl, server_obj);
					} else {
						$(this).find(".ass-server-weight").val($(this).index());
					} 
				});
			}
		});

		$(".ass-remove-server").click(function(e) {
			e.preventDefault();
			var row = $(this).closest("tr");
			row.addClass("deleted");

			if(row.hasClass("saved")) { row.removeClass("saved"); }

			var data = {action: "ass_remove_server", server_id: $(this).data("server-id"), _wpnonce: "<?php print wp_create_nonce("ass-remove-server"); ?>"};

			jQuery.post(ajaxurl, data, function(response) {
				window.setTimeout(function() {
					row.remove();
				}, 500);
			});
		});
	});

	if(window.innerWidth <= 782) { jQuery("#ass-admin-servers-table tbody").prepend( jQuery("#ass-save-server-row") ); }
</script>