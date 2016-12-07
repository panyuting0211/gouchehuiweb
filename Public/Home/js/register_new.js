fml.define("app/register_new", ["jquery", "component/validate", "component/placeholder", "component/urlHandle", "app/checkcode", "app/login"], function(a, b) {
	function u() {
		var a = "registerForm",
			b = function() {
				var b = !1,
					e = "/user/reg/submit?frm=reg_btn_mobile",
					f = {
						nt: Meilishuo.config.nt,
						mobile: h.val(),
						nickname: i.val(),
						password: j.val(),
						confirmpassword: k.val(),
						captcha: l.val(),
						agreement: c("#agreement")[0].checked,
						type: s,
						gender: "女",
						redirect: d.getParams(location.href).redirect
					},
					g = function(a) {
						var e = "/";
						a && a.code == 0 || a.url ? (a.redirect ? /^http/.test(a.redirect) ? e = a.redirect : e = "/user/register/" + a.redirect : a.url ? (a.url.length > 2 && (e = a.url), e = {
							1: "/user/register/success",
							2: "/guang/hot"
						}[a.url] || e) : e = "/user/register/success", c(document.body).trigger("register.ok"), d.redirect(e)) : (b = !1, q.html(a.message).show())
					};
				if (!f.agreement) return;
				if (c("#" + a).find(".msg_error").length) return;
				!b && c.post(e, f, g, "json"), b = !0
			},
			f = {
				mobile: {
					"req=手机号": "你还没有填写手机号哦。",
					mobile: "手机号格式有误，请重新输入！"
				},
				nickname: {
					"req=昵称": "你还没有填写昵称哦。",
					"maxlen=20": "支持中英文、数字、下划线，限长10个汉字。"
				},
				password: {
					"req=密码": "你还没有填写密码哦。",
					"minlen=6": "输入密码需在6位到32位间。",
					"maxlen=32": "输入密码需在6位到32位间。",
					"compare=conf_password": "两次密码输入不一致，请重新输入。"
				},
				conf_password: {
					"req=确认密码": "你还没有填写确认密码哦。",
					"compare=password": "两次密码输入不一致，请重新输入。"
				},
				code: {
					"req=短信验证码": "你还没有填写短信验证码哦。",
					"minlen=6": "输入验证码需要6位。"
				},
				agreement: {
					selectradio: "需要同意美丽说服务使用协议。"
				}
			},
			g = {
				"showmsgbyline=msg_error": "",
				"showmsgforsubmit=reg_btn_wrap": b
			},
			m = {
				success: "span=msg_ok",
				error: "span=msg_err",
				isExist: {
					mobile: function(a) {
						var b = "/user/reg/validate",
							d = {
								rule: "mobile",
								data: h.val()
							},
							e = function(b) {
								switch (b) {
								case 0:
									a(""), t = !0, n.is(":hidden") || o.show();
									break;
								case 7:
									a('手机号已存在。忘记密码 <a href="/user/findPwd?frm=reg_num_exist">点此找回</a>'), t = !1;
									break;
								case 8:
									a("手机号格式错误。"), t = !1;
									break;
								case 9:
									a("您的IP存在异常行为,请联系客服电话:4000800577,感谢支持!"), t = !1;
									break;
								default:
									a("")
								}
							};
						c.post(b, d, e, "json")
					},
					email: function(a) {
						var b = "/user/reg/validate",
							d = {
								rule: "email",
								data: $email.val()
							},
							e = function(b) {
								b == 1 ? a("邮箱已经存在。") : b == 3 ? a("邮箱格式错误。") : a("")
							};
						c.post(b, d, e, "json")
					},
					nickname: function(a) {
						var b = "/user/reg/validate",
							d = {
								rule: "nickname",
								data: i.val()
							},
							e = function(b) {
								b == 2 ? a("用户名已经存在。") : b == 4 ? a("支持中英文、数字、下划线，限长10个汉字。") : b == 5 ? a("用户名已经存在。") : a("")
							};
						c.post(b, d, e, "json")
					},
					password: function(a) {
						c.trim(j.val()) == "" ? a("密码不能都为空格。") : a("")
					},
					conf_password: function(a) {
						j.val() === k.val() ? a("") : a("两次密码输入不一致，请重新输入。")
					},
					code: function(a) {
						l.val().length != 6 ? a("验证码为6位数字。") : a("")
					}
				}
			};
		return e.validate(a, f, g, m)
	}
	function v(a) {
		if (!a) return;
		var b = a.length,
			d = 0;
		if (b < 6 || b > 32) {
			m.hide();
			return
		}
		m.show(), b > 8 ? d += 2 : d += 1, a.match(/[a-z]/) && (d += 1), a.match(/[A-Z]/) && (d += 1), a.match(/[0-9]/) && (d += 1), a.match(/[!,@#$%^&*?_~]/) && (d += 1), d >= 2 && d < 4 ? (c(".strength_l").addClass("pw_strength_color"), c(".strength_m").removeClass("pw_strength_color")) : d >= 4 && d < 6 ? (c(".strength_m").addClass("pw_strength_color"), c(".strength_h").removeClass("pw_strength_color")) : d == 6 && c(".strength_h").addClass("pw_strength_color")
	}
	function w(a, b) {
		setTimeout(function() {
			if (!a) return;
			a--, b(a), w(a, b)
		}, 1e3)
	}
	function x() {
		if (!t) return;
		if (x.isPending) return;
		var a = function(a, b) {
				if (a != 0) {
					c("#checkCodeForm .code_state").length || c("#checkCodeForm").append('<span style="color:#ff6699;position:relative;top:-20px;left:12px;" class="code_state">验证码错误。</span>');
					return
				}
				var d = 60,
					e = "/user/reg/send_sm_captcha",
					f = {
						mobile: h.val()
					};
				x.isPending = !0, l[0].focus(), f.checkcode = b;
				var g = function(a) {
						a.code == 0 ? w(d, function(a) {
							a ? p.val("重新发送 (" + a + "秒)") : (x.isPending = !1, p.val("获取短信验证码"))
						}) : (x.isPending = !1, c(document.body).trigger("getTextCode.ok"))
					};
				c.post(e, f, g, "json")
			};
		g.showCheckCode(a)
	}
	function y() {
		u();
		var a = {
			placeholderCSS: {
				"line-height": "34px"
			}
		};
		c("#registerForm input[placeholder]").placeholder(a), j.focus(function() {
			n.show()
		}).on("input propertychange", function() {
			v(c(this).val())
		}), k.focus(function() {
			t && o.show()
		}), p.click(function() {
			x()
		}), r.click(function() {
			c(this).next(".more_way").toggle()
		})
	}
	var c = a("jquery"),
		d = a("component/urlHandle"),
		e = a("component/validate"),
		f = a("app/checkcode"),
		g = a("app/login"),
		h = c("#mobile"),
		i = c("#nickname"),
		j = c("#password"),
		k = c("#conf_password"),
		l = c("#code"),
		m = c(".pw_safe"),
		n = c(".confpass"),
		o = c(".verifcode"),
		p = c("#get_code"),
		q = c(".regErrorMessage"),
		r = c(".more_way_wrap h4"),
		s = "register_with_mobile",
		t;
	b.init = y
});