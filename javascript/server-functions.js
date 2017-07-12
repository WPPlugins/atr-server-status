function select_text( containerid ) {
    var node = document.getElementById( containerid );

    if ( document.selection ) {
        var range = document.body.createTextRange();
        range.moveToElementText( node  );
        range.select();
    } else if ( window.getSelection ) {
        var range = document.createRange();
        range.selectNodeContents( node );
        window.getSelection().removeAllRanges();
        window.getSelection().addRange( range );
    }
}

function check_server(dom_ID, server_ID) {
    var server_box = jQuery(dom_ID);

    server = {ID: server_ID, action: "ass_check_server"}

    jQuery.post("/wp-admin/admin-ajax.php", server, function(response) {
        server_box.removeClass("checking").addClass(response.status);
        server_box.find(".server-box-status").html(response.message);
    }, "json");
}