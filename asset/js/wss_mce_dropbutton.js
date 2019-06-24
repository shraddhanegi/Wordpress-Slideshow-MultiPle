
(function() {
		tinymce.PluginManager.add('wss_mce_dropbutton', function( editor, url ) {
			var data = {
				'action'	: 'wss_shortcode_list_ajax',
				'dataType' : 'json' // wp ajax action
			};

			var menuItems = [];
			var wpshortcodes = function (e) {
					$.post( ajaxurl, data, function( response ) {
				}).done(function(response) {
					for(var i = 0, l = response.length; i < l; ++i) {
						menuItems.push({
							text : response[i].gallery_title,
							content : response[i].shortcode,
							onClick : function(e) {
								editor.execCommand('mceInsertContent', false, e.control.settings.content);
							}
						});

					}
				});

			return menuItems;
		}

		editor.addButton( 'wss_mce_dropbutton', {
				icon: false,
				disabled    : false,
				text : 'slideshow',
				type: 'menubutton',
				menu: wpshortcodes(editor),
				prependToContext: true
			});
		});
})();
