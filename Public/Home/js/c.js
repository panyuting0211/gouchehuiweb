var Embellish = Embellish || {};
Embellish.aside = function(B) {
	var A = this;
	this.tag = B.tag || "<span>";
	this.attributes = B.attributes;
	this.html = B.html;
	this.asideElement = $(this.tag, this.attributes).html(this.html);
	this.forElement = B.forElement;
	this.visable = B.visable;
	this.position = B.position ||
	function(C) {
		A.forElement.parent().append(A.asideElement)
	};
	this.set = function(C) {
		A.asideElement.html(C)
	};
	this.get = function() {
		return A.asideElement.html()
	};
	this.show = function(C) {
		A.asideElement.show(C)
	};
	this.hide = function(C) {
		A.asideElement.hide(C)
	};
	this.init = function() {
		this.position.call(this, A.asideElement);
		this.forElement.data("aside", A);
		if (A.visable) {
			A.asideElement.show()
		} else {
			A.asideElement.hide()
		}
	};
	this.init.call(this)
};
var Bitauto = Bitauto || {};
Bitauto.Register = Bitauto.Register || {};
Bitauto.Register.Aside = function(B) {
	B = B || {};
	var A = this;
	this.elementIds = B.elementIds || [""];
	this.elementNames = B.elementNames || [""];
	this.focusClass = B.focusClass || "input_on";
	this.asideElementClass = B.asideElementClass || "tips";
	this.asideElementAttr = B.asideElementAttr || "aside";
	this.asideTag = B.asideTag || "<div>";
	this.asideElements = {};
	this.init = function(C) {
		A.asideElements[C.attr("id") ? C.attr("id") : C.attr("name")] = new Embellish.aside({
			forElement: C,
			html: C.attr(A.asideElementAttr),
			tag: A.asideTag,
			attributes: {
				"class": A.asideElementClass
			},
			visable: C.attr("aside-visable") === "true"
		})
	};
	this.setAside = function() {
		$(A.elementIds).each(function(G, F) {
			var E = $("#" + F);
			A.init(E);
			E.focusin(function() {
				E.addClass(A.focusClass);
				var H = E.nextAll("div.ts_w_lh");
				if (H.length > 0) {
					A.asideElements[F].asideElement.html(E.attr(A.asideElementAttr)).attr("class", A.asideElementClass)
				}
				A.asideElements[F].asideElement.show()
			});
			E.blur(function() {
				E.removeClass(A.focusClass)
			})
		});
		if (A.elementNames.length > 0) {
			for (var D in A.elementNames) {
				var C = $("[name='" + A.elementNames[D] + "']");
				A.init(C);
				C.focusin(function() {
					C.addClass(A.focusClass);
					var E = C.nextAll("div.ts_w_lh");
					if (E.length > 0) {
						A.asideElements[A.elementNames[D]].asideElement.html(C.attr(A.asideElementAttr)).attr("class", A.asideElementClass)
					}
					A.asideElements[A.elementNames[D]].asideElement.show()
				});
				C.blur(function() {
					C.removeClass(A.focusClass)
				})
			}
		}
	};
	$(document).ready(function() {
		A.setAside()
	})
};
var RegisterAside = new Bitauto.Register.Aside({
	elementIds: ["LoginName", "txtPwd", "txtUserName", "txtImgCode", "txtMobileCode"],
	elementNames: ["txtPwd"]
});
jQuery.fn.serializeObject = function() {
	var A = {};
	var B = this.serializeArray();
	jQuery.each(B, function() {
		if (A[this.name]) {
			if (!A[this.name].push) {
				A[this.name] = [A[this.name]]
			}
			A[this.name].push(this.value || "")
		} else {
			A[this.name] = this.value || ""
		}
	});
	return A
};
jQuery.fn.getValue = function() {
	if (this.length == 1) {
		return this.val()
	} else {
		var A = jQuery.grep(this, function(D, C) {
			return D.checked || D.selected
		});
		var B = [];
		jQuery(A).each(function() {
			B.push($(this).val())
		});
		return B.join()
	}
};
var Validator = function(D) {
		if (!(this instanceof Validator)) {
			return new Validator(D)
		}
		D = D || {};
		var B = this,
			E = false;
		this.isDisable = false;
		this.items = jQuery([]);
		this.url = D.url;
		this.parameters = jQuery.isFunction(D.parameters) ? D.parameters : function() {
			return D.parameters
		};
		this.form = jQuery(D.form);
		this.button = jQuery(D.button);
		this.eventName = D.eventName === null ? null : (D.eventName || "blur");
		this.result = {
			status: true
		};
		this.msgs = jQuery.extend({
			regex: "正则不匹配",
			required: "请输入必填项",
			type: "不符合类型",
			minLength: "您的输入未超过最小长度",
			maxLength: "您的输入已超过最大长度",
			custom: "不符合自定义验证"
		}, D.msgs || {});
		this.onStart = D.onStart;
		this.onEnd = D.onEnd;
		this.onSuccess = D.onSuccess;
		this.onFail = D.onFail;
		this.onError = function() {
			C(false);
			if ($.isFunction(D.onError)) {
				D.onError.apply(this, this)
			}
		} ||
		function(F) {
			C(false);
			alert("服务器响应异常")
		};
		this.onItemStart = D.onItemStart ||
		function(G) {
			var F = G.elements.last().next("aside");
			if (F) {
				F.remove()
			}
		};
		this.onItemEnd = D.onItemEnd;
		this.onItemSuccess = D.onItemSuccess ||
		function(G) {
			var F = jQuery("<aside>", {
				style: "color:green;",
				text: "√ " + G.getMsg()
			});
			F.insertAfter(G.elements.last())
		};
		this.onItemFail = D.onItemFail ||
		function(G) {
			var F = jQuery("<aside>", {
				style: "color:red;",
				text: "× " + G.getMsg()
			});
			F.insertAfter(G.elements.last())
		};
		this.isContinueSubmitAfterSuccess = ("isContinueSubmitAfterSuccess" in D) ? D.isContinueSubmitAfterSuccess : true;
		this.serverValidate = function() {
			if (!B.isDisable) {
				if (B.onStart) {
					B.onStart.call(B, B)
				}
				var G = $.extend({}, B.form ? B.form.serializeObject(true) : null, B.parameters());
				var F, H;
				B.items.each(function() {
					if (!this.isDisable) {
						G = jQuery.extend(G, this.parameters());
						G[this.name] = this.getValue()
					} else {
						G[this.name] = null
					}
				});
				G["Gamut"] = true;
				F = (function(I, J) {
					return function() {
						if ($.isFunction(I)) {
							I.apply(this, arguments)
						}
						J()
					}
				})(B.onEnd, function() {
					C(false)
				});
				H = B.onFail;
				A(G, F, H, B.onSuccess)
			}
		};
		this.clientValidate = function() {
			if (!B.isDisable) {
				B.items.each(function() {
					this.clientValidate()
				})
			}
		};
		this.validate = function() {
			if (!B.isDisable && !E) {
				C(true);
				B.result = {
					status: true
				};
				B.clientValidate();
				if (B.result.status) {
					B.serverValidate()
				} else {
					if (B.onFail) {
						B.onFail.call(B, B)
					}
					C(false)
				}
			}
		};

		function C(F) {
			E = F
		}
		var A = function(I, G, J, F) {
				var H = B.url;
				jQuery.ajax({
					url: H,
					type: "POST",
					data: I,
					cache: false,
					dataType: "json",
					success: function(N) {
						if (N) {
							if ("items" in N) {
								B.result = N
							}
							var L = N.items || N;
							if (L) {
								B.items.each(function() {
									this.result = {}
								});
								var M;
								for (var K in L) {
									B.items.each(function() {
										M = this;
										if (M.name == K) {
											M.result = L[K];
											return false
										}
									})
								}
							}
							B.processResult(J, F)
						} else {
							if (B.onError) {
								B.onError.call(B, B)
							}
						}
					},
					error: function() {
						if (B.onError) {
							B.onError.call(B, B)
						}
					},
					complete: function() {
						if (G) {
							G.call(B, B)
						}
					}
				})
			};
		this.processResult = function(F, G) {
			B.items.each(function(H) {
				H = this;
				H.processResult(H.onFail, H.onSuccess)
			});
			if (B.result.status == false) {
				if (F) {
					F.call(B, B)
				}
			} else {
				if (G) {
					G.call(B, B)
				}
			}
		};
		this.Item = function(F) {
			if (!(this instanceof B.Item)) {
				return new B.Item(F)
			}
			B.items.push(this);
			var G = this;
			this.isDisable = ("isDisable" in F) ? F.isDisable : false;
			this.withValidatorPrams = !! F.withValidatorPrams || false;
			this.elements = jQuery(F.elements);
			this.name = F.name || this.elements.attr("name") || this.elements.attr("id");
			this.value = F.value;
			this.parameters = jQuery.isFunction(F.parameters) ? F.parameters : function() {
				return F.parameters
			};
			this.eventName = F.eventName === null ? null : (F.eventName || B.eventName);
			this.result = {
				status: true
			};
			this.rules = F.rules;
			this.msgs = jQuery.extend({}, B.msgs, F.msgs || {});
			this.onStart = F.onStart || B.onItemStart;
			this.onEnd = F.onEnd || B.onItemEnd;
			this.onSuccess = F.onSuccess || B.onItemSuccess;
			this.onFail = F.onFail || B.onItemFail;
			this.validate = function() {
				if (!G.isDisable && !B.isDisable) {
					G.clientValidate();
					if (G.result.status) {
						if (B.url) {
							G.serverValidate()
						}
					}
					if (G.onEnd) {
						G.onEnd.call(item, item)
					}
				}
			};
			this.serverValidate = function() {
				if (!G.isDisable && !B.isDisable) {
					var H = jQuery.extend({}, B.parameters(), G.parameters());
					H[G.name] = G.getValue();
					A(H)
				}
			};
			this.clientValidate = function() {
				if (!G.isDisable && !B.isDisable) {
					G.initResult();
					if (G.onStart) {
						G.onStart.call(G, G)
					}
					if (G.rules) {
						var H = G.getValue();
						if (G.result.status && G.rules.required && H == "") {
							G.setResult(false, "", "required")
						}
						if (G.result.status && G.rules.type && H != "") {
							var I;
							switch (G.rules.type) {
							case "email":
								I = /^[_a-z\d\-\./]+@[_a-z\d\-]+(\.[_a-z\d\-]+)*(\.(info|biz|com|edu|gov|net|am|bz|cn|cx|hk|jp|tw|vc|vn))$/ig;
								break;
							case "int":
								I = /^\d+$/ig;
								break;
							case "float":
								I = /^\d+\.?\d*$/ig;
								break;
							case "chinese":
								I = /^[\u4e00-\u9fa5]+$/ig;
								break;
							case "english":
								I = /^[a-zA-Z\-\s]+$/ig;
								break;
							case "url":
								I = /^(file|gopher|news|nntp|telnet|http|ftp|https|ftps|sftp):\/\/[^\s]*$/ig;
								break;
							case "mobile":
								I = /^(13|15|18|17|14)\d{9}$/ig;
								break;
							case "tel":
								I = /^((\d{3}-)?\d{8})|((\d{4}-)?\d{7})$/ig;
								break;
							case "phone":
								I = /^(((13|15|18)\d{9})|(((\d{3}-)?\d{8})|((\d{4}-)?\d{7})))$/ig;
								break;
							case "qq":
								I = /^(([1-9][0-9]{4,})|(\w+@(\w+.)+[a-z0-9_\-]+))$/ig;
								break;
							case "zip":
								I = /^[0-9]\d{5}(?!\d)$/ig;
								break;
							case "idcard":
								I = /^(\d{17}|\d{14})(\d|x)$/ig;
								break;
							case "ip":
								I = /^((?:(?:25[0-5]|2[0-4]\d|[01]?\d?\d)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d?\d))$/ig;
								break;
							case "date":
								I = /^(?=\d)(?:(?!(?:1582(?:\.|-|\/)10(?:\.|-|\/)(?:0?[5-9]|1[0-4]))|(?:1752(?:\.|-|\/)0?9(?:\.|-|\/)(?:0?[3-9]|1[0-3])))(?=(?:(?!000[04]|(?:(?:1[^0-6]|[2468][^048]|[3579][^26])00))(?:(?:\d\d)(?:[02468][048]|[13579][26]))\D0?2\D29)|(?:\d{4}\D(?!(?:0?[2469]|11)\D31)(?!0?2(?:\.|-|\/)(?:29|30))))(\d{4})([-\/.])(0?\d|1[012])\2((?!00)[012]?\d|3[01])(?:$|(?=\x20\d)\x20))?((?:(?:0?[1-9]|1[012])(?::[0-5]\d){0,2}(?:\x20[aApP][mM]))|(?:[01]\d|2[0-3])(?::[0-5]\d){1,2})?$/ig;
								break
							}
							if (I && I.test && !G.regexTest(I, H)) {
								G.setResult(false, "", "type")
							}
						}
						if (G.result.status && G.rules.regex && G.rules.regex.test && H != "") {
							if (!G.regexTest(G.rules.regex, H)) {
								G.setResult(false, "", "regex")
							}
						}
						if (G.result.status && G.rules.custom) {
							if (!G.rules.custom.call(G, G)) {
								G.setResult(false, "", "custom")
							}
						}
						if (G.result.status && G.rules.minLength) {
							if (H.length < G.rules.minLength) {
								G.setResult(false, "", "minLength")
							}
						}
						if (G.result.status && G.rules.maxLength) {
							if (H.length > G.rules.maxLength) {
								G.setResult(false, "", "maxLength")
							}
						}
						G.processResult(G.onFail)
					}
				}
			};
			this.regexTest = function(I, H) {
				I.lastIndex = 0;
				return I.test(H)
			};
			this.setResult = function(I, J, H) {
				G.result.status = I;
				G.result.msg = J;
				G.result.key = H;
				B.result.status &= I
			};
			this.initResult = function() {
				G.setResult(true, "", "")
			};
			this.processResult = function(H, I) {
				if (G.result.status == true && I) {
					I.call(G, G)
				} else {
					if (G.result.status == false && H) {
						B.result.status = false;
						H.call(G, G)
					}
				}
			};
			this.getValue = function() {
				var H;
				if (G.value) {
					H = G.value.call ? G.value.call(G, G) : G.value
				} else {
					H = this.elements.getValue()
				}
				return H
			};
			this.isFunc = function(H) {
				return typeof(H) === "function"
			};
			this.getMsg = function(J) {
				var H, I;
				if ( !! J) {
					H = G.msgs[J], I = G.isFunc(H) ? H.call(G, G) : H;
					return I || ""
				}
				if (G.result.key && G.msgs && G.msgs[G.result.key]) {
					H = G.msgs[G.result.key];
					I = G.isFunc(H) ? H.call(G, G) : H;
					G.result.msg = I
				}
				return G.result.msg || ""
			};
			if (this.eventName) {
				this.elements.on(G.eventName, G.validate)
			}
			if (G.rules && G.rules.maxLength) {
				this.elements.attr("maxlength", G.rules.maxLength);
				this.elements.bind("keydown keyup onchange propertychange", function(J) {
					J = J || window.event;
					var H = J.keyCode || J.which;
					var I = [8, 46, 35, 36, 37, 38, 39, 40];
					var K = G.rules.maxLength - G.getValue().length;
					if (jQuery.inArray(H, I) < 0 && K <= 0) {
						if (document.all) {
							J.returnValue = false
						} else {
							J.preventDefault()
						}
					}
					if (G.rules.onMaxLengthChange) {
						G.rules.onMaxLengthChange.call(this, K, G.rules.maxLength)
					}
				})
			}
		};
		if (B.button) {
			B.button.on("click", function() {
				B.validate()
			})
		}
	};
Validator.prototype.removeItem = function(A) {
	this.items = $($.grep(this.items, function(B, C) {
		if (B.name === A) {
			B.elements.unbind(B.eventName, B.validate);
			return true
		}
		return false
	}, true));
	return this
};
Validator.prototype.clearItems = function() {
	this.items = jQuery([])
};
Validator.prototype.getItem = function(A) {
	var B = null;
	this.items.each(function(D, C) {
		if (C.name === A) {
			B = C;
			return false
		}
	});
	return B
};
Validator.prototype.getItems = function(B) {
	var A = jQuery([]);
	B = B;
	this.items.each(function(D, C) {
		if (jQuery.inArray(C.name, B) > -1) {
			A.push(C)
		}
	});
	return A
};
Validator.prototype.createItems = function(C) {
	var B = this,
		D, E, A;
	for (E = 0, A = C.length; E < A; E++) {
		new B.Item(C[E])
	}
};
Validator.prototype.disableItem = function(A) {
	A = [].concat(A);
	this.items.each(function(C, B) {
		if (jQuery.inArray(B.name, A) > -1) {
			B.isDisable = true
		}
	})
};
Validator.prototype.enableItem = function(A) {
	A = [].concat(A);
	this.items.each(function(C, B) {
		if (jQuery.inArray(B.name, A) > -1) {
			B.isDisable = false
		}
	})
};
Validator.prototype.setEnableState = function(A) {
	this.isDisable = !A
};
Validator.type = {
	"email": "email",
	"int": "int",
	"float": "float",
	"chinese": "chinese",
	"english": "english",
	"url": "url",
	"mobile": "mobile",
	"tel": "tel",
	"phone": "phone",
	"qq": "qq",
	"zip": "zip",
	"idcard": "idcard",
	"ip": "ip",
	"date": "date"
};
var Bitauto = Bitauto || {};
(function(A) {
	var B = function(C) {
			if (!(this instanceof B)) {
				return new B(C)
			}
			this.option = $.extend({}, {
				isFirstRequest: false,
				isValidateMobile: true,
				url: "/ajax/authenservice/mobileverifycode.ashx",
				mobileGeter: null,
				imgCodeGeter: null,
				serverStatuMsgHash: {
					"0": "获取校验短信失败",
					"1": "已将校验码发送到您手机,请填写",
					"2": "校验码获取过于频繁,请稍后再试",
					"3": "该手机号在禁止发送信息的黑名单中",
					"4": "获取校验码已超3次,请24小时后再试",
					"5": "请输入验证码",
					"6": "验证码不正确",
					"7": "验证码正确",
					"8": "请输入验证码",
					"9": "验证码不正确"
				},
				eventsCallbacks: {}
			}, C);
			this.eventsCallbacks = this.option.eventsCallbacks
		};
	B.prototype.EnumCreateMobileMsgResult = {
		fail: "0",
		success: "1",
		frequently: "2",
		blackMobile: "3",
		outnumber: "4",
		imgcoderequired: "5",
		imgcodeerror: "6",
		imgcodeisvalid: "7",
		blurimgcoderequired: "8",
		blurimgcodeerror: "9"
	};
	B.prototype._fireEvent = function(F) {
		var D, E = this,
			C = Array.prototype.slice.call(arguments, 1);
		F = [].concat(F);
		if (F.length > 0) {
			$.each(F, function(H, G) {
				D = E.eventsCallbacks[G];
				if (D) {
					D.apply(E, C)
				}
			})
		}
	};
	B.prototype._validate = function() {
		if (this.option.isValidateMobile && (!this.option.mobileGeter || !this.option.mobileGeter())) {
			this._fireEvent("validateFail", "noMobile");
			return false
		} else {} if (!this.option.isFirstRequest && (!this.option.imgCodeGeter || !this.option.imgCodeGeter())) {
			this._fireEvent("validateFail", "noImgCode");
			return false
		}
		return true
	};
	B.prototype.createMobileMsg = function(E) {
		if (this._validate()) {
			var D = this,
				C = $.extend({
					url: D.option.url,
					type: "POST",
					data: {
						"popType": 0,
						"r": Math.random(),
						"LoginName": D.option.mobileGeter(),
						"imgCode": D.option.imgCodeGeter(),
						"guid": D.option.guid ? D.option.guid() : null
					}
				}, E);
			if (!C.data.isDisableBeforeSend) {
				D._fireEvent("begoreSend")
			}
			return $.ajax(C).then(function(H, F, G) {
				if (H == D.EnumCreateMobileMsgResult.success) {
					D.option.isFirstRequest = false;
					D._fireEvent("success", H);
					D._fireEvent("notFirstRequest")
				} else {
					if (H == D.EnumCreateMobileMsgResult.imgcodeisvalid) {
						D._fireEvent("validateImgCodeSuccess")
					} else {
						if (H == D.EnumCreateMobileMsgResult.blurimgcoderequired || H == D.EnumCreateMobileMsgResult.blurimgcodeerror) {
							D._fireEvent("validateImgCodeFail", H)
						} else {
							D._fireEvent("fail", H)
						}
					}
				}
			}, function(H, F, G) {
				D._fireEvent("fail", D.EnumCreateMobileMsgResult.fail)
			}).always(function(F) {
				D._fireEvent("sendComplated", F)
			})
		}
	};
	A.MobileMsgCreator = B
})(Bitauto);
var Bitauto = Bitauto || {};
(function(A, B) {
	var D = function() {
			var E = function() {
					return (((1 + Math.random()) * 65536) | 0).toString(16).substring(1)
				};
			return (E() + E() + "-" + E() + "-" + E() + "-" + E() + "-" + E() + E() + E())
		};
	var C = function(F) {
			if (!(this instanceof C)) {
				return new C(F)
			}
			var G = this,
				E = null;
			this.option = B.extend({
				imgSelector: "#imgCode",
				url: "/authenservice/common/CheckCode.aspx?guid=",
				delegateChangeOption: [{
					eventName: "click",
					selector: '[act="ChangeImgCode"]'
				}]
			}, F);
			this.img = B(this.option.imgSelector);
			this.guid = null;
			this.imgUrl = null;
			this.getGuid = function() {
				return E
			};
			this.changeGuid = function() {
				E = D()
			};
			B.each(this.option.delegateChangeOption, function(H, I) {
				B(document).on(I.eventName, I.selector, B.proxy(G.changeImg, G))
			})
		};
	C.prototype.changeImg = function() {
		this.changeGuid();
		this.img.attr("src", this.getImgUrl());
		return false
	};
	C.prototype.getImgUrl = function() {
		var E = this.getGuid();
		return this.option.url + E
	};
	A.ImgCoder = C
})(Bitauto, $);
var Bitauto = Bitauto || {};
(function(A) {
	DomainManager = A.Bitauto.DomainManager = A.Bitauto.DomainManager || {};
	DomainManager.setBaseDomain = function(B) {
		if (!B) {
			B = DomainManager.getBaseDomain()
		}
		A.document.domain = B
	};
	DomainManager.getBaseDomain = function() {
		var B = A.document.domain.split("."),
			C = B.length;
		return B[C - 2] + "." + B[C - 1]
	};
	DomainManager.getQichetongDomain = function() {
		var B = {
			"qichetong.com": "i",
			"bitauto.com": "i",
			"yiche.com": "i",
			"maichebao.com": "i",
			"taoche.com": "i",
			"leche.com": "i",
			"yichemall.com": "i",
			"huimaiche.com": "qct"
		},
			C = DomainManager.getBaseDomain(),
			D = (C in B) ? B[C] : "i";
		return "http://" + D + "." + C + "/"
	};
	DomainManager.createQichetongUrl = function(B) {
		if (B.indexOf("/") == 0) {
			B = B.substr(1)
		}
		return DomainManager.getQichetongDomain() + B
	}
})(this);
(function(A, B) {
	A.Bitauto = Bitauto || {};
	var C = function(F, G, E, I) {
			var H, J, D;
			I = B.type(I) === "number" ? I : 3000;
			B.each(E, function(M, L) {
				var K = B.getScript(L).done(function() {
					E.pop();
					F.notify({
						authUrl: L,
						success: true
					});
					if ( !! !E[0]) {
						F.resolve()
					}
				}).fail(function() {
					F.notify({
						authUrl: L,
						success: false
					})
				});
				if (L.indexOf(G) > -1) {
					J = K.fail(F.reject)
				}
			});
			D = B.Deferred(function(K) {
				H = setTimeout(K.resolve, I)
			}).promise();
			B.when(J, D).done(F.resolve);
			return F.always(function() {
				if (H > 0) {
					clearTimeout(H)
				}
			}).promise()
		};
	A.AuthLoader = {
		load: function(E, D, F) {
			return new C(B.Deferred(), E, D, F)
		}
	}
})(Bitauto, $);
var Bitauto = Bitauto || {};
(function(A, B) {
	var C = function(D) {
			if (!(this instanceof C)) {
				return new C(D)
			}
			var E = this;
			this.option = B.extend({}, {
				container: "#tabContainer",
				tabTag: "li",
				event: "click",
				tabDataName: "tab-data",
				defaultCurrentTabIdent: "li[currentTab]",
				onTabChange: null
			}, D);
			this.currentTab = null;
			this.lastTab = null;
			this.container = B(this.option.container);
			this.container.on("tab:change", this.option.onTabChange);
			this.container.on(this.option.event, this.option.tabTag, function() {
				if (E.currentTab != this) {
					E.lastTab = E.currentTab;
					E.currentTab = B(this);
					E.container.trigger("tab:change", {
						tabName: B(this).attr(E.option.tabDataName),
						currentTab: E.currentTab,
						lastTab: E.lastTab
					})
				}
			});
			this.currentTab = this.container.find(this.option.defaultCurrentTabIdent);
			this.container.trigger("tab:change", {
				tabName: this.currentTab.attr(this.option.tabDataName),
				currentTab: this.currentTab,
				lastTab: this.lastTab
			})
		};
	C.prototype.bindChange = function(D) {
		this.container.on("tab:change", D)
	};
	C.prototype.unbindChange = function(D) {
		this.container.off("tab:change", D)
	};
	A.TabChanger = C
})(Bitauto, $);
(function(A) {
	var B = function(C) {
			if (!(this instanceof B)) {
				return new B(C)
			}
			C = this.option = $.extend(true, {}, {
				isDisable: true,
				emailInputSelector: "#LoginName",
				containerSelector: "#maillayer",
				listContainerSelector: "#maillList",
				EmailList: null,
				emailSourceParse: function(D) {
					return !!D ? $.map(D, function(F, E) {
						return E
					}) : null
				},
				render: function(E) {
					var D = [];
					$.each(E, function(G, F) {
						D.push("<li" + (G === 0 ? ' class="current">' : ">") + '<a href="javascript:;">' + F + "</a></li>")
					});
					return D.join("")
				},
				events: {
					onEmailInputBlur: function() {
						if (!C.isDisable) {
							this.listContainer.empty();
							this.container.hide()
						}
					},
					onEmailInputFoucs: function() {
						if (!C.isDisable) {}
					},
					onKeyboardUp: function(F) {
						var E = $(C.listContainerSelector + '>li[class="current"]:first'),
							D = E.prev();
						if (D.length) {
							E.removeClass("current");
							D.addClass("current")
						}
					},
					onKeyboardDown: function(E) {
						var D = $(C.listContainerSelector + '>li[class="current"]:first'),
							F = D.next();
						if (F.length) {
							D.removeClass("current");
							F.addClass("current")
						}
					},
					onKeyboardEnter: function(E) {
						var D = $(C.listContainerSelector + '>li[class="current"]:first'),
							F = D.children().text();
						if (F.length) {
							$(C.emailInputSelector).val(F).blur()
						}
					},
					onEmailInputKeyUp: function(E) {
						if (!C.isDisable) {
							if (E.keyCode === 38) {
								this.events.onKeyboardUp(E)
							} else {
								if (E.keyCode === 40) {
									this.events.onKeyboardDown(E)
								} else {
									if (E.keyCode === 13) {
										this.events.onKeyboardEnter(E)
									} else {
										var F = this.emailInput.val(),
											D = this.filterEmail(F, this.option.EmailList),
											G = this.option.render(D);
										this.listContainer.html(G);
										if (D.length) {
											if (this.container.css("display") === "none") {
												this.container.show()
											}
										} else {
											if (this.container.css("display") != "none") {
												this.container.hide()
											}
										}
									}
								}
							}
						}
					},
					onMouseOverEmailItem: function(D) {
						if (!C.isDisable) {
							$(D.delegateTarget).children('li[class="current"]').removeClass("current");
							$(this).toggleClass("current");
							$(C.emailInputSelector).val($(this).children().text())
						}
					}
				}
			}, C);
			this.option.EmailList = this.option.emailSourceParse(this.option.EmailList);
			this.events = this.option.events;
			this.emailInput = $(this.option.emailInputSelector);
			this.container = $(this.option.containerSelector);
			this.listContainer = this.container.find(this.option.listContainerSelector);
			this.listContainer.on("mouseover", "li", this.events.onMouseOverEmailItem);
			this.emailInput.on({
				"blur": $.proxy(this.events.onEmailInputBlur, this),
				"focus": $.proxy(this.events.onEmailInputFoucs, this),
				"keyup": $.proxy(this.events.onEmailInputKeyUp, this)
			})
		};
	B.prototype = $.extend(B.prototype, {
		filterEmail: function(G, E) {
			var D, C = /^[^@]+@\S+$/ig,
				H = G.match(C),
				I, F;
			if (H) {
				I = H[0].replace(/@\S+$/ig, "");
				F = H[0].replace(/^\S+@/, "");
				D = $.grep(E, function(J, K) {
					return J.indexOf(F) > -1
				})
			} else {
				I = G.replace(/@$/, "");
				D = E
			}
			D = $.map(D, function(J) {
				return I + J
			});
			return D
		}
	});
	A.AutoEmail = B
})(Bitauto);
(function(A, B) {
	var C = function(F) {
			if (!(this instanceof C)) {
				return new C(F)
			}
			var G, I = this,
				H = B("#tishi_jym"),
				D = B("#hidReturnUrl").val(),
				F = this.option = B.extend(true, {
					loadAuthsTimeOut: 2000,
					RegMode: "mobile",
					eventName: "blur",
					ValidatorOption: {
						url: "/Ajax/Authenservice/register.ashx",
						button: B("#submit"),
						form: B("#form1"),
						parameters: function() {
							return {
								"requestAction": "submit",
								"regMode": I.option.RegMode
							}
						},
						onStart: function() {
							H.hide()
						},
						onItemFail: function(K) {
							var J = K.elements.data("aside");
							K.elements.removeClass("input_text");
							K.elements.addClass("input_border_red");
							if (B.browser.msie) {
								if (K.elements.data("brother")) {
									K.elements.data("brother").removeClass("input_text");
									K.elements.data("brother").addClass("input_border_red")
								}
							}
							if (J) {
								H.hide();
								J.asideElement.prop("class", "prompt");
								J.set(I.createAsideHtml(K.getMsg()));
								J.show()
							}
						},
						onItemSuccess: function(K) {
							K.elements.removeClass("input_border_red");
							K.elements.addClass("input_text");
							var J = K.elements.data("aside");
							J.asideElement.prop("class", "prompt");
							J.set(I.createAsidePassedHtml(K.getMsg("success")));
							J.show()
						},
						onFail: function() {},
						onSuccess: function() {
							if (I.validator.result.registeResult === 1) {
								G.isDisable = true;
								A.AuthLoader.load(A.DomainManager.getBaseDomain(), I.validator.result.hideImg, F.loadAuthsTimeOut).done(function() {
									location.href = "/authenservice/registersimple/activatesuccess.html?returnurl=" + encodeURIComponent(D)
								}).fail(function() {
									alert("登录失败，请重新登录");
									location.href = "/"
								})
							}
						},
						onError: function() {
							alert("注册失败,请刷新页面重新注册")
						}
					},
					ValidatorItemsOptions: [{
						elements: "[type='password']",
						name: "txtPwd",
						rules: {
							required: true,
							regex: /^[A-Za-z0-9]{6,16}$/,
							minLength: 6,
							maxLength: 16
						},
						msgs: {
							regex: "密码应为6-16个英文字母或数字",
							required: "请输入密码",
							minLength: " 密码应为6-16个英文字母或数字",
							maxLength: " 密码应为6-16个英文字母或数字"
						}
					}, {
						elements: "#txtUserName",
						name: "txtUserName",
						rules: {
							required: true,
							regex: /^[\u4e00-\u9fa5a-z0-9_]{3,16}$/i,
							minLength: 3,
							maxLength: 16
						},
						msgs: {
							required: "请输入用户名",
							regex: "用户名应为3-16个汉字或英文",
							minLength: "用户名应为3-16个汉字或英文",
							maxLength: "用户名应为3-16个汉字或英文",
							custom: "用户名已存在请重新输入",
							filter: "此用户名不可使用",
							success: "注册成功后将不可修改"
						}
					}, {
						elements: B("#txtImgCode"),
						name: "txtImgCode",
						parameters: function() {
							return {
								"requestAction": "submit",
								"regMode": I.option.RegMode,
								"LoginName": B("#LoginName").val()
							}
						},
						rules: {
							required: true
						},
						msgs: {
							errorcode: "验证码不正确",
							required: "请输入图中字母"
						}
					}, {
						elements: B("#txtMobileCode"),
						name: "txtMobileCode",
						onStart: function() {
							H.hide()
						},
						parameters: function() {
							return {
								"requestAction": "submit",
								"regMode": I.option.RegMode,
								"LoginName": B("#LoginName").val()
							}
						},
						rules: {
							required: true
						},
						msgs: {
							required: "请输入校验码",
							errorcode: "校验码不正确"
						}
					}, {
						elements: B("#cbxAgree"),
						name: "cbxAgree",
						eventName: "blur",
						value: function() {
							return this.elements.attr("checked") === "checked"
						},
						rules: {
							custom: function() {
								return this.getValue()
							}
						},
						onSuccess: function() {
							G.button.removeAttr("disabled").parent().parent().toggleClass("bottom_an_b", false)
						},
						onFail: function() {
							G.button.attr("disabled", "disabled").parent().parent().toggleClass("bottom_an_b", true)
						}
					}],
					MobileItem: {
						elements: "#LoginName",
						name: "LoginName",
						parameters: function() {
							return {
								"requestAction": "submit",
								"regMode": I.option.RegMode,
								"hidReturnUrl": B("#hidReturnUrl").val()
							}
						},
						rules: {
							required: true,
							type: "mobile"
						},
						msgs: {
							type: "请输入正确的11位手机号",
							regex: "请输入正确的11位手机号",
							required: "请输入手机号",
							exccedMobileValidateCountLimit: "该手机号今日不能使用",
							custom: "该手机号已注册，<a href='/AuthenService/login.aspx'>直接登录</a>"
						}
					},
					EmailItem: {
						elements: "#LoginName",
						name: "LoginName",
						parameters: function() {
							return {
								"requestAction": "submit",
								"regMode": I.option.RegMode,
								"hidReturnUrl": B("#hidReturnUrl").val()
							}
						},
						rules: {
							required: true,
							type: "email"
						},
						msgs: {
							type: "请输入正确格式的邮箱地址",
							regex: "请输入正确格式的邮箱地址",
							required: "请输入邮箱",
							custom: "该邮箱已注册，<a href='/AuthenService/login.aspx'>直接登录</a>",
							blackemail: "不接受此邮箱注册,请更换邮箱"
						}
					}
				}, F);
			this.events = F.events;
			this.validator = G = new Validator(F.ValidatorOption);
			this.validator.createItems(F.ValidatorItemsOptions);
			this.changeRegMode(this.option.RegMode);
			if (this.option.RegMode) {
				var E = this.option.RegMode;
				this.option.RegMode = null;
				this.changeRegMode(E)
			}
		};
	C.prototype = B.extend(C.prototype, {
		changeRegMode: function(E) {
			if (this.option.RegMode != E) {
				var D = this.ModelChangeDic[E];
				if (D) {
					this.option.RegMode = E;
					D.call(this)
				}
			}
		},
		ModelChangeDic: {
			"mobile": function() {
				this.validator.removeItem("LoginName");
				this.validator.getItem("txtMobileCode").isDisable = false;
				new this.validator.Item(this.option.MobileItem);
				B.each(this.validator.items, function(F, E) {
					var D = E.elements.data("aside");
					if (D) {
						if (E.name === "txtImgCode") {
							D.show()
						} else {
							D.set("");
							D.hide()
						}
					}
				})
			},
			"email": function() {
				this.validator.removeItem("LoginName");
				this.validator.getItem("txtMobileCode").isDisable = true;
				new this.validator.Item(this.option.EmailItem);
				B.each(this.validator.items, function(F, E) {
					var D = E.elements.data("aside");
					if (D) {
						D.set("");
						D.hide()
					}
				})
			}
		},
		createAsideHtml: function(D) {
			return '<em class="wrong">' + D + "</em>"
		},
		createAsidePassedHtml: function(D) {
			return '<em class="correct">' + D + "</em>"
		}
	});
	A.Register = C
})(Bitauto, $);
$(function() {
	var G = new Bitauto.ImgCoder(),
		E = new Bitauto.Register(),
		B = new Bitauto.AutoEmail({
			EmailList: Bitauto.AutoEmailList
		}),
		C = $("#a_CreateMobileCode"),
		A = $("#tishi_jym"),
		F = new Bitauto.MobileMsgCreator({
			isFirstRequest: Bitauto.IsFirstRequestMsg,
			isValidateMobile: true,
			mobileGeter: function() {
				return $("#LoginName").val()
			},
			imgCodeGeter: function() {
				return $("#txtImgCode").val()
			},
			eventsCallbacks: {
				begoreSend: function() {
					C.val("正在获取校验码").addClass("button_jym_hui")
				},
				success: function(M) {
					var K = E.validator.getItem("txtMobileCode"),
						L, J = 60;
					$("#txtImgCode").data("aside").hide();
					C.val(J + "秒后重新获取");
					L = setInterval(function() {
						J--;
						C.val(J + "秒后重新获取");
						if (J === 0) {
							clearInterval(L);
							C.one("click", D).val("重新获取校验码").removeClass("button_jym_hui")
						}
					}, 1000);
					A.show();
					var I = K.elements.data("aside");
					I.asideElement.prop("class", "prompt");
					I.set("");
					I.hide()
				},
				fail: function(M) {
					var J = E.validator.getItem("txtMobileCode"),
						K = J.elements.data("aside"),
						I = F.option.serverStatuMsgHash[M],
						L = {
							"0": function() {},
							"2": function() {
								K = J.elements.data("aside")
							},
							"3": function() {
								K = E.validator.getItem("LoginName").elements.data("aside")
							},
							"4": function() {},
							"5": function() {
								if ($("#imgCodeContainer").css("display") === "none") {
									$("#imgCodeContainer").show()
								}
								K = E.validator.getItem("txtImgCode").elements.data("aside")
							},
							"6": function() {
								K = E.validator.getItem("txtImgCode").elements.data("aside")
							}
						};
					L[M]();
					K.asideElement.attr("class", "prompt");
					K.set(E.createAsideHtml(I));
					K.show();
					C.one("click", D).val("重新获取校验码").removeClass("button_jym_hui")
				},
				validateFail: function(K) {
					var L, J, I;
					if (K == "noMobile") {
						L = E.validator.getItem("LoginName")
					} else {
						if ("noImgCode") {
							L = E.validator.getItem("txtImgCode")
						}
					}
					J = L.elements.data("aside"), I = E.createAsideHtml(L.msgs["required"]);
					J.asideElement.prop("class", "prompt");
					J.set(I);
					J.show();
					C.one("click", D)
				},
				sendComplated: function() {},
				notFirstRequest: function() {}
			}
		}),
		D = function() {
			if (E.option.RegMode === "mobile") {
				F.createMobileMsg()
			}
		},
		H = new Bitauto.TabChanger({
			container: "#tabs",
			tabDataName: "data-tab",
			onTabChange: function(K, M) {
				var L = $("#imgCodeContainer");
				if (M.lastTab) {
					M.lastTab.removeClass("current");
					M.lastTab.show()
				}
				if (M.currentTab) {
					M.currentTab.addClass("current");
					M.currentTab.hide()
				}
				E.validator.form.find(":text,:password").val("");
				E.changeRegMode(M.tabName);
				if (M.tabName === "mobile") {
					B.option.isDisable = true;
					$("#verifyCode").show();
					if (F.option.isFirstRequest) {
						L.hide()
					} else {
						G.changeImg();
						L.show()
					}
				} else {
					if (M.tabName === "email") {
						B.option.isDisable = true;
						G.changeImg();
						$("#imgCodeContainer").show();
						$("#verifyCode").hide()
					}
				}
				$("#lalName").html(M.tabName === "mobile" ? "手机：" : "邮箱：");
				$("#LoginName").attr("placeholder", M.tabName === "mobile" ? "请输入手机号" : "请输入邮箱");
				$(".input_border_red").removeClass("input_border_red");
				$(".input_text").removeClass("input_text");
				if ($.browser.msie) {
					var J = "placeholder" in document.createElement("input"),
						I = "placeholder" in document.createElement("textarea");
					setTimeout(function() {
						E.validator.form.find(":text").each(function() {
							var N = $(this);
							if (!J && !I) {
								N.val(N.attr("placeholder"));
								if (N.attr("placeholder")) {
									N.addClass("placeholder")
								}
							}
						})
					}, 10)
				}
			}
		});
	C.one("click", D)
});
var Bitauto = Bitauto || {};
Bitauto.oAuthLogin = (function() {
	var I = {
		Renren: 0,
		Sina: 1,
		Tencent: 2,
		Kaixin: 3,
		Taobao: 4,
		Baidu: 5,
		Weixin: 6
	};
	var D = location.host.split("."),
		H = D[D.length - 2] + "." + D[D.length - 1],
		F = "bitauto.com";
	var A = {
		Sina: {
			width: 600,
			heigth: 700,
			requestUrl: function(L) {
				return J(F, I.Sina, L)
			},
			openAgentLoginPage: B
		},
		Tencent: {
			width: 600,
			heigth: 700,
			requestUrl: function(L) {
				return J(F, I.Tencent, L)
			},
			openAgentLoginPage: B
		},
		Kaixin: {
			width: 600,
			heigth: 500,
			requestUrl: function(L) {
				return J(F, I.Kaixin, L)
			},
			openAgentLoginPage: B
		},
		Taobao: {
			width: 600,
			heigth: 700,
			requestUrl: function(L) {
				return J(F, I.Taobao, L)
			},
			openAgentLoginPage: B
		},
		Baidu: {
			width: 700,
			heigth: 600,
			requestUrl: function(L) {
				return J(F, I.Baidu, L)
			},
			openAgentLoginPage: B
		},
		Renren: {
			width: 600,
			heigth: 600,
			requestUrl: function(L) {
				return J(F, I.Renren, L)
			},
			openAgentLoginPage: B
		},
		Weixin: {
			width: 600,
			heigth: 600,
			requestUrl: function(L) {
				return J(H, I.Weixin, L)
			},
			openAgentLoginPage: B
		}
	};

	function J(L, N, M) {
		return "http://i." + L + "/UserIntergration/renren/Handler.ashx?source=" + N + "&type=" + M
	}
	function B(N, M, L) {
		return "http://i.bitauto.com/UserIntergration/Renren/TopLogin.aspx?key=" + N + "&type=" + L + "&returnurl=" + (typeof(M) === "function" ? M() : M)
	}
	function E() {
		var M = top === window ? window : top,
			L = M.location.href.indexOf("returnurl=");
		if (L > -1) {
			return encodeURIComponent(M.location.href.substr(L + 1))
		} else {
			return M.location.href
		}
	}
	function G(L) {
		return L in A
	}
	function C(M, L) {
		if (G(M)) {
			var N = A[M];
			if (window.opener != null) {
				window.resizeTo(N.width, N.heigth)
			}
			$.get(N.requestUrl(L), {
				r: Math.random()
			}, function(O) {
				if (O !== "") {
					location.href = O
				}
			}, "text")
		}
	}
	function K(N, M, L, S) {
		if (G(N)) {
			if (M == undefined || M == "") {
				M = ""
			}
			var Q = A[N],
				P = (top === window ? window : top),
				R = true,
				O = Q.openAgentLoginPage(N, E, M);
			if (R) {
				P.location.href = O
			} else {
				P.open(O, N, "width=" + Q.winWidth + ", height=" + Q.winHeight + ", toolbar =no, menubar=no, scrollbars=yes, resizable=no,location=no,status=no")
			}
		}
	}
	return {
		EnumUserIntergrationSource: I,
		openLoginPage: C,
		openAgentLoginPage: K,
		getReturnUrl: E,
		setGetReturnUrl: function(L) {
			E = L
		}
	}
})(); /* http://mths.be/placeholder v2.0.7 by @mathias */
(function(K, E, D) {
	var C = "placeholder" in E.createElement("input"),
		A = "placeholder" in E.createElement("textarea"),
		I = D.fn,
		B = D.valHooks,
		H, L;
	if (C && A) {
		L = I.placeholder = function() {
			return this
		};
		L.input = L.textarea = true
	} else {
		L = I.placeholder = function() {
			var M = this;
			M.filter((C ? "textarea" : ":input") + "[placeholder]").not(".placeholder").bind({
				"focus.placeholder": F,
				"blur.placeholder": G
			}).data("placeholder-enabled", true).trigger("blur.placeholder");
			return M
		};
		L.input = C;
		L.textarea = A;
		H = {
			"get": function(N) {
				var M = D(N);
				return M.data("placeholder-enabled") && M.hasClass("placeholder") ? "" : N.value
			},
			"set": function(O, M) {
				var N = D(O);
				if (!N.data("placeholder-enabled")) {
					return O.value = M
				}
				if (M == "") {
					O.value = M;
					if (O != E.activeElement) {
						G.call(O)
					}
				} else {
					if (N.hasClass("placeholder")) {
						F.call(O, true, M) || (O.value = M)
					} else {
						O.value = M
					}
				}
				return N
			}
		};
		C || (B.input = H);
		A || (B.textarea = H);
		D(function() {
			D(E).delegate("form", "submit.placeholder", function() {
				var M = D(".placeholder", this).each(F);
				setTimeout(function() {
					M.each(G)
				}, 10)
			})
		});
		D(K).bind("beforeunload.placeholder", function() {
			D(".placeholder").each(function() {
				this.value = ""
			})
		})
	}
	function J(N) {
		var M = {},
			O = /^jQuery\d+$/;
		D.each(N.attributes, function(P, Q) {
			if (Q.specified && !O.test(Q.name)) {
				M[Q.name] = Q.value
			}
		});
		return M
	}
	function F(O, M) {
		var N = this,
			Q = D(N);
		if (N.value == Q.attr("placeholder") && Q.hasClass("placeholder")) {
			if (Q.data("placeholder-password")) {
				Q = Q.hide().data("brother").show();
				if (O === true) {
					return Q[0].value = M
				}
				Q.focus()
			} else {
				N.value = "";
				Q.removeClass("placeholder");
				try {
					N == E.activeElement && N.select()
				} catch (P) {}
			}
		}
	}
	function G() {
		var M, N = this,
			R = D(N),
			P = R,
			Q = this.id;
		if (N.value == "") {
			if (N.type == "password") {
				if (!R.data("placeholder-textinput")) {
					try {
						M = R.clone().attr({
							"type": "text"
						})
					} catch (O) {
						M = D("<input>").attr(D.extend(J(this), {
							"type": "text"
						}))
					}
					M.removeAttr("name").removeAttr("id").data({
						"placeholder-password": true,
						"placeholder-id": Q,
						"brother": R
					}).bind("focus.placeholder", F);
					R.data({
						"placeholder-textinput": M,
						"placeholder-id": Q,
						"brother": M
					}).before(M)
				}
				R = R.hide().data("brother").show()
			}
			R.addClass("placeholder");
			R[0].value = R.attr("placeholder")
		} else {
			R.removeClass("placeholder")
		}
	}
}(this, document, jQuery));
$(document).ready(function() {
	$("*[placeholder]").each(function() {
		$(this).placeholder()
	})
});