$(function(){
        $("#J_bar").hide();
        var h = $(window).height();
        var li =$("#J_bar").find("li");

        $(window).scroll(function(){
            if($(window).scrollTop() > 700){
                $("#J_bar").show();
                $("#J_backTop").show();
            }
            else{
                $("#J_bar").hide();
            };
        });


    (function(){
        var i = 0;
        $.post('/index.php/Api/getBrand', {}, function(db){
            var html = template('tplLi', db);
            $('#J_BrandList').append(html);
        });

    })();

    $('#J_CateCon').find('.j_itemWrap').hover(function(){$(this).addClass('cur');
    }, function(){
        $(this).removeClass('cur');
    })
    $('#J_SalesActivities').slide({mainCell:".hot-car-type-bd", titCell:".hot-car-type-hd li"});
    $('#J_MallAcc').slide({mainCell:".hot-car-type-bd", titCell:".hot-car-type-hd li"});
    $('#J_SaleLists').slide({mainCell:".bd ul", effect:"left"});
    $('#J_BannerSlider').slide({mainCell:".banner-img ul",effect:"fold", interTime:6000, autoPlay:true});
    $('#J_CarShow').slide({mainCell:".car-show-box ul", autoPage:true, effect:"left", scroll:4, vis:4});
    $('#J_CarContrast').slide({mainCell:".bd ul", autoPage:true, effect:"left", scroll:2, vis:2});
    $('.j_hotRemCar').slide({mainCell:".inner-bd ul", titCell:".inner-hd li", effect:"fold", prevCell:'.prev-btn', nextCell:'.next-btn', autoPlay:true});
    $(".j_carLife").slide({});
    $(".car_item").mouseover(function(event) {
        $('.hide').hide();
        $(this).children('.hide').show();
    });
    $(".car_item").mouseout(function(event) {
        $('.hide').hide();
    });
})

