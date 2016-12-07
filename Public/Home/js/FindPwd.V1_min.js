if (!window.XMLHttpRequest) {
	window.XMLHttpRequest = function() {
		var a = ["MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP.2.6", "Msxml2.XMLHTTP", "Microsoft.XMLHTTP", "MSXML.XMLHTTP"];
		for (var c = 0; c < a.length; c++) {
			try {
				return new ActiveXObject(a[c])
			} catch (b) {}
		}
		return null
	}
}
Function.createDelegate = function(a, b) {
	return function() {
		return b.apply(a, arguments)
	}
};
if (typeof Autohome == "undefined") {
	var Autohome = {}
}
if (!Autohome.exetend) {
	Autohome.exetend = function(b, a) {
		for (var c in a) {
			b[c] = a[c]
		}
		return b
	}
}
Autohome.Ajax = function(a, c, b) {
	this.options = Autohome.exetend({
		_xmlHttpRequest: new XMLHttpRequest(),
		url: "",
		body: "",
		backFun: function() {
			return null
		},
		method: "POST"
	}, a || {});
	if (c && b) {
		this.options.body = c;
		this.options.backFun = b;
		this.options.url = a
	}
	this.send = function() {
		this.options._xmlHttpRequest.open(this.options.method, this.options.url, true);
		this.options._xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
		this.options._xmlHttpRequest.onreadystatechange = Function.createDelegate(this, this._completed);
		this.options._xmlHttpRequest.send(this.FormatBody())
	};
	this.FormatBody = function() {
		var d = "";
		for (var e in this.options.body) {
			d += e + "=" + escape(this.options.body[e]) + "&"
		}
		return d
	};
	this._completed = function() {
		if (this.options._xmlHttpRequest.readyState == 4) {
			this.options.backFun(this.options._xmlHttpRequest.responseText, this.options._xmlHttpRequest)
		}
	};
	this.send()
};

function $(a) {
	return typeof(a) == "string" ? document.getElementById(a) : a
}
String.prototype.trim = function() {
	return this.replace(/(^[ |　]*)|([ |　]*$)/g, "")
};
if (typeof Autohome == "undefined") {
	var Autohome = {}
}
Autohome.extend = function(a, c) {
	for (var b in c) {
		a[b] = c[b]
	}
	return a
};

function GetStrLength(b) {
	var a = /[\u4E00-\u9FA5]/g;
	var c = b.match(a);
	if (c != null) {
		return (c + "").split(",").length + b.length
	}
	return b.length
}
function RomancePr(a) {
	if (a && a.Pr) {
		a.Pr.className = a.className;
		$(a.Pr).innerHTML = a.Msg
	}
}
Autohome.Validate = function() {};
Autohome.Validate.prototype = {
	data: {},
	returnValue: function() {
		return {
			success: false,
			Msg: "",
			ErrorNo: 0
		}
	},
	RegCheckHandset: function(a) {
		return /^1[3,4,5,6,7,8,9]{1}[0-9]{9}$/.test(a)
	},
	RegEmail: function(a) {
		return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(a)
	},
	IsNumber: function(a) {
		return /^[0-9]*$/.test(a)
	},
	IsLetter: function(a) {
		return /^[a-zA-Z]*$/.test(a)
	},
	RegPassword: function(b) {
		var a = 0;
		if (b.match(/[a-zA-Z]/ig)) {
			a++
		}
		if (b.match(/[0-9]/ig)) {
			a++
		}
		if (b.match(/([^a-z0-9A-Z])/)) {
			a++
		}
		if (b.length < 6) {
			a = 1
		}
		return a
	},
	RegUserName: function(b) {
		var a = b;
		b = b.replace(/[\u4E00-\u9FA5]/g, "");
		b = b.replace(/[0-9a-zA-Z]/g, "");
		b = b.replace(/[_]/g, "");
		if (this.RegCheckHandset(a)) {
			return 2
		} else {
			if (this.RegEmail(a)) {
				return 3
			} else {
				if (b == "" && GetStrLength(a) > 1 && GetStrLength(a) < 21) {
					return 1
				} else {
					return 0
				}
			}
		}
	},
	Password: function(c) {
		var a = $(c);
		var b = this.returnValue();
		if (a.value.substring(0, 1) == " " || a.value.substring(a.value.length - 1) == " ") {
			b.Msg = Msg.PasswordNotNull
		} else {
			if (a.value.trim() == "") {
				b.Msg = Msg.PasswordPleaseEnter
			} else {
				if (a.value.trim().length < 6 || a.value.trim().length > 25) {
					b.Msg = Msg.PasswordLengthError
				} else {
					if (new Autohome.Validate().IsLetter(a.value.trim())) {
						b.Msg = Msg.PasswordNotAllLetters
					} else {
						if (new Autohome.Validate().IsNumber(a.value.trim())) {
							b.Msg = Msg.PasswordNotAllDigital
						} else {
							if (a.value.trim() == $("NickName").value) {
								b.Msg = Msg.PasswordNotTheSameUserName
							} else {
								b.Msg = Msg.PasswordSuccess;
								b.success = true
							}
						}
					}
				}
			}
		}
		return b
	},
	RePassword: function(f, a) {
		var b = $(f);
		var d = $(a);
		var e = this.returnValue();
		if (b.value.substring(0, 1) == " " || b.value.substring(b.value.length - 1) == " ") {
			e.Msg = Msg.PasswordNotNull
		} else {
			if (b.value.trim() == "") {
				e.Msg = Msg.RePasswordError1
			} else {
				if (b.value.trim() != d.value.trim()) {
					e.Msg = Msg.RePasswordError2
				} else {
					var c = this.Password(d);
					if (c.success == false) {
						e.Msg = c.Msg
					} else {
						e.Msg = "";
						e.success = true
					}
				}
			}
		}
		return e
	},
	CheckPasswordIsStrong: function(b) {
		var a = 0;
		if (b.match(/[a-zA-Z]/ig)) {
			a++
		}
		if (b.match(/[0-9]/ig)) {
			a++
		}
		if (b.match(/([^a-z0-9A-Z])/)) {
			a++
		}
		if (b.length < 6) {
			a = 1
		}
		return a
	}
};
var Msg = {
	UserNameInfoRequire: "请输入已绑定的手机号/邮箱/用户名",
	UserNameNotNull: '<i class="ic_wrong"></i>用户名不能为空',
	UserNameInNotExists: '<i class="ic_wrong"></i>该用户名不存在',
	UserNameNotBind: '<i class="ic_wrong"></i>您未绑定手机或邮箱,请联系客服4000-111-168',
	UserNameRole: '<i class="ic_wrong"></i>版主、管理员找回密码请联系工作人员',
	UserNameSuccess: '<i class="ic_right"></i>&nbsp;',
	PassWordNull: "密码不能为空",
	AccountError: "账户或密码错误",
	ValidateCodeNull: "验证码不能为空",
	ValidateCodeLengthError: "验证码不正确",
	FindPwdLimitTheNum: "对不起，您已超过找回密码次数限制",
	ContactService: "您的手机和邮箱不存在，请联系客服",
	RePassword: "<p>请再输入一次上面的密码</p>",
	RePasswordError1: "<p>请再输入一次上面的密码</p>",
	RePasswordError2: "<p>两次输入的密码不一致</p>",
	PasswordInfoRequire: "<p>6-25个字符（字母、数字、符号的组合）；不能单独使用字母、数字或符号；<strong>请注意,不能使用用户名作为密码</strong>。</p>",
	PasswordPleaseEnter: "<p>请填写您的密码</p>",
	PasswordLengthError: "<p>长度错误：密码应为6-25个字符</p>",
	PasswordNotAllLetters: "<p>密码不能为纯字母</p>",
	PasswordNotAllDigital: "<p>密码不能为纯数字</p>",
	PasswordNotTheSameUserName: "<p>密码不能和用户名相同</p>",
	PasswordSuccess: '<i class="ic_right"></i>&nbsp;</span>',
	PasswordLetGetBack: "版主、管理员找回密码请联系工作人员",
	PasswordNotNull: "<p>前后空格不计入密码</p>",
	AccountNotExistsError: "该用户不存在",
	Success: ""
};

function getCursortPosition(c) {
	var b = 0;
	if (document.selection) {
		c.focus();
		var a = document.selection.createRange();
		a.moveStart("character", -c.value.length);
		b = a.text.length
	} else {
		if (c.selectionStart || c.selectionStart == "0") {
			b = c.selectionStart
		}
	}
	return (b)
}
function setCaretPosition(b, c) {
	if (b.setSelectionRange) {
		b.focus();
		b.setSelectionRange(c, c)
	} else {
		if (b.createTextRange) {
			var a = b.createTextRange();
			a.collapse(true);
			a.moveEnd("character", c);
			a.moveStart("character", c);
			a.select()
		}
	}
}
function setCursort(b) {
	var a = getCursortPosition(b);
	setCaretPosition(b, a)
}
function CheckOnkeyPress() {
	if ($("UserName")) {
		$("UserName").onkeypress = function(a) {
			a = (a) ? a : ((window.event) ? window.event : "");
			keyCode = a.keyCode ? a.keyCode : (a.which ? a.which : a.charCode);
			if (keyCode == 13) {
				CheckUser()
			}
		}
	}
}(function() {
	var a = $("SubmitBtn");
	a.onclick = function() {
		isSubmit = true;
		Register()
	};
	CheckUserName()
})();
CheckOnkeyPress();

function ChangePassWord() {
	var d = new Autohome.Validate();

	function b() {
		var f = $("Password");
		var e = $("PasswordPr");
		var g = {
			success: false,
			Msg: "",
			Pr: e,
			className: ""
		};
		f.onfocus = function() {
			if (f.value.trim() == "") {
				g.Msg = Msg.PasswordInfoRequire;
				g.className = "tip_2";
				g.Type = 1;
				RomancePr(g)
			}
		};
		f.onblur = function() {
			var h = {
				success: false,
				Msg: "",
				Pr: e,
				className: "point_orange"
			};
			h = Autohome.extend(h, d.Password(f));
			h.className = h.success ? "verify_tip" : "tip_error_1";
			RomancePr(h);
			c();
			return h
		};
		f.onkeyup = function() {
			var j = d.CheckPasswordIsStrong(this.value.trim());
			var h = $("pwdsbox1");
			var k = $("pwdsbox2");
			var i = $("pwdsbox3");
			if (this.value.trim() == "") {
				j = 0
			}
			if (j == 0) {
				h.className = "pwds";
				k.className = "pwds";
				i.className = "pwds"
			}
			if (j == 1) {
				h.className = "pwdsb";
				k.className = "pwds";
				i.className = "pwds"
			}
			if (j == 2) {
				h.className = "pwdsa";
				k.className = "pwdsb";
				i.className = "pwds"
			}
			if (j == 3) {
				h.className = "pwdsa";
				k.className = "pwdsa";
				i.className = "pwdsb"
			}
		}
	}
	function a() {
		var g = $("Password");
		var f = $("RePassword");
		var e = $("RePasswordPr");
		var h = {
			success: false,
			Msg: "",
			Pr: e,
			className: ""
		};
		f.onfocus = function() {
			if (f.value.trim() == "") {
				h.Msg = Msg.RePassword;
				h.className = "tip_2";
				RomancePr(h)
			}
		};
		f.onblur = a.RePasswordonblur = function(i) {
			h = Autohome.extend(h, d.RePassword(f, g));
			h.className = h.success ? "verify_tip" : "tip_error_1";
			RomancePr(h);
			c();
			return h
		}
	}
	function c(e) {
		var f = false;
		return f
	}
	function c() {
		var e = $("SubmitBtn");
		var f = true;
		f = f && d.Password("Password").success;
		f = f && d.RePassword("RePassword", "Password").success;
		if (f == false) {
			e.disabled = "disabled"
		} else {
			$("pwd").value = "";
			$("pwd").value = hex_md5($("Password").value.trim());
			e.disabled = ""
		}
	}
}
var ut = 0,
	isok = false;

function Register() {
	var UserName = $("UserName");
	var Pr = $("UserNamePr");
	var isResult = ReturnValue(UserName, Pr, 1, null);
	var returnValue = {
		success: false,
		Msg: "",
		Pr: Pr,
		className: "verify_tip"
	};
	new Autohome.Ajax({
		_self: this,
		url: "/password/checkusername",
		body: {
			username: encodeURIComponent($("UserName").value.trim()),
			usertype: ut
		},
		backFun: function(Text) {
			if (Text == undefined) {
				alert("系统维护中")
			} else {
				var o = eval("(" + Text + ")");
				if (o.returncode == 1001) {
					returnValue.Msg = o.result
				} else {
					if (o.returncode == 0) {
						$("UserType").value = ut;
						$("InputConent").value = encodeURIComponent($("UserName").value.trim());
						var send = o.result.split(",");
						if (send == "" || o.result == ",") {
							returnValue.Msg = Msg.UserNameNotBind
						} else {
							if (send[0] == "" || send[1] == "") {
								returnValue.Msg = Msg.UserNameSuccess;
								returnValue.className = "verify_tip";
								if (send[0] == "") {
									$("sendType").value = "email";
									RomancePr(returnValue);
									document.form1.submit()
								} else {
									$("sendType").value = "phone";
									RomancePr(returnValue);
									document.form1.submit()
								}
								return
							} else {
								if (send.length > 1) {
									$("send").style.display = "";
									if (o.result.split(",")[0].trim() != "") {
										$("sendPhone").style.display = "";
										$("phone").innerHTML = o.result.split(",")[0]
									}
									if (o.result.split(",")[1].trim() != "") {
										$("sendEmail").style.display = "";
										$("email").innerHTML = o.result.split(",")[1]
									}
									returnValue.Msg = Msg.UserNameSuccess;
									returnValue.className = "verify_tip"
								} else {
									returnValue.Msg = Msg.UserNameNotBind
								}
							}
						}
					} else {
						returnValue.Msg = '<i class="ic_wrong"></i>' + o.message
					}
				}
			}
			RomancePr(returnValue)
		}
	})
}
var userName = "";

function CheckUserName() {
	var UserName = $("UserName");
	var Pr = $("UserNamePr");
	var btn = $("SubmitBtn");
	btn.disabled = "";
	var returnValue = {
		success: false,
		Msg: "",
		Pr: Pr,
		className: "verify_tip"
	};
	UserName.onfocus = function() {
		if (UserName.value == "") {
			returnValue.Msg = Msg.UserNameInfoRequire;
			returnValue.className = "step-span";
			RomancePr(returnValue);
			isResult = false
		}
	};
	UserName.onblur = function(T) {
		var Validate = new Autohome.Validate();
		var type = Validate.RegUserName(UserName.value.trim());
		if (userName == UserName.value.trim()) {} else {
			userName = UserName.value.trim();
			$("send").style.display = "none"
		}
		returnValue = {
			success: false,
			Msg: "",
			Pr: Pr,
			className: ""
		};

		function CallBackAjax(Text) {
			var b = eval("(" + Text + ")")
		}
		returnValue = Autohome.extend(returnValue, ReturnValue(UserName, Pr, 1, CallBackAjax));
		returnValue.success
	}
}
function ReturnValue(param, Pr, vatype, onclick) {
	var Validate = new Autohome.Validate();
	if (vatype == 1) {
		var type = Validate.RegUserName(param.value.trim());
		ut = type;
		var returnValue = {
			success: false,
			Msg: "",
			Pr: Pr,
			className: ""
		};
		if (type == 0) {
			if (param.value.trim() == "") {
				returnValue.Msg = Msg.UserNameNotNull;
				returnValue.className = "verify_tip"
			} else {
				if (param.value.substring(0, 1) == " " || param.value.substring(param.value.length - 1) == " ") {
					returnValue.Msg = Msg.UserNameInNotExists;
					returnValue.className = "verify_tip"
				} else {
					returnValue.Msg = Msg.UserNameInNotExists;
					returnValue.className = "verify_tip"
				}
			}
			setCursort(param);
			RomancePr(returnValue)
		} else {
			if (onclick) {
				new Autohome.Ajax({
					_self: this,
					url: "/password/checkusername",
					body: {
						username: encodeURIComponent($("UserName").value.trim()),
						usertype: ut
					},
					backFun: function(Text) {
						if (Text == undefined) {
							isok = false;
							alert("系统维护中")
						} else {
							var o = eval("(" + Text + ")");
							if (o.returncode == 1001) {
								returnValue.Msg = o.result;
								returnValue.className = "verify_tip"
							} else {
								if (o.returncode == 0) {
									isok = true;
									returnValue.success = true;
									returnValue.Msg = Msg.UserNameSuccess;
									returnValue.className = "verify_tip"
								} else {
									isok = false;
									returnValue.success = false;
									returnValue.Msg = '<i class="ic_wrong"></i>' + o.message;
									returnValue.className = "verify_tip"
								}
							}
							RomancePr(returnValue)
						}
						if (typeof fun == "function") {
							fun(Text)
						}
					}
				})
			} else {
				returnValue.success = true
			}
		}
		return returnValue
	}
};