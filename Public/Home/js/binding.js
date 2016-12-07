/**
 * Created by Administrator on 2016/4/8.
 */
$(function(){
    var o = {
            mobile: /^\d{11}$/,
            allmobile: /^\d{7,12}$/,
            email: /^([a-zA-Z0-9_-|\.])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/
        },
        r = {
            username: {
                "default": "请输入手机/邮箱/昵称",
                white: "请输入昵称/邮箱/手机号",
                error: "请填写正确的昵称/邮箱/手机号"
            },
            mobile: {
                white: "请输入手机号码",
                error: "请填写正确的手机号"
            },
            allmobile: {
                white: "请输入手机号码",
                error: "请填写正确的手机号"
            },
            code: {
                white: "请输入验证码"
            },
            answer: {
                white: "请输入答案"
            },
            nickname: {
                white: "请设置昵称",
                error: "昵称只能4~20位"
            },
            loginpassword: {
                white: "请输入密码"
            },
            password: {
                white: "请设置登录密码",
                error: "密码控制在6-20位置"
            },
            newpassword: {
                "default": "6-20个字母、数字或者符号",
                white: "请输入新密码",
                error: "密码需要是字母或数字，最小6位，最大20位"
            },
            repass: {
                "default": "请再次确认密码",
                white: "请再次确认密码",
                error: "2次输入的密码不一致"
            }
        };
    function showError(e, a) {
        var t = e.parents(".validateitem").find(".tips");
        t && t.length > 0 && t.remove(),
            a ? e.parents(".validateitem").append('<span class="tips error display_u">' + a + "</span>") : e.parents(".validateitem").append('<span class="tips error display_u">出错了~</span>')

    }
    function checkIsPass(e) {
        var c = this,
            m = !1,
            d = $.trim(e.val()),
            a = e.attr("data-type");
        e.siblings(".tips").remove();
        if (a) {
            if (0 == d.length) {
                showError(e, r[a].white);
                m = !1;
            }
            var p = e.data("errormsg") || r[a].error;
            switch (a) {
                case "mobile":
                    if (!o.allmobile.test(d)) {
                        showError(e, p);
                        m = !1;
                    }else{
                        $.ajax({
                            url: "/index.php/Public/userNameCheck",
                            data: {
                                tel: d
                            },
                            type: 'post',
                            success: function(json) {

                                if (json.status == -1) {
                                    $(".phonecheck_box").hide();
                                } else {
                                    $(".phonecheck_box").show();
                                }

                            }
                        });
                        m = !0;
                    }
                    break;
                case "password":
                    if (d.length < 6 || d.length > 20) {
                        showError(e, p);
                        m = !1;
                    }else{
                        m = !0;
                    }
                    break;
                default:
                    m = !1
            }
            return m
        }
    }
    $(".j_mobile").on("blur", function(){
        checkIsPass($(this));
    });
    $(".j_password").on("blur", function(){
        checkIsPass($(this));
    });
    function intervalTime(num, _this) {
        var num = parseInt(num);
        var i = 0;
        _this.addClass("G_btn_disabled");
        _this.html("<span>"+ num +"</span>秒重新发送");
        var inter = setInterval(function() {
            if (i < num) {
                i++;
                _this.find("span").text(num - i);
            } else {
                clearInterval(inter);
                _this.removeClass("G_btn_disabled");
                _this.html("重新发送").bind("click", sendCode);
            }
        }, 1000);
    }

    function sendCode() {
        var _this = $(this), e = $(".j_mobile"), flag1 = checkIsPass(e);
        if (flag1) {
            var postData = {
                tel: $.trim(e.val()),
                tpl_id: 4852,
                isReg:1
            }
            $.post('/index.php/Public/note', postData, function(data) {
                if (data.status == 0) {
                    _this.unbind("click");
                    intervalTime(60, _this);
                }else if(data.status == 205405){
                    showError(e, "号码异常/同一号码发送次数过于频繁");
                }else if(data.status == 1){
                    $(".j_mobile").focus();
                    showError(e, data.msg);
                }
            });
        }
    }

    $("#J_CreateMobileCode").bind("click", sendCode);
    $("#J_bindingForm").submit(function(event) {
        if(checkIsPass($(".j_mobile")) && checkIsPass($(".j_password"))){
            return true;
        }else{
            return false;
        }
    });
});
