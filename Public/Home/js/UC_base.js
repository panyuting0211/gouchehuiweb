$(function(){
	var li = $('.UC_nav li');
	var i =$('<i></i>');
	li.append(i);
	if(li.hasClass('cur')){
		$('.UC_nav .cur').find('a').css('color', '#fff');
		$('.UC_nav .cur').find('span').css({
			"background": '#fff',
			"color": '#fa8c35',
			"margin-top":"-10px"
		});;

	}

	 var h = $("#uc_main").height();
	 if(h>560){
	 	$(".UC_nav").css({
	 	'min-height': h,
	 	'margin-bottom': '-30px'
	 });
	 }
	 
});