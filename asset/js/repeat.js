
// Clone the previous section, and remove all of the values
jQuery(document).ready(function ($) {
	$("#rtgallery").on("click", ".docopy", function(e){
		$(".container .alert-info").remove();
		// the loop object
		$loop = $(this).parent();
		// the group to copy
		$group = $loop.find(".to-copy").clone().insertBefore($(this)).removeClass("to-copy");
		jQuery.ajax({
			type: "post",
			url: ajax_custom.ajaxurl,
			data: { action: "add_new_gallery",operation:"add",security: ajax_custom.ajax_nonce },
			success: function (response) {
				console.log(response);
			   if (response > 0) {
				 console.log('Gallery added successfully');
			   }
			   else {
				 console.log('Failed to add gallery');
			   }
			},
			complete: function(data){
				//set the attribute for all the fields of dynamic genrated gallery
				count = data.responseText;
				$id = "#"+count+"_sortable_wordpress_gallery";
				$ul = $group.find("ul#sortable_wordpress_gallery").attr("data-id",count).attr("id",count+"_sortable_wordpress_gallery");
				$spanMsgBox = $group.find("span.msgbox").removeClass("msgbox").addClass(count+"_msgbox");
				$inputTitle = $group.find("input[name='galleryTitle']").attr("id",count+"_gallery_title");
				$hiddenImageInput = $group.find("input:hidden").attr("id",count+"_sortable_wordpress_gallery_input").attr("name",count+"_sortable_wordpress_gallery");
				$removeButton = $group.find("button.dodelete").attr("data-gallery","#"+count+"_sortable_wordpress_gallery").attr("data-gallery-id",count);
				$addGalleryButton = $group.find("button.add-sortable-wordpress-gallery").attr("data-gallery","#"+count+"_sortable_wordpress_gallery").attr("id","sortable_wordpress_gallery_add").attr("data-gallery-id",count);
				$saveGalleryButton =$group.find("button.save-sortable-wordpress-gallery").attr("data-gallery","#"+count+"_sortable_wordpress_gallery").attr("id","sortable_wordpress_gallery_save").attr("data-gallery-id",count);

			}
		 });

	});

	//removing image gallery from the DB
	$("#rtgallery").on("click","button.dodelete",function(e) {
		e.preventDefault();
		var $dataGalleryID = $(this).attr("data-gallery-id");
		console.log($dataGalleryID);
		$(this).parent().remove();

		jQuery.ajax({
			type: "post",
			url: ajax_custom.ajaxurl,
			data: { action: "delete_gallery",galleryID: $dataGalleryID, security: ajax_custom.ajax_nonce},
			success: function (response) {
			   if (response == "success") {
				console.log("Gallery deleted successfully");
			   }
			   else {
				console.log("Failed to delete gallery");
			   }
			}
		});

	});

});
