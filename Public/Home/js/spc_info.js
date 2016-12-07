/**
 * Created by Administrator on 2016/2/23.
 */
$(document).ready(function(){
    $(".j_inquiryBtn").on("click",function(e) {
        e.stopPropagation();
        e.preventDefault();
        if ($(this).hasClass("G_btn_disabled")) {
            return false;
        }
        if (!$.G.getUSERID()) {
            HUI.PopOut.login();
            return false;
        }
        else {
            var car_id = $("#J_carDetail").data("id");
            var excolor = $(".j_excolor a").data("color");
            var incolor = $(".j_incolor a").data("color");
            var price = $("#J_inquiryFee").html();
            var city = $("select[name=shangpai]").find('option:selected').text();
            var deposit = $(".deposit").text();
            var url_id = $("#J_carDetail").data("id");

            var carinfo = {
                "car_id": car_id,
                "excolor": excolor,
                "incolor": incolor,
                "price": price,
                "city": city,
                "deposit": deposit,
                "car_special_id":url_id,
            }
            $.ajax({
                type: "post",
                url: "/index.php/Car/ajax_special_order",
                data: carinfo,
                success: function(a) {
                    if(a.status == 1 && a.next){
                        window.location.href = a.next;
                    }else if(1 !== a.status){
                        HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>'+ a.info +'</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
                    }
                },
                error: function () {
                    this.errormsg()
                }
            })
        }
    })
    
})

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

