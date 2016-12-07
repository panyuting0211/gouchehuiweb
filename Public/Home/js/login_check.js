(function(window, $) {
    var cookieArr = document.cookie.split(";");
    var defaultUsername = "";
    for (var i = 0; i < cookieArr.length; i++) {
        if (cookieArr[i].match("login_user_name")) {
            defaultUsername = cookieArr[i].split("=")[1];
        }
    }
    if (defaultUsername) {
        $("[name='user_name']").val(decodeURIComponent(defaultUsername));
    }

    var normalForm = $("#login_normal"),
        identify_codeFlag = false;

    var originalValue = '';
    //获取焦点及失去焦点事件
    $("form input").focus(function() {
        $(this).addClass('G_input_focus').removeClass("G_input_error");
    }).blur(function() {
        $(this).removeClass('G_input_focus');
    }).keyup(function(e) {
        if (e.keyCode === 13) {
            $("[name='submit_login']", $(this).parents("form")).trigger("click");
        }
    }).on('input', function(){
        if(this.value == originalValue){
            return;
        }
        originalValue = this.value;
        var flag = checkData(this);
        if (this.id == "telNum" && this.value && new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(this.value)) {
            $.get("/" + this.value, function(json) {
                try {
                    var json = eval("(" + json + ")");
                } catch (e) {
                    $('#login_error', $(element).parents('table')).text("验证码错误").show();
                }
                if (json.success) {
                    $("#line_9").show();
                } else {
                	$("#line_9").hide();
                }
            });
            return;
        }
    });
    //end
    //提交事件
    $("#login_normal form").submit(function(event) {
        var _this = $(event.target),
            form =$("#login_normal form"),
            button =$("#login_normal input[type=submit]"),
            flag = checkData($("input", form), 'noNull'),
            loginTxt = ["登录","正在登录..."],
            $loginNormal = $("#login_normal"),
            userData = $loginNormal.serializeObject();
            userData.user_name = $loginNormal.find("input[name=user_name]").val(), userData.password = $loginNormal.find("[name=password]").val() || "/";
        if (flag || identify_codeFlag) {
            if(!_this.hasClass("abtn-no")){
                _this.addClass("abtn-no");
                button.val(loginTxt[1]);
                $.ajax({
                    type: "POST",
                    url: "/index.php/Home/Public/ajax_dologin",
                    dataType: "json",
                    data: userData,
                    success: function(cbdata) {
                        if (1 == cbdata["status"]) {
                            var jumpurl = cbdata["next"];
                            window.location.href = jumpurl;
                            return false;
                        }else{
                            $("#login_error").show();
                            $("#login_error").html("<i class=\"icon_warn\"></i>" + cbdata["info"]);
                        }
                    },
                    error: function() {
                        this.errormsg()
                    }
                }).always(function(){_this.removeClass("abtn-no");button.val(loginTxt[0])});
            }
        }
    });

    //校验数据单个或者整体
    function checkData(ele, noNull) {
        var text = "";
        var returnflag = true;
        $("#login_error").html('').hide();
        if (!ele.length) {
            $(ele).removeClass("G_input_error");
            text = errorText(ele, noNull);
            if (text) {
				text = '<i class="icon_warn"></i>' + text;
                $('#login_error', $(ele).parents('.login_mod_box')).html(text).show();
                $(ele).addClass("G_input_error");
                return false;
            }
        } else {
            $(ele).parents('.login_mod_box').find("input.G_input_error").removeClass("G_input_error");
            $.each(ele, function(i, item) {
                text = errorText(item, noNull);
                if (text) {
					text = '<i class="icon_warn"></i>' + text;
                    $(item).trigger("focus");
                    $(item).addClass("G_input_error");
                    $('#login_error', $(item).parents('.login_mod_box')).html(text).show();
                    returnflag = false;
                    return false;
                }
            });
        }
        return returnflag;
    }

    function errorText(element, noNull) {
            if (!element.value && !noNull) {
                return '';
            }
            if ((noNull === 'noNull' && !element.value) || element.value.length > 32) {
                switch (element.name) {
                    case "user_name":
                        return "请输入正确的" + element.placeholder;
                        break;
                    case "password":
                        if ($(element).hasClass('tel')) {
                            return "请输入正确的动态密码";
                        } else {
                            return "请输入正确的密码";
                        }
                        break;
                    case "identify_code":
                        if (!element.value && $(element).is(":visible")) {
                            return "请输入正确的验证码";
                        }
                        break;
                    default:
                        return "";
                        break;
                }
            } else if (element.name == "user_name" && $(element).hasClass('tel')) {
                if (new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(element.value) == false) {
                    return '请输入正确的手机号码';
                }
            } else if (element.name == "identify_code" && $(element).is(":visible")) {
                if (!/^([a-zA-Z0-9])*$/.test(element.value) || element.value.length != 4) {
                    return "请输入正确的验证码，长度4位字母或者数字。";
                } else {
                    var tmpUrl ="/ajax/checkCaptcha?identify_code=" + element.value + "&t=" + (new Date().getTime() + Math.random());
                    $.get(tmpUrl, function(json) {
                        try {
                            var json = eval("(" + json + ")");
                        } catch (e) {
                            $('#login_error', $(element).parents('table')).text("验证码错误").show();
                        }
                        if (!json.success) {
                            identify_codeFlag = false;
                            $(element).focus();
                            $(element).parents("tr").addClass("err");
                            $('#login_error', $(element).parents('table')).text("验证码错误，请重新输入").show();

                            if ( json.errno == -2 ) {
                                change_identify_code();
                            }
                        } else {
                            identify_codeFlag = true;
                        }
                    });
                }
            } else if (element.name == "password") {
                if ($(element).hasClass("tel")) {
                    if (!/^([0-9])*$/.test(element.value) || element.value.length != 6) {
                        return "请输入正确的手机验证码，长度6位的数字。";
                    }
                } else if (element.value.length < 6) {
                    return "请输入正确的密码，密码长度最少必须6位";
                }
            } else {
                return "";
            }
            return "";
        }
        //end

    //表单的默认focus
    function defaultFocus(ele) {
            var username = $("[name='user_name']", ele);
            var password = $("[name='password']", ele);
            var identify_code = $("[name='identify_code']", ele);
            if (username.val() == '') {
                username.focus();
            } else if (password.val() == '') {
                password.focus();
            } else {
                identify_code.focus();
            }
        }
        //end

    setTimeout(function() {
        defaultFocus(normalForm);
    }, 200);
    //end



    //点击切换验证码，以及验证码完成隐藏loading
    $(".identify_img").on("click", change_identify_code);
    $(".identify_img").on("load", function() {
        $(".loadingImg").hide();
        $(this).show();
    });
    //end

    //其他方式登录的样式切换
    $(".partner-login li").hover(function() {
        $(this).addClass("cur");
    }, function() {
        $(this).removeClass("cur");
    });
    //end

    //执行一次初始化
    //change_identify_code();
    resizeScreen();
    //end

        //1200宽度与1000宽度切换
    function resizeScreen() {
            var index1200 = $("#index1200");
            //初始化页面
            if (!isBigThan1280()) {
                index1200.removeClass("index1200").addClass("index1000");
            } else {
                index1200.removeClass("index1000").addClass("index1200");
            }
            //改变屏幕大小
            $(window).resize(function() {
                if (!isBigThan1280()) {
                    if (!index1200.hasClass("index1000")) {
                        index1200.removeClass("index1200").addClass("index1000");
                    }
                } else {
                    if (!index1200.hasClass("index1200")) {
                        index1200.removeClass("index1000").addClass("index1200");
                    }
                }
            });
        }
        //end


    //判断是不是IE6
    function isIE6() {
            if ($.browser.msie && $.browser.version == 6) {
                return true;
            } else {
                return false;
            }
        }
        //end


    //判断宽度
    function isBigThan1280() {
            var w_wd = $(window).width();
            if (w_wd >= 1280) {
                return true;
            } else {
                return false;
            }
        }
        //end


    //更换验证码
    function change_identify_code() {
            $(".loadingImg").css("display", "");
            $('.identify_img').hide().attr('src', '/ajax/captcha/v/' + (new Date().getTime() + Math.random()));
        }
        //end
   
})(window, jQuery, undefined);







