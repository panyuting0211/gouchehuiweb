/**
 * Created by Administrator on 2016/4/12.
 */
$(function(){
    /*hover*/
    $(".knowledge .newsbox .news").hover(function(){
        $(this).addClass("hover").siblings().removeClass("hover");
    });

    $("#J_leftTitle li").each(function(index,element){
        var item = $("#J_infolist");
        $(element).click(function(){
            $(this).addClass("hover").siblings().removeClass("hover");
            item.animate({top:"-"+index*304+"px"},300);
        })
    });


    $("#J_commentbody").find(".likebox .comments").on("click",function(){
        $("#J_commentbody").find("#J_commentbox").toggle(300);
    });
    $(".feed_friend_list").find('.f-single').on("click",".comments",function(){
        $(this).closest("li").find(".innercomment").show(300).siblings().find(".innercomment").hide(300);
    });





    !function(){
        function previewImage(file)
        {
            var MAXWIDTH  = 260;
            var MAXHEIGHT = 180;
            var imgBox = $(file).closest('.filebox').find('.imgbox');

            if (file.files && file.files[0])
            {
                imgBox.html('<img class="imghead">');
                var img = imgBox.find('.imghead').get(0);

                img.onload = function(){
                    var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                    img.width  =  rect.width;
                    img.height =  rect.height;
//                 img.style.marginLeft = rect.left+'px';
                    img.style.marginTop = rect.top+'px';
                }
                var reader = new FileReader();
                reader.onload = function(evt){img.src = evt.target.result;}
                reader.readAsDataURL(file.files[0]);
            }
            else //兼容IE
            {
                var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
                file.select();
                var src = document.selection.createRange().text;
                div.innerHTML = '<img id=imghead>';
                var img = document.getElementById('imghead');
                img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
                var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
                div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
            }
        }
        function clacImgZoomParam( maxWidth, maxHeight, width, height ){
            var param = {top:0, left:0, width:width, height:height};
            if( width>maxWidth || height>maxHeight )
            {
                rateWidth = width / maxWidth;
                rateHeight = height / maxHeight;

                if( rateWidth > rateHeight )
                {
                    param.width =  maxWidth;
                    param.height = Math.round(height / rateWidth);
                }else
                {
                    param.width = Math.round(width / rateHeight);
                    param.height = maxHeight;
                }
            }

            param.left = Math.round((maxWidth - param.width) / 2);
            param.top = Math.round((maxHeight - param.height) / 2);
            return param;
        }
        $('input[name="cardpic_front"]').change(function(){
            previewImage($(this).get(0));
        });
        $('input[name="cardpic_back"]').change(function(){
            previewImage($(this).get(0));
        });
    }()


});