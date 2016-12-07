requirejs.config({
    baseUrl: '/Public/Home/js', 
    paths : {  
        'jquery': 'lib/jquery-1.8.1.min',
        'jquery.cxselect' : 'app/jquery.cxselect',
        'jquery.datetimepicker' : 'app/jquery.datetimepicker',
        'jquery.validate' : 'app/jquery.validate'
    },
    map: {
        '*': {
            'css': 'app/css'
        }
    },
    shim: {  
        'jquery.datetimepicker': ['jquery', 'css!jquery.datetimepicker'],
        'jquery.validate': ['jquery']
    }
});


/* 模板生成 */
requirejs(['jquery', 'app/tpl'], function($, tpl){
    var $baseUserInfo = $("#J_BaseUserInfo"),
        $mycar = $("#J_MyCar"),
        $buyCarPlan = $("#J_BuyCarPlan"),
        $attenCar = $("#J_AttenCar"),
        $loader = $('<div class="uc_loading"><i></i>数据加载中...</div>'),
        nullBox = function(arr){
            return '<div class="car_box null_box">' +
                        '<div class="car_face">' +
                            '<a class="noface" href="'+ arr[2] 
                            +'">' +
                                '<span class="horizontal_line"></span>' +
                                '<span class="vertical_line"></span>' +
                                '</a>' +
                        '</div>' +
                        '<div class="car_info">' +
                            '<div class="null_info">' +
                                '<h4 class="G_f26">'+ arr[0]
                                 +'</h4>' +
                                '<p class="G_f16">'+ arr[1]
                                 +'</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
        };
    
    //统一添加loading
    $baseUserInfo.find(".uc_loading").length || ($baseUserInfo.append($loader));
    $mycar.find(".uc_loading").length || ($mycar.append($loader));
    $buyCarPlan.find(".uc_loading").length || ($buyCarPlan.append($loader));
    $attenCar.find(".uc_loading").length || ($attenCar.append($loader));

    $leftUserInfo.on("load", function(e, db){
        var html = tpl('tplLeftUserInfo', db);
        $leftUserInfo.html(html);
    });

    $baseUserInfo.on("load", function(e, db){
        db.percentage = (parseFloat(db.total_jifen / db.denominator)*100).toString() + "%";
        var html = tpl('tplBaseUserInfo', db),
            $PunchBtn = null,
            isPunched = 0,
            punchCard = function(){
                var _punchBtn = $("#J_PunchTheClock");
                _punchBtn.unbind("click");
                $.ajax({
                        url: '/index.php/MemberGeneral/everyday',
                        type: 'POST',
                        success: function (data) {
                            if (data.status == 1) {
                                var $myCoin = $(".j_myCoin"),
                                 money = $myCoin.html();
                                if (!isNaN(money)) {
                                    $myCoin.html(parseInt(money) + 5);
                                }
                                
                                _punchBtn.html('今日已领取<span>+5</span>');
                                _punchBtn.find("span").css("top", "0px").animate({ top: '-31px', opacity: '0' }, 1000, function () {
                                _punchBtn.attr("class", "pushed").html('今日已领取');

                                });
                                _punchBtn.attr("data-punchstatus", "1");
                            }
                            else if(data.status == 2){
                                _punchBtn.html('立刻领取5车币');
                                _punchBtn.attr("data-punchstatus", "2");
                                _punchBtn.bind("click", PunchCard);
                                alert('打卡失败');
                            }
                        }
                    });
            };

        $baseUserInfo.find('.uc_loading').remove();
        $baseUserInfo.html(html);
        $PunchBtn = $("#J_PunchTheClock");
        isPunched = parseInt($PunchBtn.data("punchstatus"));
        if (isPunched == 1) {
            $PunchBtn.attr("class", "pushed").html('今日已领取');
        }
        else if(isPunched == 2){
            $PunchBtn.html('今日打卡<span>+5</span>分');
            $PunchBtn.bind("click", punchCard);
        }
    });


    $.post("/index.php/MemberGeneral/member_accinfo", function(db){
        $leftUserInfo.trigger("load", db);
        $baseUserInfo.trigger("load", db);
    });

    $.post("/index.php/MemberGeneral/carlove", function(db){
        var html = null;
        $mycar.find('.uc_loading').remove();
        if(db['list'].length){
            html = tpl('tplMyCar', db);
        }else{
            html = nullBox(["已经有车了？快告诉我们吧！", "添加你的爱车，享受购车惠提供的优质服务。", "n_uc_garage.html"]);
        }  
        $mycar.html(html); 
    });

$.post("/index.php/MemberGeneral/carplan", function(db){
        var html = null;
        $buyCarPlan.find('.uc_loading').remove();
        if(db.length){
            html = tpl('tplBuyCarPlan', db);
        }else{
            html = nullBox(["有计划购买的车型了吗？", "添加您计划购买的车型，及时获取降价信息及优惠的购车服务。", "n_uc_garage_planbuy.html"]);
        }  
        $buyCarPlan.html(html); 
    });

$.post("/index.php/MemberGeneral/attention_car", function(db){
        var html = null;
        $attenCar.find('.uc_loading').remove();
        if(db.length){
            html = tpl('tplAttenCar', db);
        }else{
            html = nullBox(["有心仪的车型了吗？", "添加您关注的车型，第一时间获取降价信息，更有特卖活动！", "n_uc_garage_attencar.html"]);
        }  
        $attenCar.html(html); 
    });
// //车库 - 我的爱车列表
// $.post("/index.php/MemberGeneral/carlove", function(db){
//         var html = null,
//             allNum = 3,
//             adderNum = db.length,
//             remainingNum = allNum - adderNum,
//             $remainingNumSel = $('.j_remainingNum');
//         $remainingNumSel.html(remainingNum);    
//         $gar_myCarList.find('.uc_loading').remove();
//         if(adderNum){
//             html = tpl('tplGarMyCarList', db);
//             $gar_myCarList.html(html); 
//         }
//         if(remainingNum == 0){
//             $('#J_AddMyCarForm').remove();
//         }
        
//     });

});