jQuery( function( $ ) {
    $( 'body' ).on( 'click', '.mac-zoneurl', function( e ) {
        e.preventDefault();
        var button = $( this ),
            custom_uploader = wp.media( {
                title: 'Insert file',
                library: {
                    type: 'application/vnd.google-earth.kml+xml' // Set the MIME type for KML files
                },
                button: {
                    text: 'Use this file'
                },
                multiple: false
            } ).on('select', function() {
                var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
                button.prev( 'input' ).val( attachment.url );
            } ).open();
    } );
} );
