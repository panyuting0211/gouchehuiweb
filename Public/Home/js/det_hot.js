/**
 * Created by Administrator on 2015/12/16.
 */
$(function(){
	$('.j_pos').on('click', function(event){
		event.preventDefault();
		event.stopPropagation();
		var $this = $(this),
			_href = $this.attr('href'),
			$box = $('#J_' + _href),
			_top = $box.offset().top;
			$('html, body').animate({
				scrollTop: _top
			}, 500);
		$this.addClass("active").siblings("a").removeClass("active");
	});
	
	
	$(window).scroll(function(){
		var a = $(".car_model_pic_box").offset().top,
		    b = $(".car_model_pic_box").height(),
		    c = a + b,
		    d = $(window).scrollTop(),
		    e = d - a;
        if( e > 0 && e < b){
            $(".carimg-right").css("top",e);
        }
    }); 

	
})

$(function(){
          getList(1);
    });
function getList(page) {
    $("#hotList .carbox-le-con > ul").css('display','none');
    $('#hotList .carbox-le-con > ul').empty();
    $("#hotList").prepend('<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>');
    
    $.post( "/index.php/Car/bestselling",
        	{},
            function(data,textStatus){
                $('#hotList .carbox-le-con > ul').empty();
                $("#hotList .carbox-le-con > ul").show();
                $('#hotList').find('.onload').remove();
                
				var infosale = data.sales;
                var l1div = "<div class='carbox-le-head G_f18'><i class='G_f14 G_fl'>省</i>"+infosale.discount+"万</div>";
                var l1a = "<a href='"+infosale.url+"' title='"+infosale.brand_name+'  '+infosale.car_model_name+'  '+infosale.car_name+"'><img src='"+infosale.car_model_imageurl+"'/></a>";
                var l1h3 = "<h3 class='G_f14'><a href='"+infosale.url+"' title='"+infosale.brand_name+'  '+infosale.car_model_name+'  '+infosale.car_name+"'>"+infosale.brand_name+'  '+infosale.car_model_name+"</a></h3>";
                var lip = "<p class='G_f14'>购车惠底价：<span class='G_f20'>"+infosale.low_price+"万</span></p>";
                var l1a2 = "<a href='"+infosale.url+"' class='carbox-le-link G_f12'>立即查看</a>";
                  
                $('#hotList .carbox-ac > ul').append('<li>'+l1div+l1a+l1h3+lip+l1a2+'</li>');
				
				var infohot = data.hot_model;
				$.each(infohot,function(key,val){
	                var l2a = "<a href='"+val.url+"' title='"+val.brand_name+'  '+val.car_model_name+'  '+val.car_name+"'><img src='"+val.car_model_imageurl+"'/></a>";
	                var l2h = "<h3 class='G_f14'><a href='"+val.url+"' title='"+val.brand_name+'  '+val.car_model_name+'  '+val.car_name+"'>"+val.brand_name+'  '+val.car_model_name+"</a></h3>";
	                var l2p = "<p class='G_f14'>官方指导价：<span style='text-decoration:line-through;color:#666666'>"+val.auth_price+"万</span></p>";
	                var l2a2 = "<a href='"+val.url+"' class='carbox-le-link G_f12'>查询底价</a>";
	                
	                  
	                $('#hotList .carbox-hot > ul').append('<li>'+l2a+l2h+l2p+l2a2+'</li>');
	           });
	            
		    },
            "json");
  }










