jQuery('#video_embed_wrap_tab_ul li a').click(function(e){
	e.preventDefault();
	jQuery('.video_embed_active').removeClass('video_embed_active');
	jQuery(this).parent('li').addClass('video_embed_active');
	jQuery('.video_embed_wrap_tab_content > div').hide();
	jQuery('.video_embed_wrap_tab_show').removeClass('video_embed_wrap_tab_show').addClass('video_embed_wrap_tab_hide');
	jQuery('.'+jQuery(this).attr('id')).addClass('video_embed_wrap_tab_show');
	return false;
});

jQuery('.video_embed_wrap_title').click(function(){
	jQuery('.video_embed_wrap').slideToggle();
	jQuery('#arraw').toggleClass('down');
	jQuery('.video_embed_wrap_title').toggleClass('activated');
});

function video_embed_confirm(){
	var agree= confirm("Are you sure you want to delete this video embed record ?");
	if (agree)
	     return true;
	else
	     return false;
}