(function ($) {
	$(document).ready(function () {
		sortable_gallery_image_remove();
		var imageButton = $(".add-sortable-wordpress-gallery");
		imageButton.each(function () {
			var galleryID = $(this).attr("data-gallery");
			var $addButtonId =  $(this).attr("id"); //fetcing id from add-image button
			var imageContainer = $(galleryID);
			var imageInput = $(galleryID + "_input");
			imageContainer.sortable();
					imageContainer.on("sortupdate", function (event, ui) { console.log('here');
						$ids = [];
						$images = imageContainer.children("li");
						$images.each(function () {
							$ids.push($(this).attr("data-id"));
						});
						imageInput.val($ids.join());
					});

			var addImage = "#rtgallery .of-repeat-group button#"+$addButtonId;
			//.of-repeat-group button"+addImage
			$(document).on("click",addImage,function (e) {
				e.preventDefault();
				//we need to get again galleryID for dynamically genrated gallery
					var dataGalleryID = $(this).attr("data-gallery-id");
					$("."+dataGalleryID+"_msgbox").addClass("invisible");
					var galleryID = $(this).attr("data-gallery");
					var imageContainer = $(galleryID);

					var imageInput = $("#rtgallery .of-repeat-group input"+ galleryID+"_input");
					//now we have to maked daynamically added gallery sortable
					//and we will save its id in imageInput
					imageContainer.sortable();
					imageContainer.on("sortupdate", function (event, ui) {
						$ids = [];
						$images = imageContainer.children("li");
						$images.each(function () {
							$ids.push($(this).attr("data-id"));
						});
						imageInput.val($ids.join());
					});

				sortable_image_gallery_media(imageContainer, imageInput);
			});
		});


	});
})(jQuery)
