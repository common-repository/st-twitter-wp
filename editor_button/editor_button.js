(function() {
	'use strict';
	
	tinymce.create('tinymce.plugins.STtwitterShortcodeEditorButton', {
		init: function(editor, url) {
		  
			editor.addButton('STtwitterShortcode', {
				title: 'ST Twiiter',
				cmd: 'sttwitterShortcode',
				image: url + '/images/st-twitter-icon.png'
			});
			
			editor.addCommand('sttwitterShortcode', function() {
				editor.windowManager.open({
					title: 'ST Twitter | Global Setting',
					body: [
                        {
							type: 'textbox',
							name: 'title',
							label: 'Title',
							minWidth: 200
						},
						{
							type: 'textbox',
							name: 'username',
							label: 'Twitter Username',
							minWidth: 200
						},
						{
							type: 'textbox',
							name: 'count',
							label: 'Tweet Limit',
							minWidth: 50
						},
                        {
							type: 'listbox',
							name: 'template',
							label: 'Template',
							'values': [
								{text: 'Default', value: '0'}
							]
						},
                        {
							type: 'textbox',
							name: 'footer',
							label: 'Footer Text',
							minWidth: 200
						},
                        {
							type: 'listbox',
							name: 'display_fullname',
							label: 'Display FullName',
							'values': [
								{text: 'No', value: '1'},
								{text: 'Yes', value: '2'}
							]
						},
                        {
							type: 'listbox',
							name: 'display_screenname',
							label: 'Display Screen Name',
							'values': [
								{text: 'No', value: '1'},
								{text: 'Yes', value: '2'}
							]
						},
                        {
							type: 'listbox',
							name: 'display_reply',
							label: 'Display Reply',
							'values': [
								{text: 'Yes', value: '2'},
								{text: 'No', value: '1'}
							]
						},
                        {
							type: 'listbox',
							name: 'display_retweet',
							label: 'Display Retweet',
							'values': [
								{text: 'Yes', value: '2'},
								{text: 'No', value: '1'}
							]
						},
                        {
							type: 'listbox',
							name: 'display_favorite',
							label: 'Display Favorite',
							'values': [
								{text: 'Yes', value: '2'},
								{text: 'No', value: '1'}
							]
						}
					],
					onsubmit: function(e) {
					   if ( e.data.username ) {
					        var shortcode = '[STtwitter';
						
    						if (e.data.username) {
    							shortcode += ' username="' + e.data.username;
                                shortcode += '"';
    						}
    						
    						if (e.data.count) {
    							shortcode += ' count="' + e.data.count;
                                shortcode += '"';
    						}
                            
                            if (e.data.title) {
    							shortcode += ' title="' + e.data.title;
                                shortcode += '"';
    						}
                            
                            if (e.data.template) {
    							shortcode += ' template="' + e.data.template;
                                shortcode += '"';
    						}
                            
                            if (e.data.footer) {
    							shortcode += ' footer="' + e.data.footer;
                                shortcode += '"';
    						}
                            
                            if ( e.data.display_fullname ) {
    							shortcode += ' display_fullname="' + e.data.display_fullname;
                                shortcode += '"';
    						}
                            
                            if (e.data.display_screenname) {
    							shortcode += ' display_screenname="' + e.data.display_screenname;
                                shortcode += '"';
    						}
                            
                            if (e.data.display_retweet) {
    							shortcode += ' display_retweet="' + e.data.display_retweet;
                                shortcode += '"';
    						}
                            if (e.data.display_reply) {
    							shortcode += ' display_reply="' + e.data.display_reply;
                                shortcode += '"';
    						}
                            if (e.data.display_favorite) {
    							shortcode += ' display_favorite="' + e.data.display_favorite;
                                shortcode += '"';
    						}
    						shortcode += editor.selection.getContent() + ']';
    						
    						editor.execCommand('mceInsertContent', 0, shortcode);   
					   } else {
					       alert('ERORR: Twitter Username not empty !');
					   }
						
					}
				});
			});
		},
		
		getInfo: function() {
			return {
				longname:  'ST Twitter',
				author:    'Line',
				authorurl: 'http://beautiful-templates.com',
				version:   '1.0.0'
			};
		}
	});
	
	tinymce.PluginManager.add('STtwitterShortcodeEditorButton', tinymce.plugins.STtwitterShortcodeEditorButton);
}());