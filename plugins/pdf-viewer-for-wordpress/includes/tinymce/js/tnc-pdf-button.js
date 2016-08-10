(function() {
	tinymce.PluginManager.add('tnc_pdf_mce_button', function( editor, url ) {
		editor.addButton( 'tnc_pdf_mce_button', {
			text: 'Insert PDF Viewer Shortcode',
			icon: false,
			type: 'menubutton',
			menu: [
				{
					text: 'Link',
					onclick: function() {
						editor.windowManager.open( {
							classes: 'tnc_scroll',
							title: 'Insert PDF Link Shortcode',
							body: [
								{
									type: 'filepicker',
									filetype: 'pdf',
									name: 'file',
									label: 'File url'
								},
								{
									type: 'textbox',
									name: 'linkclass',
									label: 'Link Class',
									classes: 'col-sm-4'
								},
								{
									type: 'textbox',
									name: 'linktext',
									label: 'Link Text',
									classes: 'col-sm-4'
								},
								{
									type: 'listbox',
									name: 'linktarget',
									label: 'Link Target',
									'values': [
										{text: 'Same Tab', value: '_parent'},
										{text: 'New Tab', value: '_blank'}
									]
								},
								{
									type: 'listbox',
									name: 'download',
									label: 'Download',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'print',
									label: 'Print',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'fullscreen',
									label: 'Fullscreen',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'share',
									label: 'Share',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'zoom',
									label: 'Zoom',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'open',
									label: 'Open',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'logo',
									label: 'Logo',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'pagenav',
									label: 'Pagenav',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'find',
									label: 'Find',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'textbox',
									name: 'jumptopage',
									label: 'Jump to Page'
								},
								{
									type: 'listbox',
									name: 'defaultzoom',
									label: 'Default Zoom',
									'values': [
										{text: 'Auto', value: 'auto'},
										{text: 'Page Fit', value: 'page-fit'},
										{text: 'Page Width', value: 'page-width'},
										{text: 'Page Height', value: 'page-height'},
										{text: '75%', value: '75'},
										{text: '100%', value: '100'},
										{text: '150%', value: '150'},
										{text: '200%', value: '200'},
									]
								},
								{
									type: 'listbox',
									name: 'pagemode',
									label: 'Page Mode',
									'values': [
										{text: 'Default', value: ''},
										{text: 'Thumbnails', value: 'thumbs'},
										{text: 'Bookmarks', value: 'bookmarks'}
									]
								},
								{
									type: 'listbox',
									name: 'v_language',
									label: 'Language',
									'values': [
										{value: 'en-US', text: 'en-US'},
										{value: 'ach', text: 'ach'},
										{value: 'af', text: 'af'},
										{value: 'ak', text: 'ak'},
										{value: 'an', text: 'an'},
										{value: 'ar', text: 'ar'},
										{value: 'as', text: 'as'},
										{value: 'ast', text: 'ast'},
										{value: 'az', text: 'az'},
										{value: 'be', text: 'be'},
										{value: 'bg', text: 'bg'},
										{value: 'bn-BD', text: 'bn-BD'},
										{value: 'bn-IN', text: 'bn-IN'},
										{value: 'br', text: 'br'},
										{value: 'bs', text: 'bs'},
										{value: 'ca', text: 'ca'},
										{value: 'cs', text: 'cs'},
										{value: 'csb', text: 'csb'},
										{value: 'cy', text: 'cy'},
										{value: 'da', text: 'da'},
										{value: 'de', text: 'de'},
										{value: 'el', text: 'el'},
										{value: 'en-GB', text: 'en-GB'},
										{value: 'en-ZA', text: 'en-ZA'},
										{value: 'eo', text: 'eo'},
										{value: 'es-AR', text: 'es-AR'},
										{value: 'es-CL', text: 'es-CL'},
										{value: 'es-ES', text: 'es-ES'},
										{value: 'es-MX', text: 'es-MX'},
										{value: 'et', text: 'et'},
										{value: 'eu', text: 'eu'},
										{value: 'fa', text: 'fa'},
										{value: 'ff', text: 'ff'},
										{value: 'fi', text: 'fi'},
										{value: 'fr', text: 'fr'},
										{value: 'fy-NL', text: 'fy-NL'},
										{value: 'ga-IE', text: 'ga-IE'},
										{value: 'gd', text: 'gd'},
										{value: 'gl', text: 'gl'},
										{value: 'gu-IN', text: 'gu-IN'},
										{value: 'he', text: 'he'},
										{value: 'hi-IN', text: 'hi-IN'},
										{value: 'hr', text: 'hr'},
										{value: 'hu', text: 'hu'},
										{value: 'hy-AM', text: 'hy-AM'},
										{value: 'id', text: 'id'},
										{value: 'is', text: 'is'},
										{value: 'it', text: 'it'},
										{value: 'ja', text: 'ja'},
										{value: 'ka', text: 'ka'},
										{value: 'kk', text: 'kk'},
										{value: 'km', text: 'km'},
										{value: 'kn', text: 'kn'},
										{value: 'ko', text: 'ko'},
										{value: 'ku', text: 'ku'},
										{value: 'lg', text: 'lg'},
										{value: 'lij', text: 'lij'},
										{value: 'lt', text: 'lt'},
										{value: 'lv', text: 'lv'},
										{value: 'mai', text: 'mai'},
										{value: 'mk', text: 'mk'},
										{value: 'ml', text: 'ml'},
										{value: 'mn', text: 'mn'},
										{value: 'mr', text: 'mr'},
										{value: 'ms', text: 'ms'},
										{value: 'my', text: 'my'},
										{value: 'nb-NO', text: 'nb-NO'},
										{value: 'nl', text: 'nl'},
										{value: 'nn-NO', text: 'nn-NO'},
										{value: 'nso', text: 'nso'},
										{value: 'oc', text: 'oc'},
										{value: 'or', text: 'or'},
										{value: 'pa-IN', text: 'pa-IN'},
										{value: 'pl', text: 'pl'},
										{value: 'pt-BR', text: 'pt-BR'},
										{value: 'pt-PT', text: 'pt-PT'},
										{value: 'rm', text: 'rm'},
										{value: 'ro', text: 'ro'},
										{value: 'ru', text: 'ru'},
										{value: 'rw', text: 'rw'},
										{value: 'sah', text: 'sah'},
										{value: 'si', text: 'si'},
										{value: 'sk', text: 'sk'},
										{value: 'sl', text: 'sl'},
										{value: 'son', text: 'son'},
										{value: 'sq', text: 'sq'},
										{value: 'sr', text: 'sr'},
										{value: 'sv-SE', text: 'sv-SE'},
										{value: 'sw', text: 'sw'},
										{value: 'ta', text: 'ta'},
										{value: 'ta-LK', text: 'ta-LK'},
										{value: 'te', text: 'te'},
										{value: 'th', text: 'th'},
										{value: 'tl', text: 'tl'},
										{value: 'tn', text: 'tn'},
										{value: 'tr', text: 'tr'},
										{value: 'uk', text: 'uk'},
										{value: 'ur', text: 'ur'},
										{value: 'vi', text: 'vi'},
										{value: 'wo', text: 'wo'},
										{value: 'xh', text: 'xh'},
										{value: 'zh-CN', text: 'zh-CN'},
										{value: 'zh-TW', text: 'zh-TW'},
										{value: 'zu', text: 'zu'},
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tnc-pdf-viewer-link file="' + e.data.file + '" target="' + e.data.linktarget + '" download="' + e.data.download + '" print="' + e.data.print + '" fullscreen="' + e.data.fullscreen + '" share="' + e.data.share + '" zoom="' + e.data.zoom + '" open="' + e.data.open + '" pagenav="' + e.data.pagenav + '" logo="' + e.data.logo + '" find="' + e.data.find + '" language="' + e.data.v_language + '" class="' + e.data.linkclass + '" text="' + e.data.linktext + '" page="' + e.data.jumptopage + '" default_zoom="' + e.data.defaultzoom + '" pagemode="' + e.data.pagemode + '"]');
							}
						});
					}
				},

				// Iframe Menu Start
				{
					text: 'Iframe',
					onclick: function() {
						editor.windowManager.open( {
							classes: 'tnc_scroll',
							title: 'Insert PDF Viewer Iframe Shortcode',
							body: [
								{
									type: 'filepicker',
									filetype: 'pdf',
									name: 'file',
									label: 'File url',
									classes: 'header_logo'
								},
								{
									type: 'textbox',
									name: 'iframewidth',
									label: 'Iframe Width',
									classes: 'col-sm-4'
								},
								{
									type: 'textbox',
									name: 'iframeheight',
									label: 'Iframe Height',
									classes: 'col-sm-4'
								},
								{
									type: 'listbox',
									name: 'download',
									label: 'Download',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'print',
									label: 'Print',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'fullscreen',
									label: 'Fullscreen',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'share',
									label: 'Share',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'zoom',
									label: 'Zoom',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'open',
									label: 'Open',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'logo',
									label: 'Logo',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'pagenav',
									label: 'Pagenav',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'find',
									label: 'Find',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'textbox',
									name: 'jumptopage',
									label: 'Jump to Page'
								},
								{
									type: 'listbox',
									name: 'defaultzoom',
									label: 'Default Zoom',
									'values': [
										{text: 'Auto', value: 'auto'},
										{text: 'Page Fit', value: 'page-fit'},
										{text: 'Page Width', value: 'page-width'},
										{text: 'Page Height', value: 'page-height'},
										{text: '75%', value: '75'},
										{text: '100%', value: '100'},
										{text: '150%', value: '150'},
										{text: '200%', value: '200'},
									]
								},
								{
									type: 'listbox',
									name: 'pagemode',
									label: 'Page Mode',
									'values': [
										{text: 'Default', value: ''},
										{text: 'Thumbnails', value: 'thumbs'},
										{text: 'Bookmarks', value: 'bookmarks'}
									]
								},
								{
									type: 'listbox',
									name: 'v_language',
									label: 'Language',
									'values': [
										{value: 'en-US', text: 'en-US'},
										{value: 'ach', text: 'ach'},
										{value: 'af', text: 'af'},
										{value: 'ak', text: 'ak'},
										{value: 'an', text: 'an'},
										{value: 'ar', text: 'ar'},
										{value: 'as', text: 'as'},
										{value: 'ast', text: 'ast'},
										{value: 'az', text: 'az'},
										{value: 'be', text: 'be'},
										{value: 'bg', text: 'bg'},
										{value: 'bn-BD', text: 'bn-BD'},
										{value: 'bn-IN', text: 'bn-IN'},
										{value: 'br', text: 'br'},
										{value: 'bs', text: 'bs'},
										{value: 'ca', text: 'ca'},
										{value: 'cs', text: 'cs'},
										{value: 'csb', text: 'csb'},
										{value: 'cy', text: 'cy'},
										{value: 'da', text: 'da'},
										{value: 'de', text: 'de'},
										{value: 'el', text: 'el'},
										{value: 'en-GB', text: 'en-GB'},
										{value: 'en-ZA', text: 'en-ZA'},
										{value: 'eo', text: 'eo'},
										{value: 'es-AR', text: 'es-AR'},
										{value: 'es-CL', text: 'es-CL'},
										{value: 'es-ES', text: 'es-ES'},
										{value: 'es-MX', text: 'es-MX'},
										{value: 'et', text: 'et'},
										{value: 'eu', text: 'eu'},
										{value: 'fa', text: 'fa'},
										{value: 'ff', text: 'ff'},
										{value: 'fi', text: 'fi'},
										{value: 'fr', text: 'fr'},
										{value: 'fy-NL', text: 'fy-NL'},
										{value: 'ga-IE', text: 'ga-IE'},
										{value: 'gd', text: 'gd'},
										{value: 'gl', text: 'gl'},
										{value: 'gu-IN', text: 'gu-IN'},
										{value: 'he', text: 'he'},
										{value: 'hi-IN', text: 'hi-IN'},
										{value: 'hr', text: 'hr'},
										{value: 'hu', text: 'hu'},
										{value: 'hy-AM', text: 'hy-AM'},
										{value: 'id', text: 'id'},
										{value: 'is', text: 'is'},
										{value: 'it', text: 'it'},
										{value: 'ja', text: 'ja'},
										{value: 'ka', text: 'ka'},
										{value: 'kk', text: 'kk'},
										{value: 'km', text: 'km'},
										{value: 'kn', text: 'kn'},
										{value: 'ko', text: 'ko'},
										{value: 'ku', text: 'ku'},
										{value: 'lg', text: 'lg'},
										{value: 'lij', text: 'lij'},
										{value: 'lt', text: 'lt'},
										{value: 'lv', text: 'lv'},
										{value: 'mai', text: 'mai'},
										{value: 'mk', text: 'mk'},
										{value: 'ml', text: 'ml'},
										{value: 'mn', text: 'mn'},
										{value: 'mr', text: 'mr'},
										{value: 'ms', text: 'ms'},
										{value: 'my', text: 'my'},
										{value: 'nb-NO', text: 'nb-NO'},
										{value: 'nl', text: 'nl'},
										{value: 'nn-NO', text: 'nn-NO'},
										{value: 'nso', text: 'nso'},
										{value: 'oc', text: 'oc'},
										{value: 'or', text: 'or'},
										{value: 'pa-IN', text: 'pa-IN'},
										{value: 'pl', text: 'pl'},
										{value: 'pt-BR', text: 'pt-BR'},
										{value: 'pt-PT', text: 'pt-PT'},
										{value: 'rm', text: 'rm'},
										{value: 'ro', text: 'ro'},
										{value: 'ru', text: 'ru'},
										{value: 'rw', text: 'rw'},
										{value: 'sah', text: 'sah'},
										{value: 'si', text: 'si'},
										{value: 'sk', text: 'sk'},
										{value: 'sl', text: 'sl'},
										{value: 'son', text: 'son'},
										{value: 'sq', text: 'sq'},
										{value: 'sr', text: 'sr'},
										{value: 'sv-SE', text: 'sv-SE'},
										{value: 'sw', text: 'sw'},
										{value: 'ta', text: 'ta'},
										{value: 'ta-LK', text: 'ta-LK'},
										{value: 'te', text: 'te'},
										{value: 'th', text: 'th'},
										{value: 'tl', text: 'tl'},
										{value: 'tn', text: 'tn'},
										{value: 'tr', text: 'tr'},
										{value: 'uk', text: 'uk'},
										{value: 'ur', text: 'ur'},
										{value: 'vi', text: 'vi'},
										{value: 'wo', text: 'wo'},
										{value: 'xh', text: 'xh'},
										{value: 'zh-CN', text: 'zh-CN'},
										{value: 'zh-TW', text: 'zh-TW'},
										{value: 'zu', text: 'zu'},
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tnc-pdf-viewer-iframe file="' + e.data.file + '" width="' + e.data.iframewidth + '" height="' + e.data.iframeheight + '" download="' + e.data.download + '" print="' + e.data.print + '" fullscreen="' + e.data.fullscreen + '" share="' + e.data.share + '" zoom="' + e.data.zoom + '" open="' + e.data.open + '" pagenav="' + e.data.pagenav + '" logo="' + e.data.logo + '" find="' + e.data.find + '" language="' + e.data.v_language + '" page="' + e.data.jumptopage + '" default_zoom="' + e.data.defaultzoom + '" pagemode="' + e.data.pagemode + '"]');
							}
						});
					}
				},

				// Shortlink Menu
				{
					text: 'Shortlink',
					onclick: function() {
						editor.windowManager.open( {
							classes: 'tnc_scroll',
							title: 'Insert PDF Viewer Shortlink Shortcode',
							body: [
								{
									type: 'filepicker',
									filetype: 'pdf',
									name: 'file',
									label: 'File url',
									classes: 'header_logo'
								},
								{
									type: 'textbox',
									name: 'linkclass',
									label: 'Link Class',
									classes: 'col-sm-4'
								},
								{
									type: 'textbox',
									name: 'linktext',
									label: 'Link Text',
									classes: 'col-sm-4'
								},
								{
									type: 'listbox',
									name: 'linktarget',
									label: 'Link Target',
									'values': [
										{text: 'Same Tab', value: '_parent'},
										{text: 'New Tab', value: '_blank'}
									]
								},
								{
									type: 'textbox',
									name: 'jumptopage',
									label: 'Jump to Page'
								},
								{
									type: 'listbox',
									name: 'defaultzoom',
									label: 'Default Zoom',
									'values': [
										{text: 'Auto', value: 'auto'},
										{text: 'Page Fit', value: 'page-fit'},
										{text: 'Page Width', value: 'page-width'},
										{text: 'Page Height', value: 'page-height'},
										{text: '75%', value: '75'},
										{text: '100%', value: '100'},
										{text: '150%', value: '150'},
										{text: '200%', value: '200'},
									]
								},
								{
									type: 'listbox',
									name: 'pagemode',
									label: 'Page Mode',
									'values': [
										{text: 'Default', value: ''},
										{text: 'Thumbnails', value: 'thumbs'},
										{text: 'Bookmarks', value: 'bookmarks'}
									]
								},
								{
									type: 'listbox',
									name: 'v_language',
									label: 'Language',
									'values': [
										{value: 'en-US', text: 'en-US'},
										{value: 'ach', text: 'ach'},
										{value: 'af', text: 'af'},
										{value: 'ak', text: 'ak'},
										{value: 'an', text: 'an'},
										{value: 'ar', text: 'ar'},
										{value: 'as', text: 'as'},
										{value: 'ast', text: 'ast'},
										{value: 'az', text: 'az'},
										{value: 'be', text: 'be'},
										{value: 'bg', text: 'bg'},
										{value: 'bn-BD', text: 'bn-BD'},
										{value: 'bn-IN', text: 'bn-IN'},
										{value: 'br', text: 'br'},
										{value: 'bs', text: 'bs'},
										{value: 'ca', text: 'ca'},
										{value: 'cs', text: 'cs'},
										{value: 'csb', text: 'csb'},
										{value: 'cy', text: 'cy'},
										{value: 'da', text: 'da'},
										{value: 'de', text: 'de'},
										{value: 'el', text: 'el'},
										{value: 'en-GB', text: 'en-GB'},
										{value: 'en-ZA', text: 'en-ZA'},
										{value: 'eo', text: 'eo'},
										{value: 'es-AR', text: 'es-AR'},
										{value: 'es-CL', text: 'es-CL'},
										{value: 'es-ES', text: 'es-ES'},
										{value: 'es-MX', text: 'es-MX'},
										{value: 'et', text: 'et'},
										{value: 'eu', text: 'eu'},
										{value: 'fa', text: 'fa'},
										{value: 'ff', text: 'ff'},
										{value: 'fi', text: 'fi'},
										{value: 'fr', text: 'fr'},
										{value: 'fy-NL', text: 'fy-NL'},
										{value: 'ga-IE', text: 'ga-IE'},
										{value: 'gd', text: 'gd'},
										{value: 'gl', text: 'gl'},
										{value: 'gu-IN', text: 'gu-IN'},
										{value: 'he', text: 'he'},
										{value: 'hi-IN', text: 'hi-IN'},
										{value: 'hr', text: 'hr'},
										{value: 'hu', text: 'hu'},
										{value: 'hy-AM', text: 'hy-AM'},
										{value: 'id', text: 'id'},
										{value: 'is', text: 'is'},
										{value: 'it', text: 'it'},
										{value: 'ja', text: 'ja'},
										{value: 'ka', text: 'ka'},
										{value: 'kk', text: 'kk'},
										{value: 'km', text: 'km'},
										{value: 'kn', text: 'kn'},
										{value: 'ko', text: 'ko'},
										{value: 'ku', text: 'ku'},
										{value: 'lg', text: 'lg'},
										{value: 'lij', text: 'lij'},
										{value: 'lt', text: 'lt'},
										{value: 'lv', text: 'lv'},
										{value: 'mai', text: 'mai'},
										{value: 'mk', text: 'mk'},
										{value: 'ml', text: 'ml'},
										{value: 'mn', text: 'mn'},
										{value: 'mr', text: 'mr'},
										{value: 'ms', text: 'ms'},
										{value: 'my', text: 'my'},
										{value: 'nb-NO', text: 'nb-NO'},
										{value: 'nl', text: 'nl'},
										{value: 'nn-NO', text: 'nn-NO'},
										{value: 'nso', text: 'nso'},
										{value: 'oc', text: 'oc'},
										{value: 'or', text: 'or'},
										{value: 'pa-IN', text: 'pa-IN'},
										{value: 'pl', text: 'pl'},
										{value: 'pt-BR', text: 'pt-BR'},
										{value: 'pt-PT', text: 'pt-PT'},
										{value: 'rm', text: 'rm'},
										{value: 'ro', text: 'ro'},
										{value: 'ru', text: 'ru'},
										{value: 'rw', text: 'rw'},
										{value: 'sah', text: 'sah'},
										{value: 'si', text: 'si'},
										{value: 'sk', text: 'sk'},
										{value: 'sl', text: 'sl'},
										{value: 'son', text: 'son'},
										{value: 'sq', text: 'sq'},
										{value: 'sr', text: 'sr'},
										{value: 'sv-SE', text: 'sv-SE'},
										{value: 'sw', text: 'sw'},
										{value: 'ta', text: 'ta'},
										{value: 'ta-LK', text: 'ta-LK'},
										{value: 'te', text: 'te'},
										{value: 'th', text: 'th'},
										{value: 'tl', text: 'tl'},
										{value: 'tn', text: 'tn'},
										{value: 'tr', text: 'tr'},
										{value: 'uk', text: 'uk'},
										{value: 'ur', text: 'ur'},
										{value: 'vi', text: 'vi'},
										{value: 'wo', text: 'wo'},
										{value: 'xh', text: 'xh'},
										{value: 'zh-CN', text: 'zh-CN'},
										{value: 'zh-TW', text: 'zh-TW'},
										{value: 'zu', text: 'zu'},
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tnc-pdf-viewer-shortlink file="' + e.data.file + '" language="' + e.data.v_language + '" class="' + e.data.linkclass + '" text="' + e.data.linktext + '" page="' + e.data.jumptopage + '" target="'+ e.data.linktarget +'" default_zoom="' + e.data.defaultzoom + '" pagemode="' + e.data.pagemode + '"]');
							}
						});
					}
				},

				// Raw Link Menu
				{
					text: 'Raw Link',
					onclick: function() {
						editor.windowManager.open( {
							classes: 'tnc_scroll',
							title: 'Insert Raw Link Shortcode',
							body: [
								{
									type: 'filepicker',
									filetype: 'pdf',
									name: 'file',
									label: 'File url'
								},
								{
									type: 'listbox',
									name: 'download',
									label: 'Download',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'print',
									label: 'Print',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'fullscreen',
									label: 'Fullscreen',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'share',
									label: 'Share',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'zoom',
									label: 'Zoom',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'open',
									label: 'Open',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'logo',
									label: 'Logo',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'pagenav',
									label: 'Pagenav',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'find',
									label: 'Find',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'textbox',
									name: 'jumptopage',
									label: 'Jump to Page'
								},
								{
									type: 'listbox',
									name: 'defaultzoom',
									label: 'Default Zoom',
									'values': [
										{text: 'Auto', value: 'auto'},
										{text: 'Page Fit', value: 'page-fit'},
										{text: 'Page Width', value: 'page-width'},
										{text: 'Page Height', value: 'page-height'},
										{text: '75%', value: '75'},
										{text: '100%', value: '100'},
										{text: '150%', value: '150'},
										{text: '200%', value: '200'},
									]
								},
								{
									type: 'listbox',
									name: 'pagemode',
									label: 'Page Mode',
									'values': [
										{text: 'Default', value: ''},
										{text: 'Thumbnails', value: 'thumbs'},
										{text: 'Bookmarks', value: 'bookmarks'}
									]
								},
								{
									type: 'listbox',
									name: 'v_language',
									label: 'Language',
									'values': [
										{value: 'en-US', text: 'en-US'},
										{value: 'ach', text: 'ach'},
										{value: 'af', text: 'af'},
										{value: 'ak', text: 'ak'},
										{value: 'an', text: 'an'},
										{value: 'ar', text: 'ar'},
										{value: 'as', text: 'as'},
										{value: 'ast', text: 'ast'},
										{value: 'az', text: 'az'},
										{value: 'be', text: 'be'},
										{value: 'bg', text: 'bg'},
										{value: 'bn-BD', text: 'bn-BD'},
										{value: 'bn-IN', text: 'bn-IN'},
										{value: 'br', text: 'br'},
										{value: 'bs', text: 'bs'},
										{value: 'ca', text: 'ca'},
										{value: 'cs', text: 'cs'},
										{value: 'csb', text: 'csb'},
										{value: 'cy', text: 'cy'},
										{value: 'da', text: 'da'},
										{value: 'de', text: 'de'},
										{value: 'el', text: 'el'},
										{value: 'en-GB', text: 'en-GB'},
										{value: 'en-ZA', text: 'en-ZA'},
										{value: 'eo', text: 'eo'},
										{value: 'es-AR', text: 'es-AR'},
										{value: 'es-CL', text: 'es-CL'},
										{value: 'es-ES', text: 'es-ES'},
										{value: 'es-MX', text: 'es-MX'},
										{value: 'et', text: 'et'},
										{value: 'eu', text: 'eu'},
										{value: 'fa', text: 'fa'},
										{value: 'ff', text: 'ff'},
										{value: 'fi', text: 'fi'},
										{value: 'fr', text: 'fr'},
										{value: 'fy-NL', text: 'fy-NL'},
										{value: 'ga-IE', text: 'ga-IE'},
										{value: 'gd', text: 'gd'},
										{value: 'gl', text: 'gl'},
										{value: 'gu-IN', text: 'gu-IN'},
										{value: 'he', text: 'he'},
										{value: 'hi-IN', text: 'hi-IN'},
										{value: 'hr', text: 'hr'},
										{value: 'hu', text: 'hu'},
										{value: 'hy-AM', text: 'hy-AM'},
										{value: 'id', text: 'id'},
										{value: 'is', text: 'is'},
										{value: 'it', text: 'it'},
										{value: 'ja', text: 'ja'},
										{value: 'ka', text: 'ka'},
										{value: 'kk', text: 'kk'},
										{value: 'km', text: 'km'},
										{value: 'kn', text: 'kn'},
										{value: 'ko', text: 'ko'},
										{value: 'ku', text: 'ku'},
										{value: 'lg', text: 'lg'},
										{value: 'lij', text: 'lij'},
										{value: 'lt', text: 'lt'},
										{value: 'lv', text: 'lv'},
										{value: 'mai', text: 'mai'},
										{value: 'mk', text: 'mk'},
										{value: 'ml', text: 'ml'},
										{value: 'mn', text: 'mn'},
										{value: 'mr', text: 'mr'},
										{value: 'ms', text: 'ms'},
										{value: 'my', text: 'my'},
										{value: 'nb-NO', text: 'nb-NO'},
										{value: 'nl', text: 'nl'},
										{value: 'nn-NO', text: 'nn-NO'},
										{value: 'nso', text: 'nso'},
										{value: 'oc', text: 'oc'},
										{value: 'or', text: 'or'},
										{value: 'pa-IN', text: 'pa-IN'},
										{value: 'pl', text: 'pl'},
										{value: 'pt-BR', text: 'pt-BR'},
										{value: 'pt-PT', text: 'pt-PT'},
										{value: 'rm', text: 'rm'},
										{value: 'ro', text: 'ro'},
										{value: 'ru', text: 'ru'},
										{value: 'rw', text: 'rw'},
										{value: 'sah', text: 'sah'},
										{value: 'si', text: 'si'},
										{value: 'sk', text: 'sk'},
										{value: 'sl', text: 'sl'},
										{value: 'son', text: 'son'},
										{value: 'sq', text: 'sq'},
										{value: 'sr', text: 'sr'},
										{value: 'sv-SE', text: 'sv-SE'},
										{value: 'sw', text: 'sw'},
										{value: 'ta', text: 'ta'},
										{value: 'ta-LK', text: 'ta-LK'},
										{value: 'te', text: 'te'},
										{value: 'th', text: 'th'},
										{value: 'tl', text: 'tl'},
										{value: 'tn', text: 'tn'},
										{value: 'tr', text: 'tr'},
										{value: 'uk', text: 'uk'},
										{value: 'ur', text: 'ur'},
										{value: 'vi', text: 'vi'},
										{value: 'wo', text: 'wo'},
										{value: 'xh', text: 'xh'},
										{value: 'zh-CN', text: 'zh-CN'},
										{value: 'zh-TW', text: 'zh-TW'},
										{value: 'zu', text: 'zu'},
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tnc-pdf-viewer-raw-link file="' + e.data.file + '" download="' + e.data.download + '" print="' + e.data.print + '" fullscreen="' + e.data.fullscreen + '" share="' + e.data.share + '" zoom="' + e.data.zoom + '" open="' + e.data.open + '" pagenav="' + e.data.pagenav + '" logo="' + e.data.logo + '" find="' + e.data.find + '" language="' + e.data.v_language + '" page="' + e.data.jumptopage + '" default_zoom="' + e.data.defaultzoom + '" pagemode="' + e.data.pagemode + '"]');
							}
						});
					}
				},

				// Thumbnail Shortcode
				{
					text: 'Thumbnail/Image Link',
					onclick: function() {
						editor.windowManager.open( {
							classes: 'tnc_scroll',
							title: 'Insert PDF Viewer Image Link Shortcode',
							body: [
								{
									type: 'filepicker',
									filetype: 'pdf',
									name: 'file',
									label: 'File url',
									classes: 'header_logo'
								},
								{
									type: 'textbox',
									name: 'linkclass',
									label: 'Link Class',
									classes: 'col-sm-4'
								},
								{
									type: 'listbox',
									name: 'linktarget',
									label: 'Link Target',
									'values': [
										{text: 'Same Tab', value: '_parent'},
										{text: 'New Tab', value: '_blank'}
									]
								},
								{
									type: 'listbox',
									name: 'download',
									label: 'Download',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'print',
									label: 'Print',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'fullscreen',
									label: 'Fullscreen',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'share',
									label: 'Share',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'zoom',
									label: 'Zoom',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'open',
									label: 'Open',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'logo',
									label: 'Logo',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'pagenav',
									label: 'Pagenav',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'listbox',
									name: 'find',
									label: 'Find',
									'values': [
										{text: 'Show', value: 'true'},
										{text: 'Hide', value: 'false'}
									]
								},
								{
									type: 'textbox',
									name: 'jumptopage',
									label: 'Jump to Page'
								},
								{
									type: 'listbox',
									name: 'defaultzoom',
									label: 'Default Zoom',
									'values': [
										{text: 'Auto', value: 'auto'},
										{text: 'Page Fit', value: 'page-fit'},
										{text: 'Page Width', value: 'page-width'},
										{text: 'Page Height', value: 'page-height'},
										{text: '75%', value: '75'},
										{text: '100%', value: '100'},
										{text: '150%', value: '150'},
										{text: '200%', value: '200'},
									]
								},
								{
									type: 'listbox',
									name: 'pagemode',
									label: 'Page Mode',
									'values': [
										{text: 'Default', value: ''},
										{text: 'Thumbnails', value: 'thumbs'},
										{text: 'Bookmarks', value: 'bookmarks'}
									]
								},
								{
									type: 'listbox',
									name: 'v_language',
									label: 'Language',
									'values': [
										{value: 'en-US', text: 'en-US'},
										{value: 'ach', text: 'ach'},
										{value: 'af', text: 'af'},
										{value: 'ak', text: 'ak'},
										{value: 'an', text: 'an'},
										{value: 'ar', text: 'ar'},
										{value: 'as', text: 'as'},
										{value: 'ast', text: 'ast'},
										{value: 'az', text: 'az'},
										{value: 'be', text: 'be'},
										{value: 'bg', text: 'bg'},
										{value: 'bn-BD', text: 'bn-BD'},
										{value: 'bn-IN', text: 'bn-IN'},
										{value: 'br', text: 'br'},
										{value: 'bs', text: 'bs'},
										{value: 'ca', text: 'ca'},
										{value: 'cs', text: 'cs'},
										{value: 'csb', text: 'csb'},
										{value: 'cy', text: 'cy'},
										{value: 'da', text: 'da'},
										{value: 'de', text: 'de'},
										{value: 'el', text: 'el'},
										{value: 'en-GB', text: 'en-GB'},
										{value: 'en-ZA', text: 'en-ZA'},
										{value: 'eo', text: 'eo'},
										{value: 'es-AR', text: 'es-AR'},
										{value: 'es-CL', text: 'es-CL'},
										{value: 'es-ES', text: 'es-ES'},
										{value: 'es-MX', text: 'es-MX'},
										{value: 'et', text: 'et'},
										{value: 'eu', text: 'eu'},
										{value: 'fa', text: 'fa'},
										{value: 'ff', text: 'ff'},
										{value: 'fi', text: 'fi'},
										{value: 'fr', text: 'fr'},
										{value: 'fy-NL', text: 'fy-NL'},
										{value: 'ga-IE', text: 'ga-IE'},
										{value: 'gd', text: 'gd'},
										{value: 'gl', text: 'gl'},
										{value: 'gu-IN', text: 'gu-IN'},
										{value: 'he', text: 'he'},
										{value: 'hi-IN', text: 'hi-IN'},
										{value: 'hr', text: 'hr'},
										{value: 'hu', text: 'hu'},
										{value: 'hy-AM', text: 'hy-AM'},
										{value: 'id', text: 'id'},
										{value: 'is', text: 'is'},
										{value: 'it', text: 'it'},
										{value: 'ja', text: 'ja'},
										{value: 'ka', text: 'ka'},
										{value: 'kk', text: 'kk'},
										{value: 'km', text: 'km'},
										{value: 'kn', text: 'kn'},
										{value: 'ko', text: 'ko'},
										{value: 'ku', text: 'ku'},
										{value: 'lg', text: 'lg'},
										{value: 'lij', text: 'lij'},
										{value: 'lt', text: 'lt'},
										{value: 'lv', text: 'lv'},
										{value: 'mai', text: 'mai'},
										{value: 'mk', text: 'mk'},
										{value: 'ml', text: 'ml'},
										{value: 'mn', text: 'mn'},
										{value: 'mr', text: 'mr'},
										{value: 'ms', text: 'ms'},
										{value: 'my', text: 'my'},
										{value: 'nb-NO', text: 'nb-NO'},
										{value: 'nl', text: 'nl'},
										{value: 'nn-NO', text: 'nn-NO'},
										{value: 'nso', text: 'nso'},
										{value: 'oc', text: 'oc'},
										{value: 'or', text: 'or'},
										{value: 'pa-IN', text: 'pa-IN'},
										{value: 'pl', text: 'pl'},
										{value: 'pt-BR', text: 'pt-BR'},
										{value: 'pt-PT', text: 'pt-PT'},
										{value: 'rm', text: 'rm'},
										{value: 'ro', text: 'ro'},
										{value: 'ru', text: 'ru'},
										{value: 'rw', text: 'rw'},
										{value: 'sah', text: 'sah'},
										{value: 'si', text: 'si'},
										{value: 'sk', text: 'sk'},
										{value: 'sl', text: 'sl'},
										{value: 'son', text: 'son'},
										{value: 'sq', text: 'sq'},
										{value: 'sr', text: 'sr'},
										{value: 'sv-SE', text: 'sv-SE'},
										{value: 'sw', text: 'sw'},
										{value: 'ta', text: 'ta'},
										{value: 'ta-LK', text: 'ta-LK'},
										{value: 'te', text: 'te'},
										{value: 'th', text: 'th'},
										{value: 'tl', text: 'tl'},
										{value: 'tn', text: 'tn'},
										{value: 'tr', text: 'tr'},
										{value: 'uk', text: 'uk'},
										{value: 'ur', text: 'ur'},
										{value: 'vi', text: 'vi'},
										{value: 'wo', text: 'wo'},
										{value: 'xh', text: 'xh'},
										{value: 'zh-CN', text: 'zh-CN'},
										{value: 'zh-TW', text: 'zh-TW'},
										{value: 'zu', text: 'zu'},
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tnc-pdf-viewer-image file="' + e.data.file + '" target="' + e.data.linktarget + '" download="' + e.data.download + '" print="' + e.data.print + '" fullscreen="' + e.data.fullscreen + '" share="' + e.data.share + '" zoom="' + e.data.zoom + '" open="' + e.data.open + '" pagenav="' + e.data.pagenav + '" logo="' + e.data.logo + '" find="' + e.data.find + '" language="' + e.data.v_language + '" class="' + e.data.linkclass + '" page="' + e.data.jumptopage + '" default_zoom="' + e.data.defaultzoom + '" pagemode="' + e.data.pagemode + '"]PLACE YOUR IMG TAG HERE[/tnc-pdf-viewer-image]');
							}
						});
					}
				},
			]
		});
	});
})();