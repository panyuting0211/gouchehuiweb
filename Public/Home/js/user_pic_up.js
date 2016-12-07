$(document).ready(function(){
    function previewImage(file)
    {
        var MAXWIDTH  = 150;
        var MAXHEIGHT = 150;
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
    $('input[name="headimg"]').change(function(){
        previewImage($(this).get(0));
    });


    /*积分兑换*/
    $("#J_giftWrap").find(".giftbox .exchange").on("click",function(e){
        var $this  = $(this);
        var  souce_wran = "<div><i class='ui_icon  j_icon_warn_err'></i><span>\u60a8\u7684\u79ef\u5206\u4e0d\u8db3\uff0c\u65e0\u6cd5\u5151\u6362\u5546\u54c1\u3002</span></div>";
        if($(this).hasClass("usable")){
            //手机号判断
            var userscore = $("#J_userscore").data("userscore");
            var score_value = $this.closest(".giftbox").data("score");
            var surplusScore = userscore-score_value;
            //jifenxinxi
            var goods_data={
                goods_name:$this.closest(".giftbox").data("name"),
                score_value:$this.closest(".giftbox").data("score"),
                goods_id:$this.closest(".giftbox").data("id"),
                //isdelete:true
            };
            var userID = $("#J_userid").val();
            console.log(userID);
            $.ajax({
                url:'get_user_addr',
                data:{user_id:userID},
                success:function(e){
                    if(e.status == 0){
                        //没有收货地址，
                        var indextwo = layer.open({
                            type: 1,
                            closeBtn: 1, //显示关闭按钮
                            shift: 2,
                            shadeClose: true, //开启遮罩关闭
                            area: ['500px','480px'],
                            content: '<div class="integralInfo">' +
                            '<div class="infobox" data-id="">' +
                            '<dl><dt>您的当前积分为：</dt><dd><span class="text_y">' + userscore + '</span>积分</dd></dl>' +
                            '<dl><dt>消耗积分：</dt><dd><span class="text_y">' + score_value +'</span>积分</dd></dl>' +
                            '<dl><dt>剩余积分：</dt><dd><span class="text_y">' + surplusScore +  '</span>积分</dd></dl>' +
                            '<dl><dt>收货人姓名：</dt><dd><input type="text" name="receiver" id="J_receiver" class="receiver" value=""><div class="tips"></div></dd></dl>' +
                            '<dl><dt>收货人手机号：</dt><dd><input type="text" id="J_telphone" name="telphone" class="telphone" value=""><div class="tips"></div></dd></dl>' +
                            '<dl><dt>收货地址：</dt><dd>' +
                            '<div class="addressbox">' +
                            '<select name="province" id="province"><option value="0">请选择省</option></select><select name="city" id="city"><option value="0">请选择市</option></select><select name="district" id="district"><option value="0">请选择县/区</option></select><input type="text" name="area" class="area" value=""></div>' +
                            '<div class="tips"></div>' +
                            '</dd></dl>' +
                            '<dl><dt></dt><dd><input type="submit" class="G_btn_a submit" value="确定"></dd></dl>' +
                            '</div></div>'
                        });


                        var provinceDom = $(".integralInfo").find("[name=province]"),
                            cityDom = $(".integralInfo").find("[name=city]"),
                            districtDom = $(".integralInfo").find("[name=district]");

                        //数据检查

                        function  checkInp(ele){
                            $ele=$(ele);
                            console.log($ele);
                            if (!$ele) {
                                return true;
                            }
                            var value = $.trim($ele.val());
                            switch ($ele.attr('name')) {
                                case "telphone":
                                    if(!value || new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(value) == false){
                                        $ele.closest('dd').find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请输入正确的手机号！</p>');
                                        return false;
                                    }else{
                                        $ele.closest('dd').find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确！</p>');
                                        return true;
                                    };
                                    break;
                                case "receiver":
                                    if(!value || new RegExp("/[^\u4E00-\u9FA5]/gi") ==false ){
                                        $ele.closest('dd').find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>收件人必须为中文哦！</p>');
                                        return false;
                                    }else {
                                        $ele.closest('dd').find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确</p>');
                                        return true;
                                    };
                                    break;
                                case "area":
                                    if(!value || provinceDom.val == "0" || cityDom.val() == "0" || districtDom == "0"){
                                        //地址值为空
                                        $ele.closest('dd').find(".tips").show().html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请填写收货地址</p>');
                                        return false;
                                    }else {
                                        $ele.closest('dd').find(".tips").show   ().html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>地址填写正确！</p>');
                                        return true;
                                    };
                                    break;
                                default:
                                    return false;
                                    break;
                            }
                        };
                        /*
                         * 默认添加省份
                         * */
                        /*省*/
                        $.ajax({
                            url:"/member.php/Member4s/province",
                            success:function(db){
                                console.log(db);
                                var provinceStr='<option value="0">请选择省</option>';
                                for(var i =0;i<db.length ;i++){
                                    provinceStr+='<option value="'+ db[i].value +'">'+ db[i].name  +'</option>'
                                }
                                provinceDom.html(provinceStr);
                            }
                        });
                        /*市*/
                        provinceDom.on("change",function(){
                            var data= {
                                province: provinceDom.find("option:selected").val()
                            };
                            $.ajax({
                                url:"/member.php/Member4s/city",
                                data:data,
                                success:function(db){
                                    var citesStr='<option value="">请选择市</option>';
                                    for(var k =0;k<db.length ;k++){
                                        citesStr+='<option value="'+ db[k].value +'">'+ db[k].name  +'</option>'
                                    }
                                    cityDom.html(citesStr);
                                }
                            });
                        });
                        /*区*/
                        cityDom.on("change",function(){
                            var data= {
                                city:cityDom.find("option:selected").val()
                            };

                            $.ajax({
                                url:"/member.php/Member4s/district",
                                data:data,
                                success:function(db){
                                    var districtStr='<option value="">请选择区/县</option>';
                                    for(var k =0;k<db.length ;k++){
                                        districtStr+='<option value="'+ db[k].value +'">'+ db[k].name  +'</option>'
                                    }
                                    districtDom.html(districtStr);
                                }
                            });
                        });


                        $(".integralInfo").find(".receiver").on("blur",function(){
                            checkInp($(".integralInfo").find(".receiver"));
                        });
                        //判断地址值是否为空
                        $(".integralInfo").find(".area").on("blur",function(){
                            checkInp($(".integralInfo").find(".area"));
                        })

                        //判断手机号
                        $(".integralInfo").on("blur",".telphone", function () {
                            checkInp($(".integralInfo").find(".telphone"));
                        });

                        //ajaxajax提交信息
                            $(".integralInfo").find(".submit").on("click",function(){
                                //地址信息


                                var receiverFlag = checkInp($(".integralInfo").find(".receiver"));
                                var areaFlag = checkInp($(".integralInfo").find(".area"));
                                var telphoneFlag = checkInp($(".integralInfo").find(".telphone"));
                                //输入合法；
                                if(receiverFlag && areaFlag && telphoneFlag){
                                    var Data = {
                                    receiver:$(".integralInfo").find(".receiver").val(),
                                    telphone:$(".integralInfo").find(".telphone").val(),
                                    receipt_province:provinceDom.find("option:selected").text(),
                                    province_id:provinceDom.find("option:selected").val(),
                                    receipt_city:cityDom.find("option:selected").text(),
                                    city_id:cityDom.find("option:selected").val(),
                                    receipt_quarter:districtDom.find("option:selected").text(),
                                    quarter_id:districtDom.find("option:selected").val(),
                                    receipt_address:$(".integralInfo").find(".area").val(),
                                    goods_name:goods_data.goods_name,
                                    score_value:goods_data.score_value,
                                    goods_id:goods_data.goods_id,
                                };

                                $.ajax({
                                    url:"fs_score_address_add",
                                    data:Data,
                                    success:function(e){
                                        if(e.status == 1){
                                            //chenggong
                                            layer.close(indextwo);
                                            layer.msg(e.msg, {
                                                time: 2000, //2s后自动关闭
                                                btn: ['好的']
                                            });
                                        }else{
                                            layer.close(indextwo);
                                            layer.msg(e.msg, {
                                                time: 2000, //2s后自动关闭
                                                btn: ['再次兑换']
                                            })
                                        }

                                    }
                                });
                            };
                        });
                    }else{
                        var indexone = layer.open({
                            type: 1,
                            closeBtn: 1, //显示关闭按钮
                            shift: 2,
                            shadeClose: true, //开启遮罩关闭
                            area: ['500px','480px'],
                            content: '<div class="integralInfo">' +
                            '<div class="infobox" data-id="'+ e.list.id  + '">' +
                            '<dl><dt>您的当前积分为：</dt><dd><span class="text_y">' + userscore + '</span>积分</dd></dl>' +
                            '<dl><dt>消耗积分：</dt><dd><span class="text_y">' + score_value +'</span>积分</dd></dl>' +
                            '<dl><dt>剩余积分：</dt><dd><span class="text_y">' + surplusScore +  '</span>积分</dd></dl>' +
                            '<dl><dt>收货人姓名：</dt><dd><input type="text" name="receiver" id="J_receiver" class="receiver" value="'+ e.list.receiver +'"><div class="tips"></div></dd></dl>' +
                            '<dl><dt>收货人手机号：</dt><dd><input type="text" id="J_telphone" name="telphone" class="telphone" value="'+ e.list.telphone +'"><div class="tips"></div></dd></dl>' +
                            '<dl><dt>收货地址：</dt><dd>' +
                            '<div class="addressbox">' +
                            '<select name="province" id="province"><option value="0">请选择省</option></select><select name="city" id="city"><option value="0">请选择市</option></select><select name="district" id="district"><option value="0">请选择县/区</option></select><input type="text" name="area" class="area" value="'+ e.list.receipt_address +'">' +
                            '</div>' +
                            '<div class="tips"></div>' +
                            '</dd></dl>' +
                            '<dl><dt></dt><dd><input type="submit" class="G_btn_a submit" value="确定"></dd></dl>' +
                            '</div></div>'
                        });


                        var provinceDom = $(".integralInfo").find("[name=province]"),
                            cityDom = $(".integralInfo").find("[name=city]"),
                            districtDom = $(".integralInfo").find("[name=district]");

                        //数据检查

                        function  checkInp(ele){
                            $ele=$(ele);
                            if (!$ele) {
                                return true;
                            }
                            var value = $.trim($ele.val());
                            switch ($ele.attr('name')) {
                                case "telphone":
                                    if(!value || new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(value) == false){
                                        $ele.closest('dd').find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请输入正确的手机号！</p>');
                                        return false;
                                    }else{
                                        $ele.closest('dd').find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确！</p>');
                                        return true;
                                    };
                                    break;
                                case "receiver":
                                    if(!value || new RegExp("/[^\u4E00-\u9FA5]/gi") ==false ){
                                        $ele.closest('dd').find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>收件人必须为中文哦！</p>');
                                        return false;
                                    }else {
                                        $ele.closest('dd').find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确</p>');
                                        return true;
                                    };
                                    break;
                                case "area":
                                    if(!value || provinceDom.val == "0" || cityDom.val() == "0" || districtDom == "0"){
                                        //地址值为空
                                        $ele.closest('dd').find(".tips").show().html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请填写收货地址</p>');
                                        return false;
                                    }else {
                                        $ele.closest('dd').find(".tips").show   ().html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>地址填写正确！</p>');
                                        return true;
                                    };
                                    break;
                                default:
                                    return false;
                                    break;
                            }
                        };
                        /*
                         * 默认添加省份
                         * */
                        /*省*/
                        $.ajax({
                            url:"/member.php/Member4s/province",
                            success:function(db){
                                var provinceStr='';
                                for(var i =0;i<db.length ;i++){
                                    provinceStr+='<option value="'+ db[i].value +'">'+ db[i].name  +'</option>'
                                }
                                provinceDom.html(provinceStr);
                                provinceDom.val(e.list.province_id);
                            }
                        });

                        /*设置默认城市*/
                        $.ajax({
                            url:"/member.php/Member4s/city",
                            data:{province:e.list.province_id},
                            success:function(db){
                                var citesStr='<option value="">请选择市</option>';
                                for(var k =0;k<db.length ;k++){
                                    citesStr+='<option value="'+ db[k].value +'">'+ db[k].name  +'</option>'
                                }
                                cityDom.html(citesStr);
                                cityDom.val(e.list.city_id);
                            }
                        });
                        /*设置默认区*/

                        $.ajax({
                            url: "/member.php/Member4s/district",
                            data:{city:e.list.city_id},
                            success: function (db) {
                                var districtStr='<option value="">请选择区/县</option>';
                                for (var k = 0; k < db.length; k++) {
                                    districtStr += '<option value="' + db[k].value + '">' + db[k].name + '</option>'
                                }
                                districtDom.html(districtStr);
                                districtDom.val(e.list.quarter_id);
                            }
                        });


                        /*市*/
                        $(".integralInfo").find("[name=province]").on("change",function(){
                            var data= {
                                province:provinceDom.find("option:selected").val()
                            };

                            $.ajax({
                                url:"/member.php/Member4s/city",
                                data:data,
                                success:function(db){
                                    var citesStr='<option value="">请选择市</option>';
                                    for(var k =0;k<db.length ;k++){
                                        citesStr+='<option value="'+ db[k].value +'">'+ db[k].name  +'</option>'
                                    }
                                    cityDom.html(citesStr);
                                }
                            });
                        });
                        /*区*/
                        $(".integralInfo").find("[name=city]").on("change",function(){
                            var data= {
                                city:cityDom.find("option:selected").val()
                            };
                            $.ajax({
                                url:"/member.php/Member4s/district",
                                data:data,
                                success:function(db){
                                    var districtStr='<option value="">请选择区/县</option>';
                                    for(var k =0;k<db.length ;k++){
                                        districtStr+='<option value="'+ db[k].value +'">'+ db[k].name  +'</option>'
                                    }
                                    districtDom.html(districtStr);
                                }
                            });
                        });



                        $(".integralInfo").find(".receiver").on("blur",function(){
                            checkInp($(".integralInfo").find(".receiver"));
                        });
                        //判断地址值是否为空
                        $(".integralInfo").find(".area").on("blur",function(){
                            checkInp($(".integralInfo").find(".area"));
                        })

                        //判断手机号
                        $(".integralInfo").on("blur",".telphone", function () {
                            checkInp($(".integralInfo").find(".telphone"));
                        });


                        //ajaxajax提交信息
                        $(".integralInfo").find(".submit").on("click",function(){
                            //地址信息
                            var $this = $(this),
                                isReceiver = checkInp($(".integralInfo").find(".receiver")),
                                isArea = checkInp($(".integralInfo").find(".area")),
                                isTelphone = checkInp($(".integralInfo").find(".telphone"));
                                //输入合法；
                            if(isReceiver && isArea && isTelphone){
                                var Data = {
                                    address_id:$(".integralInfo").find(".infobox").data('id'),
                                    receiver:$(".integralInfo").find(".receiver").val(),
                                    telphone:$(".integralInfo").find(".telphone").val(),
                                    receipt_province:provinceDom.find("option:selected").text(),
                                    province_id:provinceDom.find("option:selected").val(),
                                    receipt_city:cityDom.find("option:selected").text(),
                                    city_id:cityDom.find("option:selected").val(),
                                    receipt_quarter:districtDom.find("option:selected").text(),
                                    quarter_id:districtDom.find("option:selected").val(),
                                    receipt_address:$(".integralInfo").find(".area").val(),
                                    goods_name:goods_data.goods_name,
                                    score_value:goods_data.score_value,
                                    goods_id:goods_data.goods_id,
                                };

                                $this.hasClass("G_btn_disabled") || ($this.addClass("G_btn_disabled"), $.ajax({
                                    url: "fs_score_address_save",
                                    data: Data,
                                    success: function (e) {
                                        $this.removeClass("G_btn_disabled");
                                        if (e.status == 1) {
                                            layer.close(indexone);
                                            layer.msg(e.msg, {
                                                time: 2000 //2s后自动关闭
                                            });
                                            window.location.reload();
                                        } else {
                                            layer.close(indexone);
                                            layer.msg(e.msg, {
                                                time: 2000
                                            })
                                        }
                                    }
                                }));
                            }
                        });
                    }
                }
            })
        }else{
            layer.msg('您的积分不足', {
                time: 2000, //2s后自动关闭
                btn: ['好的']
            });
        }
    })



    /*zhongwenjiance*/
    function chkCn(){
        var $this = $(".J_scoreAddress .username");
        var s = $this.val();
        if(s == "" || (/[^\u4E00-\u9FA5]/gi.test(s))){
            $this.parent().find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>真实姓名必须为中文</p>');
        }else{
            $this.parent().find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确</p>');
        }
    }
    /*地址*/
    //输入框检测
    $(".J_scoreAddress").on("blur", ".username", chkCn);


    $(".J_scoreAddress").on("blur",".userphone",function(){
        var $this = $(".J_scoreAddress .userphone");
        var mobile = /^((1[0-9][0-9]{1})|159|153)+\d{8}$/;
        if($this.val() == "" || !$this.val().match(mobile)){
            $this.parent().find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请输入正确的手机号码</p>');
        }else{
            $this.parent().find(".tips").html('<p class="suc"><i class="ui_icon_20 icon_warn icon_warn_suc"></i>输入正确</p>');
        }
    });


    /*三级联动*/
    function liantong(obj) {
        var $this = obj,
            _id = $this.val(),
            _lvl = $this.data("lvl"),
            _emptyStr = "",
            $next = $this.next(),
            $nextNext = $next.next();
        if(_lvl == 0){
            $next.find("option:gt(0)").remove();
            $nextNext.find("option:gt(0)").remove();
        }else{
            $next.find("option:gt(0)").remove();
        }
        if(_lvl == 0){
            province && (province.forEach(function (v) {
                if(v.id == _id && v.city.length > 0){
                    $.each(v.city, function (i, o) {
                        _emptyStr += "<option value='" + o.id + "'>" + o.name + "</option>";
                    });
                    $next.append(_emptyStr);
                }
            }));
        }else if(_lvl == 1){
            area && area.length && (area.forEach(function(j, k){
                if(j.pid == _id){
                    _emptyStr += "<option value='" + j.id + "'>" + j.name + "</option>";
                }
            }));
            $next.append(_emptyStr);
        }

        $next.on("change", function(e){
            liantong($(this));
        });

    }
    var sourceId = $("#address1").val();
    /*添加地址*/
    $(".J_scoreAddress .addBtn").on("click",function(){
        console.log("U");
        var _initProvince = '<option data-id="" value="选择省">选择省</option>';
        province && (province.forEach(function (v) {
            _initProvince += "<option value='" + v.id + "'>" + v.name + "</option>";
        }));
        var $li =$('<li class="choiceaddress addAdre">'+
            '<select data-lvl="0" name="proveice" id="">'+
            _initProvince +
            '</select>'+
            '<select data-lvl="1" name="city" >'+
            '<option value="选择市">选择市</option>'+
            '</select>'+
            '<select data-lvl="2" name="quarter">'+
            ' <option  value="选择省">选择区</option>'+
            '</select>'+
            '<input type="text" class="detailaddress" placeholder="详细地址">' +
            '<div class="tips"></div>'+
            '<div class="modefybox">'+
            '<ul class="modefyDel">'+
            '<li class="modefy">确定</li>'+
            '</ul>'+
            '</div>'+
            '</li>');
        var lispace = $(".addAdre");

        if(lispace.length == 0){
            $(".J_scoreAddress .choiceaddressbox").find("ul").eq(0).append($li);
        }
        $li.find("[name=proveice]").on("change", function(e){
            liantong($(this));
        });

        $li.find(".detailaddress").on("blur",function(){
            if($li.find(".detailaddress").val() == ""){
                $li.find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>详细地址不能为空哦！</p>')
            }else{
                $li.find(".tips").empty();
            }

        });

        $li.find(".modefy").on("click",function(){
            var addressData = {
                "provice":$li.find("select[name=proveice] option:selected").text(),
                "city":$li.find("select[name=city] option:selected").text(),
                "quarter":$li.find("select[name=quarter] option:selected").text(),
                "address":$li.find(".detailaddress").val()
            };
            console.log(addressData);
            var d = radomId();
            if(addressData.provice =="" || addressData.city == "" || addressData.quarter == "" || addressData.address == ""){
                $li.find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>详细地址不能为空哦！</p>')
            }else{
                $(".address_list_wrap").find(".hadaddress").html('<li class="item default" data-provice="'+ addressData.provice +'"' +
                    ' data-address="'+ addressData.address +'"' +
                    'data-city="'+ addressData.city +'"' +
                    'data-quarter="'+ addressData.quarter +'">'+
                    '<input type="radio" name="address" id="address1" value="'+ sourceId +'" checked><label for="'+ d +'">'+
                    addressData.provice +addressData.city + addressData.quarter+addressData.address
                    +'</label>'+
                    '<div class="modefybox">'+
                    '<ul class="modefyDel">'+
                    '<li class="modefy">修改</li>'+
                    '</ul>'+
                    '</div>'+
                    '</li>');
                $(this).closest(".choiceaddress").remove();
            }
        });
        $li.find(".del").on("click",function(){
            $(this).closest(".choiceaddress").remove();
        })
    });

    $(".address_list_wrap").on("click",".del",function(){
        var data = {
            provice:$(this).closest(".item").data("provice"),
            city:$(this).closest(".item").data("city"),
            quarter:$(this).closest(".item").data("quarter"),
            address:$(this).closest(".item").data("address"),
        }
        $(this).closest(".item").remove();
    });

    $(".address_list_wrap").on("click",".modefy",function(){
        var _secondLi = $(this).parent().closest(".item");
        console.log(_secondLi);
        var addData = {
            "provice":_secondLi.data("provice"),
            "city":_secondLi.data("city"),
            "quarter":_secondLi.data("quarter"),
            "address":_secondLi.data("address")
        };

        var _initProvince = '<option data-id="" value="选择省">选择省</option>';
        province && (province.forEach(function (v) {
            _initProvince += "<option value='" + v.id + "'>" + v.name + "</option>";
        }));
        var _li =$('<li class="choiceaddress modefyedLi">'+
            '<select data-lvl="0" name="proveice" id="">'+
            _initProvince +
            '</select>'+
            '<select data-lvl="1" name="city" >'+
            '<option value="选择市">选择市</option>'+
            '</select>'+
            '<select data-lvl="2" name="quarter">'+
            ' <option  value="选择省">选择区</option>'+
            '</select>'+
            '<input type="text" class="detailaddress" placeholder="详细地址">' +
            '<div class="tips"></div>'+
            '<div class="modefybox">'+
            '<ul class="modefyDel">'+
            '<li class="modefy">确定</li>'+
            '</ul>'+
            '</div>'+
            '</li>');
        var  modefyedLi = $(".modefyedLi");
        if(modefyedLi.length == 0)
            $(".J_scoreAddress .choiceaddressbox").find("ul").eq(0).append(_li);

        _li.find("[name=proveice]").on("change", function(e){
            liantong($(this));
        });

        _li.find(".modefy").on("click",function(){
            var addressData = {
                "provice":_li.find("select[name=proveice] option:selected").text(),
                "city":_li.find("select[name=city] option:selected").text(),
                "quarter":_li.find("select[name=quarter] option:selected").text(),
                "address":_li.find(".detailaddress").val()
            };
            var d = radomId();
            if(addressData.provice =="" || addressData.city == "" || addressData.quarter == "" || addressData.address == ""){
                _li.find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>详细地址不能为空哦！</p>')
            }else{
                $(".address_list_wrap").find(".hadaddress").html('<li class="item default" data-provice="'+ addressData.provice +'"' +
                    ' data-address="'+ addressData.address +'"' +
                    'data-city="'+ addressData.city +'"' +
                    'data-quarter="'+ addressData.quarter +'">'+
                    '<input type="radio" name="address" id="address1" value="'+ sourceId +'" checked><label for="'+ d +'">'+
                    addressData.provice +addressData.city + addressData.quarter+addressData.address
                    +'</label>'+
                    '<div class="modefybox">'+
                    '<ul class="modefyDel">'+
                    '<li class="modefy">修改</li>'+
                    '</ul>'+
                    '</div>'+
                    '</li>');
                $(this).closest(".choiceaddress").remove();
            }
        });
        //shanchu
        //_li.find(".del").on("click",function(){
        //    $(this).closest(".choiceaddress").remove();
        //})
    })
    function radomId(){
        var d = 0;
        d += 1;
        return d;
    }


    $(".J_scoreAddress .submitbox").on("click",".submit",function(){
        var moreLi = $(".J_scoreAddress .address_list_wrap").find("input:radio[name='address']:checked");
        var userdata = {
            receiver:$(".J_scoreAddress").find(".username").val() || $(".J_scoreAddress").find(".usernameP").text(),
            telphone:$(".J_scoreAddress").find(".userphone").val() || $(".J_scoreAddress").find(".userphoneP").text(),
            receipt_province:moreLi.closest("li").data("provice"),
            receipt_city:moreLi.closest("li").data("city"),
            receipt_quarter:moreLi.closest("li").data("quarter"),
            receipt_address:moreLi.closest("li").data("address")
        };
        console.log(userdata);
        console.log(moreLi);
        if($("#J_defalut").length){//有默认地址，走修改路线
            $.ajax({
                url:"fs_address_save",
                type:"POST",
                data:{
                    id: moreLi.val(),
                    receiver:$(".J_scoreAddress").find(".username").val() || $(".J_scoreAddress").find(".usernameP").text(),
                    telphone:$(".J_scoreAddress").find(".userphone").val() || $(".J_scoreAddress").find(".userphoneP").text(),
                    receipt_province:moreLi.closest("li").data("provice"),
                    receipt_city:moreLi.closest("li").data("city"),
                    receipt_quarter:moreLi.closest("li").data("quarter"),
                    receipt_address:moreLi.closest("li").data("address")
                },
                success:function(e){
                    //提交成功
                    HUI && (HUI.PopOut.alert([e.info,""], "m"), $({}).delay(3e3).queue(function() {
                        HUI.PopOut.closeMask()
                    }))
                },
                error:function(e){
                    HUI && (HUI.PopOut.alert([e.info,""], "m"));//提交失败
                }
            })
        }else{//无默认地址，走添加路线
            $.ajax({
                url:"fs_address_add",
                type:"POST",
                data:userdata,
                success:function(e){
                    //提交成功
                    if(e.status == 1){
                        HUI && (HUI.PopOut.alert([e.info,""], "m"), $({}).delay(3e3).queue(function() {
                            HUI.PopOut.closeMask()
                        }));
                        setInterval(function(){
                            location.href = e.next;

                        },2000)
                    }else{
                        HUI && (HUI.PopOut.alert([e.info,""], "m"), $({}).delay(3e3).queue(function() {
                            HUI.PopOut.closeMask()
                        }));
                    }
                },
                error:function(e){
                    HUI && (HUI.PopOut.alert([e.info,""], "m"));//提交失败
                }
            })
        }
    })

    /*修改默认地址以及姓名*/
    $(".J_scoreAddress").find(".usernameP").on("click",function(){
        var _parent = $(this).closest("dd");
        _parent.find(".usernameP").remove();
        _parent.html('<input type="text" class="username" value="'+ $(this).text() +'"><div class="tips"></div>');
    });

    $(".J_scoreAddress").find(".userphoneP").on("click",function(){
        var _parent = $(this).closest("dd");
        _parent.find(".usernameP").remove();
        _parent.html('<input type="text" class="userphone" value="'+ $(this).text() +'"><div class="tips"></div>')
    })






    //
    ///*积分兑换弹框*/
    //$(".pointD_wrap").find("#J_duihuan").on("click",function(e){
    //    e.stopPropagation();
    //    e.preventDefault();
    //       var souce_suc = "<div class='J_giftPhone'>" +
    //           "   <div class='ui_text'>" +
    //           "       <input type='text' placeholder='请输入您的手机号码' class='phone' name='username' id='J_phonenum'>" +
    //           "   </div>" +
    //           "   <div class='ui_text'>" +
    //           "       <input type='text' name='code' class='codes' placeholder='请输入验证码'>" +
    //           "       <div class='send'>发送</div>" +
    //           "   </div>" +
    //           "   <div class='msg'></div>" +
    //           "   <div class='submit'>提交</div>" +
    //           "</div>";
    //    /*生成弹框*/
    //       HUI && (HUI.PopOut.alert(["\u624b\u673a\u9a8c\u8bc1\u7801",souce_suc,""], "m"));
    //
    //    $(".J_giftPhone").on("blur",".phone",function(){
    //        var mobile = /^((1[0-9][0-9]{1})|159|153)+\d{8}$/;
    //        if($(".J_giftPhone .phone").val() == "" || !$(".J_giftPhone .phone").val().match(mobile)){
    //            var error = $(".error");
    //            error.length || $(".J_giftPhone .msg").append("<p class='error'><i class='ui_icon_20 icon_warn icon_warn_err'></i>请输入正确的手机号！</p>");
    //        }else{
    //            $(".J_giftPhone .msg").find(".error").remove();
    //        }
    //
    //    });
    //    //发送验证码
    //    function checkData(ele) {
    //        if (!ele) {
    //            return true;
    //        }
    //        var value = $.trim(ele.value);
    //        switch (ele.name) {
    //            case "username":
    //                if (!value || new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(value) == false) {
    //                    $(".J_giftPhone").find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请输入正确的手机号码</p>');
    //                    return false;
    //                }else{
    //                    return true;
    //                }
    //                break;
    //            case "code":
    //                if (!value || value.length < 6 || new RegExp("^([0-9])+$").test(value) == false) {
    //                    $(".J_giftPhone").find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>请输入正确的验证码</p>');
    //                    return false;
    //                } else if (value.length == 6) {
    //                    return true;
    //                }
    //                break;
    //        }
    //    }
    //
    //    function sendCode(e) {
    //        var flag1 = checkData(document.getElementById("J_phonenum"));
    //        if (flag1) {
    //            var postData = {
    //                tel: $("#J_phonenum").val(),
    //            }
    //            $.ajax({
    //                type: "POST",
    //                url: "/index.php/Public/note",
    //                dataType: "JSON",
    //                data: postData,
    //                success: function(data) {
    //                    if (data.status == 0) {
    //                        $(".J_giftPhone").find(".send").unbind("click");
    //                        intervalTime(60);
    //                    }else if(data.status == 205405){
    //                        $(".J_giftPhone").find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>号码异常/同一号码发送次数过于频繁</p>');
    //                    }else if(data.status == 1){
    //                        $("#J_phonenum").focus();
    //                        $(".J_giftPhone").find(".tips").html('<p class="err"><i class="ui_icon_20 icon_warn icon_warn_err"></i>'+ data.msg +'</p>');
    //                    }
    //                }
    //            });
    //        }
    //    }
    //
    //    function intervalTime(num) {
    //        var num = parseInt(num);
    //        var i = 0;
    //        $(".J_giftPhone").find(".send").addClass("G_btn_disabled");
    //        $(".J_giftPhone").find(".send").html("<span></span>秒重新发送");
    //        $(".J_giftPhone").find(".send").find("span").text(num);
    //        var inter = setInterval(function() {
    //            if (i < num) {
    //                i++;
    //                $(".J_giftPhone").find(".send").find("span").text(num - i);
    //            } else {
    //                clearInterval(inter);
    //                $(".J_giftPhone").find(".send").removeClass("G_btn_disabled");
    //                $(".J_giftPhone").find(".send").html("重新发送").bind("click", sendCode);
    //            }
    //        }, 1000);
    //    }
    //    $(".J_giftPhone").find(".send").bind("click", sendCode);
    //    /* 提交 */
    //    /* 提交 */
    //    $(".J_giftPhone .submit").on("click",function(){
    //        var data={
    //            telphone:$(".J_giftPhone .phone").val(),
    //            code :$(".J_giftPhone .codes").val(),
    //            //goods_name:$this.closest(".giftbox").data("name"),
    //            //score_value:$this.closest(".giftbox").data("score"),
    //            //goods_id:$this.closest(".giftbox").data("id"),
    //            //isdelete:true
    //        }
    //        $.ajax({
    //            url:"fs_codecheck",
    //            type:"POST",
    //            data:data,
    //            success:function(e){
    //                if(e.status ==1){
    //                    HUI && (HUI.PopOut.alert(["\u63d0\u4ea4\u6210\u529f",""], "m"));//提交成功
    //                    location.href = e.next;
    //                }else{
    //                    HUI && (HUI.PopOut.alert([e.msg,""], "m"));//提交成功
    //                }
    //            },
    //            error:function(){
    //                HUI && (HUI.PopOut.alert(["\u63d0\u4ea4\u5931\u8d25",""], "m"));//提交失败
    //            }
    //        })
    //
    //
    //    })
    //});
})