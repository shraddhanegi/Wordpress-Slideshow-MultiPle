/*
* save the soratble gallery to database using Ajax
*@return string
*/
jQuery(document).ready(function () {
	var $dataGalleryId =  $(this).attr("data-gallery");
	var saveImage = "#rtgallery .of-repeat-group button#"+$dataGalleryId;

   jQuery(document).on("click","#rtgallery .of-repeat-group button.save-sortable-wordpress-gallery",function (e) {
	  e.preventDefault();
	 var $gallery = jQuery(this).attr("data-gallery");
	 var $image_id = jQuery( $gallery + "_input" ).val();
	 var $galleryId = jQuery(this).attr("data-gallery-id");
	 var $galleryTitle =jQuery("#"+$galleryId+"_gallery_title").val();

      jQuery.ajax({
         type: "post",
         url: ajax_custom.ajaxurl,
         data: { action: "save_gallery", imageID: $image_id, galleryID: $galleryId, galleryTitle: $galleryTitle, security: ajax_custom.ajax_nonce },
         success: function (response) {
            if (response == "success") {
			   jQuery("."+$galleryId+"_msgbox")
			   .removeClass("invisible")
			   .removeClass("text-danger")
               .addClass("text-success")
               .text("Gallery saved successfully")
               .delay(2200).fadeIn(300);
               console.log('Gallery saved successfully');
            }
            else {
               jQuery("."+$galleryId+"_msgbox").removeClass("invisible").addClass("text-danger").text("Failed to save Gallery");
               console.log("Failed to save Gallery");
            }

         }
      });

   });

});
