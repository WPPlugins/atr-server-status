<?php foreach(SessionStatusMessage::get("all") as $message) { ?>
	<div class="ass-server-status-message <?php print $message->type; ?> notice">
	 	<p><?php print $message->message; ?></p>
    </div>
<?php } ?>