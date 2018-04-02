var colPickerOn = false,
	colPickerShow = false;

(function($){

function del_icon (button_this)
{
	var $tr = $(button_this).parent().parent();
	$tr.remove	();
}

function add_icon (button_this)
{
	var pluginUrl = $('#plugin-url').val();
	var ids = $(button_this).attr('id');
	var p = ids.indexOf('-');
	var id = ids.substring(4,p);
	var n=$('.sort'+id+'-item-icon').length;
	
	n++;
	$('#item-right-table-'+id).append(
'							<tr class="field-row" id="icon'+id+'-'+n+'-row">'+
'								<td style="width: 130px;">'+
'								<label for="icon'+id+'-'+n+'">Icon '+n+':</label><br />'+
'								<a href="#" style="padding: 0px; margin: 0px; top: 3px;" class="add-new-h2 itemdelicon">[Remove]</a>'+
'								</td>'+
'								<td style="width: auto; text-align: right;">'+
'									Link: <input class="sort'+id+'-item-icon" name="sort'+id+'-item-icon'+n+'-link" style="width: 175px;" value="" type="text"  />'+
'									<span style="padding-top: 20px; display: inline-block;">Image:</span> <div class="tsort-image" style="height: 50px; width:175px; float: right;"><img style="float: left; height: 20px; width: 30px;" id="sort'+id+'-item-icon'+n+'-image" /><a href="#" id="sort'+id+'-item-icon'+n+'-image-change" style="left: 120px;" class="tsort-change2">Change</a>\n' +
'										<input id="sort'+id+'-item-icon'+n+'-image-input" name="sort'+id+'-item-icon'+n+'-image" type="hidden" value="" />\n'+
'										<a href="#" id="sort'+id+'-item-icon'+n+'-image-remove" class="tsort-remove">Remove</a>\n'+
'									</div>'+
'								</td>'+
'							</tr>');
	$('.itemdelicon').click(function(e) {
		e.preventDefault();
		del_icon(this);
	});
}

function saved_alert (msg, duaration) {
	if (typeof msg == 'undefined') msg='Saved';
	if (typeof duaration == 'undefined') duaration=1500;
	var v = $('.form_result').html();
		$('.form_result').html('<div style="background-color: #FFF607; border: 1px solid #FFDD05; padding: 5px; margin-bottom: 5px;">'+msg+'</div>')
		setTimeout(function() {
			$('.form_result').fadeOut('slow', function(){
				$('.form_result').html(v);
				$('.form_result').show();
			});
		}, duaration);
}

function apply_color_plugin()
{

	$('.cw-color-picker').each(function(){
		var $this = $(this),
			id = $this.attr('rel');
 
		$this.farbtastic('#' + id);
		$this.click(function(){
			$this.show();
		});
		$('#' + id).click(function(e){
			e.preventDefault();
			$('.cw-color-picker:visible').hide();
			$('#' + id + '-picker').show();
			colPickerOn = true;
			colPickerShow = true;
			return false;
		});
		$this.click(function(){
			colPickerShow = true;	
		});
		
	});
	$('body').click(function(){
		if(colPickerShow) colPickerShow = false;
		else {
			colPickerOn = false;
			$('.cw-color-picker:visible').hide();
		}
	});
}

function usquare_get_thumb(callback, fwd, url, w, h, gray) {
	
	if (typeof gray=='undefined') gray=0;
	url=encodeURIComponent(url);
	var sdata='action=usquare_get_thumb&url='+url+'&w='+w+'&h='+h+'&gray='+gray;
	$.ajax({
		url:"admin-ajax.php",
		type:"POST",
		data:sdata,
		success:function(result){
			callback(result, fwd);
		}
	});
}
function usquare_get_thumb_callback(url, fwd) {
	//alert('url = '+url);
	if (typeof fwd.action != 'undefined') {
		if (fwd.action == 'put_in_img') {
			$(fwd.selector).attr('src', url);
		}
	}
}

$(document).ready(function(){	
	var	pluginUrl = $('#plugin-url').val(); //,

	apply_color_plugin();
	
	$(document).on('blur', '.image_orig_field', function(){
		var name='#'+$(this).attr('name');
		var url=$(this).attr('value');
		if (url.substring(0,4)=='http') {
			var w, h;
			w=180; 
			h=125;
			usquare_get_thumb(usquare_get_thumb_callback, {action: 'put_in_img', selector: name}, url, w, h);
		}
	});

	// IMAGE UPLOAD
	var thickboxId =  '',
		thickItem = false; 
	
	// background images
	$('.cw-image-upload').click(function(e) {
		e.preventDefault();
		thickboxId = '#' + $(this).attr('id');
		formfield = $(thickboxId + '-input').attr('name');
		if (usquare_upload_type==1) {
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		} else {
			wp.media.editor.send.attachment = function(props, attachment) {
				usquare_handle_upload(2, formfield, attachment.url);
			}
			wp.media.editor.open(this);
		}
		return false;
	});

	function usquare_handle_upload(type, field, url) {
		old_upload_handler(url);
	}

	window.send_to_editor = function(html) {
		if (usquare_upload_type==1) old_upload_handler(html);
	}
	
	function old_upload_handler(url) {
	//alert(html);
		//console.log('url='+url);
		var img_pos=url.indexOf('<img');
		if (img_pos>-1) {
			url=url.substring(img_pos);
			img_pos2=url.indexOf('>');
			if (img_pos2>0) {
				url=url.substring(0, img_pos2+1);
				while (url.indexOf('\\"')>-1) url=url.replace('\\"','"');
				var $jurl=$(url);
				url = $jurl.attr('src');
			}
		}
		imgurl = url;
		$(thickboxId + '-input').val(imgurl);
		if (thickItem) {
			thickItem = false;
			var w, h;
			w=180; 
			h=125;
			usquare_get_thumb(usquare_get_thumb_callback, {action: 'put_in_img', selector: thickboxId}, imgurl, w, h);
		}
		else {
			$(thickboxId).css('background', 'url('+imgurl+') repeat');
		}
		if (usquare_upload_type==1) {
			tb_remove();
		}
	}
	
	$('.remove-image').click(function(e){
		e.preventDefault();
		$(this).parent().parent().find('input').val('');
		$(this).parent().parent().find('.cw-image-upload').css('background-image', 'url(' + pluginUrl + '/images/no_image.jpg)');
	});

	// CATEGORIES
	if ($('#cat-type').val() == 'categories') {
		$('.cat-display').show();
		$('.data_id').css('color', 'gray');
	}
	else {
		$('.category_id').css('color', 'gray');
	}
	$('#cat-type').change(function(){
		if ($(this).val() == 'months') {
			$('.cat-display').hide();
			$('.category_id').css('color', 'gray');
			$('.data_id').css('color', '');
			alert('Check the Date field of your items before you save!');
		}
		else {
			$('.cat-display').show();
			$('.data_id').css('color', 'gray');
			$('.category_id').css('color', '');
			alert('Check the Category field of your items, and pick categoryes you want to show before you save!');
		}
	});
	
	$('#cat-check-all').click(function(){
		$('.cat-name').attr('checked', true);
	});
	
	$('#cat-uncheck-all').click(function(){
		$('.cat-name').attr('checked', false);
	});
	
	
	// SORTABLE
	
	$('#usquare-sortable').sortable({
		placeholder: "tsort-placeholder"
	});
	
	//---------------------------------------------
	// usquare Sortable Actions
	//---------------------------------------------
	
	// add
	$('#tsort-add-new').click(function(e){
		e.preventDefault();
		usquareAddNew(pluginUrl);
	});
	$('#tsort-add-new2').click(function(e){
		e.preventDefault();
		usquareAddNew2(pluginUrl, 1);
	});
	$('#tsort-add-new3').click(function(e){
		e.preventDefault();
		usquareAddNew2(pluginUrl, 2);
	});

	// open item
	$('.tsort-plus').live('click', function(){
		if (!$(this).hasClass('open')) {
			$(this).addClass('open');
			$(this).html('-').css('padding', '5px 8px');
			$(this).next().next('.tsort-content').show();
		}
		else {
			$(this).removeClass('open');
			$(this).html('+').css('padding', '7px 5px');
			$(this).next().next('.tsort-content').hide();
		}
	});
	// delete
	$('.tsort-delete').live('click', function(e){
		e.preventDefault();
		$(this).parent().parent().remove();
	});
	
	$('.tsort-remove').live('click', function(e){
		e.preventDefault();
		$(this).parent().find('input').val('');
		$(this).parent().find('img').attr('src', pluginUrl + '/images/no_image.jpg');
	});
	
	
	// item images
	$('.tsort-change').live('click', function(e) {
		e.preventDefault();
		thickItem = true;
		thickboxId = '#' + $(this).parent().find('img').attr('id');
		//alert(thickboxId);
		formfield = $(thickboxId + '-input').attr('name');
		if (usquare_upload_type==1) {
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		} else {
			wp.media.editor.send.attachment = function(props, attachment) {
				usquare_handle_upload(1, formfield, attachment.url);
			}
			wp.media.editor.open(this);
		}
		return false;
	});
	$('.tsort-change2').live('click', function(e) {
		e.preventDefault();
		thickItem = true;
		thickboxId = '#' + $(this).parent().find('img').attr('id');
		//alert('image');
		formfield = $(thickboxId + '-input').attr('name');
		if (usquare_upload_type==1) {
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		} else {
			wp.media.editor.send.attachment = function(props, attachment) {
				usquare_handle_upload(1, formfield, attachment.url);
			}
			wp.media.editor.open(this);
		}
		return false;
	});
	
	// item images
	$('.tsort-start-item').live('click', function(e) {
		$('.tsort-start-item').attr('checked', false);
		$(this).attr('checked', 'checked');
	});
	
	// ----------------------------------------
	
	// AJAX subbmit
	$('#save-usquare').click(function(e){
		e.preventDefault();
		var postForm = $('#post_form').serialize();
		postForm=postForm.replace(/\&/g, '[odvoji]');
		$('#save-loader').show();
		$.ajax({
			type:'POST', 
			url: 'admin-ajax.php', 
			data:'action=usquare_save&data=' + postForm, 
			success: function(response) {
				$('#usquare_id').val(response);
				$('#save-loader').hide();
				saved_alert ();
			}
		});
	});
	
	$('#preview-usquare').click(function(e){
		e.preventDefault();
		var html = '<div id="TBct_overlay" class="TBct_overlayBG"></div>';
		html += '<div id="TBct_window" style="width:250px; margin-left:-75px; height:80px; margin-top:-40px; visibility: visible;">';
		html += '<div id="TBct_title"><div id="TBct_ajaxWindowTitle">Preview</div>';
		html += '<div id="TBct_closeAjaxWindow"><a id="TBct_closeWindowButton" title="Close" href="#"><img src="'+pluginUrl+'/images/tb-close.png" alt="Close"></a></div>';
		html += '</div>';
		html += '<div id="usquareHolder" style="margin:0 auto;">';
		html += '<img style="margin:20px 20px;" id="TBct_loader" src="'+pluginUrl+'/images/loadingAnimation.gif" />';
		html += '</div>';
		html += '<div style="clear:both;"></div></div>';
		html += '</div>';
		$('body').append(html);
		var postForm = $('#post_form').serialize();
		postForm=postForm.replace(/\&/g, '[odvoji]');
		//alert(postForm);
		$.ajax({
			type:'POST', 
			url: 'admin-ajax.php', 
			data:'action=usquare_preview&data=' + postForm, 
			success: function(response) {
				$('#TBct_loader').hide();
				$('#TBct_window').animate({width: '100%', marginLeft:'-50%', marginTop: '-250px', height: '500px'}, 500, function(){
					$('#usquareHolder').html(response);
					$('#usquareHolder').css({'overflow-y':'scroll', 'position': 'relative', 'width':'100%', 'height':'470px'});
				
					$('#preview-loader').hide();
					
					$('#TBct_closeWindowButton').click(function(ev){
						ev.preventDefault();
						$('#TBct_overlay').remove();
						$('#TBct_window').remove();
					});
				});
				
			}
		});
	});
	
	
});


function usquareSortableActions(pluginUrl) {
}

function usquareAddNew(pluginUrl) {
	usquareItem = usquareGenerateItem();
	$('#usquare-sortable').append(usquareItem);
	$('.tsort-start-item').eq($('.tsort-start-item').length-1).trigger('click').attr('checked', 'checked');
	$('.itemaddicon').click(function(e) {
		e.preventDefault();
		add_icon(this);
	});
	apply_color_plugin();
	return;
}
 
var last_itemNumber=0;
function usquareGenerateItem(properties) {
	// set globals
	var pluginUrl = $('#plugin-url').val();
	
	// calculate item number
	var itemNumber = 1;
	while($('#sort'+itemNumber).length > 0) {
		itemNumber++;
	}
	last_itemNumber=itemNumber;

	// get input properties
	var pr = $.extend({
		'itemTitle':			'Title',
		'itemContent':			'Content',
		'itemDescription':		'Description',
		'itemImage':			'',
		'background_color':		'#ef4939',
		'title_color':			'#ffffff',
		'description_color':	'#ffffff',
		'content_color':		'#ffffff',
		'info_color':			'#ffffff',
		'itemBackgroundImage':	'',
		'alt':					''
	}, properties);
	
	// bring all the pieces together
	var itemHtml = '\n'+	
'					<li id="sort'+itemNumber+'" class="sortableItem">\n'+
'						<div class="tsort-plus open" style="padding: 5px 8px;">-</div>\n'+
'						<div class="tsort-header">Item '+itemNumber+' <small><i>- '+pr.itemTitle+'</i></small> &nbsp;<a href="#" class="tsort-delete"><i>delete</i></a></div>\n'+
'						<div class="tsort-content" style="display: block;">\n'+
'							<div class="tsort-content-left">\n'+
'							<table class="fields-group">'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								<label for="title">Title:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-title" value="'+pr.itemTitle+'" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								<label for="description">Description:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-description" value="'+pr.itemDescription+'" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								<label for="image">Image:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<div class="tsort-image"><img style="height: 125px; width:180px;" id="sort'+itemNumber+'-item-image" src="'+((pr.itemImage != '') ? pr.itemImage : pluginUrl + 'images/no_image.jpg')+ '" /><a href="#" id="sort'+itemNumber+'-item-image-change" class="tsort-change">Change</a>\n' +
'										<a href="#" id="sort'+itemNumber+'-item-image-remove" class="tsort-remove">Remove</a>\n'+
'									</div>'+
'									URL: <input id="sort'+itemNumber+'-item-image-input" name="sort'+itemNumber+'-item-image" type="text" style="width: 187px;" class="image_orig_field" value="'+pr.itemImage+'" />\n'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								<label for="alt">Image alt tag:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-alt" value="'+pr.alt+'" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								<label for="content">Content:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<textarea class="tsort-contarea"  name="sort'+itemNumber+'-item-content">'+pr.itemContent+'</textarea>\n'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'								</td>'+
'								<td>'+
'									<label for="sort'+itemNumber+'-item-dont-open"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="sort'+itemNumber+'-item-dont-open" id="sort'+itemNumber+'-item-dont-open" value="1" /> Don\'t open extended content</label>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Color of title</span></span>'+
'								<label for="title-color">Title color:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<input id="sort'+itemNumber+'-item-title-color" name="sort'+itemNumber+'-item-title-color" value="'+pr.title_color+'" type="text" style="title: '+pr.title_color+';">'+
'										<div class="cw-color-picker-holder" style="left:-70px;">'+
'											<div id="sort'+itemNumber+'-item-title-color-picker" class="cw-color-picker" rel="sort'+itemNumber+'-item-title-color"></div>'+
'										</div>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Color of description</span></span>'+
'								<label for="description-color">Description color:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<input id="sort'+itemNumber+'-item-description-color" name="sort'+itemNumber+'-item-description-color" value="'+pr.description_color+'" type="text" style="description: '+pr.description_color+';">'+
'										<div class="cw-color-picker-holder" style="left:-70px;">'+
'											<div id="sort'+itemNumber+'-item-description-color-picker" class="cw-color-picker" rel="sort'+itemNumber+'-item-description-color"></div>'+
'										</div>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Color of content</span></span>'+
'								<label for="content-color">Content color:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<input id="sort'+itemNumber+'-item-content-color" name="sort'+itemNumber+'-item-content-color" value="'+pr.content_color+'" type="text" style="content: '+pr.content_color+';">'+
'										<div class="cw-color-picker-holder" style="left:-70px;">'+
'											<div id="sort'+itemNumber+'-item-content-color-picker" class="cw-color-picker" rel="sort'+itemNumber+'-item-content-color"></div>'+
'										</div>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Color of "info" label</span></span>'+
'								<label for="info-color">Info label color:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<input id="sort'+itemNumber+'-item-info-color" name="sort'+itemNumber+'-item-info-color" value="'+pr.info_color+'" type="text" style="info: '+pr.info_color+';">'+
'										<div class="cw-color-picker-holder" style="left:-70px;">'+
'											<div id="sort'+itemNumber+'-item-info-color-picker" class="cw-color-picker" rel="sort'+itemNumber+'-item-info-color"></div>'+
'										</div>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">Linking image:'+
'										</td>'+
'										<td style="text-align: right;">'+
'											<label for="sort'+itemNumber+'-item-link-image" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="sort'+itemNumber+'-item-link-image" id="sort'+itemNumber+'-item-link-image" value="1" /> &nbsp;&nbsp;Enable image linking:</label><br />'+
'<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">What link to open when someone click on image; Leave it empty if you want to open already selected item image</span></span>Link: <input name="sort'+itemNumber+'-item-link-image-url" value="" type="text" style="width: 165px;" /><br />'+
'											<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">Enter here "lightbox" (without a quotes) if you want opening in Lightbox, otherwise leave it empty</span></span> Rel: <input name="sort'+itemNumber+'-item-link-image-rel" value="" type="text" style="width: 165px;" /><br />'+
'											<span class="usquare-help" style="padding: 2px 0 0 3px; font-size: 12px;">?<span class="usquare-tooltip">Enter here "_blank" (without a quotes) if you want opening in new tab</span></span> Target: <input name="sort'+itemNumber+'-item-link-image-target" value="" type="text" style="width: 165px;" /><br />'+
'											<label for="sort'+itemNumber+'-item-link-image-opened" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="sort'+itemNumber+'-item-link-image-opened" id="sort'+itemNumber+'-item-link-image-opened" value="1" /> Link img only when item is open</label><br />'+
'											<label for="sort'+itemNumber+'-item-link-square" style="float: left;"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="sort'+itemNumber+'-item-link-square" id="sort'+itemNumber+'-item-link-square" value="1" /> Also make square linkable</label>'+
'										</td>'+
'									</tr>'+
'							</table></div>'+
'							<div class="tsort-content-right">\n'+
'							<table class="fields-group" id="item-right-table-'+itemNumber+'">'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Color of background</span></span>'+
'								<label for="background-color">Background color:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'								<input id="sort'+itemNumber+'-item-background-color" name="sort'+itemNumber+'-item-background-color" value="'+pr.background_color+'" type="text" style="background: '+pr.background_color+';">'+
'										<div class="cw-color-picker-holder" style="left:-70px;">'+
'											<div id="sort'+itemNumber+'-item-background-color-picker" class="cw-color-picker" rel="sort'+itemNumber+'-item-background-color"></div>'+
'										</div>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Image for background</span></span>'+
'								<label for="image">Background image:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<div class="tsort-image" style="height: 125px; width:216px;"><img style="height: 125px; width: 180px;" id="sort'+itemNumber+'-item-background-image" src="'+((pr.itemBackgroundImage != '') ? pr.itemBackgroundImage : pluginUrl + 'images/no_image.jpg')+ '" /><a href="#" id="sort'+itemNumber+'-item-background-image-change" class="tsort-change">Change</a>\n' +
'										<a href="#" id="sort'+itemNumber+'-item-background-image-remove" class="tsort-remove">Remove</a>\n'+
'									</div>'+
'									URL: <input id="sort'+itemNumber+'-item-background-image-input" name="sort'+itemNumber+'-item-background-image" type="text" style="width: 187px;" class="image_orig_field" value="'+pr.itemBackgroundImage+'" />\n'+
'								</td>'+
'							</tr>'+

'								<tr class="field-row">'+
'									<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Where to put main image. "Full space" will remove title and description and put image on both square.</span></span>'+
'										<label for="image-position">Image position:</label>'+
'									</td>'+
'									<td style="width: auto;">'+
'										<select name="sort'+itemNumber+'-item-image-position" >'+
'											<option value="0" selected="selected">Left</option>'+
'											<option value="1">Right</option>'+
'											<option value="2">Full space</option>'+
'										</select>'+
'									</td>'+
'								</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Some other options...</span></span><label for="options">Other options:</label>'+
'								</td>'+
'								<td>'+
'									<label for="sort'+itemNumber+'-item-dont-move"><input style="display: inline-block; width: 15px; height: 15px; clear: none; padding:0; margin:0;" type="checkbox" name="sort'+itemNumber+'-item-dont-move" id="sort'+itemNumber+'-item-dont-move" value="1" /> Don\'t move whole item up</label>'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no web page for this item</span></span>'+
'								<label for="www">Web page:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-www" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no email for this item</span></span>'+
'								<label for="email">Email:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-email" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="facebook">Facebook:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-facebook" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="twitter">Twitter:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-twitter" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="linkedin">LinkedIn:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-linkedin" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="pinterest">Pinterest:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-pinterest" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="yahoo">Yahoo:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-yahoo" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Leave it empty if there is no social account for this item</span></span>'+
'								<label for="digg">Digg:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-digg" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;"><span class="usquare-help">? <span class="usquare-tooltip">Enter here "_blank" (without a quotes) if you want icons open links in new tab</span></span>'+
'								<label for="icons-target">Icons target:</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<input name="sort'+itemNumber+'-item-icons-target" value="" type="text"  />'+
'								</td>'+
'							</tr>'+
'							<tr class="field-row">'+
'								<td style="width: 130px;">'+
'									<label for="other">Other...</label>'+
'								</td>'+
'								<td style="width: auto;">'+
'									<a id="sort'+itemNumber+'-item-add-icon" class="itemaddicon alignleft button button-highlighted" style="display:block; padding:0px 15px; margin:4px 10px;" href="#">+ Additional icons</a>'+
'								</td>'+
'							</tr>'+
'							</table></div>\n'+
'							<div class="clear"></div>'+
'						</div>\n'+
'					</li>\n';
	return itemHtml;
}

function apply_new_makethumb(itemNumber, properties) {
	//console.log()
	var myselector='#sort'+itemNumber+'-item-image';
	if (properties.itemImage!='') {
		usquare_get_thumb(usquare_get_thumb_callback, {action: 'put_in_img', selector: myselector}, properties.itemImage, 180, 125);
	}
}

var usquare_selected=0;
function usquareAddNew2(pluginUrl, type) {
	var display1, display2, win_height;
	var searches = new Array();
	usquare_selected=type;
	if (type==1) {
		display1='display: block';
		display2='display: none';
		win_height=250;
	}
	if (type==2) {
		display1='display: none';
		display2='display: block';
		win_height=80;
	}
	searches[''] = '';
	var html = '<div id="TBct_overlay" class="TBct_overlayBG"></div>';
	html += '<div id="TBct_window" style="width:450px; margin-left:-225px; margin-top:-35px; height:'+win_height+'px; visibility: visible;">';
	html += '<div id="TBct_title"><div id="TBct_ajaxWindowTitle">Add new uSquare item</div>'
	html += '<div id="TBct_closeAjaxWindow"><a id="TBct_closeWindowButton" title="Close" href="#"><img src="'+pluginUrl+'/images/tb-close.png" alt="Close"></a></div>';
	html += '</div>';
	// html += '<select id="TBct_usquareSelect" style="margin:10px; width:150px;"><option value="new">Add New</option><option value="post">From Post</option><option value="category">Whole Category</option></select>';
	html += '<div id="TBct_usquareFromPost" style="padding:10px; border-top:1px solid gray; '+display1+';"><label for="usquareFromPost">Search posts:</label> <span id="usquareFromPostHolder"><input id="usquareFromPost" name="usquareFromPost" style="width:260px;"/><a href="#" style="margin:0px;" class="button button-highlighted alignright TBct_usquareSubmit">Add</a><img id="usquareFromPostLoader" src="'+pluginUrl+'/images/ajax-loader.gif" /> <ul style="display:none;" id="usquareFromPostComplete"></ul></span>';
	html += '</div>';

	html += '<div id="TBct_usquareWholeCategory" style="padding:10px; border-top:1px solid gray; '+display2+';">';
	html += '<label for="TBct_usquareCategorySelect">Pick category:</label> <select style="width:200px" id="TBct_usquareCategorySelect" name="TBct_usquareCategorySelect">'
	var allCats = $('#categories-hidden').val();
	allCats = allCats.split('||');
	for (cate in allCats) {
		html += '<option value="'+allCats[cate]+'">'+allCats[cate]+'</option>';
	}
	
	html += '</select><a href="#" style="margin:0px;" class="button button-highlighted alignright TBct_usquareSubmit">Add</a><img id="TBct_usquareSubmitLoader" class="alignright" style="margin:4px;" src="'+pluginUrl+'/images/ajax-loader.gif" />';
	html += '</div>';
	html += '</div>';
	$('body').prepend(html);
	
	if (usquare_selected==1) $('#usquareFromPost').focus();
	

	$('#TBct_closeWindowButton').click(function(e){
		e.preventDefault();
		$('#TBct_overlay').remove();
		$('#TBct_window').remove();
	});
	
/*
	$('#TBct_usquareSelect').change(function(){
		if ($(this).val() == 'new') {
			$('#TBct_window').css({marginTop:'-35px', height:'70px'});
			$('#TBct_usquareFromPost').hide();
			$('#TBct_usquareWholeCategory').hide();
		}
		if ($(this).val() == 'category') {
			$('#TBct_window').css({marginTop:'-60px', height:'120px'});
			$('#TBct_usquareWholeCategory').show();
			$('#TBct_usquareFromPost').hide();
		}
		else {
			$('#TBct_window').css({marginTop:'-150px', height:'300px'});
			$('#TBct_usquareFromPost').show();
			$('#TBct_usquareWholeCategory').hide();
		}	
	});
*/	
	$('.TBct_usquareSubmit').click(function(e){
		e.preventDefault();
		var usquareItem = '';
/*		if ($('#TBct_usquareSelect').val() == 'new') {
			usquareItem = usquareGenerateItem();
			$('#usquare-sortable').append(usquareItem);
			$('.tsort-start-item').eq($('.tsort-start-item').length-1).trigger('click').attr('checked', 'checked');
			$('#TBct_overlay').remove();
			$('#TBct_window').remove();
		}
		else*/
		if (usquare_selected==2) {
			var sdata='action=usquare_post_category_get&cat_name='+$('#TBct_usquareCategorySelect').val();
			$('#TBct_usquareSubmitLoader').show();
			$.ajax({
				url:"admin-ajax.php",
				type:"POST",
				data:sdata,
				
				success:function(results){
					var resultsArray = results.split('||');
					var ii = 0;
					while (typeof resultsArray[0+ii] != 'undefined') {
							
						var properties = {
							'itemTitle' : resultsArray[0+ii],
							'itemContent' : resultsArray[3+ii],
							'itemImage' : resultsArray[4+ii],
							'itemDescription' : resultsArray[2+ii]
							}
						usquareItem = usquareGenerateItem(properties);
						$('#usquare-sortable').append(usquareItem);
						apply_new_makethumb(last_itemNumber, properties);
						ii +=6;
						$('.itemaddicon').click(function(e) {
							e.preventDefault();
							add_icon(this);
						});
						apply_color_plugin();
					}
					$('.tsort-start-item').eq($('.tsort-start-item').length-1).trigger('click').attr('checked', 'checked');
					$('#TBct_overlay').remove();
					$('#TBct_window').remove();
				}
			});
		}
		
/*		else if($('#usquareFromPostComplete li a.active').length < 1) {
			alert('You have to select post you want to add, or choose add new!');
		}*/
		else if (usquare_selected==1) {
			var postId = $('#usquareFromPostComplete li a.active').attr('href');
			$('#TBct_usquareSubmitLoader').show();
			$.ajax({
				url:"admin-ajax.php",
				type:"POST",
				data:'action=usquare_post_get&post_id='+postId,
				
				success:function(results){
					var resultsArray = results.split('||');
					var properties = {
						'itemTitle' : resultsArray[0],
						'itemContent' : resultsArray[3],
						'itemImage' : resultsArray[4],
						'itemDescription' : resultsArray[2]
						}
					usquareItem = usquareGenerateItem(properties);
					$('#usquare-sortable').append(usquareItem);
					apply_new_makethumb(last_itemNumber, properties);
					$('.itemaddicon').click(function(e) {
						e.preventDefault();
						add_icon(this);
					});
					apply_color_plugin();
					$('.tsort-start-item').eq($('.tsort-start-item').length-1).trigger('click').attr('checked', 'checked');
					$('#TBct_overlay').remove();
					$('#TBct_window').remove();
				}
			});
		}
		
	})
	
	$('#usquareFromPost').keyup(function(e){
		var icall = null,
			qinput = $('#usquareFromPost').val();
		
		if(qinput in searches) {
			if(icall != null) icall.abort();
			$('#usquareFromPostComplete').html(searches[qinput]).show();
			$('#usquareFromPostComplete li a').click(function(e){
				e.preventDefault();
				$('#usquareFromPostComplete li a.active').removeClass('active');
				$(this).addClass('active');
			});
			$('#usquareFromPostLoader').hide();
		}
		else {
			$('#usquareFromPostLoader').show();
			if(icall != null) icall.abort();
			icall = $.ajax({
				url:"admin-ajax.php",
				type:"POST",
				data:'action=usquare_post_search&query='+qinput,
				
				success:function(results){
					$('#usquareFromPostComplete').html(results).show();
					searches[qinput] = results;
					$('#usquareFromPostComplete li a').click(function(e){
						e.preventDefault();
						$('#usquareFromPostComplete li a.active').removeClass('active');
						$(this).addClass('active');
					});
					$('#usquareFromPostLoader').hide();
				}
			});
		}
	});
}


$(document).ready(function() {

	$('#usquare_new_jquery_checkbox').change(function(e) {e.preventDefault(); save_options_2val('use_new_jquery', '#usquare_new_jquery_checkbox', 'new_jquery_url', '#usquare_new_jquery_url', '#usquare_new_jquery_div');});
	$('#usquare_new_jquery_save').click(function(e) {e.preventDefault(); save_options_2val('use_new_jquery', '#usquare_new_jquery_checkbox', 'new_jquery_url', '#usquare_new_jquery_url', '#usquare_new_jquery_div');});
	$('#usquare_new_jquery_check').click(function(e) {e.preventDefault(); check_latest_jquery(); });

	$('#usquare_use_lightbox_checkbox').change(function(e) {e.preventDefault(); save_options_1val('use_lightbox', '#usquare_use_lightbox_checkbox');});
	$('#usquare_skip_head_section_checkbox').change(function(e) {e.preventDefault(); save_options_1val('skip_head_section', '#usquare_skip_head_section_checkbox');});
	$('#usquare_fix_encoding_checkbox').change(function(e) {e.preventDefault(); save_options_1val('fix_encoding', '#usquare_fix_encoding_checkbox');});
	$('#usquare_do_not_resize_images_checkbox').change(function(e) {e.preventDefault(); save_options_1val('do_not_resize_images', '#usquare_do_not_resize_images_checkbox');});
	$('#usquare_use_separated_jquery_checkbox').change(function(e) {e.preventDefault(); save_options_1val('use_separated_jquery', '#usquare_use_separated_jquery_checkbox');});
	
	if (typeof should_check_latest_usquare_version != 'undefined') check_latest_usquare_version(should_check_latest_usquare_version);
	if (typeof usquare_download_google_fonts != 'undefined') usquare_download_google_fonts_function();

	function put_in_elements(variant_selector, key, val) {
		//console.log('weight_selector='+weight_selector+"\n"); console.log('style_selector='+style_selector+"\n"); console.log('key='+key+"\n"); console.log('val='+val+"\n");
		$('#font-loader').show();
		var sdata={action: 'usquare_put_in_element', key: key, val: val};
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(results){
				//alert(results);
				$(variant_selector).html(results);
				$('#font-loader').hide();
			}
		});
	}

	function bind_font_change(selector_from, variant_selector) {
		$(selector_from).change(function(){
			var val = $(selector_from).val();
			put_in_elements(variant_selector, 'get_font_variants', val);
		});
	}
	
	
	bind_font_change('#title-font-name', '#title-font-variant');
	bind_font_change('#description-font-name', '#description-font-variant');
	bind_font_change('#content-font-name', '#content-font-variant');
	bind_font_change('#info-font-name', '#info-font-variant');

	$('.itemaddicon').click(function(e) {
		e.preventDefault();
		add_icon(this);
	});
	$('.itemdelicon').click(function(e) {
		e.preventDefault();
		del_icon(this);
	});

	function save_options_1val(var1, checkbox) {
		var val1;
		if ($(checkbox).is(':checked')) val1=1;
		else val1=0;
		
		//alert('var1='+var1+', val1='+val1);

		ajax_save_options_1val(var1, val1);
	}
	
	function save_options_2val(var1, checkbox, var2, editfield, div) {
		var val1;
		if ($(checkbox).is(':checked')) val1=1;
		else val1=0;

		if (val1==1)
		{
			$(div).show();
		}
		else
		{
			$(div).hide();
		}
		var val2 = $(editfield).val();
		ajax_save_options_2val(var1, val1, var2, val2);
	}

	function ajax_save_options_1val(var1, val1) {
		var sdata='action=usquare_set_settings_1val&var1='+var1+'&val1='+val1;
		// action=usquare_set_settings_1val&var1=use_separated_jquery&val1=1
		//alert(sdata);
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(results){
				saved_alert (results);
			}
		});
	}

	function ajax_save_options_2val(var1, val1, var2, val2) {
		var sdata='action=usquare_set_settings_2val&var1='+var1+'&val1='+val1+'&var2='+var2+'&val2='+val2;
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(results){
				saved_alert (results);
			}
		});
	}
	
	function check_latest_jquery() {
		var sdata='action=usquare_get_responder_answer&action2=get_latest_jquery';
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(results){
				if (results.indexOf(' ')==-1) $('#usquare_new_jquery_url').val(results);
			}
		});
	}

	function check_latest_usquare_version(ver) {
		var sdata='action=usquare_get_responder_answer&action2=check_for_update&var1='+ver;
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(results){
				if (results.length>2)
				{
					if (results.indexOf('version available')>0) {
						$('.form_result').html('<div style="background-color: #FFF607; border: 1px solid #FFDD05; padding: 5px; margin-bottom: 5px;">'+results+'</div>');
					}
				}
			}
		});
	}
	
	function usquare_download_google_fonts_function() {
		var sdata='action=usquare_download_google_fonts';
		$.ajax({
			url:"admin-ajax.php",
			type:"POST",
			data:sdata,
			success:function(result){
				//alert(result);
			}
		});
	}

});

})(jQuery);