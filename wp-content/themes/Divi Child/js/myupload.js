var site_url = jQuery('#Site_Url').val();

function uploadState(state=3) {
	jQuery('#modal_loader_text').text('In progress….');
	jQuery('#loadingModal').show();
	setTimeout(function(){ jQuery('#loadingModal').hide(); }, 3000);
	window.location.href = site_url+'?page_id=776&state='+state;
} 

function loaduploads(obj,uid,range,offset){
	var site_url = jQuery('#Site_Url').val();
	jQuery('#modal_loader_text').text('In progress….');
	jQuery('#loadingModal').show();
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/loadmoreupload.php',
			type: "post",
			data : {uid:uid,range:range,offset:offset},
			success: function (res){
				jQuery('#loadingModal').hide();
				if(res!=''){
					jQuery(obj).parent().parent().append(res);
					jQuery(obj).parent().remove();
					jQuery('.upload-image a').simpleLightbox();
				}
			}
	});
}
function confirmDelUpld(uid){
	var site_url = jQuery('#Site_Url').val();
	
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/deletupload.php',
			type: "post",
			data : {uploadid:uid},
			success: function (res){
				if(res==''){
					jQuery('#loadingModalupid_'+uid).hide();
					jQuery('#updivid_'+uid).slideUp(2000).remove();
				}
			}
	});
}

function UpCapChangeUpld(uid){
	var site_url = jQuery('#Site_Url').val();
	var newcaption = jQuery('#newCap_'+uid).val();
	jQuery.ajax({
			url: site_url + '/wp-content/themes/Divi Child/ajax/edituploadcaption.php',
			type: "post",
			data : {newcaption:newcaption,uploadID:uid},
			success: function (res){
				if(res==''){
					jQuery('#EditCapupid_'+uid).hide();
					jQuery('#updivid_'+uid).find('.captxt').text(newcaption);
					window.location.href=window.location.href;
				}
			}
	});
}