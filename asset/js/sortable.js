/**
 * function to sort the images of image gallery
 *
 */
function sortable_gallery_image_remove() {
	jQuery(document).on("click","#rtgallery ul li button.remove-sortable-wordpress-gallery-image", function () {
	$id = jQuery(this).parent().attr("data-id");// attached image id
	$dataID = jQuery(this).parents("ul").attr("data-id"); //ul attribute
	jQuery("."+$dataID+"_msgbox").addClass("invisible");
    $gallery = jQuery(this).attr("data-gallery");
    $imageInput = jQuery($gallery + "_input");
    jQuery(this).parent().remove();
    var ids = $imageInput.val().split(',');
    $idIndex = ids.indexOf($id);
    if ($idIndex >= 0) {
      ids.splice($idIndex, 1);
      $imageInput.val(ids.join());
    }
  });
}
/**
 * function is to create image gallery /add image to gallery
 * uploaded from media uploader
 *
 * @param {*} $attachment
 * @param {*} $galleryID
 * @returns $output
 */
function sortable_gallery_image_create($attachment, $galleryID) {
  var $output = '<li tabindex="0" role="checkbox" aria-label="' + $attachment.title + '" aria-checked="true" data-id="' + $attachment.id + '" class="attachment save-ready selected details ui-sortable-handle">';
  $output += '<div class="attachment-preview js--select-attachment type-image subtype-jpeg portrait">';
  $output += '<div class="thumbnail">'

  $output += '<div class="centered">'
  $output += '<img src="' + $attachment.sizes.thumbnail.url + '" draggable="false" alt="">'
  $output += '</div>'

  $output += '</div>'

  $output += '</div>'

  $output += '<button type="button" data-gallery="#' + $galleryID + '" class="button-link check remove-sortable-wordpress-gallery-image" tabindex="0"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>'


  $output += '</li>';
  return $output;

}
/**
 * function to open the media uploader to upload the image
 * it will close the uploder on image selection
 *
 * @param {*} $imageContainer
 * @param {*} $imageInput
 * @returns
 */
function sortable_image_gallery_media( $imageContainer, $imageInput ) {
    'use strict';

    var file_frame;

    /**
     * If an instance of file_frame already exists, then we can open it
     * rather than creating a new instance.
     */

    if ( undefined !== file_frame ) {

        file_frame.open();

        return;

    }

    /**
     * If we're this far, then an instance does not exist, so we need to
     * create our own.
     *
     * Here, use the wp.media library to define the settings of the Media
     * Uploader. We're opting to use the 'post' frame which is a template
     * defined in WordPress core and are initializing the file frame
     * with the 'insert' state.
     *
     * We're also not allowing the user to select more than one image.
     */
    file_frame =  wp.media({
        multiple: true,
    });

    file_frame.on('open',function() {

	  var selection = file_frame.state().get('selection');
			var ids = $imageInput.val().split(',');
			ids.forEach(function(id) {
			var attachment = wp.media.attachment(id);
			attachment.fetch();
			selection.add( attachment ? [ attachment ] : [] );
			});
    });

    // When an image is selected in the media frame...
    file_frame.on( 'select', function() {
      // Get media attachment details from the frame state
      var attachments = file_frame.state().get('selection').toJSON();

      var attachmentIDs = [];
      $imageContainer.empty();
      var $galleryID = $imageContainer.attr("id");
      for( var i = 0; i < attachments.length; i++ ) {
        if( attachments[ i ].type == "image" ) {
          attachmentIDs.push( attachments[ i ].id );
          $imageContainer.append( sortable_gallery_image_create( attachments[ i ], $galleryID ) );
        }
      }

	  $imageInput.val( attachmentIDs.join() );

      sortable_gallery_image_remove();
    });

    // Now display the actual file_frame
    file_frame.open();

}
