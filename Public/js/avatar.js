KindEditor.ready(function(K) {
	var uploadbutton = K.uploadbutton({
		button : K('#uploadButton')[0],
		fieldName : 'imgFile',
		url : K('#uploadButton').attr('upload_url'),
		afterUpload : function(data) {
			if (data.status === 0) {
				upload_load(0);
				alert(data.message);
			}else{
				var html = '<img id="avatar_image" src="'+data.avatar+'?'+Math.random()+'" />';
				var aspectRatio,box_size,thumb_w,thumb_h;
				box_size = 350;
				$('#avatar_perview_area').html(html);
				$('#avatar_perview_area').show();
				$('#upload-area').hide();
			   // $('#face_image').load(function(){
					if(data.width == data.height){
						aspectRatio = 1;
					}else{
						if(data.width>data.height){
							aspectRatio = data.width/box_size;
						}
						if(data.height>data.width){
							aspectRatio = data.height/box_size;
						}
					}
					thumb_w = data.width/aspectRatio;
					thumb_h = data.height/aspectRatio;
					$('#aspectRatio').val(aspectRatio);
					$('#avatar_image').attr('width',thumb_w);
					$('#avatar_image').attr('height',thumb_h);
						//$("#face_image").VMiddleImg({"width":data.width/test,"height":data.height/test});
					$('#avatar_src').val(data.avatar);
					$('.crop img').show();
					$('#crop-preview-100,#crop-preview-60').attr('src',data.avatar+'?'+Math.random());																		
					Jcrop($('#avatar_image'));
					var m_left = (box_size-thumb_w)/2;
					var m_top =  (box_size-thumb_h)/2;
					$('#avatar_perview_area').css({'padding-left':m_left,'width':box_size-m_left});
					$('#avatar_perview_area').css({'padding-top':m_top,'height':box_size-m_top});
				//});										
			}
		},
	});
	uploadbutton.fileBox.change(function(e) {
		uploadbutton.submit();
		upload_load(1);						
	});
});

function upload_load(flag){
	if(flag == 1){
		$('.upload-area').hide();
		$('#upload-load').show();
	}else{
		$('.upload-area').show();
		$('#upload-load').hide();
	}
}
function reset_upload(){
	$('#upload-area').show();
	$('#avatar_perview_area').hide();
	$('#avatar_src').val('');
	$('#avatar_image').empty();
	$('.jcrop-preview').removeAttr('src');
	$('.crop img').hide();
	upload_load(0);
}
function Jcrop(obj){
	var jcrop_api, boundx, boundy;
	$(obj).Jcrop({
		onChange: updatePreview,
		onSelect: updatePreview,
		aspectRatio: 1
	},function(){
		var bounds = this.getBounds();
		boundx = bounds[0];
		boundy = bounds[1];
		jcrop_api = this;
	});
	function updatePreview(c){
		if (parseInt(c.w) > 0){
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
			var big_w = 200;
			var small_w = 60;
			var rx = big_w / c.w;
			var ry = big_w / c.h;
			var tsrx = small_w / c.w;
			var tsry = small_w / c.h;
			$('#crop-preview-100').css({
				width: Math.round(rx * boundx) + 'px',
				height: Math.round(ry * boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
			$('#crop-preview-60').css({
				width: Math.round(tsrx * boundx) + 'px',
				height: Math.round(tsry * boundy) + 'px',
				marginLeft: '-' + Math.round(tsrx * c.x) + 'px',
				marginTop: '-' + Math.round(tsry * c.y) + 'px'
			});
		}
	}
}
