$(function(){
    var qianzhui='/member.php';
    var $baseUserInfo = $("#J_BaseUserInfo"),
        $mycar = $("#J_MyCar"),
        $buyCarPlan = $("#J_BuyCarPlan"),
        $otherAttenCar = $("#J_otherAttenCar"),
        $footprint = $('#J_FootprintList'),
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
    $otherAttenCar.find(".uc_loading").length || ($otherAttenCar.append($loader));
    $footprint.find(".uc_loading").length || ($footprint.append($loader));


     $.post(qianzhui+"/MemberGeneral/footprint", function(db){
        var html = null;
        $footprint.find('.uc_loading').remove();
        if(db.list.length){
            html = template('tplFootprintList', db);
        }else{
            html = '<p class="null">你还没有浏览记录，请前往<a href="http://www.gouchehui.com">购车惠</a>网站浏览吧！</p>';
        }  
        $footprint.html(html); 
    });
                   
    $baseUserInfo.on("load", function(e, db){
        db.percentage = (parseFloat(db.total_jifen / db.denominator)*100).toString() + "%";
        var html = template('tplBaseUserInfo', db),
            $PunchBtn = null,
            isPunched = 0,
            punchCard = function(){
                var _punchBtn = $("#J_PunchTheClock");
                _punchBtn.unbind("click");
                $.ajax({
                        url: qianzhui+'/MemberGeneral/everyday',
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


    $.post(qianzhui+"/MemberGeneral/member_accinfo", function(db){
        $baseUserInfo.trigger("load", db);
    });

    $.post(qianzhui+"/MemberGeneral/carlove", function(db){
        var html = null;
        $mycar.find('.uc_loading').remove();
        if(db['list'].length){
            html = template('tplMyCar', db);
        }else{
            html = nullBox(["已经有车了？快告诉我们吧！", "添加你的爱车，享受购车惠提供的优质服务。", "n_uc_garage.html"]);
        }  
        $mycar.html(html); 
    });

$.post(qianzhui+"/MemberGeneral/carplan", function(db){
        var html = null;
        $buyCarPlan.find('.uc_loading').remove();
        if(db.list.length){
            html = template('tplBuyCarPlan', db);
        }else{
            html = nullBox(["有计划购买的车型了吗？", "添加您计划购买的车型，及时获取降价信息及优惠的购车服务。", "n_uc_garage_planbuy.html"]);
        }  
        $buyCarPlan.html(html); 
    });

$.post(qianzhui+"/MemberGeneral/attention_car", function(db){
        var html = null;
        $attenCar.find('.uc_loading').remove();
        if(db.list.length){
            html = template('tplAttenCar', db);
        }else{
            html = nullBox(["有心仪的车型了吗？", "添加您关注的车型，第一时间获取降价信息，更有特卖活动！", "n_uc_garage_attencar.html"]);
        }  
        $attenCar.html(html); 
    });

$.post(qianzhui+"/MemberGeneral/other_attention_car", function(db){
        var html = null;
        $otherAttenCar.find('.uc_loading').remove();
        if(db.list.length){
            html = template('tplOtherAttenCar', db);
        }else{
            html = '<p class="null">暂无其他数据！</p>';
        }  
        $otherAttenCar.html(html);
        $otherAttenCar.find('.j_addAtten').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            console.log(e);
            var $li = $(e.target).closest('li');
            $.ajax({
                                    type: "POST",
                                    url: qianzhui+"/MemberGeneral/attention_add",
                                    dataType: "json",
                                    data: {
                                        brand_id: $li.data('brandid'),
                                        car_model_id: $li.data('modelid')
                                    },
                                    success:function(data){
                                        if(data.status == 1){
                                            layer.msg('添加关注车型失败！');
                                        }else if(data.status == 2){
                                            location.reload();
                                        }else if(data.status == 3){
                                            layer.msg(data.msg);
                                        }
                                     }
                                    });
        });  
    });

});