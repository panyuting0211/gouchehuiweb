window.QRLogin = {};
window.code = 408;
jQuery(function(){
    //var qrcodeChangeInterval = setInterval(changeQrcode, 290 * 1000), //<300s
    ////host = "http://10.12.22.241",
    //    host = "https://wx.tenpay.com",
    //    uuid = "",
    //    payMsg$ = $("#payMsg"),
    //    defalutPayClass = payMsg$[0].className,
    //    qrCodeTimeout = null;
    //
    //function changePayInfo(info){
    //    payMsg$[0].className = defalutPayClass + " " + info;
    //}

    //function changeQrcode() {
    //    var self = arguments.callee,
    //        restart = function () {
    //            clearTimeout(qrCodeTimeout);
    //            qrCodeTimeout = setTimeout(self, 10000);
    //        },
    //        data = {
    //            _:new Date().getTime()
    //        };
    //    for (var i in setting) {
    //        data[i] = setting[i];
    //    }
    //    $.ajax({
    //        url:"https://login.weixin.qq.com/jspay",
    //        type:"GET",
    //        dataType:"script",
    //        data: data,
    //        cache:false,
    //        success:function () {
    //            var _code = window.QRLogin.code;
    //            if (_code == 200 && window.QRLogin.uuid) {
    //                uuid = window.QRLogin.uuid;
    //
    //                var QRcode$ = $("#QRcode");
    //                if(!QRcode$.attr("src")){
    //                    setTimeout(function(){
    //                        var w$ = $(window), h = w$.height(), ot = 715;
    //                        if(h < ot){
    //                            var t = ot - h,i = 0, interval;
    //                            interval = setInterval(function(){
    //                                if(i < t) w$.scrollTop(i=i+3);
    //                                else clearInterval(interval)
    //                            }, 1);
    //                        }
    //                    }, 10);
    //                }
    //
    //                QRcode$.attr("src", host + "/cgi-bin/mmpayweb-bin/getpayqrcode?uuid=" + uuid);
    //                changePayInfo("qr_default");
    //
    //                _poll();
    //            } else {
    //                restart();
    //            }
    //        },
    //        error:restart
    //    });
    //}

    //function _poll() {
    //    var self = arguments.callee,
    //        pollUUID = uuid,
    //        repoll = function (pollUUID) {
    //            if(pollUUID != uuid) return;
    //            setTimeout(self, 1000);
    //        },
    //        succCallback = function(code){
    //            switch(code){
    //                case 408://扫描未知
    //                case 200:
    //                    repoll(pollUUID);
    //                    break;
    //                case 203://扫描成功
    //                    clearInterval(qrcodeChangeInterval);
    //                    changePayInfo("qr_succ");
    //                    repoll(pollUUID);
    //                    break;
    //                case 205://扫描成功——取消
    //                    changeQrcode();
    //                    changePayInfo("qr_default");
    //                    repoll(pollUUID);
    //                    break;
    //                case 204://支付未知
    //                    repoll(pollUUID);
    //                    break;
    //                case 201://支付成功
    //                    changePayInfo("pay_succ");
    //                    $.ajax({
    //                        url: host + "/cgi-bin/mmpayweb-bin/paysuccdetail",
    //                        data : {
    //                            appid: setting.appid,
    //                            req_key: setting.req_key,
    //                            uuid: pollUUID
    //                        },
    //                        dataType:"text",
    //                        type:"GET",
    //                        success:function(text){
    //                            var data = text.match("<script>(.*?)</script>");
    //                            if(!data || data.length < 2) return;
    //
    //                            data = eval("(" + data[1] + ")");
    //                            $("#userName").html(decodeURIComponent(data.username));
    //                            $("#bankCard").text(data.bank_card);
    //                            $("#resultLink").attr("href", data.result_url);
    //
    //                            $("#payMsgDetail").css("visibility", "visible");
    //
    //                            var count = parseInt($("#redirectTimer").text());
    //                            var interval = setInterval(function(){
    //                                $("#redirectTimer").text(--count);
    //                                if(count == 0){
    //                                    location.href = data.result_url;
    //                                    clearInterval(interval);
    //                                }
    //                            }, 1000);
    //                        }
    //                    });
    //                    break;
    //                case 202://支付失败
    //                    changePayInfo("pay_error");
    //                    break;
    //                case 400://uuid失效
    //                    break;
    //                default:
    //                    repoll(pollUUID);
    //            }
    //        };
    //    window.debug = succCallback;
    //    $.ajax({
    //        url:"https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login",
    //        type:"GET",
    //        dataType:"script",
    //        data: {
    //            uuid: pollUUID,
    //            tip: 1,
    //            _: new Date().getTime(),
    //            code: window.code
    //        },
    //        cache:false,
    //        timeout:33 * 1000,//后台是30s
    //        success:function () {
    //            if(!window.code){
    //                repoll(pollUUID);
    //                return;
    //            }
    //            succCallback(window.code);
    //        },
    //        error:function(){
    //            repoll(pollUUID);
    //        }
    //    });
    //}



    function init() {
        //changeQrcode();
        // add by xusheng on 04/26
        $(".pay_bill .pay_bill_unit:last-child").addClass("no_extra");

        var _nTimer = 0,
            _oGuide$ = $("#guide"),
            _oGuideTrigger$ = $("#QRcode");

        function _back() {
            _nTimer = setTimeout(function() {
                _oGuide$.stop().animate({marginLeft:"-101px",opacity:0}, "400", "swing",function(){
                    _oGuide$.hide();
                });
            }, 100);
        }

        /*guide*/
        _oGuide$.css({"left":"50%", "opacity":0});
        _oGuideTrigger$.mouseover(function(){
            clearTimeout(_nTimer);
            _oGuide$.css("display", "block").stop().animate({marginLeft:"+147px", opacity:1}, 900, "swing", function() {
                _oGuide$.animate({marginLeft:"+134px"}, 300);
            });
        }).mouseout(_back);

        _oGuide$.mouseover(function(){
            clearTimeout(_nTimer);
        }).mouseout(_back);
    }

    init();
});
