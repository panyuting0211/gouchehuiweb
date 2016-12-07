/* Global 对象 */

var ossImg = 'http://yichenghui.oss-cn-shanghai.aliyuncs.com/gouchehui/web';
var qianzui = '/index.php';

/* 工具库 */
var tool = {
	browser: {
		browserRedirect: function (pcUrl, wapUrl) {
			var sUserAgent= navigator.userAgent.toLowerCase();
			var bIsIpad= sUserAgent.match(/ipad/i) == "ipad";
			var bIsIphoneOs= sUserAgent.match(/iphone os/i) == "iphone os";
			var bIsMidp= sUserAgent.match(/midp/i) == "midp";
			var bIsUc7= sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
			var bIsUc= sUserAgent.match(/ucweb/i) == "ucweb";
			var bIsAndroid= sUserAgent.match(/android/i) == "android";
			var bIsCE= sUserAgent.match(/windows ce/i) == "windows ce";
			var bIsWM= sUserAgent.match(/windows mobile/i) == "windows mobile";
			if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
				window.location.href = wapUrl;
			}
		}
	},
	event: {
		observe: function(o, type, fn, capture) {
			if (o.addEventListener) {
				o.addEventListener(type, fn, !! capture);
			} else {
				o.attachEvent('on' + type, fn);
			}
		},
		unobserve: function(o, type, fn, capture) {
			if (o.removeEventListener) {
				o.removeEventListener(type, fn, !! capture);
			} else {
				o.detachEvent('on' + type, fn);
			}
		},
		throttle: function(fn, delay) {
			var timer = null;
			return function() {
				var _this = this;
				var args = arguments;
				if (!timer) {
					timer = window.setTimeout(function() {
						fn.apply(_this, args);
						timer = null;
					}, delay || 100);
				}
			};
		},
		debounce: function(fn, delay) {
			var timer = null;
			delay = delay || 100;
			return function() {
				var _this = this;
				var args = arguments;
				window.clearTimeout(timer);
				timer = window.setTimeout(function() {
					fn.apply(_this, args);
				}, delay);
			};
		}
	},
	each: function(o, callback) {
		if (o.length === +o.length) {
			for (var i = 0, l = o.length; i < l; i++) {
				callback(o[i], i);
			}
		} else {
			for (var k in o) {
				o.hasOwnProperty(k) && callback(o[k], k);
			}
		}
	},
	every: function(arr, callback) {
		for (var i = 0, l = arr.length; i < l; i++) {
			if (!callback(arr[i], i)) {
				break;
			}
		}
	},
	get: function(url, callback) {
		var xhr;
		if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest;
		} else {
			xhr = new ActiveXObject('Microsoft.XMLHTTP');
		}
		xhr.open('GET', url, true);
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				if (xhr.status === 200 || xhr.status === 304) {
					callback && callback(xhr.responseText);
				}
			}
		};
		xhr.send();
	},
	getScript: function(src, callback) {
		var loaded = false;
		var script = document.createElement('script');
		script.src = src;
		if (callback) {
			script.onload = script.onreadystatechange = function() {
				if (loaded) {
					return;
				}
				loaded = true;
				if (!this.readyState || /complete|loaded/.test(this.readyState)) {
					callback.call(this);
				}
			};
		}
		document.body.appendChild(script);
	},
	delay: function(fn, milliseconds) {
		milliseconds = milliseconds || 1000;
		window.setTimeout(fn, milliseconds);
	},
	loadcss: function(url) {
		if (document.createStyleSheet) {
			document.createStyleSheet(url);
		} else {
			var link = document.createElement("link");
			link.type = "text/css";
			link.rel = "stylesheet";
			link.href = url;
			document.getElementsByTagName("head")[0].appendChild(link);
		}
	},
	imgcache: (function() {
		var canvas = document.createElement('canvas'),
			storage = window.localStorage,
			ctx, support = !! (storage && canvas.toDataURL && canvas.getContext && (ctx = canvas.getContext('2d')));
		return {
			support: support,
			get: function(key, config) {
				var ver = config && config.ver,
					ver_key = key + '_ver',
					src;
				if (!support) return false;
				if (ver && storage.getItem(ver_key) == ver && (src = storage.getItem(key))) {
					return src;
				}
				return false;
			},
			set: function(key, src, config, callback) {
				var ver = config && config.ver,
					ver_key = key + '_ver',
					img = new Image,
					imgonload;
				if (!support) return false;
				if (!ver) return false;
				img.crossOrigin = '';
				img.src = src;
				imgonload = function() {
					canvas.width = img.naturalWidth || img.width;
					canvas.height = img.naturalHeight || img.height;
					ctx.drawImage(img, 0, 0);
					src = canvas.toDataURL();
					storage.setItem(key, src);
					storage.setItem(ver_key, ver);
					callback && callback('succeed');
				};
				if (img.complete) {
					imgonload();
				} else {
					img.onload = img.onerror = function(e) {
						e = e || window.event;
						if (e.type === 'load') {
							imgonload();
						} else {
							callback && callback('failed');
						}
					};
				}
			},
			remove: function(key) {
				if (!support) return false;
				storage.removeItem(key);
				storage.removeItem(key + '_ver');
			}
		};
	}()),
	jscodecache: {
		get: function(key, config, src, callback) {
			var code, script, ver = config && config.ver,
				ver_key = key + '_ver',
				storage = window.localStorage,
				getScript = tool.getScript;
			if (ver && storage) {
				if (storage.getItem(ver_key) == ver && (code = storage.getItem(key))) {
					try {
						eval(code);
						callback && callback('cache');
					} catch (ex) {
						getScript(src, callback);
					}
				} else {
					getScript(src, callback);
				}
			} else {
				getScript(src, callback);
			}
		},
		remove: function(key) {
			var storage;
			if (storage = window.localStorage) {
				storage.removeItem(key);
				storage.removeItem(key + '_ver');
			}
		}
	},
	cookie: {
		set: function(key, val, path, domain, expireseconds) {
			val = escape(val);
			var exdate, str = key + '=' + val;
			str += path ? ';path=' + path : '';
			str += domain ? ';domain=' + domain : '';
			if (expireseconds != undefined) {
				exdate = new Date;
				exdate.setTime(exdate.getTime() + expireseconds * 1000);
				str += ';expires=' + exdate.toGMTString();
			}
			document.cookie = str;
		},
		get: function(key) {
			if (document.cookie.length > 0) {
				var start = document.cookie.indexOf(key + '='),
					end;
				if (start !== -1) {
					start += key.length + 1;
					end = document.cookie.indexOf(';', start);
					if (end === -1) end = document.cookie.length;
					return unescape(document.cookie.substring(start, end));
				}
			}
			return '';
		}
	},
	base64: {
		encode: function(str) {
			var out, i, len;
			var c1, c2, c3;
			var encodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
			len = str.length;
			i = 0;
			out = "";
			while (i < len) {
				c1 = str.charCodeAt(i++) & 0xff;
				if (i == len) {
					out += encodeChars.charAt(c1 >> 2);
					out += encodeChars.charAt((c1 & 0x3) << 4);
					out += "==";
					break;
				}
				c2 = str.charCodeAt(i++);
				if (i == len) {
					out += encodeChars.charAt(c1 >> 2);
					out += encodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
					out += encodeChars.charAt((c2 & 0xF) << 2);
					out += "=";
					break;
				}
				c3 = str.charCodeAt(i++);
				out += encodeChars.charAt(c1 >> 2);
				out += encodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
				out += encodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
				out += encodeChars.charAt(c3 & 0x3F);
			}
			return out;
		},
		decode: function(str) {
			var c1, c2, c3, c4;
			var i, len, out;
			var decodeChars = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1];
			len = str.length;
			i = 0;
			out = "";
			while (i < len) {
				do {
					c1 = decodeChars[str.charCodeAt(i++) & 0xff];
				}
				while (i < len && c1 == -1);
				if (c1 == -1) break;
				do {
					c2 = decodeChars[str.charCodeAt(i++) & 0xff];
				}
				while (i < len && c2 == -1);
				if (c2 == -1) break;
				out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
				do {
					c3 = str.charCodeAt(i++) & 0xff;
					if (c3 == 61) return out;
					c3 = decodeChars[c3];
				}
				while (i < len && c3 == -1);
				if (c3 == -1) break;
				out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
				do {
					c4 = str.charCodeAt(i++) & 0xff;
					if (c4 == 61) return out;
					c4 = decodeChars[c4];
				}
				while (i < len && c4 == -1);
				if (c4 == -1) break;
				out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
			}
			return out;
		},
		utf16to8: function(str) {
			var out, i, len, c;
			out = "";
			len = str.length;
			for (i = 0; i < len; i++) {
				c = str.charCodeAt(i);
				if ((c >= 0x0001) && (c <= 0x007F)) {
					out += str.charAt(i);
				} else if (c > 0x07FF) {
					out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
					out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F));
					out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
				} else {
					out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F));
					out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F));
				}
			}
			return out;
		},
		utf8to16: function(str) {
			var out, i, len, c;
			var char2, char3;
			out = "";
			len = str.length;
			i = 0;
			while (i < len) {
				c = str.charCodeAt(i++);
				switch (c >> 4) {
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
						out += str.charAt(i - 1);
						break;
					case 12:
					case 13:
						char2 = str.charCodeAt(i++);
						out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
						break;
					case 14:
						char2 = str.charCodeAt(i++);
						char3 = str.charCodeAt(i++);
						out += String.fromCharCode(((c & 0x0F) << 12) | ((char2 & 0x3F) << 6) | ((char3 & 0x3F) << 0));
						break;
				}
			}
			return out;
		}
	}
};

/* js-模板引擎(artTemplate) */
/*!art-template - Template Engine | http://aui.github.com/artTemplate/*/
!function(){function a(a){return a.replace(t,"").replace(u,",").replace(v,"").replace(w,"").replace(x,"").split(/^$|,+/)}function b(a){return"'"+a.replace(/('|\\)/g,"\\$1").replace(/\r/g,"\\r").replace(/\n/g,"\\n")+"'"}function c(c,d){function e(a){return m+=a.split(/\n/).length-1,k&&(a=a.replace(/[\n\r\t\s]+/g," ").replace(/<!--.*?-->/g,"")),a&&(a=s[1]+b(a)+s[2]+"\n"),a}function f(b){var c=m;if(j?b=j(b,d):g&&(b=b.replace(/\n/g,function(){return m++,"$line="+m+";"})),0===b.indexOf("=")){var e=l&&!/^=[=#]/.test(b);if(b=b.replace(/^=[=#]?|[\s;]*$/g,""),e){var f=b.replace(/\s*\([^\)]+\)/,"");n[f]||/^(include|print)$/.test(f)||(b="$escape("+b+")")}else b="$string("+b+")";b=s[1]+b+s[2]}return g&&(b="$line="+c+";"+b),r(a(b),function(a){if(a&&!p[a]){var b;b="print"===a?u:"include"===a?v:n[a]?"$utils."+a:o[a]?"$helpers."+a:"$data."+a,w+=a+"="+b+",",p[a]=!0}}),b+"\n"}var g=d.debug,h=d.openTag,i=d.closeTag,j=d.parser,k=d.compress,l=d.escape,m=1,p={$data:1,$filename:1,$utils:1,$helpers:1,$out:1,$line:1},q="".trim,s=q?["$out='';","$out+=",";","$out"]:["$out=[];","$out.push(",");","$out.join('')"],t=q?"$out+=text;return $out;":"$out.push(text);",u="function(){var text=''.concat.apply('',arguments);"+t+"}",v="function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);"+t+"}",w="'use strict';var $utils=this,$helpers=$utils.$helpers,"+(g?"$line=0,":""),x=s[0],y="return new String("+s[3]+");";r(c.split(h),function(a){a=a.split(i);var b=a[0],c=a[1];1===a.length?x+=e(b):(x+=f(b),c&&(x+=e(c)))});var z=w+x+y;g&&(z="try{"+z+"}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:"+b(c)+".split(/\\n/)[$line-1].replace(/^[\\s\\t]+/,'')};}");try{var A=new Function("$data","$filename",z);return A.prototype=n,A}catch(B){throw B.temp="function anonymous($data,$filename) {"+z+"}",B}}var d=function(a,b){return"string"==typeof b?q(b,{filename:a}):g(a,b)};d.version="3.0.0",d.config=function(a,b){e[a]=b};var e=d.defaults={openTag:"<%",closeTag:"%>",escape:!0,cache:!0,compress:!1,parser:null},f=d.cache={};d.render=function(a,b){return q(a,b)};var g=d.renderFile=function(a,b){var c=d.get(a)||p({filename:a,name:"Render Error",message:"Template not found"});return b?c(b):c};d.get=function(a){var b;if(f[a])b=f[a];else if("object"==typeof document){var c=document.getElementById(a);if(c){var d=(c.value||c.innerHTML).replace(/^\s*|\s*$/g,"");b=q(d,{filename:a})}}return b};var h=function(a,b){return"string"!=typeof a&&(b=typeof a,"number"===b?a+="":a="function"===b?h(a.call(a)):""),a},i={"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"},j=function(a){return i[a]},k=function(a){return h(a).replace(/&(?![\w#]+;)|[<>"']/g,j)},l=Array.isArray||function(a){return"[object Array]"==={}.toString.call(a)},m=function(a,b){var c,d;if(l(a))for(c=0,d=a.length;d>c;c++)b.call(a,a[c],c,a);else for(c in a)b.call(a,a[c],c)},n=d.utils={$helpers:{},$include:g,$string:h,$escape:k,$each:m};d.helper=function(a,b){o[a]=b};var o=d.helpers=n.$helpers;d.onerror=function(a){var b="Template Error\n\n";for(var c in a)b+="<"+c+">\n"+a[c]+"\n\n";"object"==typeof console&&console.error(b)};var p=function(a){return d.onerror(a),function(){return"{Template Error}"}},q=d.compile=function(a,b){function d(c){try{return new i(c,h)+""}catch(d){return b.debug?p(d)():(b.debug=!0,q(a,b)(c))}}b=b||{};for(var g in e)void 0===b[g]&&(b[g]=e[g]);var h=b.filename;try{var i=c(a,b)}catch(j){return j.filename=h||"anonymous",j.name="Syntax Error",p(j)}return d.prototype=i.prototype,d.toString=function(){return i.toString()},h&&b.cache&&(f[h]=d),d},r=n.$each,s="break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",t=/\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|[\s\t\n]*\.[\s\t\n]*[$\w\.]+/g,u=/[^\w$]+/g,v=new RegExp(["\\b"+s.replace(/,/g,"\\b|\\b")+"\\b"].join("|"),"g"),w=/^\d[^,]*|,\d[^,]*/g,x=/^,+|,+$/g;e.openTag="{{",e.closeTag="}}";var y=function(a,b){var c=b.split(":"),d=c.shift(),e=c.join(":")||"";return e&&(e=", "+e),"$helpers."+d+"("+a+e+")"};e.parser=function(a,b){a=a.replace(/^\s/,"");var c=a.split(" "),e=c.shift(),f=c.join(" ");switch(e){case"if":a="if("+f+"){";break;case"else":c="if"===c.shift()?" if("+c.join(" ")+")":"",a="}else"+c+"{";break;case"/if":a="}";break;case"each":var g=c[0]||"$data",h=c[1]||"as",i=c[2]||"$value",j=c[3]||"$index",k=i+","+j;"as"!==h&&(g="[]"),a="$each("+g+",function("+k+"){";break;case"/each":a="});";break;case"echo":a="print("+f+");";break;case"print":case"include":a=e+"("+c.join(",")+");";break;default:if(-1!==f.indexOf("|")){var l=b.escape;0===a.indexOf("#")&&(a=a.substr(1),l=!1);for(var m=0,n=a.split("|"),o=n.length,p=l?"$escape":"$string",q=p+"("+n[m++]+")";o>m;m++)q=y(q,n[m]);a="=#"+q}else a=d.helpers[e]?"=#"+e+"("+c.join(",")+");":"="+a}return a},"function"==typeof define?define(function(){return d}):"undefined"!=typeof exports?module.exports=d:this.template=d}();

function callFavOffset(e) {
	if (e.length) {
		var t = e.find("div.woo-pcont:visible").offset(),
			a = t.top,
			o = t.left,
			n = parseInt(e.css("marginLeft")) || 0;
		e.data("offset", {
			left: o - n,
			top: a
		})
	}
}
function callFavorite() {
	function e(e, t) {
		t ? e.addClass("woocov") : e.removeClass("woocov")
	}
	$("div.woo .collbtn .y").SGfavorite(), $("div.woo .collbtn .x").SGcomment(), $("div.woo .collbtn .z").SGlike();
	var t = $("#woo-holder"),
		a = $(window);
	if (t.length) {
		var o = $.browser.msie,
			n = o && "6.0" === $.browser.version,
			i = -20,
			s = 0,
			l = 65;
		n ? t.delegate("div.woo", "mousemove", function(o) {
			callFavOffset(t);
			var n = $(this),
				i = parseInt(n.css("top")) || 0,
				r = a.scrollTop(),
				c = t.data("offset"),
				d = c.top;
			e(n.find(".a u"), !0), n.find(".collbtn").css({
				display: "block",
				top: Math.max(r - (d + i) - s + l, s)
			})
		}) : (t.delegate("div.woo", "mouseenter", function(o) {
			callFavOffset(t);
			var n = $(this),
				r = n.find(".collbtn"),
				c = parseInt(n.css("top")) || 0,
				d = a.scrollTop(),
				u = t.data("offset"),
				p = u.top;
			if (e(n.find(".a u"), !0), $("#collbtn").css("display", "none").removeAttr("id").addClass("collbtn"), r.attr("id", "collbtn").addClass("collbtn"), d - (p + c) + i + s + l > 0) {
				var f = n.offset().left;
				r.css({
					left: f,
					position: "fixed",
					top: s + l,
					display: "block"
				})
			} else r.css({
				position: "absolute",
				left: 0,
				top: s,
				display: "block"
			})
		}), t.delegate("div.woo", "mousemove", function(e) {
			var t = $(this),
				a = t.find(".collbtn");
			$("#collbtn")[0] != a[0] && $("#collbtn").css("display", "none").removeAttr("id").addClass("collbtn"), a.attr("id", "collbtn").addClass("collbtn")
		})), t.delegate("div.woo", "mouseleave", function(t) {
			var a = $(this);
			e(a.find(".a u"), !1), a.find(".collbtn").css("display", "none").removeAttr("id").addClass("collbtn")
		})
	}
}
!function() {
	document.cookie = "js=1; path=/"
}(),function(e) {
	e.Bom = function() {
		var t = function(t) {
				var n = {};
				if ("string" === e.type(t) && t.length > 0) for (var r = t.split(/;\s/g), i = null, o = null, a = null, s = 0, u = r.length; u > s; s++) a = r[s].match(/([^=]+)=/i), a instanceof Array ? (i = unescape(a[1]), o = unescape(r[s].substring(a[1].length + 1))) : (i = unescape(r[s]), o = ""), n[i] = o;
				return n
			},
			n = function(e) {
				var t = e.split("|"),
					n = null,
					r = {};
				if (e.length > 0) for (var i = 0, o = t.length; o > i; ++i) n = t[i].split("="), r[unescape(n[0])] = unescape(n[1]);
				return r
			},
			r = function(t, n, r) {
				r = r || {
						path: "/"
					};
				var i = escape(t) + "=" + escape(n);
				if (e.isPlainObject(r)) {
					if (r.expires instanceof Date && (i += "; expires=" + r.expires.toUTCString()), "number" === e.type(r.expires) && 0 !== r.expires) {
						var o = new Date;
						o.setTime(o.getTime() + 24 * r.expires * 60 * 60 * 1e3), i += "; expires=" + o.toUTCString()
					}
					i += "string" === e.type(r.path) && "" !== r.path ? "; path=" + r.path : "; path=/", "string" === e.type(r.domain) && "" !== r.domain && (i += "; domain=" + r.domain), r.secure === !0 && (i += "; secure")
				}
				return i
			},
			i = function(t) {
				if (e.isPlainObject(t)) {
					var n = [];
					for (var r in t) e.isFunction(t[r]) || "undefined" === e.type(t[r]) || n.push(escape(r) + "=" + escape(String(t[r])));
					return n.join("|")
				}
			};
		return {
			getCookie: function(n) {
				if ("string" !== e.type(n) || "" === n) return null;
				var r = t(document.cookie);
				return "undefined" === e.type(r[n]) ? "" : r[n]
			},
			getSubCookie: function(t, n) {
				var r = this.getSubCookies(t);
				return null != r ? "string" !== e.type(n) || "" === n ? "" : r[n] ? r[n] : "" : ""
			},
			getSubCookies: function(t) {
				if ("string" !== e.type(t) || "" === t) return null;
				var r = this.getCookie(t);
				return "string" === e.type(r) ? n(r) : ""
			},
			setCookie: function(t, n, i) {
				if (i = i || {}, "string" === e.type(t) && "undefined" !== e.type(n)) {
					var o = r(t, n, i);
					return document.cookie = o, o
				}
			},
			setSubCookie: function(t, n, r, i) {
				if ("string" === e.type(t) && "" !== t && "string" === e.type(n) && "" !== n && "undefined" !== e.type(r)) {
					var o = this.getSubCookies(t);
					return e.isPlainObject(o) || (o = {}), o[n] = r, this.setSubCookies(t, o, i)
				}
			},
			setSubCookies: function(t, n, o) {
				if ("string" === e.type(t) && "undefined" !== e.type(n)) {
					var a = r(t, i(n), o);
					return document.cookie = a, a
				}
			},
			removeCookie: function(t, n) {
				return "string" === e.type(t) && !t == !1 ? (n = e.extend(n || {}, {
					expires: new Date(0)
				}), this.setCookie(t, "", n)) : void 0
			},
			removeSubCookie: function(t, n) {
				if ("string" === e.type(t) && "" !== t && "string" === e.type(n) && "" !== n) {
					var r = this.getSubCookies(t);
					return e.isPlainObject(r) && r.hasOwnProperty(n) ? (delete r[n], this.setSubCookies(t, r)) : void 0
				}
			},
			addBookmark: function(e, t) {
				window.sidebar ? window.sidebar.addPanel(t ? t : document.title, e ? e : window.location.href, "") : window.external ? window.external.AddFavorite(e ? e : location.href, t ? t : document.title) : alert("\u52a0\u5165\u6536\u85cf\u5931\u8d25\uff0c\u8bf7\u4f7f\u7528Ctrl+D\u8fdb\u884c\u6dfb\u52a0")
			},
			setHome: function(e) {
				e = e ? e : location.href;
				try {
					document.body.style.behavior = "url(#default#homepage)", document.body.setHomePage(e)
				} catch (t) {
					if (window.sidebar) {
						if (window.netscape) try {
							netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")
						} catch (t) {
							alert("\u8be5\u64cd\u4f5c\u88ab\u6d4f\u89c8\u5668\u62d2\u7edd\uff0c\u5047\u5982\u60f3\u542f\u7528\u8be5\u529f\u80fd\uff0c\u8bf7\u5728\u5730\u5740\u680f\u5185\u8f93\u5165 about:config,\u7136\u540e\u5c06\u9879 signed.applets.codebase_principal_support \u503c\u8be5\u4e3atrue")
						}
						var n = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefBranch);
						n.setCharPref("browser.startup.homepage", e)
					}
				}
			}
		}
	}()
}(jQuery), "undefined" == typeof $.ui && ($.ui = {}), function(a, b) {
	function c(a) {
		return "[object Object]" === Object.prototype.toString.call(a);
	}
	function d(a) {
		try {
			a = "true" === a ? !0 :"false" === a ? !1 :"null" === a ? null :+a + "" === a ? +a :/(?:\{[\s\S]*\}|\[[\s\S]*\])$/.test(a) ? JSON.parse(a) :a;
		} catch (c) {
			a = b;
		}
		return a;
	}
	function e(a) {
		var g, h, c = {}, e = a && a.attributes, f = e && e.length;
		for (;f--; ) h = e[f], g = h.name, "data-" === g.substring(0, 5) && (g = g.substring(5),
			h = d(h.value), h === b || (c[g] = h));
		return c;
	}
	function f() {
		var e, b = [].slice.call(arguments), d = b.length;
		for (;d--; ) e = e || b[d], c(b[d]) || b.splice(d, 1);
		return b.length ? a.extend.apply(null, [ !0, {} ].concat(b)) :e;
	}
	a.ui.guid = 0;
	function g(b, c) {
		function d(c, g) {
			var h = this;
			h.el = a(c);
			h.options = f(d.options, e(c), g);
			return h.name = b.toLowerCase(), a.ui.guid++, h.guid = a.ui.guid, h.options.hasCssLink && h.options.cssLinkVersion && h.options.baseVersion && "undefined" != typeof seajs ? seajs.use(("https:" == document.location.protocol ? "//" :"http://") + "www.gouchehui.com/hui/" + h.options.baseVersion + "/ui/" + b + "/" + h.options.cssLinkVersion + "/" + b + ".css", function() {
				h.init();
			}) :h.init(), /isdebug=(-\d)*-0/.test(location.search) && window.pageConfig && (window.pageConfig.uiLog ? window.pageConfig.uiLog.push(h) :window.pageConfig.uiLog = [ h ],
				console.log(h)), h;
		}
		var g = [ "options" ];
		for (var h = 0; h < g.length; h++) {
			var i = g[h];
			c[i] && (d[i] = c[i]), delete c[i];
		}
		for (var h in c) d.prototype[h] = c[h];
		return d;
	}
	a.ui.fn = function(b) {
		var b = b.toLowerCase();
		a.fn[b] = function(c) {
			var d;
			return a.each(this, function(e, f) {
				d = new a.ui[b](f, c);
			}), d;
		};
	}, a.ui.define = function(b, c) {
		a.ui[b] = g(b, c), a.ui.fn(b);
	};
}(jQuery), !function(a, b) {
	a.ui.define("switchable", {
		options:{
			hasCssLink:!1,
			baseVersion:"1.0.0",
			cssLinkVersion:"1.0.0",
			type:"tab",
			direction:"left",
			hasSetup:!1,
			navClass:"ui-switchable-trigger",
			navItem:"ui-switchable-item",
			navSelectedClass:"ui-switchable-selected",
			navDisabledClass:"disabled",
			navIframe:"data-iframe",
			contentClass:"ui-switchable-panel-main",
			mainClass:"ui-switchable-panel",
			mainSelectedClass:"ui-switchable-panel-selected",
			bodyClass:"ui-switchable-panel-body",
			hasPage:!1,
			autoLock:!1,
			prevClass:"ui-switchable-prev",
			nextClass:"ui-switchable-next",
			pagCancelClass:"ui-switchable-page-cancel",
			hasArrow:!1,
			arrowClass:"ui-switchable-arrow",
			event:"mouseover",
			speed:400,
			callback:null,
			onNext:null,
			onPrev:null,
			delay:150,
			defaultPanel:0,
			autoPlay:!1,
			playDirection:"next",
			stayTime:5e3,
			mouseenterStopPlay:!0,
			includeMargin:!1,
			width:0,
			height:0,
			seamlessLoop:!1,
			hasHash:!1,
			imgscrollClass:"ui-switchable-imgscroll-img",
			imgscrollItemClass:"ui-switchable-imgscroll-item",
			imgscrollLazyload:!1,
			step:1,
			visible:1,
			easing:"swing",
			hasLoop:!1
		},
		init:function() {
			var b = this;
			var c = b.options;
			var d = !0;
			if (b.addClass(), c.visible < c.step && (c.visible = c.step), b.nav = b.el.find("." + c.navItem),
					b.main = b.el.find("." + c.mainClass), c.step = Math.max(c.step || 1, 1), b.size = b.main.size(),
					b.pageCount = Math.ceil(b.main.size() / c.step), b.content = b.el.find("." + c.contentClass),
					b.mainWidth = b.main.outerWidth(c.includeMargin), d = c.step < b.size, "tab" == c.type && c.navSelectedClass && b.nav.length > 0) {
				var e = -1;
				b.nav.each(function(b) {
					var d = a(this);
					d.hasClass(c.navSelectedClass) && (-1 == e ? e = b :d.removeClass(c.navSelectedClass));
				}), e > -1 && (c.defaultPanel = e);
			}
			if (c.width && (b.mainWidth = c.width), b.mainHeight = b.main.outerHeight(c.includeMargin),
				c.height && (b.mainHeight = c.height), b.cloneCount = Math.max(c.step, c.visible),
				c.seamlessLoop && d) {
				var f = [];
				var g = [];
				var h = b.cloneCount;
				for (var i = 0; h > i; i++) f.push(b.main.eq(i).clone().attr("data-switchable-clone", 1).data("switchable-clone-from", h + i)),
					g.push(b.main.eq(b.size - (i + 1)).clone().attr("data-switchable-clone", 1).data("switchable-clone-from", b.size + i));
				for (var j = 0; h > j; j++) b.content.prepend(g[j]).append(f[j]);
				b.main = b.el.find("." + c.mainClass);
			}
			b.main.each(function(b) {
				a(this).data("switchable-idx", b);
			});
			var k = c.defaultPanel;
			c.hasHash && (k = b.getHash(k)), b.last = k, b.current = k, b.isInit = !0, c.seamlessLoop && d ? b.switchTo(k, k + b.cloneCount) :b.switchTo(k, k),
				b.autoInterval = null, b.eventTimer = null, c.hasPage && (d && b.page(), c.autoLock && b.updatePageButState()),
			d && (b.autoPlay(), b.bind());
		},
		addClass:function() {},
		bind:function() {
			var b = this;
			var c = b.options;
			b.nav.each(function(d) {
				var e = a(this);
				e.bind(c.event, function() {
					clearInterval(b.autoInterval), c.navDisabledClass && e.hasClass(c.navDisabledClass) || (0 === c.delay ? (b.current = d,
						b.switchTo(d, c.seamlessLoop ? d + b.cloneCount :d)) :(clearTimeout(b.eventTimer),
						b.eventTimer = setTimeout(function() {
							b.current = d, b.switchTo(d, c.seamlessLoop ? d + b.cloneCount :d);
						}, c.delay)));
				}).bind("mouseleave", function() {
					clearTimeout(b.eventTimer), c.mouseenterStopPlay || b.autoPlay();
				}), "click" == c.event && e.bind("mouseover", function() {
					clearTimeout(b.eventTimer), clearInterval(b.autoInterval);
				});
			}), c.mouseenterStopPlay && b.el.each(function() {
				a(this).bind("mouseenter", function() {
					clearInterval(b.autoInterval);
				}).bind("mouseleave", function() {
					b.autoPlay();
				});
			});
		},
		getHash:function(b) {
			var c = this;
			var d = window.location.hash;
			if ("" != d) {
				var e = c.nav;
				var f = null;
				if (a.each(e, function(b) {
						a(this).attr("data-hash") == d && (f = b);
					}), null != f) {
					b = f;
					var g = c.nav.eq(f).offset().top;
					var h = a.browser.webkit ? 50 :0;
					setTimeout(function() {
						a(window).scrollTop(g);
					}, h);
				}
			}
			return b;
		},
		setHash:function(a) {
			var b = this;
			if (b.options.hasHash) {
				if (b.isInit && !window.location.hash) return;
				var c = b.nav.eq(a).attr("data-hash");
				c = c.replace(/#/, ""), window.location.hash = c;
			}
		},
		switchTo:function(a, b) {
			var c = this;
			if ("undefined" == typeof b) var b = a;
			c.switchNavTo(a), c.switchMainTo(b);
		},
		switchNavTo:function(a) {
			var b = this;
			var c = b.options;
			b.nav.removeClass(c.navSelectedClass), b.nav.eq(a).addClass(c.navSelectedClass),
				b.setHash(a);
		},
		switchMainTo:function(b) {
			var c = this;
			var d = c.options;
			if (c.iframe(b), "imgscroll" != d.type && (c.main.removeClass(d.mainSelectedClass),
					c.main.eq(b).addClass(d.mainSelectedClass)), c.isInit || c.last != b) {
				if (c.switchType(b), null != d.callback) {
					var e = b;
					var f = !1;
					var g = this.main.eq(e);
					e + 1 == c.pageCount && (f = !0), d.seamlessLoop && this.main.each(function() {
						return e == a(this).data("switchable-clone-from") ? (g = g.add(a(this)), !1) :void 0;
					}), d.callback.call(c, e, f, g);
				}
				c.last = b;
			}
		},
		switchType:function(a) {
			var b = this;
			var c = b.options;
			switch (c.type) {
				case "tab":
					b.tab(a);
					break;

				case "focus":
					b.focus(a);
					break;

				case "slider":
					b.slider(a);
					break;

				case "carousel":
					b.carousel(a);
					break;

				case "imgscroll":
					b.imgscroll(a);
			}
		},
		switchDefault:function(a) {
			var b = this;
			b.main.hide(), b.main.eq(a).show();
		},
		tab:function(a) {
			var b = this;
			var c = b.options;
			if (c.hasSetup || b.switchDefault(a), c.hasArrow) {
				var d = c.arrowClass;
				var e = b.nav.eq(a).outerWidth(!0) * a;
				if (b.isInit) {
					var f = b.nav.parent();
					f.prepend('<div class="' + d + '"><b></b></div>').css({
						position:"relative"
					}), b.el.find("." + d).css({
						left:e
					}), b.isPlayLock = !1;
				} else setTimeout(function() {
					b.isPlayLock = !1;
				}, c.speed), b.el.find("." + d).stop(!0).animate({
					left:e
				}, c.speed, c.easing);
			}
			b.isInit = !1;
		},
		focus:function(b) {
			var c = this;
			var d = c.options;
			c.isInit ? (c.main.parent().css({
				position:"relative"
			}), c.main.css({
				position:"absolute",
				zIndex:0,
				opacity:0
			}).show(), c.main.eq(b).css({
				zIndex:1,
				opacity:1
			}), c.isPlayLock = !1) :(setTimeout(function() {
				c.isPlayLock = !1;
			}, d.speed), c.main.eq(c.last).css({
				zIndex:0
			}).stop(!0).animate({
				opacity:1
			}, d.speed, d.easing, function() {
				a(this).css("opacity", 0);
			})), c.main.eq(b).css({
				zIndex:1
			}).stop(!0).animate({
				opacity:1
			}, d.speed, d.easing), c.isInit = !1;
		},
		slider:function(a) {
			var b = this;
			var c = b.options;
			var d = b.content;
			var e = b.mainHeight;
			var f = e * a;
			var g = b.mainWidth;
			var h = g * a;
			b.isInit ? ("left" == c.direction ? (b.main.css({
				"float":"left"
			}), d.css(c.seamlessLoop ? {
				width:g * (b.size + 2 * b.cloneCount)
			} :{
				width:g * b.size
			}), d.css({
				left:-h
			})) :"top" == c.direction && d.css({
				top:-f
			}), d.parent().css({
				position:"relative"
			}), d.css({
				position:"absolute"
			}), b.switchDefault(a), b.isInit = !1, b.isPlayLock = !1) :(setTimeout(function() {
				b.isPlayLock = !1;
			}, c.speed), "left" == c.direction ? d.stop(!0).animate({
				left:-h
			}, c.speed, c.easing) :"top" == c.direction && d.stop(!0).animate({
				top:-f
			}, c.speed, c.easing)), b.main.show();
		},
		carousel:function(a) {
			var b = this;
			b.slider(a);
		},
		imgscroll:function(b) {
			var c = this;
			var d = c.options;
			var e = c.mainWidth;
			var f = c.el.find("." + d.imgscrollClass);
			if (c.isInit) {
				c.el.find("." + d.bodyClass).css({
					position:"relative",
					overflow:"hidden",
					width:e * d.visible
				}), c.content.css({
					position:"absolute",
					width:e * c.size
				}), c.main.css({
					"float":"left"
				});
				var g = d.mainSelectedClass;
				if (c.main.eq(0).addClass(g), !f.attr("src")) {
					var h = c.el.find("." + d.imgscrollItemClass).eq(0).attr("data-url");
					f.attr("src", h);
				}
				if (d.imgscrollLazyload) for (var b = c.current; b < d.visible + 1; b++) {
					var i = c.main.eq(b).find("." + d.imgscrollItemClass);
					var h = i.attr("data-src");
					i.attr("src", h);
				}
				c.main.bind(d.event, function() {
					var b = a(this);
					var e = b.find("." + d.imgscrollItemClass).attr("data-url");
					c.main.removeClass(g), b.addClass(g), f.attr("src", e);
				}), c.isInit = !1, c.isPlayLock = !1;
			} else {
				setTimeout(function() {
					c.isPlayLock = !1;
				}, d.speed);
				var j = c.current * e;
				if (d.imgscrollLazyload) {
					var i = c.main.eq(d.visible + c.current).find("." + d.imgscrollItemClass);
					var h = i.attr("data-src");
					i.attr("src", h);
				}
				c.content.stop(!0).animate({
					left:-j
				}, d.speed);
			}
		},
		page:function() {
			var a = this;
			var b = a.options;
			var c = a.el.find("." + b.nextClass);
			var d = a.el.find("." + b.prevClass);
			d.bind("click", function(c) {
				a.isPlayLock && a.content && a.content.length > 0 || b.autoLock && 0 == a.current || (a.isPlayLock = !0,
					a.prev(), c.stopPropagation());
			}), c.bind("click", function(c) {
				a.isPlayLock && a.content && a.content.length > 0 || b.autoLock && a.current >= a.size - b.visible || (a.isPlayLock = !0,
					a.next(), c.stopPropagation());
			});
		},
		next:function() {
			var b = this;
			var c = b.options;
			b.current = b.current + c.step, b.offsetIndex();
			var d = 0;
			!c.seamlessLoop && c.hasLoop && (d = -c.visible + c.step), b.current >= b.size + d && (b.current = 0);
			var e = c.visible > c.step ? c.visible :c.step;
			!c.seamlessLoop && b.current + e > b.size && (b.current = b.size > e ? b.size - e :0);
			var f = c.seamlessLoop ? b.current + b.cloneCount :b.current;
			b.switchTo(b.current, f), b.updatePageButState(), a.isFunction(c.onNext) && c.onNext.call(b);
		},
		prev:function() {
			var b = this;
			var c = b.options;
			c.seamlessLoop ? b.offsetIndex(!0) :(b.current -= c.step, b.current < 0 && (b.current = b.current > -c.step ? 0 :b.size - c.step));
			var d = c.seamlessLoop ? b.current + b.cloneCount :b.current;
			b.switchTo(b.current, d), b.updatePageButState(), a.isFunction(c.onPrev) && c.onPrev.call(b);
		},
		updatePageButState:function() {
			var a = this;
			var b = a.options;
			if (b.hasPage && b.autoLock) {
				var c = a.el.find("." + b.nextClass);
				var d = a.el.find("." + b.prevClass);
				var e = b.pagCancelClass;
				a.current >= a.size - Math.max(b.visible, b.step) ? c.addClass(e) :c.removeClass(e),
					a.current <= 0 ? d.addClass(e) :d.removeClass(e);
			}
		},
		offsetIndex:function(a) {
			var b = this;
			var c = b.content;
			var d = b.options;
			var e = b.mainWidth;
			var f = b.mainHeight;
			var g = null;
			var h = null;
			var i = null;
			a && d.seamlessLoop ? (i = b.current, b.current <= 0 ? (i = b.size - d.step + b.current,
				g = -((b.size + (b.cloneCount + b.current)) * e), h = -((b.size + (b.cloneCount + b.current)) * f)) :i -= d.step,
				b.current = i) :b.current >= b.size && d.seamlessLoop && (i = b.current - b.size,
				g = -((i + b.cloneCount - d.step) * e), h = -((i + b.cloneCount - d.step) * f),
				b.current = i), null != g && "left" == d.direction ? c.css({
				left:g
			}) :null != h && "top" == d.direction && c.css({
				top:h
			});
		},
		autoPlay:function() {
			var a = this;
			a.options.autoPlay && a.startPlay();
		},
		startPlay:function() {
			var a = this;
			var b = a.options;
			a.stopPlay(), a.autoInterval = setInterval(function() {
				a.main.length <= b.step ? a.stopPlay() :"prev" == b.playDirection ? a.prev() :a.next();
			}, b.stayTime);
		},
		stopPlay:function() {
			var a = this;
			clearInterval(a.autoInterval);
		},
		iframe:function(a) {
			var b = this;
			var c = b.main.eq(a);
			var d = b.nav.eq(a);
			var e = d.attr(b.options.navIframe);
			if (e) {
				var f = document.createElement("iframe");
				f.src = e, f.border = 0, f.frameborder = "no", f.marginwidth = 0, f.marginheight = 0,
					c.html(f), d.removeAttr(b.options.navIframe);
			}
		},
		update:function(c, d) {
			var e = this;
			var f = e.options;
			var g = e.main.length;
			var h = -1;
			var i = -1;
			var j = !1;
			if (a.isFunction(c) && (d = c, c = 0), isNaN(parseInt(c)) ? (c.hasClass(f.mainClass) || (c = c.closest("." + f.mainClass)),
				c.hasClass(f.mainClass) && (i = c.data("switchable-idx"))) :i = c, h = i, f.autoPlay && (e.stopPlay(),
					f.autoPlay = !1, j = !0), f.seamlessLoop && g > f.step) {
				var k = e.main.length - 2 * f.step;
				var l = !1;
				e.main.each(function() {
					var c = a(this);
					"1" == c.data("switchable-clone") && (l = !0, a(this).remove());
				}), l && (h = i < f.step || i >= k + f.step ? i >= k + f.step ? i - k - f.step :k - f.step + i :i - f.step);
			}
			if (f.hasPage) {
				var m = e.el.find("." + f.nextClass);
				var n = e.el.find("." + f.prevClass);
				m.unbind("click"), n.unbind("click");
			}
			e.main = e.el.find("." + f.mainClass), g = e.main.length;
			var o = function(a) {
				if (a == b || null == a) {
					var c = e.el.find("." + f.mainClass).length;
					g > c ? h -= g - c :h = c > g ? c > f.visible ? c - f.visible :0 :e.current, 0 > h && (h = 0),
					j && (f.autoPlay = !0), f.autoLock && !f.seamlessLoop && h + f.visible >= c && (h = c - f.visible,
					0 > h && (h = 0));
				} else h = a;
				f.defaultPanel = h, e.init();
			};
			d.call(e.main.eq(h), e.content, h, o) ? e.el.find("." + f.mainClass).each(function(b) {
				a(this).data("switchable-idx", b);
			}) :o();
		}
	});
}(jQuery),$.G = {
	gaq: function(e) {
	},
	isRedirect: function(e) {
		return e = e.split("?")[0], e.indexOf("/dj/go2/") > -1 || e.indexOf("/redirect/") > -1
	},
	accSub: function(arg1, arg2) {
		var r1, r2, m, n;
		try {
			r1 = arg1.toString().split(".")[1].length;
		}
		catch (e) {
			r1 = 0;
		}
		try {
			r2 = arg2.toString().split(".")[1].length;
		}
		catch (e) {
			r2 = 0;
		}
		m = Math.pow(10, Math.max(r1, r2)); //last modify by deeka //动态控制精度长度
		n = (r1 >= r2) ? r1 : r2;
		return ((arg1 * m - arg2 * m) / m).toFixed(n);
	},
	addfavorite: function(e, t) {
		return document.all ? (window.external.addFavorite(e, t), !0) : window.sidebar && window.sidebar.addPanel ? (window.sidebar.addPanel(t, e, ""), !0) : !1
	},
	store: function(e) {
		var t = $("#win-house");
		return t.length || (t = $('<div id="win-house" class="h0"></div>').appendTo("body")), e && t.append(e), t
	},
	getToken: function(e) {
		var t = {},
			a = "",
			o = "",
			n = "",
			i = $(window),
			s = i.data("verif") || [],
			l = $("input", "#form-token");
		return (a = $.Bom.getCookie("csrftoken")) ? (o = "csrfmiddlewaretoken=" + a, t.csrfmiddlewaretoken = a) : l.length && (a = l.val(), t.csrfmiddlewaretoken = a, o = $.param(t)), n = '<input type="hidden" name="csrfmiddlewaretoken" value="' + a + '" />', s.length && (o += "&ccode=" + s[0], o += "&ctoken=" + s[1], t.ccode = s[0], t.ctoken = s[1], n += '<input type="hidden" name="ccode" value="' + s[0] + '" /><input type="hidden" name="ctoken" value="' + s[1] + '" />'), e ? 3 == e ? n : 2 == e ? t : o : o
	},
	getTokenVal: function() {
		var e = "",
			t = $("input", "#form-token");
		return (e = $.Bom.getCookie("csrftoken")) || t.length && (e = t.val()), e
	},
	setCookie: function(e, t) {
		$.Bom.setSubCookie("sg", e, t)
	},
	setDayCookie: function(e, t) {
		$.Bom.setSubCookie("sgd", e, t, {
			expires: 1
		})
	},
	setWeekCookie: function(e, t) {
		$.Bom.setSubCookie("sgw", e, t, {
			expires: 7
		})
	},
	setMonthCookie: function(e, t) {
		$.Bom.setSubCookie("sgm", e, t, {
			expires: 30
		})
	},
	setYearCookie: function(e, t) {
		$.Bom.setSubCookie("sgy", e, t, {
			expires: 365
		})
	},
	getCookie: function(e) {
		return $.Bom.getSubCookie("sg", e)
	},
	getDayCookie: function(e) {
		return $.Bom.getSubCookie("sgd", e)
	},
	getWeekCookie: function(e) {
		return $.Bom.getSubCookie("sgw", e)
	},
	getMonthCookie: function(e) {
		return $.Bom.getSubCookie("sgm", e)
	},
	getYearCookie: function(e) {
		return $.Bom.getSubCookie("sgy", e)
	},
	simpleMarqueeW: function(e, t, a, o) {
		var n = $(e),
			i = n.find(":first-child"),
			s = n.parent();
		if (i.length) {
			var l, i, r, t = t || 4e3,
				a = a || 2,
				o = o || 20,
				c = !1,
				d = function() {
					r = i.outerWidth(!0), clearInterval(l), l = setInterval(u, o)
				},
				u = function() {
					c || (s.scrollLeft() + a >= r ? (clearInterval(l), n.append(i), s.scrollLeft(0), setTimeout(d, t)) : s.scrollLeft(s.scrollLeft() + a))
				};
			n.mouseover(function() {
				c = !0
			}), n.mouseout(function() {
				c = !1
			}), setTimeout(d, t)
		}
	},
	simpleMarqueeH: function(e, t, a, o, n) {
		var i = $(e),
			s = i.find(":first-child");
		if (s.length) {
			var l, s, r, t = t || 4e3,
				o = o || 2,
				n = n || 20,
				c = !1,
				d = function() {
					r = s.outerHeight(!0), clearInterval(l), l = setInterval(u, n)
				},
				u = function() {
					c || (i.scrollTop() + o >= r ? (clearInterval(l), i.append(s), i.scrollTop(0), setTimeout(d, t)) : i.scrollTop(i.scrollTop() + o))
				};
			i.mouseover(function() {
				c = !0
			}), i.mouseout(function() {
				c = !1
			}), setTimeout(d, a)
		}
	},
	scrollToAnchor: function(e, t) {
		var t = t || 0,
			a = ($(window), $("body,html")),
			o = $("a[name=" + e + "]");
		if (e && o.length) {
			var n = o.offset().top - t || 0;
			a.animate({
				scrollTop: n
			}, 200)
		} else window.ActiveXObject && !window.XMLHttpRequest && a.animate({
			scrollTop: n
		}, 200)
	},
	bindChecks: function(e, t, a, o, n, i) {
		function s(e) {
			var t = [],
				a = "",
				o = c.find(l);
			e.prop("checked") ? (o.each(function(e, a) {
				var o = $(a),
					n = o.attr("value");
				o.prop("checked", !0), void 0 !== n && o.attr("dvalue", n), t.push(o.attr("dvalue"))
			}), r.prop("checked", !0).add(o).addClass("checked")) : (o.each(function(e, t) {
				$(t).prop("checked", !1)
			}), r.prop("checked", !1).add(o).removeClass("checked")), a = $.trim(t.join(" ")).replace(/ /gi, ","), r.attr("dvalue", a), r.filter("[type=checkbox]").val(a)
		}
		var l = "input[type=checkbox],[checked],[checked=false]",
			r = $(e),
			c = $(t);
		a ? r.prop("checked", !0) : void 0 !== a ? r.prop("checked", !1) : "true" == r.attr("dchecked") ? r.prop("checked", !0) : r.prop("checked") || r.prop("checked", !1), s(r), "checkbox" != r.attr("type") && r.click(function(e) {
			var t = !0;
			$.isFunction(o) && (t = o()), t && r.prop("checked", !r.prop("checked"))
		}), r.click(function(e) {
			e.stopPropagation();
			var t = $(this),
				a = !0;
			$.isFunction(o) && (a = o.call(this, r, c, l)), a && (s(t), $.isFunction(n) && n.call(this, r, c, l))
		}), c.delegate(l, "click", function(e) {
			e.stopPropagation();
			var t = $(this);
			"checkbox" != t.attr("type") && t.prop("checked", !t.prop("checked"));
			var a = $.trim(r.attr("dvalue")),
				o = a ? a.split(",") : [],
				n = "",
				s = t.attr("value");
			void 0 !== s && t.attr("dvalue", s), s = t.attr("dvalue"), o = $(o).filter(function(e, t) {
				return t !== s
			}).get();
			var d = t.prop("checked");
			d ? (o.push(s), t.addClass("checked")) : t.removeClass("checked"), n = $.trim(o.join(" ")).replace(/ /gi, ","), r.attr("dvalue", n), "checkbox" == r.attr("type") && r.val(n), $.isFunction(i) && i.call(this, r, c, l, d)
		})
	},
	getCursorPosition: function(e) {
		var t = {
			text: "",
			start: 0,
			end: 0
		};
		if (e.setSelectionRange) t.start = e.selectionStart, t.end = e.selectionEnd, t.text = t.start != t.end ? e.value.substring(t.start, t.end) : "";
		else if (document.selection) {
			var a, o = document.selection.createRange(),
				n = document.body.createTextRange();
			for (n.moveToElementText(e), t.text = o.text, t.bookmark = o.getBookmark(), a = 0; n.compareEndPoints("StartToStart", o) < 0 && 0 !== o.moveStart("character", -1); a++)"\n" == e.value.charAt(a) && a++;
			t.start = a, t.end = t.text.length + t.start
		}
		return t
	},
	setCursorPosition: function(e, t) {
		if (t || alert("You must get cursor position first."), e.setSelectionRange) e.focus(), e.setSelectionRange(t.start, t.end);
		else if (e.createTextRange) {
			var a = e.createTextRange();
			e.value.length === t.start ? (a.collapse(!1), a.select()) : (a.moveToBookmark(t.bookmark), a.select())
		}
	},
	getSelectedText: function() {
		var e = window,
			t = document;
		return e.getSelection ? e.getSelection().toString() : t.getSelection ? t.getSelection() : t.selection ? t.selection.createRange().text : void 0
	},
	blinkIt: function(e, t, a, o, n) {
		return n = n || 1e3, 0 === o ? void a() : ($.isFunction(e) && e(), void window.setTimeout(function() {
			$.G.blinkIt(t, e, a, --o, n)
		}, n))
	},
	recurseDo: function(e, t, a, o, n) {
		return 0 == a ? void($.isFunction(n) && n()) : (t = e.apply(null, t), void(t[0].length ? setTimeout(function() {
			$.G.recurseDo(e, t, --a, o, n)
		}, o) : $.isFunction(n) && n()))
	},
	getUSERID: function() {
		return "undefined" != typeof USER && USER.ID ? USER.ID : ""
	},
	isSTAFF: function() {
		return "undefined" != typeof USER && USER.ISSTAFF ? !0 : !1
	},
	dtImageTrans: function(e, t, a, o, n) {
		var i = $.trim(e).replace(/^http(s)?:\/\//gi, ""),
			i = i.split("/"),
			s = i[0],
			i = i[1];
		return -1 == s.indexOf("duitang.com") || !i || "uploads" != i && "misc" != i ? e : t ? (a = a || 0, o = o || 0, n = n ? "_" + n : "", $.G.dtImageTrans(e).replace(/(\.[a-z_]+)$/gi, ".thumb." + a + "_" + o + n + "$1")) : e.replace(/(?:\.thumb\.\w+|\.[a-z]+!\w+)(\.[a-z_]+)$/gi, "$1")
	},
	getFitSize: function(e, t) {
		if (e[0] && e[1] && t[0] && (t[1] || (t[1] = t[0]), e[0] > t[0] || e[1] > t[1])) {
			var a = e[0] / e[1],
				o = a >= t[0] / t[1];
			return o ? [t[0], parseInt(t[0] / a)] : [parseInt(t[1] * a), t[1]]
		}
		return e
	},
	setImgSize: function(e, t, a) {
		e.onload = null, e.removeAttribute("width"), e.removeAttribute("height");
		var o = e;
		if (o && o.width && o.height && t && (a || (a = t), o.width > t || o.height > a)) {
			var n = o.width / o.height,
				i = n >= t / a;
			e[i ? "width" : "height"] = i ? t : a, document.all && (e[i ? "height" : "width"] = (i ? t : a) * (i ? 1 / n : n))
		}
		e.style.visibility = "visible"
	},
	setImgSizeByAncestor: function(e, t) {
		e.onload = null;
		var a = $(e).parent(t)[0];
		if (a) {
			var o = parseInt($(a).css("width"));
			o = o ? o : a.offsetWidth, $.G.setImgSize(e, o)
		}
	},
	getNum: function(e) {
		return e ? +e.replace(/^[^\d]*(\d+\.?\d*).*/, "$1") || 0 : 0
	},
	isFromDomain: function(e) {
		e = e.replace("http://", "").replace("https://", ""), e = e.split("?"), e = e[0].split("/"), e = e[0];
		for (var t = 1; t < arguments.length; t++) if (e.indexOf(arguments[t]) > -1) return !0;
		return !1
	},
	trimLink: function(e, t) {
		return e.replace(/(?:http(?:s)?:\/\/)(?:(?:[\w-]+\.)+[\w-]+)(?:\:\d+)?(?:\/[\w-\.\/%]*)?(?:[?][\w-\.\/%!*\(\);\:@&=+$,\[\]]*)?(?:#[\w-\.\/%!*\(\);\:@&=+$,\[\]]*)?/gi, function(e) {
			return t ? '<a href="' + e + '" target="_blank">' + e + "</a>" : ""
		})
	},
	isLink: function(e) {
		return !!e.match(/^(?:http(?:s)?:\/\/)(?:(?:[\w-]+\.)+[\w-]+)(?:\:\d+)?(?:\/[^ \t\n]*)?$/gi)
	},
	removeParam: function(e, t) {
		var a = new RegExp("\\?" + t + "(=[^&]*)?"),
			o = new RegExp("\\&" + t + "(=[^&]*)?");
		return e.replace(a, "?").replace(o, "").replace(/\?&/, "?").replace(/\?$/, "")
	},
	getParams: function(e) {
		var t, a, o, n = {},
			e = e.split("#")[0],
			i = e.indexOf("?"),
			s = i > -1 ? e.substr(i + 1) : "",
			l = s.split("&");
		for (o = 0, len = l.length; o < len; o++) {
			var r = l[o].indexOf("=");
			if (r > 0) {
				t = l[o].substring(0, r), a = l[o].substr(r + 1);
				try {
					a.indexOf("+") > -1 && (a = a.replace(/\+/g, " ")), n[t] = decodeURIComponent(a)
				} catch (c) {}
			}
		}
		return n
	},
	addParam: function(e, t, a) {
		var o = new RegExp("([&\\?])" + t + "=[^& ]*", "g");
		e = e.replace(o, function(e, t) {
			return "?" == t ? "?" : ""
		});
		var n = e.indexOf("?");
		return e += (n > -1 ? n + 1 != e.length ? "&" : "" : "?") + t + "=" + a
	}
}, Array.prototype.indexOf || (Array.prototype.indexOf = function(e, t) {
	null == t ? t = 0 : 0 > t && (t = Math.max(0, this.length + t));
	for (var a = t; a < this.length; a++) if (this[a] === e) return a;
	return -1
}),  String.prototype.lenB || (String.prototype.lenB = function() {
	return this.replace(/[^\x00-\xff]/g, "**").length
}), String.prototype.leftB || (String.prototype.leftB = function(e) {
	var t = this,
		a = t.slice(0, e),
		o = a.replace(/[^\x00-\xff]/g, "**").length;
	if (e >= o) return a;
	switch (o -= a.length) {
		case 0:
			return a;
		case e:
			return t.slice(0, e >> 1);
		default:
			var n = e - o,
				i = t.slice(n, e),
				s = i.replace(/[\x00-\xff]/g, "").length;
			return s ? t.slice(0, n) + i.leftB(s) : t.slice(0, n)
	}
}), String.prototype.cut || (String.prototype.cut = function(e, t, a) {
	var o = this;
	return r = a ? o.substr(0, e) : o.leftB(e), r == o ? r : r + ("undefined" == typeof t ? "\u2026" : t)
}), Date.prototype.pattern = function(e) {
	var t = {
			"M+": this.getMonth() + 1,
			"d+": this.getDate(),
			"h+": this.getHours() % 12 == 0 ? 12 : this.getHours() % 12,
			"H+": this.getHours(),
			"m+": this.getMinutes(),
			"s+": this.getSeconds(),
			"q+": Math.floor((this.getMonth() + 3) / 3),
			S: this.getMilliseconds()
		},
		a = {
			0: "\u65e5",
			1: "\u4e00",
			2: "\u4e8c",
			3: "\u4e09",
			4: "\u56db",
			5: "\u4e94",
			6: "\u516d"
		};
	/(y+)/.test(e) && (e = e.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length))), /(E+)/.test(e) && (e = e.replace(RegExp.$1, (RegExp.$1.length > 1 ? RegExp.$1.length > 2 ? "\u661f\u671f" : "\u5468" : "") + a[this.getDay() + ""]));
	for (var o in t) new RegExp("(" + o + ")").test(e) && (e = e.replace(RegExp.$1, 1 == RegExp.$1.length ? t[o] : ("00" + t[o]).substr(("" + t[o]).length)));
	return e
}, $.ajaxSetup({
	type: "POST",
	timeout: 2e4,
	wrongmsg: function(e) {
		var t = $.trim(mergeServerMessage(e.message));
		t && HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>' + t + "</h3></div>"), $({}).delay(4e3).queue(function() {
			HUI.PopOut.closeMask()
		}))
	},
	errormsg: function() {
		HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>\u7f51\u7edc\u51fa\u95ee\u9898\u4e86\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5</h3></div>'), $({}).delay(2e3).queue(function() {
			HUI.PopOut.closeMask()
		}))
	},
	success: function(e, t, a) {
		if (-1 == $.inArray("html", this.dataTypes)) {
			var o = $.isPlainObject(e) ? e : $.parseJSON(e);
			if (!o) return void $.G.gaq("/_trc/Error/ajax/json_parse_fail_" + this.url);
			if (o.success || 1 == o.status) $.isFunction(this.mysuccess) && this.mysuccess(o, e);
			else {
				$.isFunction(this.myfailure) ? this.myfailure(o, e) : this.wrongmsg(o);
				var n = mergeServerMessage(o.message);
				n && $.G.gaq("/_trc/Error/ajax/response_" + n + "_" + this.url)
			}
		}
	},
	error: function(e, t) {
		$.G.gaq("/_trc/Error/ajax/status_" + t + "_" + (this.url ? this.url : "null_" + window.location.href)), $.isFunction(this.myerror) && this.myerror(e, t)
	}
}), function(e) {
	e.fn.serializeObject = function() {
		var t = {},
			a = function(t) {
				return t.name = e.camelCase(t.name), t
			},
			o = function(e, a) {
				var o = t[a.name];
				"undefined" != typeof o && null !== o ? t[a.name] = o.push ? o.push(a.value) : [o, a.value] : t[a.name] = a.value
			};
		return e.each(e.map(this.serializeArray(), a), o), t
	}, e.fn.getFormAction = function() {
		var e = this,
			t = e[0];
		return t && "form" === t.tagName.toLowerCase() ? encodeURI(e.attr("action")) : null
	}, e.fn.paramForm = function(t) {
		var a = e(this),
			o = {};
		return a.find("input,select,textarea").not("[type=submit]").filter("[name]").each(function(t, a) {
			(("checkbox" === e(a).attr("type") || "radio" === e(a).attr("type")) && e(a).prop("checked") === !0 || "checkbox" !== e(a).attr("type") && "radio" !== e(a).attr("type")) && ("undefined" !== e.type(o[a.name]) ? o[a.name] += "," + a.value : o[a.name] = a.value)
		}), e.isPlainObject(t) && e.extend(o, t), e.param(o)
	}, e.fn.lengthLimit = function(t) {
		return this.filter("textarea,input[type=text]").each(function() {
			var t = e(this),
				a = t.attr("maxlength"),
				o = function(e) {
					var t = e ? e.keyCode : null;
					if (t && 8 !== t && 13 !== t && !(t > 36 && 41 > t)) {
						var o = this,
							n = o.value,
							i = n.cut(a, "");
						i.length < n.length && (o.value = i, o.scrollTop = o.scrollHeight)
					}
				};
			e(this).change(function(e) {
				o.call(this, e)
			}).keyup(function(e) {
				o.call(this, e)
			}), o.apply(this)
		}), this
	}, e.fn.inputTagLimit = function(t) {
		var a = e.extend(!0, {}, {
			invalid: new RegExp("/"),
			taglen: 20
		}, t);
		return this.filter("textarea,input[type=text]").each(function() {
			var t = e(this),
				o = a.taglen,
				n = function(n) {
					var i = n ? n.keyCode : null;
					if (i && 8 !== i && 13 !== i && !(i > 36 && 41 > i)) {
						var s = t.val(),
							l = s.split(" "),
							l = l[l.length - 1],
							r = s.substring(0, s.length - l.length);
						" " != s[s.length - 1] && l && l.lenB() > o && (l = l.cut(o, ""), s = r + l), t.val(s.replace(a.invalid, "")), vl = t.val().length, e.G.setCursorPosition(t[0], {
							start: vl,
							end: vl
						})
					}
				};
			e(this).change(function(e) {
				n.call(this, e)
			}).keyup(function(e) {
				n.call(this, e)
			}), n.apply(this)
		}), this
	}, e.fn.pagelimit = function(t) {
		function a() {
			var e = parseInt(this.value) || 0,
				t = i || 0;
			e > t ? this.value = t : 1 > e ? this.value = 1 : this.value = e
		}
		function o(e) {
			e.keyCode >= 37 && e.keyCode <= 40 || 46 == e.keyCode || 8 == e.keyCode || a.call(this)
		}
		var n = e(this),
			i = t.length || 0;
		n.change(o).keyup(o), a.call(this)
	}
}(jQuery), function(e) {
	e.fn.confirm = function(t, n, r) {
		"function" != typeof t && (r = n, n = t, t = e.noop), "string" != typeof n && (r = n, n = ""), r = e.extend({}, r);
		var i = r.dir ? 1 : -1,
			o = r.tipword || "",
			a = function(t, n) {
				u.css({
					display: "block",
					overflow: "hidden"
				}), e(".PL-cfm-cont", u).stop().clearQueue().css({
					marginTop: i * t
				}).animate({
					marginTop: i * n
				}, 300, "linear", function() {
					u.css("overflow", "visible").css("display", n > t ? "none" : "block")
				})
			},
			s = function(n) {
				n.preventDefault();
				var r, s, l = e(this),
					c = l.offset(),
					f = l.outerWidth(),
					p = l.outerHeight(),
					d = e(document).width(),
					h = e(document).height(),
					g = e(window).scrollTop(),
					m = e(window).scrollLeft(),
					y = e(window).width(),
					v = e(window).height(),
					b = u.outerWidth(),
					x = u.outerHeight(),
					w = 12,
					T = -1 === i ? c.top + p + w : c.top - x - w,
					C = c.left - b / 2 + f / 2;
				r = h - x - w, T = w > T ? w : T > r ? r : T, r = d - b - w, C = w > C ? w : C > r ? r : C, r = T - w, s = C - w, e("html").animate({
					scrollTop: g > r ? r : (r = T + w + x - v) > g ? r : "+=0",
					scrollLeft: m > s ? s : (s = C + w + b - y) > m ? s : "+=0"
				}, 300), u.css({
					top: T,
					left: C
				}).data({
					"this": this,
					event: n,
					fn: t
				}), e(".PL-cfm-wds", u).html(o), a(x, 0)
			},
			u = e("#PL-confirm");
		if (!u.length) {
			u = e('<div id="PL-confirm" class="PL-confirm"><div class="PL-cfm-cont"><div class="PL-cfm-wds tc">' + o + '</div><div class="PL-cfm-btns tc"><a class="abtn abtn-s PL-cfm-yes dib" href="javascript:;""><u>\u786e\u5b9a</u></a><a class="abtn abtn-s PL-cfm-no dib" target="_self" href="javascript:;"><u>\u53d6\u6d88</u></a></div></div></div>').appendTo("body");
			var l = function() {
				var e = u.outerHeight();
				a(0, e)
			};
			e(".PL-cfm-yes", u).click(function(e) {
				e.preventDefault(), u.data("fn").call(u.data("this"), u.data("event"))
			}).click(l), e(".PL-cfm-no", u).click(l)
		}
		return n ? this.delegate(n, "click", s) : this.click(s), this
	}
}(jQuery),(function(e){
	function t(t, o) {
		var i = t == window,
			d = o && void 0 !== o.message ? o.message : void 0;
		o = e.extend({}, e.blockUI.defaults, o || {}), o.overlayCSS = e.extend({}, e.blockUI.defaults.overlayCSS, o.overlayCSS || {});
		var m = e.extend({}, e.blockUI.defaults.css, o.css || {}),
			g = e.extend({}, e.blockUI.defaults.themedCSS, o.themedCSS || {});
		if (d = void 0 === d ? o.message : d, i && f && a(window, {
				fadeOut: 0
			}), d && "string" != typeof d && (d.parentNode || d.jquery)) {
			var v = d.jquery ? d[0] : d,
				b = {};
			e(t).data("blockUI.history", b), b.el = v, b.parent = v.parentNode, b.display = v.style.display, b.position = v.style.position, b.parent && b.parent.removeChild(v)
		}
		e(t).data("blockUI.onUnblock", o.onUnblock);
		var $, w, y = o.baseZ,
			k = e(e.browser.msie || o.forceIframe ? '<iframe class="blockUI" style="z-index:' + y+++';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="' + o.iframeSrc + '"></iframe>' : '<div class="blockUI" style="display:none"></div>'),
			_ = e(o.theme ? '<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:' + y+++';display:none"></div>' : '<div class="blockUI blockOverlay" style="z-index:' + y+++';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>');
		w = o.theme && i ? '<div class="blockUI ' + o.blockMsgClass + ' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:' + (y + 10) + ';display:none;position:fixed"><div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">' + (o.title || "&nbsp;") + '</div><div class="ui-widget-content ui-dialog-content"></div></div>' : o.theme ? '<div class="blockUI ' + o.blockMsgClass + ' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:' + (y + 10) + ';display:none;position:absolute"><div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">' + (o.title || "&nbsp;") + '</div><div class="ui-widget-content ui-dialog-content"></div></div>' : i ? '<div class="blockUI ' + o.blockMsgClass + ' blockPage" style="z-index:' + (y + 10) + ';display:none;position:fixed"></div>' : '<div class="blockUI ' + o.blockMsgClass + ' blockElement" style="z-index:' + (y + 10) + ';display:none;position:absolute"></div>', $ = e(w), d && (o.theme ? ($.css(g), $.addClass("ui-widget-content")) : $.css(m)), o.theme || o.applyPlatformOpacityRules && e.browser.mozilla && /Linux/.test(navigator.platform) || _.css(o.overlayCSS), _.css("position", i ? "fixed" : "absolute"), (e.browser.msie || o.forceIframe) && k.css("opacity", 0);
		var x = [k, _, $],
			C = e(i ? "body" : t);
		e.each(x, function() {
			this.appendTo(C)
		}), o.theme && o.draggable && e.fn.draggable && $.draggable({
			handle: ".ui-dialog-titlebar",
			cancel: "li"
		});
		var T = u && (!e.boxModel || e("object,embed", i ? null : t).length > 0);
		if (p || T) {
			if (i && o.allowBodyStretch && e.boxModel && e("html,body").css("height", "100%"), (p || !e.boxModel) && !i) var S = r(t, "borderTopWidth"),
				j = r(t, "borderLeftWidth"),
				P = S ? "(0 - " + S + ")" : 0,
				I = j ? "(0 - " + j + ")" : 0;
			e.each([k, _, $], function(e, t) {
				var a = t[0].style;
				if (a.position = "absolute", 2 > e) i ? a.setExpression("height", "Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.boxModel?0:" + o.quirksmodeOffsetHack + ') + "px"') : a.setExpression("height", 'this.parentNode.offsetHeight + "px"'), i ? a.setExpression("width", 'jQuery.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"') : a.setExpression("width", 'this.parentNode.offsetWidth + "px"'), I && a.setExpression("left", I), P && a.setExpression("top", P);
				else if (o.centerY) i && a.setExpression("top", '(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"'), a.marginTop = 0;
				else if (!o.centerY && i) {
					var n = o.css && o.css.top ? parseInt(o.css.top) : 0,
						s = "((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + " + n + ') + "px"';
					a.setExpression("top", s)
				}
			})
		}
		if (d && (o.theme ? $.find(".ui-widget-content").append(d) : $.append(d), (d.jquery || d.nodeType) && e(d).show()), (e.browser.msie || o.forceIframe) && o.showOverlay && k.show(), o.fadeIn) {
			var G = o.onBlock ? o.onBlock : c,
				A = o.showOverlay && !d ? G : c,
				D = d ? G : c;
			o.showOverlay && _._fadeIn(o.fadeIn, A), d && $._fadeIn(o.fadeIn, D)
		} else o.showOverlay && _.show(), d && $.show(), o.onBlock && o.onBlock();
		if (n(1, t, o), i ? (f = $[0], h = e(":input:enabled:visible", f).not("[type=file]"), o.focusInput && setTimeout(s, 20)) : l($[0], o.centerX, o.centerY), o.timeout) {
			var U = setTimeout(function() {
				i ? e.unblockUI(o) : e(t).unblock(o)
			}, o.timeout);
			e(t).data("blockUI.timeout", U)
		}
	}
	function a(t, a) {
		var i = t == window,
			s = e(t),
			l = s.data("blockUI.history"),
			r = s.data("blockUI.timeout");
		r && (clearTimeout(r), s.removeData("blockUI.timeout")), a = e.extend({}, e.blockUI.defaults, a || {}), n(0, t, a), null === a.onUnblock && (a.onUnblock = s.data("blockUI.onUnblock"), s.removeData("blockUI.onUnblock"));
		var c;
		c = i ? e("body").children().filter(".blockUI").add("body > .blockUI") : e(".blockUI", t), i && (f = h = null), a.fadeOut ? (c.fadeOut(a.fadeOut), setTimeout(function() {
			o(c, l, a, t)
		}, a.fadeOut)) : o(c, l, a, t)
	}
	function o(t, a, o, n) {
		t.each(function(e, t) {
			this.parentNode && this.parentNode.removeChild(this)
		}), a && a.el && (a.el.style.display = a.display, a.el.style.position = a.position, a.parent && a.parent.appendChild(a.el), e(n).removeData("blockUI.history")), "function" == typeof o.onUnblock && o.onUnblock(n, o)
	}
	function n(t, a, o) {
		var n = a == window,
			s = e(a);
		if ((t || (!n || f) && (n || s.data("blockUI.isBlocked"))) && (n || s.data("blockUI.isBlocked", t), o.bindEvents && (!t || o.showOverlay))) {
			var l = "mousedown mouseup keydown keypress";
			t ? e(document).bind(l, o, i) : e(document).unbind(l, i)
		}
	}
	function i(t) {
		if (t.keyCode && 9 == t.keyCode && f && t.data.constrainTabKey) {
			var a = h,
				o = !t.shiftKey && t.target === a[a.length - 1],
				n = t.shiftKey && t.target === a[0];
			if (o || n) return setTimeout(function() {
				s(n)
			}, 10), !1
		}
		var i = t.data;
		return e(t.target).parents("div." + i.blockMsgClass).length > 0 ? !0 : 0 == e(t.target).parents().children().filter("div.blockUI").length
	}
	function s(e) {
		if (h) {
			var t = h[e === !0 ? h.length - 1 : 0];
			t && t.focus()
		}
	}
	function l(e, t, a) {
		var o = e.parentNode,
			n = e.style,
			i = (o.offsetWidth - e.offsetWidth) / 2 - r(o, "borderLeftWidth"),
			s = (o.offsetHeight - e.offsetHeight) / 2 - r(o, "borderTopWidth");
		t && (n.left = i > 0 ? i + "px" : "0"), a && (n.top = s > 0 ? s + "px" : "0")
	}
	function r(t, a) {
		return parseInt(e.css(t, a)) || 0
	}
	e.fn._fadeIn = e.fn.fadeIn;
	var c = function() {},
		d = document.documentMode || 0,
		u = e.browser.msie && (e.browser.version < 8 && !d || 8 > d),
		p = e.browser.msie && /MSIE 6.0/.test(navigator.userAgent) && !d;
	e.blockUI = function(e) {
		t(window, e)
	}, e.unblockUI = function(e) {
		a(window, e)
	}, e.growlUI = function(t, a, o, n) {
		var i = e('<div class="growlUI"></div>');
		t && i.append("<h1>" + t + "</h1>"), a && i.append("<h2>" + a + "</h2>"), void 0 == o && (o = 3e3), e.blockUI({
			message: i,
			fadeIn: 700,
			fadeOut: 1e3,
			centerY: !1,
			timeout: o,
			showOverlay: !1,
			onUnblock: n,
			css: e.blockUI.defaults.growlCSS
		})
	}, e.fn.block = function(a) {
		return this.unblock({
			fadeOut: 0
		}).each(function() {
			"static" == e.css(this, "position") && (this.style.position = "relative"), e.browser.msie && (this.style.zoom = 1), t(this, a)
		})
	}, e.fn.unblock = function(e) {
		return this.each(function() {
			a(this, e)
		})
	}, e.blockUI.version = 1.0, e.blockUI.defaults = {
		message: "<h1>Please wait...</h1>",
		title: null,
		draggable: !0,
		theme: !1,
		css: {
			padding: 0,
			margin: 0,
			width: "30%",
			top: "40%",
			left: "35%",
			textAlign: "center",
			color: "#000",
			border: "none",
			backgroundColor: "#fff"
		},
		themedCSS: {
			width: "30%",
			top: "40%",
			left: "35%"
		},
		overlayCSS: {
			backgroundColor: "#000",
			opacity: .2
		},
		growlCSS: {
			width: "350px",
			top: "10px",
			left: "",
			right: "10px",
			border: "none",
			padding: "5px",
			opacity: .6,
			cursor: "default",
			color: "#fff",
			backgroundColor: "#000",
			"-webkit-border-radius": "10px",
			"-moz-border-radius": "10px",
			"border-radius": "10px"
		},
		iframeSrc: /^https/i.test(window.location.href || "") ? "javascript:false" : "about:blank",
		forceIframe: !1,
		baseZ: 1e3,
		centerX: !0,
		centerY: !0,
		allowBodyStretch: !0,
		bindEvents: !0,
		constrainTabKey: !0,
		fadeIn: 200,
		fadeOut: 200,
		timeout: 0,
		showOverlay: !0,
		focusInput: !1,
		applyPlatformOpacityRules: !0,
		onBlock: null,
		onUnblock: null,
		quirksmodeOffsetHack: 4,
		blockMsgClass: "blockMsg"
	};
	var f = null,
		h = []
})(jQuery), HUI = function() {
	var e = $.browser.msie,
		t = ($.browser.mozilla, e && "6.0" === $.browser.version);
	e && "7.0" === $.browser.version, e && "8.0" === $.browser.version, e && "9.0" === $.browser.version, $.browser.opera;
	return {
		PopOut: function() {
			return {
				fnCloseMask: function() {
					this.closeMask()
				},
				poptorylen: 10,
				poptory: [],
				WD: [400, 484, 660, 924],
				STR: ['<a href="javascript:;" target="_self" class="abtn l" onclick="HUI.PopOut.closeMask();"><button type="button"><u>\u5173\u95ed</u></button></a>', '<a href="javascript:;" target="_self" class="abtn l" onclick="HUI.PopOut.closeMask();"><button type="button"><u>\u53d6\u6d88</u></button></a>'],
				go: function(e) {
					if (e = -e, !(0 > e || e > 9)) {
						var t, a = this;
						if (t = a.poptory[e]) {
							var o = a.setCont([t.head, t.cont], t.n);
							a.blockPop(o, t.n, t.ht, t.opt)
						}
					}
				},
				alert: function(e, t, h, tp, a) {
					var o = this,
						t = "s" === t ? 0 : "m" === t ? 1 : "l" === t ? 2 : t,
						t = t || 0;
					o.WD, $.G.store();
					("string" === $.type(e) || "number" === $.type(e)) && (e = ["", e + ""]), e[0] = e[0], e[1] = e[1] || "";
					var n = "string" === $.type(e[1]),
						i = o.setCont(e, t, h),
						s = i.outerHeight();
					if (n) {
						var l = i.html();
						i.remove(), i = '<div class="mask-body">' + l + "</div>"
					} else o.poptory = [{
						head: e[0],
						cont: $(e[1]),
						n: t,
						ht: s,
						opt: a
					}].concat(o.poptory), o.poptory.length = o.poptorylen;
					o.blockPop(i, t, s, tp, a)
				},
				setCont: function(e, t, h) {
					var a, o, n = this,
						i = n.WD,
						c = h ? '' : '<a class="mask-close" target="_self" href="javascript:;" onclick="HUI.PopOut.closeMask();">\u5173\u95ed</a>';
					s = $.G.store();
					return a = null == e[0] ? "" : $('<div class="tt-s"><span>' + e[0] + '</span>'+ c +'</div>'), o = $('<div class="mask-body"></div>').css("width", i[t]).appendTo(s), o.append(a).append($('<div class="mask-cont"></div>').append(e[1])), o
				},
				blockPop: function(e, t, a, tp, o) {
					var n = this,
						i = n.WD,
						s = $.G.store();
					o = $.extend({
						position: "fixed"
					}, o);
					var l, r, c, d = !1,nt = tp ? tp : "50%";
					"fixed" === o.position ? d = !0 : (l = $(window), r = l.scrollTop(), c = l.height()), $.blockUI({
						message: e,
						baseZ: 9e3,
						focusInput: o && void 0 !== o.focus ? o.focus : !0,
						onUnblock: function() {
							s.find(".mask-cont:empty").closest(".mask-body").remove(), o && $.isFunction(o.fn) && o.fn()
						},
						css: {
							position: o.position,
							top: d ? nt : r,
							left: "50%",
							textAlign: "left",
							marginLeft: -(i[t] / 2),
							marginTop: d ? -(a / 2) - 20 : a >= c ? 0 : (c - a) / 2,
							width: i[t],
							//height: a,
							border: "none",
							background: "none",
							border: "10px solid rgba(0, 0, 0, 0.1)",
							"border-radius": "5px",
							"-moz-border-radius": "5px",
							"-webkit-border-radius": "5px"

						},
						fadeIn: $(".blockOverlay:visible").length ? 0 : 200
					}), 1 > t && n.setOverLay()
				},
				setOverLay: function() {
					if (!t) {
						var e = $("div.blockPage"),
							a = e.outerWidth(),
							o = e.outerHeight(),
							n = parseInt(e.css("marginTop")),
							i = parseInt(e.css("marginLeft"));
						$("div.blockOverlay").css({
							width: a + 24,
							height: o + 24,
							top: "50%",
							left: "50%",
							marginTop: n - 12,
							marginLeft: i - 12,
							"border-radius": "8px",
							"-moz-border-radius": "8px",
							"-webkit-border-radius": "8px"
						})
					}
				},
				closeMask: function() {
					$.unblockUI()
				}
			}
		}()
	}
}(), (function(e){
	e.extend({
		orderTipOpen: function(o) {
			var graceTip = e('#J_GraceTip'),
				scrollT = e(document).scrollTop(),
				winWidth = e(window).width(),
				winH = e(window).height() / 2,
				dy = $(window).height(), bh = $('body').height();
			txt = o || '\u64cd\u4f5c\u4e2d\uff0c\u8bf7\u7a0d\u7b49...';
			var overlay = $('<div class="shade"></div>');
			// if(!$(".shade").length)
			// {
			//   $('body').append(overlay);
			// }
			// overlay.css({'height':bh - dy > 0 ? bh : dy + 'px','z-index':'100', opacity: .6}).fadeIn(100);


			graceTip.length ? graceTip.css('display','block') : e(document.body).append('<div id="J_GraceTip" class="tc-popup tc-gracetip"><div class="tc-popup-content">' + txt + '</div></div>');
			var gW = e('#J_GraceTip').width(),
				gH = e('#J_GraceTip').height(),
				l = (winWidth / 2) - (gW / 2),
				t = winH + scrollT - (gH / 2);
			e('#J_GraceTip').css({'top': t, 'left': l});
		}
	});
	e.extend({
		orderTipClose: function(o) {
			e('#J_GraceTip').length && e('#J_GraceTip').css('display', 'none');
			e('.shade').length && e('.shade').remove();
		}
	});
})(jQuery),function(){
	function t(){
		var e = $("#dt-form-login"),
			t = e.serializeObject();
		t.user_name = e.find("input[name=username]").val(), t.password = e.find("[name=password]").val() || "/";

		var eSub = $("#loginbtn"),
			eSub_u = eSub.find('u'),
			loginTxt = ["登录","正在登录..."];
		if(!eSub.hasClass("abtn-no")){
			$("#dt-form-login").find(".login-msg").remove();
			eSub.addClass("abtn-no");
			eSub_u.text(loginTxt[1]);
			$.ajax({
				type: "get",
				url: "/member.php/Public/ajax_dologin",
				dataType: "jsonp",
				jsonpCallback:"login",
				data: t,
				success: function(e, t) {
					if (1 === e.status) {
						var a = e.next;
						window.location.href = a
					} else if(1 !== e.status){
						$("#dt-form-login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</p></div>');
						$('.dt-ccodepic').click();
					}
				}
			}).always(function(){eSub.removeClass("abtn-no");eSub_u.text(loginTxt[0]);});
		}

	}

	function a() {
		$.ajax({
			type: "GET",
			cache: !1,
			url: "/captcha/",
			mysuccess: function(e, t) {
				if (e.data && e.data.img) {
					var a = $("#dt-form-login");
					$(".dt-ccodepic").attr({
						src: e.data.img
					}), a.find("input[name=token]").val(e.data.id)
				}
			}
		})
	}

	if (HUI && HUI.PopOut) {
		var o = $.browser.msie,
			n = (o && "6.0" === $.browser.version, o && "7.0" === $.browser.version, o && "8.0" === $.browser.version);
		o && "9.0" === $.browser.version;
		$.extend(HUI.PopOut, {
			login: function(o) {
				var i = this,
					s = $("#poplogin"),
					l = $.G.store(),
					r = o;
				r || (r = location.pathname + location.search + location.hash), s.find("[name=next]").val(r), l.length || (l = $('<div id="win-house" class="h0"></div>').appendTo("body")), s.length || (s = $('<div id="poplogin" class="win-wraper clearfix"><div class="login clearfix"><div class="cont"><form id="dt-form-login" method="POST"  action="/user.php?act=signin&next=' + r + '" method="POST" target="_self"><div class="dt-unme cnt-i clearfix"><input type="text" id="p-username" name="username" autocomplete="off" placeholder="\u7528\u6237\u540d/\u90ae\u7bb1" /></div><div class="dt-pswd cnt-i clearfix"><input type="password" autocomplete="off" id="p-password" name="password"  placeholder="\u5bc6\u7801" /></div><div class="u-chk clearfix"><input class="chk" type="checkbox" name="remember" id="poplogin-rem" value="" checked /><label for="poplogin-rem" >\u8bb0\u4f4f\u6211</label><a href="/member.php/Public/findpw_verification.html" class="G_fr">\u5fd8\u8bb0\u5bc6\u7801\uff1f</a></div><input type="hidden" name="next" value="'+ r +'" /><div class="submit clearfix"><a href="javascript:;"  id="loginbtn"><button type="submit" class="G_btn_a btn_100P"><u>\u767b\u5f55</u></button></a></div><div class="otherLogin"><a href="/member.php/public/loginsdk/type/qq" target="_blank" title="QQ登录" class="qq_login">QQ登录</a><a href="/member.php/public/loginsdk/type/weixin"  target="_blank" title="微信登录" class="wx_login">微信登录</a></div></form></div></div><div class="toreg clearfix"><a href="/member.php/Public/reg">\u8fd8\u6ca1\u6709\u8d26\u53f7?\u7acb\u5373\u6ce8\u518c&gt;</a></div></div>'), l.append(s), $("#dt-form-login").submit(function(e) {
					e.preventDefault(), t()
				}), $(document).on("click", ".dt-ccodepic", function() {
					var rand = "captcha.php?is_login=1&" + Math.random();
					$(this).attr({src:rand})
				}));
				var c = $("#p-username, #p-password");
				$("#poplogin").find(".toreg").on("click", function(e){
					e.stopPropagation();
					e.preventDefault();
					HUI.PopOut.register();
				});
				if (c.length && "undefined" == typeof c.get(0).placeholder) {
					var d = $("#poplogin").find("i");
					d.css("display", "block"), d.click(function(e) {
						$(this).css("display", "none"), $(this).siblings("input").focus()
					}), c.focusin(function(e) {
						$(this).siblings("i").css("display", "none")
					}), c.focusout(function(e) {
						"" == $.trim(this.value) && $(this).siblings("i").css("display", "block")
					}), n && c.keydown(function(e) {
						13 == e.keyCode && $.trim($("#p-username").val()) && $.trim($("#p-password").val()) && (e.preventDefault(), e.stopPropagation(), s.find("form").submit())
					})
				}
				i.alert(["\u767b\u5f55", s, ""], "m"), $({}).delay(100).queue(function() {
					var e;
					(e = $("#p-username")[0]) && e.focus()
				})
			}
		})
	}
}(),function(){
	function intervalTime(num) {
		var num = parseInt(num);
		var i = 0;
		$("#J_CreateMobileCode").addClass("G_btn_disabled");
		$("#J_CreateMobileCode").html("<span></span>秒重新发送");
		$("#J_CreateMobileCode span").text(num);
		var inter = setInterval(function() {
			if (i < num) {
				i++;
				$('#J_CreateMobileCode span').text(num - i);
			} else {
				clearInterval(inter);
				$("#J_CreateMobileCode").removeClass("G_btn_disabled");
				$("#J_CreateMobileCode").html("重新发送").bind("click", sendCode);
			}
		}, 1000);
	}
	function checkData(ele) {
		if (!ele) {
			return true;
		}
		var value = $.trim(ele.value);
		switch (ele.name) {
			case "username":
				if (!value || new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(value) == false) {
					$("#J_PopRegister").find(".login-msg").remove();
					$("#J_PopRegister").find(".login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>请输入正确的手机号</p></div>');
					return false;
				}else{
					return true;
				}
				break;
			case "code":
				if (!value || value.length < 6 || new RegExp("^([0-9])+$").test(value) == false) {
					$("#J_PopRegister").find(".login-msg").remove();
					$("#J_PopRegister").find(".login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>请输入正确的手机号</p></div>');
					return false;
				} else if (value.length == 6) {
					return true;
				}
				break;
		}
	}

	function sendCode() {
		var flag1 = checkData(document.getElementById("username"));
		if (flag1) {

			var postData = {
				tel: $("#J_UserName").val(),
				tpl_id: 4852,
				isReg:1
			}
			$.ajax({
				type: "get",
				url: "/member.php/Public/noteVerify",
				dataType: "jsonp",
				jsonpCallback:"code",
				data: postData,
				success: function(data) {
					if (data.status == 0) {
						$("#J_CreateMobileCode").unbind("click");
						intervalTime(60);
					}else if(data.status == 205405){
						$("#J_PopRegister").find(".login-msg").remove();
						$("#J_PopRegister").find(".login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>号码异常/同一号码发送次数过于频繁</p></div>');
					}else if(data.status == 1){
						$("#J_UserName").focus();
						$("#J_PopRegister").find(".login-msg").remove();
						$("#J_PopRegister").find(".login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>'+ data.msg +'</p></div>');
					}
				}
			});
		}
	}

	function t(){
		var e = $("#J_FormRegister"),
			t = e.serializeObject();
		t.user_name = e.find("input[name=username]").val(),t.code = e.find("input[name=vcode]").val(), t.password = e.find("[name=password]").val();

		$.ajax({
			type: "get",
			url: "/member.php/Public/doreg_ajax",
			dataType: "jsonp",
			jsonpCallback:"regCallback",
			data: t,
			success: function(e) {
				if (0 === e.status) {
					$.Bom.setCookie("visited", "1", {path:"/index.php/car/product_details/id/"});
					var a = e.next;
					window.location.href = a
				} else if(0 !== e.status){
					$("#J_PopRegister").find(".login-msg").remove();
					$("#J_PopRegister").find(".login").prepend('<div class="login-msg msg"><p class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</p></div>');
				}
			}
		});
	}


	if (HUI && HUI.PopOut) {
		var o = $.browser.msie,
			n = (o && "6.0" === $.browser.version, o && "7.0" === $.browser.version, o && "8.0" === $.browser.version);
		o && "9.0" === $.browser.version;
		$.extend(HUI.PopOut, {
			register: function(o) {
				var i = this,
					s = $("#J_PopRegister"),
					l = $.G.store(),
					r = o;
				r || (r = location.pathname + location.search + location.hash), s.find("[name=next]").val(r), l.length || (l = $('<div id="win-house" class="h0"></div>').appendTo("body")), s.length || (s = $('<div id="J_PopRegister" class="win-wraper clearfix"><div class="login clearfix"><div class="cont"><form id="J_FormRegister" method="POST"  action="/user.php?act=signin&next=' + r + '" method="POST" target="_self"><div class="dt-unme cnt-i clearfix"><input type="text" id="J_UserName" name="username" placeholder="请输入您的手机号" autocomplete="off" /></div><div class="get-vcode clearfix"><div class="dt-vcode cnt-i clearfix"><input type="text" maxlength="6" id="J_VCode" name="vcode" placeholder="验证码" autocomplete="off" /></div><a href="javascript:;" id="J_CreateMobileCode" class="G_btn_b btn_42px">\u83b7\u53d6\u9a8c\u8bc1\u7801</a></div><div class="dt-pswd cnt-i clearfix"><input type="password" id="p-password" autocomplete="off" name="password" placeholder="密码"></div><input type="hidden" name="next" value="'+ r +'" /><div class="submit clearfix"><a href="javascript:;"  id="J_SubmitBtn"><button type="submit" class="G_btn_a btn_100P"><u>快速注册</u></button></a></div></form></div></div><div class="toreg clearfix"><a href="/index.php/public/login">已有账号?立即登录&gt;</a></div></div>'), l.append(s), $("#J_FormRegister").submit(function(e) {
					e.preventDefault(), t()
				}));
				var c = $("#J_VCode, #J_UserName");
				$("#J_PopRegister").find(".toreg").on("click", function(e){
					e.stopPropagation();
					e.preventDefault();
					HUI.PopOut.login();
				});
				if (c.length && "undefined" == typeof c.get(0).placeholder) {
					n && c.keydown(function(e) {
						13 == e.keyCode && $.trim($("#J_UserName").val()) && $.trim($("#J_PassWord").val()) && (e.preventDefault(), e.stopPropagation(), s.find("form").submit())
					})
				}
				$("#J_CreateMobileCode").bind("click", sendCode);
				i.alert(["手机号注册,免费看底价", s, ""], "m"), $({}).delay(100).queue(function() {
					var e;
					(e = $("#J_UserName")[0]) && e.focus()
				})
			}
		})
	}
}(), function(e){
	function t(e) {
		HUI.PopOut.alert([null, f, ""], 2)
	}
	function a() {
		v.find("a.abtn").addClass("abtn-no"), v.find(".s-sina").css({
			opacity: .5,
			color: "#aaa"
		})
	}
	function o() {
		v.find("a.abtn").removeClass("abtn-no"), v.find(".s-sina").css({
			opacity: 1,
			color: "#333"
		})
	}
	function n(e, t) {
		g.find("input[name=album]").val(e).end().find("a,span").html(t)
	}
	function i(e, t) {
		b.html(t).attr("class", e)
	}
	function s() {
		h.val(""), i("", "")
	}
	function l(t, a) {
		e.G.blinkIt(function() {
			i("re-postsuc", y[0] + "\u6210\u529f\uff01")
		}, null, function() {
			HUI.PopOut.closeMask(), e.isFunction(a) && a(t), s()
		}, 1, 800);
		var o = e(".collection").find("em");
		1 === o.length && t.blog && o.each(function() {
			o.html(parseInt(o.html()) + 1)
		})
	}
	function r(t, a) {
		if (t) {
			var o = e(p);
			if (o.addClass("re-done").attr("href", "/people/mblog/" + t + "/detail/").attr("target", "_blank").attr("title", "\u53bb\u770b\u6211\u7684\u6536\u96c6"), a) {
				var n = o.closest(".woo").find(".d1"),
					i = e(".otheralbums h4").find("span").text(),
					s = parseInt(n.text()) || parseInt(i) || 0;
				n.html(s + 1).removeClass("dn"), o.find("em").length && o.find("i").addClass("y-done").end().find("em").html("\u5df2\u6536\u96c6 " + (s + 1))
			}
		}
	}
	function c(a, n, i, l, r) {
		w = !1, y = ["\u6536\u96c6", "\u6536\u96c6\u5230", "\u6536\u96c6"];
		var c, d = a.closest(".center"),
			p = "";
		d.length ? (c = d.find(".js-favorite-blogtit"), p = c.text()) : (c = a.closest(".woo").find("div.g"), p = e("<div>" + c.html() + "</div>").find("a").remove().end().text()), h.val(e.trim(p)), e("#re-inp-parent").attr("name", "parent").val(n), m.empty().scrollTop(0);
		var g, v, b = d.find(".js-favorite-blogimg");
		b.length ? (g = b.data("width"), v = b.data("height")) : (b = a.closest("div.woo").find("div.mbpho img"), g = b.outerWidth(), v = parseInt(b.attr("height"))), g > 200 && (v = 200 * v / g, g = 200), $i = e("<img />").attr("src", b.attr("src")).appendTo(m), $i.css({
			width: g,
			height: v,
			marginTop: 200 >= v ? (200 - v) / 2 : 0,
			cursor: 200 >= v ? "default" : "move"
		}), e.data(m[0], "imgProp", {
			height: v
		}), t(a)
	}
	function d(a, n, i, l, r, c, d, p) {
		w = p, y = ["\u7f16\u8f91", "\u8f6c\u79fb\u5230", "\u63d0\u4ea4"], u("/blog/edit/", r, i, c, d, p), f.css("display", "block"), s(), o();
		var g, v = a.closest(".center"),
			b = "";
		v.length ? (g = v.find(".js-favorite-blogtit"), b = g.text()) : (g = a.closest(".woo").find("div.g"), b = e("<div>" + g.html() + "</div>").find("a").remove().end().text()), h.val(e.trim(b)), e("#re-inp-parent").attr("name", "blog").val(n), m.empty().scrollTop(0);
		var $ = v.find("img.js-favorite-blogimg");
		if ($.length) var k = $.data("height"),
			_ = $.data("width"),
			k = 200 * k / _,
			x = e("<img />").attr("src", $.attr("src")).appendTo(m);
		else {
			$ = a.closest("div.woo").find("div.mbpho img");
			var k = parseInt($.attr("height")),
				x = e("<img />").attr("src", $.attr("src")).appendTo(m)
		}
		x.css({
			width: 200,
			marginTop: 200 > k ? (200 - k) / 2 : 0,
			cursor: 200 >= k ? "default" : "move"
		}), e.data(m[0], "imgProp", {
			height: k
		}), t(a)
	}
	function u(t, s, c, d, u, p) {
		function k(e) {
			keyupLenLimitForU(e.currentTarget, 300, !0, !0)
		}
		if (f && f.length) e.G.store(f);
		else {
			f = e('<div id="re-favorite"><form action="' + t + '" target="_self"><div id="re-head"><a id="re-close" target="_self" href="javascript:;" onclick="HUI.PopOut.closeMask();">\u5173\u95ed</a><h1>' + y[0] + '</h1></div><div id="re-cont" class="clr"><div id="re-left" class="l"></div> <div id="re-right" class="r"> <p>' + y[1] + '</p> <div id="re-albumsel"><input class="dn" type="text" data-optional="1" value="0" name="album"><a id="re-albumtrig" href="javascript:;">\u9ed8\u8ba4\u4e13\u8f91</a><span id="re-onlyeditwds"></span></div><textarea name="content"' + ("\u7f16\u8f91" != y[0] || c != e.G.getUSERID() && !e.G.isSTAFF() ? ' class="txa txa-no" disabled ' : ' class="txa" ') + ' data-optional="1" ></textarea> <div id="re-subpan" class="u-chk clr"> <a href="javascript:;" class="abtn l "><button type="submit"><u>' + y[2] + "</u></button></a>" + (typeof BIND_SITES != $ && BIND_SITES.sina ? '<input id="re-sycn-sina" type="checkbox" value="sina" class="chk s-sina" name="syncpost" /><label class="s-sina" title="\u540c\u6b65\u5230\u65b0\u6d6a\u5fae\u535a" for="re-sycn-sina">\u540c\u6b65</label><div class="re-mbsite s-sina">\u65b0\u6d6a</div>' : "") + '<div id="re-poststat"></div></div></div></div><input id="re-inp-parent" type="hidden" name="parent" value="" data-optional="1" /></form></div>'), e.G.store(f);
			var _ = e.G.getYearCookie("sync");
			e("#re-sycn-sina").prop("checked", -1 === _.indexOf("sina") ? !1 : !0).change(function() {
				var t = e(this),
					a = t.attr("value");
				_ = _.replace(new RegExp("," + a, "ig"), ""), t.prop("checked") || -1 !== _.indexOf(a) ? t.prop("checked") && e.G.setYearCookie("sync", _ + "," + a) : e.G.setYearCookie("sync", _)
			}), b = e("#re-poststat"), g = f.find("#re-albumsel"), g.removeClass("re-onlyedit").find("a").myalbums({
				sel_valueipt: g.find("input[name=album]"),
				sel_holder: f
			}), m = e("#re-left").mousemove(function(t) {
				if (t.stopPropagation(), !e.data(this, "movelock")) {
					var a = e(this),
						o = e.data(this, "imgProp") || {},
						n = o.height;
					if (n > 200) {
						var i = t.pageY,
							s = m.offset().top,
							l = i - s - 50,
							l = 0 > l ? 0 : l;
						e.data(this, "movelock", !0), a.stop().animate({
							scrollTop: l * (n - 200) * 2 / 200
						}, 50, "linear", function() {
							e.data(a[0], "movelock", !1)
						})
					}
				}
			}), h = f.find("textarea.txa"), h.keyup(k).blur(k).focus(k), keyupLenLimitForU(h[0], 300, !0, !0), e.fn.at && h.at({
				isFixed: !0
			}), v = f.find("form").safeSubmit(function(t) {
				var n = e(this),
					c = h.val(),
					d = g.find("input").val(),
					u = e("#re-albumtrig").text(),
					p = n.find("a.abtn");
				p.find("[type=submit]");
				p.hasClass("abtn-no") || (a(), i("re-inpost", "\u6b63\u5728\u63d0\u4ea4\uff0c\u8bf7\u7a0d\u5019"), e.ajax({
					url: n.getFormAction(),
					data: n.paramForm(getToken(2)),
					success: function(t) {
						var a = e.isPlainObject(t) ? t : e.parseJSON(t);
						if (!a || "object" != typeof a) return void i("re-posterr", "\u51fa\u73b0\u5f02\u5e38\uff0c\u53ef\u80fd\u662f\u7f51\u7edc\u539f\u56e0");
						if (a.data || (a.data = {}), a.data.content = c, a.data.albumid = d, a.data.albumname = u, a.success) {
							var n = g.find("[name=album]").val(),
								p = g.find("a").html();
							!w && n && "0" != n && (e.Bom.removeCookie("sgt"), e.Bom.setSubCookie("sgt", "ai" + e.G.getUSERID(), n, {
								expires: 30
							}), e.Bom.setSubCookie("sgt", "an" + e.G.getUSERID(), p, {
								expires: 30
							})), l(a.data, s), r(a.data && a.data.blog, 1)
						} else if (a.data && a.data.robot_check);
						else {
							var f = e.trim(mergeServerMessage(a.message));
							i("re-posterr", f), o(), "\u4f60\u5df2\u7ecf\u6536\u96c6\u4e86\u8be5\u5206\u4eab" == f ? r() : o(), e.G.gaq("/_trc/Error/ajax/response_" + f + "_forward")
						}
					},
					myerror: function() {
						i("re-posterr", "\u7f51\u7edc\u539f\u56e0\u5bfc\u81f4\u5931\u8d25\uff0c\u8bf7\u7a0d\u5019\u518d\u8bd5"), o()
					}
				}).always(function() {}))
			}, function(t) {
				e.G.blinkIt(function() {
					h.css({
						backgroundColor: "#d7ebf7"
					})
				}, function() {
					h.css({
						backgroundColor: "#fff"
					})
				}, function() {
					h.focus()
				}, 4, 200)
			})
		}
		if (p ? (g.addClass("re-onlyedit"), e("#re-subpan").find("input,label,div.re-mbsite").css("visibility", "hidden")) : (g.removeClass("re-onlyedit"), e("#re-subpan").find("input,label,div.re-mbsite").css("visibility", "visible")), "undefined" != typeof d) d = d || "", u = u || "\u9ed8\u8ba4\u4e13\u8f91", n(d, u);
		else {
			var x = e.Bom.getSubCookie("sgt", "ai" + e.G.getUSERID()) || "",
				C = e.Bom.getSubCookie("sgt", "an" + e.G.getUSERID()) || "\u9ed8\u8ba4\u4e13\u8f91";
			n(x, C)
		}
		v.attr("action", t), f.find("h1").html(y[0]).end().find("#re-right p").eq(0).html(y[1]).end().find(".abtn u").html(y[2])
	}
	var p, f, h, m, g, v, b, $, w = !1,
		y = ["\u6536\u96c6", "\u6536\u96c6\u5230", "\u6536\u96c6"];
	e.fn.SGfavorite = function(t) {
		function a(t) {
			if (e(this).hasClass("unlike")) return !0;
			t.stopPropagation(), t.preventDefault();
			var a = e(this),
				o = a.data("favoriteid"),
				o = o ? o : a.closest(".productOperation").data("favoriteid"),
				i = o ? o : 0,
				s = e.G.getUSERID();

			e.ajax({
				type: "GET",
				url: "/user.php?act=collect",
				dataType: "json",
				data: 'id=' + i,
				success: function(e, t) {
					if (1 === e.status) {
						a.removeClass('like').addClass('unlike');
						a.find('span').html('已收藏');
					} else 0 === e.status && (window.location.href = "/user.php")
				}
			});
		}
		t = t || {};
		var o = t.etype || "click",
			n = t.callback;
		return e.G.getUSERID() ? e(document).on(o, this.selector, a) : (this.attr("title") || this.attr("title", "\u767b\u5f55\u624d\u80fd\u8fdb\u884c\u64cd\u4f5c\u54e6\uff0c\u70b9\u51fb\u5c31\u53ef\u4ee5\u767b\u5f55\u5566"), e(document).on(o, this.selector, function(e) {
			e.stopPropagation(), e.preventDefault(), HUI.PopOut.login()
		})), this
	}, e.fn.SGcomment = function(t) {
		function a(t, a, o, n, i, s, l) {
			var r = i.find(".re-comt");
			i.find(".re-comt").stop().clearQueue().animate({
				height: 0
			}, 200).queue(function() {
				e.Woo.resetCol(-118, s, l), e.data(n[0], "comment", -1), r.find("textarea").blur()
			}), e.data(n[0], "comment", 0)
		}
		function o(t, a, o, n, i, s, l, r) {
			if ("undefined" == typeof e.data(n[0], "comment")) {
				var c = e('<li class="re-comt"><img width="24" height="24" src=' + USER.smallAvatar + '><form action="/napi/comment/create/"><div class="pb8"><textarea class="txa" name="content" placeholder="\u53d1\u5e03\u8bc4\u8bba..."></textarea><a class="abtn l" href="#"><u>\u8bc4\u8bba</u></a></div><input type="hidden" name="_type" value=""/><input type="hidden" value="' + a + '" name="comment_message_id"></form></li>');
				i.after(c), e.fn.at && c.find("textarea").at({
					pageMembers: s.find("li p a:first-child")
				}), c.find("a.abtn").click(function(t) {
					t.stopPropagation(), t.preventDefault();
					var a = e(this),
						o = a.closest("form"),
						n = o.find("textarea"),
						i = e.trim(n.val());
					return i ? void(a.hasClass("abtn-no") || (a.addClass("abtn-no"), e.ajax({
						url: "/napi/comment/create/",
						data: o.paramForm(getToken(2)),
						mysuccess: function(t, o) {
							var i = e(['<li><a href="/myhome/" target="_blank"><img src="', t.data.replyerImg, '" width="24" height="24"></a><p><a href="/myhome/" target="_blank">\u6211</a><br/><span>', t.data.content.replace(/<br\s*\/?>/i, " ").replace(/@[\u2E80-\u9FFF\d\w]{1,20}/gi, ""), "</span></p></li>"].join(""));
							c.before(i.css("display", "none")), n.val(""), i.slideDown(200, function() {
								e.Woo.resetCol(i.outerHeight(!0), l, r)
							});
							var s = a.closest(".woo").find(".d3"),
								d = parseInt(s.text()) || 0;
							s.html(++d).removeClass("dn")
						},
						myerror: function() {
							this.errormsg()
						}
					}).always(function() {
						a.removeClass("abtn-no")
					}))) : void alert("\u8bf7\u8f93\u5165\u5185\u5bb9\uff01")
				})
			}
			var d = e(window),
				u = d.height(),
				p = d.scrollTop(),
				f = s.find(".re-comt"),
				h = f.offset(),
				m = h.top,
				g = m + 118 - (u + p);
			if (g > 0) {
				var v = e.browser,
					b = v.webkit,
					$ = e(b ? "body" : "html");
				$.animate({
					scrollTop: "+=" + g
				}, 400, function() {})
			}
			f.stop().clearQueue().animate({
				height: 118
			}, 200).queue(function() {
				e.Woo.resetCol(118, l, r), e.data(n[0], "comment", 1), f.find("textarea").focus()
			}), e.data(n[0], "comment", 0)
		}
		function n(t) {
			t.stopPropagation(), t.preventDefault();
			var n, i = e(t.target).closest("a"),
				s = i.closest(".woo"),
				l = s.find(".f").last(),
				r = s.find("li").not(".re-comt").last(),
				c = i.closest(".collbtn").data("favorite"),
				c = c ? c : l.data("favorite");
			if (!l.length && c && c.id) return void(window.location.href = "/people/mblog/" + c.id + "/detail/#reply");
			if (n = e.data(l[0], "comment"), 0 !== n) {
				var d = c.id,
					u = c.owner,
					p = e.data(e("#woo-holder")[0], "zindex") || 10,
					n = n == $ || 0 > n ? !1 : !0,
					f = s[0].className,
					h = e.G.getNum(f.match(/\bco\d+\b/gi).toString()),
					m = parseInt(s.css("top")) || 0;
				s.css("zIndex", ++p), e.data(e("#woo-holder")[0], "zindex", p), n ? a(i, d, u, l, s, h, m) : o(i, d, u, l, r, s, h, m)
			}
		}
		t = t || {};
		var i = "click";
		return e.G.getUSERID() ? e(document).on(i, this.selector, n) : (this.attr("title", "\u767b\u5f55\u624d\u80fd\u8bc4\u8bba\uff0c\u70b9\u51fb\u4e0b\u5c31\u53ef\u4ee5\u767b\u5f55\u5566"), e(document).on(i, this.selector, function(e) {
			e.stopPropagation(), e.preventDefault(), HUI.PopOut.login()
		})), this
	}, e.fn.SGlike = function(t) {
		function a(t, a) {
			t.hasClass("no-sub") || (t.addClass("no-sub"), e.ajax({
				url: "/like/",
				data: "object_id=" + a + "&category=1&" + getToken(1),
				mysuccess: function(a, o) {
					t.addClass("re-zan");
					var n = t.closest(".woo").find(".d2"),
						i = t.closest(".action").find("em"),
						s = n.html(),
						l = 0;
					t.data("like") ? t.data("like").likeid = a.data.like_id : t.data("like", {
						likeid: a.data.like_id + ""
					}), l = s ? parseInt(s) + 1 : 1, n.length && n.html(l).removeClass("dn") && (t.find("i").length ? t.find("i").html(l).addClass("z-done") : t.html(l).addClass("z-done")), i.length && i.each(function() {
						e(this).html(parseInt(i.html()) + 1)
					})
				},
				myerror: function() {
					this.errormsg()
				}
			}).always(function() {
				t.removeClass("no-sub")
			}))
		}
		function o(t, a) {
			t.hasClass("no-sub") || (t.data("like") && (a = t.data("like").likeid), a && (t.addClass("no-sub"), e.ajax({
				url: "/unlike/",
				data: "like_id=" + a + "&" + getToken(1),
				mysuccess: function(a, o) {
					t.removeClass("re-zan");
					var n = t.closest(".woo").find(".d2"),
						i = t.closest(".action").find("em"),
						s = n.html(),
						l = 0;
					l = parseInt(s) > 1 ? parseInt(s) - 1 : 0, n.length && (l ? n.html(l) : n.html(l).addClass("dn")) && (t.find("i").length ? t.find("i").html(l).removeClass("z-done") : t.html(l).removeClass("z-done")), i.length && i.each(function() {
						e(this).html(parseInt(i.html()) - 1 < 0 ? 0 : parseInt(i.html()) - 1)
					})
				},
				myerror: function() {
					this.errormsg()
				}
			}).always(function() {
				t.removeClass("no-sub")
			})))
		}
		function n(t) {
			t.stopPropagation(), t.preventDefault();
			var n = e(this),
				i = n.data("favorite"),
				i = i ? i : n.closest(".collbtn").data("favorite"),
				s = i.id,
				l = (i.owner, i.likeid);
			n.hasClass("re-zan") ? o(n, l) : a(n, s)
		}
		t = t || {};
		var i = "click";
		e.G.getUSERID() ? e(document).on(i, this.selector, n) : (this.attr("title") || this.attr("title", "\u767b\u5f55\u4e4b\u540e\u624d\u80fd\u8d5e\u54e6\uff01"), e(document).on(i, this.selector, function(e) {
			e.stopPropagation(), e.preventDefault(), HUI.PopOut.login()
		}))
	}, e.fn.updownSlider = function(temp, main, prevCell, nextCell, i) {
		var that = e(this),
			tempWrap = that.find(temp),
			mainCell = that.find(main),
			len = tempWrap.find("li").length,
			mainCellHeight = tempWrap.find("li").height() * parseInt(i),
			page= 1,
			i = i ? i : 3,
			page_count = Math.ceil(len/i);
		that.on('click', nextCell, function(event){
			var _this = e(event.target);
			if(!tempWrap.is(":animated")){
				if(page == page_count){
					tempWrap.animate({top:'0px'},"slow");
					page = 1;
				}else{
					tempWrap.animate({top:'-='+mainCellHeight},"slow");
					page++;
				}
			}
		});
		that.on('click', prevCell, function(event){
			var _this = e(event.target);
			if(!tempWrap.is(":animated")){
				if(page == 1){
					tempWrap.animate({top:'-='+ mainCellHeight*(page_count-1)},"slow");
					page =page_count;
				}else{
					tempWrap.animate({top:'+='+ mainCellHeight},"slow");
					page--;
				}
			}
		});
	}
}(jQuery), function(e) {
	e.fn.uploadpic = function(t, a, o) {
		function n() {
			var e = c;
			a ? c.delegate(a, "mousemove", l).delegate(a + " " + d.sel_input, "change", r) : (c.bind("mousemove", l).delegate(d.sel_input, "change", r), e = c.closest(d.sel_holder)), e.delegate("a.uploadpic-delthepic", "click", i), c.delegate(a + " " + d.sel_input, "click", s)
		}
		function i(t) {
			var a = e(this),
				o = a.closest(d.sel_holder),
				n = o.find(d.sel_uploading),
				i = o.find(d.sel_uploaded);
			o.find(d.sel_normal).removeClass("dn"), n.addClass("dn"), i.addClass("dn"), i.find("a.uploadpic-thepic").remove(), o.find("a.abtn-no").removeClass("abtn-no")
		}
		function s(t) {
			if (!e.G.getUSERID()) return t.preventDefault(), t.stopPropagation(), void HUI.PopOut.login();
			var a = e(this);
			a.closest("a").hasClass("abtn-no") && (t.preventDefault(), t.stopPropagation())
		}
		function l(t) {
			var a = e(this),
				o = a.offset();
			a.find(d.sel_input).css({
				left: t.pageX - o.left - 70,
				top: t.pageY - o.top - 10
			})
		}
		function r() {
			var t = e(this),
				a = t.closest(d.sel_form),
				o = e(getToken(3)),
				n = parseInt(1e10 * Math.random());
			"" !== t.val() && (d.onupload(), a.find("input[name=tid]").val(n).attr("uploadpic-tid", n).data("uploadpic-config", d).data("uploadpic-callback", function(t, a) {
				var o = t.data("uploadpic-config"),
					n = t.closest(o.sel_holder);
				if (a.success) {
					a.src = a.src.replace(/^http\:\/\/img4\.roscoe\.com/gi, "http://img4q.roscoe.com");
					var i = n.find(o.sel_input),
						s = i.val();
					n.find(o.sel_form).closest("a").addClass("abtn-no").end().find("a.uploadpic-thepic").remove();
					var l = n.find(o.sel_input).blur(),
						r = l.clone();
					l.replaceWith(r);
					var c = n.find(o.sel_uploaded).removeClass("dn").prepend('<a class="uploadpic-thepic graylk" href="' + a.src + '" target="_blank">' + s.substr(s.lastIndexOf("\\") + 1) + "</a>");
					c.find("a.uploadpic-delthepic").length || c.append('<a class="uploadpic-delthepic ml8 mr8" href="javascript:;">x\u5220\u9664</a>')
				} else n.find(o.sel_error).removeClass("dn").html(mergeServerMessage(a.message)).end().find("a.abtn-no").removeClass("abtn-no");
				n.find(o.sel_uploading).addClass("dn"), e.isFunction(o.fn) && o.fn(a, n, o)
			}).end().append(o).submit(), o.remove(), t.closest(d.sel_holder).find(d.sel_normal).addClass("dn").end().find(d.sel_error).addClass("dn").end().find(d.sel_uploaded).addClass("dn").end().find(d.sel_uploading).removeClass("dn"), a.closest("a").addClass("abtn-no"))
		}
		var c = this;
		if (!(c.length <= 0)) {
			"function" != typeof t && (o = a, a = t, t = e.noop), "string" != typeof a && (o = a, a = "");
			var d = e.extend({}, e.fn.uploadpic.defaults, o);
			return d.fn = t, n(), this
		}
	}, e.fn.uploadpic.defaults = {
		onupload: e.noop,
		sel_holder: "",
		sel_form: "",
		sel_input: "input[type=file]",
		sel_normal: "",
		sel_error: "",
		sel_uploading: "",
		sel_uploaded: ""
	}, e.fn.uploadpic.upPicCallBack = function(t) {
		if (e.isPlainObject(t)) {
			var a = e("input[uploadpic-tid=" + t.tid + "]");
			a.length && e.isFunction(a.data("uploadpic-callback")) && a.data("uploadpic-callback")(a, t)
		}
	}
}(jQuery);
(function(e) {
	e.fn.salesAreaPop = function(options) {
		e.fn.salesAreaPop.defaults = {
			'popObj': $('.J_salesArea'),
			'selectedArea': []
		};
		var a = "你妹",
			options = options || {},
			opt = $.extend({}, $.fn.salesAreaPop.defaults, options),
			provinceList = '',
			_cur = this,
			allArea = '',
			allAreaBox = '',
			citiesList = '',
			pop = opt.popObj,
			tipsMsg = ['\u8bf7\u9009\u62e9\u7701\u4efd',
				'\u9009\u62e9'],
			l = function(){
				var _this = e(this);
				_thisContainer = _this.closest('.j_cities'),
					_thisCitiesInner = _thisContainer.find('.j_citiesInner'),
					_thisCities = _thisContainer.find('.j_delCity'),
					newCity = $('<span data-name="'+ a +'" class="city j_delCity" title="'+ a +'">'+ a +'</span>');
				_thisCitiesInner.append(newCity);
			},
			createPop = function(){
				var _this = e(this);
				_thisContainer = _this.closest('.j_cities'),
					_thisCitiesInner = _thisContainer.find('.j_citiesInner'),
					_thisCities = _thisContainer.find('.j_delCity');
				if(!pop.length){
					provinceList = '<select name="province" class="province_list" id="J_province"><option value="'+ tipsMsg[0] +'" data-id="0" >'+ tipsMsg[0] +'</option>';
					province && $.each(province, function(j, p){
						provinceList += '<option value="'+ p.name +'" data-id="'+ p.id +'" >'+ p.letter +' '+ p.name +'</option>';
						allAreaBox += '<div class="hide" id="J_allArea'+ p.id +'">';
						allAreaBox += '<a href="javascript:;" id="j_city'+ p.id +'" data-pid="1" data-id="'+ p.id +'" data-name="'+ p.name +'" title="'+ tipsMsg[1] + p.name +'">'+ p.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
						p.city && $.each(p.city, function(k, c){
							allAreaBox += '<a href="javascript:;" id="j_city'+ c.id +'" data-pid="'+ p.id +'" data-id="'+ c.id +'" data-name="'+ c.name +'" title="' + tipsMsg[1] + c.name +'">'+ c.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
						});
						allAreaBox += '</div>';
					});
					provinceList += '</select>';
					pop = $('<div class="sales_area" id="J_salesArea"><dl class="selected_area clearfix"><dt class="G_f16">\u5df2\u9009\u533a\u57df：</dt><dd id="J_selectedArea">'+ citiesList  +'</dd></dl><dl class="all_area clearfix"><dt class="G_f16">\u5168\u56fd\u533a\u57df：</dt><dd class="all_area_province"><a href="javascript:;" title="\u9009\u62e9\u5168\u56fd">\u5168\u56fd<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a></dd>  </dl><dl class="all_area clearfix"><dt class="G_f16">\u9009\u62e9\u9500\u552e\u533a\u57df\u0028\u7701\u4efd\u0029：</dt><dd class="all_area_province">'+ provinceList +'<span class="G_f16"><b class="ftx08">\u53cb\u60c5\u63d0\u793a：</b>\u5982\u679c\u4f60\u9009\u62e9\u7701\u4efd\uff0c\u65e2\u5305\u62ec\u4e86\u8be5\u7701\u6240\u6709\u57ce\u5e02\uff01</span></dd><dd class="all_area_city" id="J_curAllArea">' + allAreaBox + '</dd></dl><div class="btn_box"><a href="javascript:;" class="G_btn_b btn_34px j_saveAreas">\u4fdd\u5b58</a></div></div>');
				}

				HUI.PopOut.alert(["\u8bf7\u9009\u62e9\u60a8\u7684\u9500\u552e\u533a\u57df", pop ,""], 3, !0, "30%"); //区域弹窗

				$('#J_salesArea').on('click', '.j_saveAreas',function(e){
					_cur.find('.j_citiesInner').find('.j_delCity').remove();
					var newCity = '',
						cityNameList = '',
						cityLvlList = '';
					$.each($('#J_selectedArea').find('a'), function(i, m){
						newCity += '<span data-lvl="'+ $(m).data('lvl') +'" data-name="'+ $(m).data('name') +'" class="city j_delCity" title="'+ $(m).data('name') +'">'+ $(m).data('name') +'</span>';
						cityNameList = $(m).data('name') + '|' + cityNameList;
						cityLvlList = $(m).data('lvl') + '|' + cityLvlList
					});
					newCity += '<input type="hidden" name="selas_area_name" value="'+ cityNameList +'">';
					newCity += '<input type="hidden" name="selas_area_level" value="'+ cityLvlList +'">';
					_cur.find('.j_citiesInner').prepend(newCity);
					$('#J_curAllArea a.yes_select').off("click");
					HUI.PopOut.closeMask();
				});
			};

		return this.each(function(){
			var $this = e(this);
			$this.click(createPop);
		});

	}
})(jQuery);

;(function(e) {
//关注
	e.fn.Attention = function(t) {
		function a(t, a) {
			t.hasClass("no-atten") || (t.addClass("no-atten"), e.ajax({
				url: "/index.php/Car/ajax_attention",
				data: "model_id=" + $('#J_carDetail').data('mid'),
				success: function(a, o) {
					if(a.status == 1){
						t.addClass("selected");
						t.find("span").html("\u5df2\u5173\u6ce8");
						souce = "<div class=\"pop_attention_suc\"><div class=\"ico G_f28 ftx02\"><i class=\"ui_icon\"><\/i>\u5173\u6ce8\u6210\u529f<\/div><div class=\"txt G_f16 ftx02\">\u60a8\u53ef\u4ee5\u901a\u8fc7<span class=\"ftx08\">\u4f1a\u5458\u4e2d\u5fc3<\/span> -&gt; <span class=\"ftx08\">\u6211\u7684\u5173\u6ce8<\/span> -&gt; \u7b2c\u4e00\u65f6\u95f4\u67e5\u770b\u8be5\u8f66\u76f8\u5173\u7684\u6700\u65b0\u52a8\u6001\u007e<\/div><\/div>";
						HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m");
						//HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m");
					}else if(a.status == -2){
						HUI.PopOut.login();
					}else if(a.status == 2){
						t.removeClass('selected');
						t.find('span').html("关注");
					}else if(a.status == 3){
						souce = '<div class="prompt prompt-suc"><h3><i class="ui_icon ui_icon_32 ui_icon_wb_suc_32"></i>'+ a.info +'</h3></div>';
						HUI && (HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m"), $({}).delay(2e3).queue(function() {
							HUI.PopOut.closeMask()
						}));
					}
					else if(a.status == -1){
						souce = '<div class="prompt prompt-fail"><h3><i class="ui_icon ui_icon_32 ui_icon_wb_err_32"></i>'+ a.info +'</h3></div>';
						HUI && (HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m"), $({}).delay(2e3).queue(function() {
							HUI.PopOut.closeMask()
						}));
					}

				},
				error: function() {
					this.errormsg()
				}
			}).always(function() {
				t.removeClass("no-atten")
			}))
		}
		function o(t, a) {
			t.hasClass("no-atten") || (t.data("like") && (a = t.data("like").likeid), a && (t.addClass("no-atten"), e.ajax({
				url: "/unlike/",
				data: "like_id=" + a,
				success: function(a, o) {
					t.removeClass("selected");
					t.find("span").html("\u70b9\u51fb\u5173\u6ce8");
					var n = t.closest(".woo").find(".d2"),
						s = n.html(),
						l = 0;
					l = parseInt(s) > 1 ? parseInt(s) - 1 : 0, n.length && (l ? n.html(l) : n.html(l).addClass("dn")) && (t.find("i").length ? t.find("i").html(l).removeClass("z-done") : t.html(l).removeClass("z-done"))
				},
				error: function() {
					this.errormsg()
				}
			}).always(function() {
				t.removeClass("no-atten")
			})))
		}
		function n(t) {
			t.stopPropagation(), t.preventDefault();
			var n = e(this),
				i = n.data("attention"),
				i = i ? i : n.closest(".J_collbtn").data("attention"),
				s = i.id,
				l = i.likeid;
			a(n, s) // n.hasClass("selected") ? o(n, l) : 
		}
		t = t || {};
		var i = "click";
		e.G.getUSERID() ? e(document).on(i, this.selector, n) : (this.attr("title") || this.attr("title", "\u767b\u5f55\u540e\u624d\u80fd\u5173\u6ce8\u54e6\u007e"), e(document).on(i, this.selector, function(e) {
			e.stopPropagation(), e.preventDefault(), HUI.PopOut.login()
		}))
	},
		/* 弹出窗城市插件 */
		e.fn.areaPop = function (options) {
			var options = options || {},
				opt = $.extend({}, $.fn.areaPop.defaults, options);
			$.fn.areaPop.defaults = {
				'popObj': $('#J_salesArea'),
				'selectedArea': []
			};
			var provinceList = '',
				_cur = this,
				allArea = '',
				allAreaBox = '',
				citiesList = '',
				pop = opt.popObj,
				tipsMsg = ['\u8bf7\u9009\u62e9\u7701\u4efd',
					'\u9009\u62e9'];

			function filterYesSelect(){
				$('#J_curAllArea').off('click').on('click','.yes_select', function(e){
					var _this = $(this),
						selectArea = $('#J_selectedArea'),
						selectAreaArr = selectArea.find('a'),
						pid = _this.data('pid'),
						mid = _this.data('id'),
						areaName = _this.data('name'),
						newArea = '';
					e.stopPropagation();
					e.preventDefault();
					if(pid == 1){
						_this.attr("class","selected");
						_this.siblings().attr("class","no_select");
						newArea = $('<a href="javascript:void(0);"  title="'+ areaName +'" data-name="'+ areaName +'" data-lvl="2" data-pid="'+ pid +'" data-id="'+ mid +'"><abbr>'+ areaName +'<abbr><i title="\u5220\u9664' + areaName + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');
						$.each(selectAreaArr, function(i, o){
							if($(o).data('pid') == mid){
								$(o).remove();
							}
						});
					}else{
						_this.attr("class","selected");
						newArea = $('<a href="javascript:void(0);"  title="'+ areaName +'" data-name="'+ areaName +'" data-lvl="3" data-pid="'+ pid +'" data-id="'+ mid +'"><abbr>'+ areaName +'<abbr><i title="\u5220\u9664' + areaName + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');
					}
					selectArea.prepend(newArea);
				});
			}
			provinceList = '<select name="province" class="province_list" id="J_province"><option value="'+ tipsMsg[0] +'" data-id="0" >'+ tipsMsg[0] +'</option>';
			province && $.each(province, function(j, p){
				provinceList += '<option value="'+ p.name +'" data-id="'+ p.id +'" >'+ p.letter +' '+ p.name +'</option>';
				allAreaBox += '<div class="hide" id="J_allArea'+ p.id +'">';
				allAreaBox += '<a href="javascript:;" id="j_city'+ p.id +'" data-pid="1" data-id="'+ p.id +'" data-name="'+ p.name +'" title="'+ tipsMsg[1] + p.name +'">'+ p.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
				p.city && $.each(p.city, function(k, c){
					allAreaBox += '<a href="javascript:;" id="j_city'+ c.id +'" data-pid="'+ p.id +'" data-id="'+ c.id +'" data-name="'+ c.name +'" title="' + tipsMsg[1] + c.name +'">'+ c.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
				});
				allAreaBox += '</div>';
			});
			provinceList += '</select>';

			pop.length || (pop = $('<div class="sales_area" id="J_salesArea"><dl class="selected_area clearfix"><dt class="G_f16">\u5df2\u9009\u533a\u57df：</dt><dd id="J_selectedArea">'+ citiesList  +'</dd></dl><dl class="all_area clearfix"><dt class="G_f16">\u5168\u56fd\u533a\u57df：</dt><dd class="all_area_province"><a href="javascript:;" title="\u9009\u62e9\u5168\u56fd">\u5168\u56fd<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a></dd>  </dl><dl class="all_area clearfix"><dt class="G_f16">\u9009\u62e9\u9500\u552e\u533a\u57df\u0028\u7701\u4efd\u0029：</dt><dd class="all_area_province">'+ provinceList +'<span class="G_f16"><b class="ftx08">\u53cb\u60c5\u63d0\u793a：</b>\u5982\u679c\u4f60\u9009\u62e9\u7701\u4efd\uff0c\u65e2\u5305\u62ec\u4e86\u8be5\u7701\u6240\u6709\u57ce\u5e02\uff01</span></dd><dd class="all_area_city" id="J_curAllArea">' + allAreaBox + '</dd></dl><div class="btn_box"><a href="javascript:;" class="G_btn_b btn_34px j_saveAreas">\u4fdd\u5b58</a></div></div>'));

			HUI.PopOut.alert(["\u8bf7\u9009\u62e9\u60a8\u7684\u9500\u552e\u533a\u57df", pop ,""], 3, !0, "30%"); //区域弹窗
			$('.all_area_province a').attr("class","yes_select");
			$('#J_curAllArea a').attr("class","yes_select");

			opt['selectedArea'].length && $.each(opt['selectedArea'], function(i, s){
				if($.trim(s) == "全国"){
					$('#J_curAllArea').find('a').attr("class","no_select");
					$('.all_area_province a').attr("class","selected");
					citiesList = '<a href="javascript:void(0);" title="\u5168\u56fd" data-name="\u5168\u56fd" data-pid="0" data-lvl="1" data-id="1"><abbr>\u5168\u56fd</abbr><i title="\u5220\u9664\u5168\u56fd" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';
					return false;
				}
				province && $.each(province, function(j, p){
					if($.trim(s) === $.trim(p.name)){
						citiesList += '<a href="javascript:void(0);" data-lvl="2" title="'+ p.name +'" data-name="'+ p.name +'" data-pid="1" data-id="'+ p.id +'"><abbr>'+ p.name +'</abbr><i title="\u5220\u9664' + p.name + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';
						J_allArea = '#j_city' + p.id;
						$(J_allArea).attr("class","selected");
						$(J_allArea).siblings().attr("class","no_select");

					}else{
						p.city && $.each(p.city, function(k, c){
							if(c.name === s){
								J_allArea = '#j_city' + c.id;
								$(J_allArea).attr("class","selected");
								citiesList += '<a href="javascript:void(0);"  data-lvl="3" title="'+ p.name +'" data-name="'+ c.name +'" data-pid="'+ p.id +'" data-id="'+ c.id +'"><abbr>'+ c.name +'</abbr><i title="\u5220\u9664' + c.name + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';
							}
						});
					}});
			});
			$('#J_selectedArea').html(citiesList);
			$("#J_province").change(function(e){
				var _this = $(this),
					curOpt = _this.find("option:selected"),
					curID = $.trim(curOpt.data('id')),
					curName = $.trim(curOpt.val());
				changeCurAllArea = '#J_allArea' + curID;
				$(changeCurAllArea).siblings().hide();
				$(changeCurAllArea).show();

			});
			filterYesSelect();
			$('.all_area_province').on('click','a', function(e){
				var _this = $(this),
					selectArea = $('#J_selectedArea'),
					selectAreaArr = selectArea.find('a');
				_this.attr("class","selected");
				e.stopPropagation();
				e.preventDefault();
				$.each(selectAreaArr, function(i, o){
					$(o).remove();
				});

				newArea = $('<a href="javascript:void(0);" title="\u5168\u56fd" data-name="\u5168\u56fd" data-lvl="1" data-id="1">\u5168\u56fd<i title="\u5220\u9664\u5168\u56fd" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');
				selectArea.prepend(newArea);

				$('#J_curAllArea').find('a').attr("class","no_select");
				filterYesSelect();
			});
			$('#J_selectedArea').on('click', 'a', function(e){
				var _this = $(this),
					allArea = $('#J_curAllArea'),
					allAreaObjs = allArea.find('a'),
					_thisId = _this.data('id'),
					_thisPid = _this.data('pid'),
					_thisAllArea = '#J_allArea' + _thisId,
					_thisArea = '#j_city' + _thisId;
				e.preventDefault();
				_this.remove();
				if(_thisId == 1){
					$('.all_area_province a').attr("class","yes_select");
					allAreaObjs.attr("class","yes_select");
				}else if(_thisPid == 1){
					$(_thisAllArea).find('a').attr("class","yes_select");
				}else{
					$(_thisArea).attr("class","yes_select");
				}
				filterYesSelect();
			});

			$('#J_salesArea').on('click', '.j_saveAreas',function(e){

				_cur.find('.j_citiesInner').find('.j_delCity').remove();
				var newCity = '',
					cityNameList = '',
					cityLvlList = '';
				$.each($('#J_selectedArea').find('a'), function(i, m){
					newCity += '<span data-lvl="'+ $(m).data('lvl') +'" data-name="'+ $(m).data('name') +'" class="city j_delCity" title="'+ $(m).data('name') +'">'+ $(m).data('name') +'</span>';
					cityNameList = $(m).data('name') + '|' + cityNameList;
					cityLvlList = $(m).data('lvl') + '|' + cityLvlList
				});
				$('input[name="selas_area_name"]').length && $('input[name="selas_area_name"]').val(cityNameList);
				$('input[name="selas_area_level"]').length && $('input[name="selas_area_level"]').val(cityLvlList);
				_cur.find('.j_citiesInner').prepend(newCity);
				$('#J_curAllArea a.yes_select').off("click");
				HUI.PopOut.closeMask();
			});

		}
})(jQuery);

/* 懒加载插件 */
$.fn.datalazyload = function(){
	var check_list = [];
	window.check_list = check_list;
	var checkall_enabled = false;
	var each = tool.each;
	var every = tool.every;
	var debounce = tool.event.debounce;
	var optional_callback = function(o) {
		var $el = o.$el;
		var el = o.el;
		each(o.callbacks, function(param) {
			var callback = param.callback;
			var filter = param.filter ||
				function() {
					return true;
				};
			filter.call(el, $el, el) && callback && callback.call(el, $el, el);
		});
	};
	var succeed_callback = function(o) {
		var $el = o.$el;
		var type = $el.data('lazyload-type');
		var from = $el.data('lazyload-from');
		var src = $el.data('lazyload-src');
		if (type === 'data') {
			if (from === 'textarea') {
				var $textarea = $el.find('textarea[data-lazyload-textarea]');
				if ($textarea.length > 0) {
					$el.html($textarea.text());
				}
			} else if (from === 'ajax') {}
		} else if (type === 'img' && src && src !== $el.attr('src')) {
			$el.attr('src', src);
		}
		$el.removeClass('loading');
		optional_callback(o);
	};
	var check_single = function(o) {
		var $el = o.$el;
		var el = o.el;
		var threshold = o.threshold;
		var rect = el.getBoundingClientRect();
		var win_height = $(window).height();
		if ($el.is(':visible') && (rect.top - threshold <= win_height) && (rect.bottom + threshold >= 0)) {
			succeed_callback(o);
			return true;
		}
	};
	var checkall = debounce(function() {
		var i = 0,
			o;
		if (check_list.length === 0) {
			disable_checkall();
			return;
		}
		while (o = check_list[i]) {
			if (check_single(o)) {
				check_list.splice(i, 1);
				i = 0;
			} else {
				i += 1;
			}
		}
	});
	var check = function(callback, filter) {
		var threshold = $(this).data('lazyload-threshold') || 0;
		var param = {
			callback: callback,
			filter: filter
		};
		var el = this;
		var o = check_list.find(function(o) {
			return el === o.el;
		});
		if (o) {
			o.callbacks.push(param);
		} else {
			o = {
				$el: $(this),
				el: this,
				callbacks: [param],
				threshold: threshold
			};
			if (!check_single(o)) {
				check_list.push(o);
				enable_checkall();
			}
		}
	};
	var enable_checkall = function() {
		if (!checkall_enabled) {
			checkall_enabled = true;
			$(window).on('resize', checkall);
			$(window).on('scroll', checkall);
		}
	};
	var disable_checkall = function() {
		if (checkall_enabled) {
			checkall_enabled = false;
			$(window).off('resize', checkall);
			$(window).off('scroll', checkall);
		}
	};
	check_list.find = function(compare) {
		var ret = null;
		every(check_list, function(o) {
			if (compare(o)) {
				ret = o;
				return false;
			}
			return true;
		});
		return ret;
	};
	var args = arguments;
	return $(this).each(function() {
		check.apply(this, args);
	});
};

(function ($) {
	'use strict';

	var escape = /["\\\x00-\x1f\x7f-\x9f]/g,
		meta = {
			'\b': '\\b',
			'\t': '\\t',
			'\n': '\\n',
			'\f': '\\f',
			'\r': '\\r',
			'"': '\\"',
			'\\': '\\\\'
		},
		hasOwn = Object.prototype.hasOwnProperty;

	/**
	 * jQuery.toJSON
	 * Converts the given argument into a JSON representation.
	 *
	 * @param o {Mixed} The json-serializable *thing* to be converted
	 *
	 * If an object has a toJSON prototype, that will be used to get the representation.
	 * Non-integer/string keys are skipped in the object, as are keys that point to a
	 * function.
	 *
	 */
	$.toJSON = typeof JSON === 'object' && JSON.stringify ? JSON.stringify : function (o) {
		if (o === null) {
			return 'null';
		}

		var pairs, k, name, val,
			type = $.type(o);

		if (type === 'undefined') {
			return undefined;
		}

		// Also covers instantiated Number and Boolean objects,
		// which are typeof 'object' but thanks to $.type, we
		// catch them here. I don't know whether it is right
		// or wrong that instantiated primitives are not
		// exported to JSON as an {"object":..}.
		// We choose this path because that's what the browsers did.
		if (type === 'number' || type === 'boolean') {
			return String(o);
		}
		if (type === 'string') {
			return $.quoteString(o);
		}
		if (typeof o.toJSON === 'function') {
			return $.toJSON(o.toJSON());
		}
		if (type === 'date') {
			var month = o.getUTCMonth() + 1,
				day = o.getUTCDate(),
				year = o.getUTCFullYear(),
				hours = o.getUTCHours(),
				minutes = o.getUTCMinutes(),
				seconds = o.getUTCSeconds(),
				milli = o.getUTCMilliseconds();

			if (month < 10) {
				month = '0' + month;
			}
			if (day < 10) {
				day = '0' + day;
			}
			if (hours < 10) {
				hours = '0' + hours;
			}
			if (minutes < 10) {
				minutes = '0' + minutes;
			}
			if (seconds < 10) {
				seconds = '0' + seconds;
			}
			if (milli < 100) {
				milli = '0' + milli;
			}
			if (milli < 10) {
				milli = '0' + milli;
			}
			return '"' + year + '-' + month + '-' + day + 'T' +
				hours + ':' + minutes + ':' + seconds +
				'.' + milli + 'Z"';
		}

		pairs = [];

		if ($.isArray(o)) {
			for (k = 0; k < o.length; k++) {
				pairs.push($.toJSON(o[k]) || 'null');
			}
			return '[' + pairs.join(',') + ']';
		}

		// Any other object (plain object, RegExp, ..)
		// Need to do typeof instead of $.type, because we also
		// want to catch non-plain objects.
		if (typeof o === 'object') {
			for (k in o) {
				// Only include own properties,
				// Filter out inherited prototypes
				if (hasOwn.call(o, k)) {
					// Keys must be numerical or string. Skip others
					type = typeof k;
					if (type === 'number') {
						name = '"' + k + '"';
					} else if (type === 'string') {
						name = $.quoteString(k);
					} else {
						continue;
					}
					type = typeof o[k];

					// Invalid values like these return undefined
					// from toJSON, however those object members
					// shouldn't be included in the JSON string at all.
					if (type !== 'function' && type !== 'undefined') {
						val = $.toJSON(o[k]);
						pairs.push(name + ':' + val);
					}
				}
			}
			return '{' + pairs.join(',') + '}';
		}
	};

	/**
	 * jQuery.evalJSON
	 * Evaluates a given json string.
	 *
	 * @param str {String}
	 */
	$.evalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) {
		/*jshint evil: true */
		return eval('(' + str + ')');
	};

	/**
	 * jQuery.secureEvalJSON
	 * Evals JSON in a way that is *more* secure.
	 *
	 * @param str {String}
	 */
	$.secureEvalJSON = typeof JSON === 'object' && JSON.parse ? JSON.parse : function (str) {
		var filtered =
			str
				.replace(/\\["\\\/bfnrtu]/g, '@')
				.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
				.replace(/(?:^|:|,)(?:\s*\[)+/g, '');

		if (/^[\],:{}\s]*$/.test(filtered)) {
			/*jshint evil: true */
			return eval('(' + str + ')');
		}
		throw new SyntaxError('Error parsing JSON, source is not valid.');
	};

	/**
	 * jQuery.quoteString
	 * Returns a string-repr of a string, escaping quotes intelligently.
	 * Mostly a support function for toJSON.
	 * Examples:
	 * >>> jQuery.quoteString('apple')
	 * "apple"
	 *
	 * >>> jQuery.quoteString('"Where are we going?", she asked.')
	 * "\"Where are we going?\", she asked."
	 */
	$.quoteString = function (str) {
		if (str.match(escape)) {
			return '"' + str.replace(escape, function (a) {
					var c = meta[a];
					if (typeof c === 'string') {
						return c;
					}
					c = a.charCodeAt();
					return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
				}) + '"';
		}
		return '"' + str + '"';
	};

}(jQuery));

/**
 * slider插件可悬停控制
 */
; $(function ($, window, document, undefined) {

	Slider = function (container, options) {
		/*
		 options = {
		 auto: true,
		 time: 3000,
		 event: 'hover' | 'click',
		 mode: 'slide | fade',
		 controller: $(),
		 activeControllerCls: 'className',
		 exchangeEnd: $.noop
		 }
		 */

		"use strict"; //stirct mode not support by IE9-

		if (!container) return;

		var options = options || {},
			currentIndex = 0,
			cls = options.activeControllerCls,
			delay = options.delay,
			isAuto = options.auto,
			controller = options.controller,
			event = options.event,
			interval,
			slidesWrapper = container.children().first(),
			slides = slidesWrapper.children(),
			length = slides.length,
			childWidth = container.width(),
			totalWidth = childWidth * slides.length;

		function init() {
			var controlItem = controller.children();

			mode();

			event == 'hover' ? controlItem.mouseover(function () {
				stop();
				var index = $(this).index();

				play(index, options.mode);
			}).mouseout(function () {
				isAuto && autoPlay();
			}) : controlItem.click(function () {
				stop();
				var index = $(this).index();

				play(index, options.mode);
				isAuto && autoPlay();
			});

			isAuto && autoPlay();
		}

		//animate mode
		function mode() {
			var wrapper = container.children().first();

			options.mode == 'slide' ? wrapper.width(totalWidth) : wrapper.children().css({
				'position': 'absolute',
				'left': 0,
				'top': 0
			})
				.first().siblings().hide();
		}

		//auto play
		function autoPlay() {
			interval = setInterval(function () {
				triggerPlay(currentIndex);
			}, options.time);
		}

		//trigger play
		function triggerPlay(cIndex) {
			var index;

			(cIndex == length - 1) ? index = 0 : index = cIndex + 1;
			play(index, options.mode);
		}

		//play
		function play(index, mode) {
			slidesWrapper.stop(true, true);
			slides.stop(true, true);

			mode == 'slide' ? (function () {
				if (index > currentIndex) {
					slidesWrapper.animate({
						left: '-=' + Math.abs(index - currentIndex) * childWidth + 'px'
					}, delay);
				} else if (index < currentIndex) {
					slidesWrapper.animate({
						left: '+=' + Math.abs(index - currentIndex) * childWidth + 'px'
					}, delay);
				} else {
					return;
				}
			})() : (function () {
				if (slidesWrapper.children(':visible').index() == index) return;
				slidesWrapper.children().fadeOut(delay).eq(index).fadeIn(delay);
			})();

			try {
				controller.children('.' + cls).removeClass(cls);
				controller.children().eq(index).addClass(cls);
			} catch (e) { }

			currentIndex = index;

			options.exchangeEnd && typeof options.exchangeEnd == 'function' && options.exchangeEnd.call(this, currentIndex);
		}

		//stop
		function stop() {
			clearInterval(interval);
		}

		//prev frame
		function prev() {
			stop();

			currentIndex == 0 ? triggerPlay(length - 2) : triggerPlay(currentIndex - 2);

			isAuto && autoPlay();
		}

		//next frame
		function next() {
			stop();

			currentIndex == length - 1 ? triggerPlay(-1) : triggerPlay(currentIndex);

			isAuto && autoPlay();
		}

		//init
		init();

		//expose the Slider API
		return {
			prev: function () {
				prev();
			},
			next: function () {
				next();
			}
		}
	};

}(jQuery, window, document));

jQuery.browser = {}; (function () { jQuery.browser.msie = false; jQuery.browser.version = 0; if (navigator.userAgent.match(/MSIE ([0-9]+)./)) { jQuery.browser.msie = true; jQuery.browser.version = RegExp.$1; } })();
(function ($) {
	$.fn.extend({
		"changeTips": function (value) {
			value = $.extend({
				divTip: ""
			}, value)

			var $this = $(this);
			var indexLi = 0;

			//点击document隐藏下拉层
			$(document).click(function (event) {
				if ($(event.target).attr("class") == value.divTip || $(event.target).is("li")) {
					var liVal = $(event.target).text();
					$this.val(liVal);
					blus();
				} else {
					blus();
				}
			})

			//隐藏下拉层
			function blus() {
				$(value.divTip).hide();
			}

			//键盘上下执行的函数
			function keychang(up) {
				if (up == "up") {
					if (indexLi == 1) {
						indexLi = $(value.divTip).children().length - 1;
					} else {
						indexLi--;
					}
				} else {
					if (indexLi == $(value.divTip).children().length - 1) {
						indexLi = 1;
					} else {
						indexLi++;
					}
				}
				$(value.divTip).children().eq(indexLi).addClass("active").siblings().removeClass();
			}

			//值发生改变时
			function valChange() {
				var tex = $this.val();//输入框的值
				var fronts = "";//存放含有“@”之前的字符串
				var af = /@/;
				var regMail = new RegExp(tex.substring(tex.indexOf("@")));//有“@”之后的字符串,注意正则字面量方法，是不能用变量的。所以这里用的是new方式。


				//让提示层显示，并对里面的LI遍历
				if ($this.val() == "") {
					blus();
				} else {

					$(value.divTip).show().
						children().
						each(function (index) {
							var valAttr = $(this).attr("email");
							if (index == 1) { $(this).text(tex).addClass("active").siblings().removeClass(); }
							//索引值大于1的LI元素进处处理
							if (index > 1) {
								//当输入的值有“@”的时候
								if (af.test(tex)) {
									//如果含有“@”就截取输入框这个符号之前的字符串
									fronts = tex.substring(tex.indexOf("@"), 0);
									$(this).text(fronts + valAttr);
									//判断输入的值“@”之后的值，是否含有和LI的email属性
									if (regMail.test($(this).attr("email"))) {
										$(this).show();
									} else {
										if (index > 1) {
											$(this).hide();
										}
									}

								}
								//当输入的值没有“@”的时候
								else {
									$(this).text(tex + valAttr);
								}
							}
						})
				}
			}


			//输入框值发生改变的时候执行函数，这里的事件用判断处理浏览器兼容性;
			if ($.browser.msie) {
				$(this).bind("propertychange", function () {
					valChange();
				})
			} else {
				$(this).bind("input", function () {
					valChange();
				})
			}


			//鼠标点击和悬停LI
			$(value.divTip).children().
				hover(function () {
					indexLi = $(this).index();//获取当前鼠标悬停时的LI索引值;
					if ($(this).index() != 0) {
						$(this).addClass("active").siblings().removeClass();
					}
				})


			//按键盘的上下移动LI的背景色
			$this.keydown(function (event) {
				if (event.which == 38) {//向上
					keychang("up")
				} else if (event.which == 40) {//向下
					keychang()
				} else if (event.which == 13) { //回车
					var liVal = $(value.divTip).children().eq(indexLi).text();
					$this.val(liVal);
					blus();
				}
			})
		}
	})
})(jQuery);
(function(e){
	//ajax提交评论
	e.fn.setAjaxComment = function(options){
		e.fn.setAjaxComment.defaults = {
			holder: 'li',
			inputer: '.j_inpArea',
			curNumBox: '.curWordsNum',
			allNumBox: '.allWordsNum',
			numBox: '.j_inpWordLmt',
			submitBtner: '.j_saveBtn',
			limit: 600,
			cur: 'active'
		};
		var options = options || {},
			opt = e.extend({}, e.fn.setAjaxComment.defaults, options),
			limitFlag,
			curNumber,
			Qid,
			firstFocusFlag = !1,
			replyTips = {theDefault:"精准的的描述有利于我们更好地回复问题，切勿提交违法，违禁词汇，广告以及其他与购车无关内容，否则购车惠将依法追究相关法律责任"},
			souce = '<div class="prompt prompt-fail"><h3><i class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i>\u5185\u5bb9\u4e0d\u80fd\u4e3a\u7a7a\uff01\u007e</h3></div>',
			gbThis = this;
		function init(){
			var checkeder = opt.holder + '.' + opt.cur;
			$(opt.allNumBox).text(opt.limit);
			Qid = gbThis.find(checkeder).data('id');
			curNumber = 0;
			gbThis.find(opt.allNumBox).text(opt.limit);
			gbThis.find(opt.curNumBox).text(curNumber);
			limitFlag = !0;

			$(opt.inputer).css({"fontSize":"14px", "color": "#BEBEBE"});
			$(opt.inputer).val(replyTips.theDefault);
		}
		init();
		gbThis.on('click', opt.holder, function(){
			$(this).addClass(opt.cur).siblings().removeClass(opt.cur);
			Qid = $(this).data('id');
		});
		gbThis.on('focus', opt.inputer, function(event){
			var __this = $(event.currentTarget);
			if(__this.val() == replyTips.theDefault){
				__this.removeAttr("style");
				__this.val('');
			}
		});
		gbThis.on('blur', opt.inputer, function(event){
			var __this = $(event.currentTarget);
			if(__this.val() == ''){
				init();
			}
		});
		gbThis.on('keyup', opt.inputer, function(){
			curNumber = $.trim($(opt.inputer).val()).length;
			$(opt.curNumBox).text(curNumber);
			if(curNumber > opt.limit){
				$(opt.numBox).addClass('inpWordLmt_Wrong');
				souce = '<div class="prompt prompt-fail"><h3><i class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i>\u5185\u5bb9\u8d85\u51fa\u4e86\u5b57\u6570\u9650\u5236\uff01\u007e</h3></div>';
				limitFlag = !0;
			}else if(curNumber == 0){
				$(opt.numBox).removeClass('inpWordLmt_Wrong');
				souce = '<div class="prompt prompt-fail"><h3><i class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i>\u5185\u5bb9\u4e0d\u80fd\u4e3a\u7a7a\uff01\u007e</h3></div>';
				limitFlag = !0;
			}
			else{
				$(opt.numBox).removeClass('inpWordLmt_Wrong');
				limitFlag = !1;
			}
		});
		gbThis.on('click', opt.submitBtner, function(event){
			if(!e.G.getUSERID()){
				$(this).attr("title") || $(this).attr("title", "\u767b\u5f55\u540e\u624d\u80fd\u8bc4\u8bba\u54e6\u007e");
				event.stopPropagation();
				event.preventDefault();
				HUI.PopOut.login();
				return false;
			}
			var db = {};
			db.brand_id = gbThis.data('brandid');
			db.model_id = gbThis.data('modelid');
			db.id = Qid;
			db.bd = filterXSS($.trim(gbThis.find(opt.inputer).val()));
			if(limitFlag){
				HUI && (HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m"), $({}).delay(2e3).queue(function() {
					HUI.PopOut.closeMask()
				}));
				return false;
			}
			$.ajax({
				url: qianzui+"/Car/ajax_set_comment",
				data: db,
				success: function(a) {
					var souce = '<div class="prompt prompt-suc"><h3><i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i>\u8bc4\u8bba\u63d0\u4ea4\u6210\u529f\uff01\u007e</h3></div>';
					if(a.status == 1){
						HUI && (HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m"), $({}).delay(2e3).queue(function() {
							HUI.PopOut.closeMask()
						}));
						init();
					}
				},
				error: function() {
					this.errormsg()
				}
			});
		});

	},
		//获取评论内容生成列表
		e.fn.getAjaxComment = function(options){
			e.fn.getAjaxComment.defaults = {
				filter: '.j_commentFilter',
				container: '.j_commentCon',
				pageer: '.j_commentPager',
				tpl: 'J_commentConTpl',
				pageSize: 1,
				maxPage: 3
			};
			var options = options || {},
				opt = e.extend({}, e.fn.getAjaxComment.defaults, options),
				_this = this,
				$commentContainer = _this.find(opt.container),
				defaultCommentId = 0;
			//测试数据
			//var data = {
			//	id: 1,
			//	type: "商品咨询",
			//	list: [
			//		{'commentFaceUrl': "http://shop.gouchehui.com.cn/data/avatar/1451019398419551021.jpg",
			//			'commentName': "yu",
			//			'commentBd':"你还爱我吗？",
			//			'commentDate':"2015-05-13 09:22:28",
			//			'replyName':"tingting",
			//			'replyBd': "对不起！我已不爱你了！",
			//			'replyDate': "2015-05-16 09:22:28"
			//		},
			//		{'commentFaceUrl': "http://shop.gouchehui.com.cn/data/avatar/1451019398419551021.jpg",
			//			'commentName': "yu",
			//			'commentBd':"为什么你不爱我了！",
			//			'commentDate':"2015-05-13 09:22:28",
			//			'replyName':"tingting",
			//			'replyBd': "我对你的心死了，我已经习惯了没有你的生活！",
			//			'replyDate': "2015-05-16 09:22:28"
			//		}]
			//};
			//var html = template(opt.tpl, data);
			//$(opt.container).html(html);
			//初始化评论数据，默认是全部数据
			function init(id){
				var db = {},
					$commentLoader = $commentContainer.find('.commentLoading');
				db.commId = id;
				db.brand_id = $('#J_detailQuestion').data('brandid');
				db.model_id = $('#J_detailQuestion').data('modelid');

				if($commentLoader.length){
					$commentLoader.css({
						'display': 'table'
					});
				}else{
					$commentLoader = $('<div class="commentLoading"><div class="commentLoadingCon"><i><img src="images/common/5-121204193R7.gif" alt=""></i>\u6570\u636e\u52a0\u8f7d\u4e2d...</div></div>');
					$commentLoader.css({
						'width': $commentContainer.width(),
						'height': $commentContainer.height()
					});
					$commentContainer.append($commentLoader);
				}
				$.ajax({
					url: qianzui+"/Car/ajax_set_comment_type",
					data: db,
					success: function(a) {
						if(a.status == 1){
							var html = template(opt.tpl, a.data);
							$(opt.container).html(html);
						}else{
							$(opt.filter).hide();
							$(opt.container).html("<div class=\"null_list\">暂无问题咨询！</div>");
						}
					},
					error: function() {
						this.errormsg()
					}
				}).always(function(){$commentLoader.css({'display': 'none'})});
			}
			init(defaultCommentId);
			_this.on('change', opt.filter, function(){
				var db = {},
					$commentLoader = $commentContainer.find('.commentLoading'),
					commentId = $(this).find("option:selected").val();
				db.commId = commentId;
				db.brand_id = $('#J_detailQuestion').data('brandid');
				db.model_id = $('#J_detailQuestion').data('modelid');
				if(commentId == defaultCommentId){
					return false;
				}
				if($commentLoader.length){
					$commentLoader.css({
						'display': 'table'
					});
				}else{
					$commentLoader = $('<div class="commentLoading"><div class="commentLoadingCon"><i><img src="images/common/5-121204193R7.gif" alt=""></i>\u6570\u636e\u52a0\u8f7d\u4e2d...</div></div>');
					$commentLoader.css({
						'width': $commentContainer.width(),
						'height': $commentContainer.height()
					});
					$commentContainer.append($commentLoader);
				}
				$.ajax({
					url: qianzui+"/Car/ajax_set_comment_type",
					data: db,
					success: function(a) {
						if(a.status == 1){
							defaultCommentId = commentId;
							var html = template(opt.tpl, a.data);
							$(opt.container).html(html);
						}else{
							_this.find(opt.filter).val(defaultCommentId);
							var souce = '<div class="prompt prompt-suc"><h3><i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i>'+ a.info +'</h3></div>';
							HUI && (HUI.PopOut.alert(["\u63d0\u793a", souce, ""], "m"), $({}).delay(2e3).queue(function() {
								HUI.PopOut.closeMask()
							}));
						}
					},
					error: function() {
						this.errormsg()
					}
				}).always(function(){$commentLoader.css({'display': 'none'})});
			});


		},
		//滚动定位插件
		e.fn.scrollPos = function(options){
			e.fn.scrollPos.defaults = {
				holder: '.j_switchNav_tab',
				trigger: 'li',
				container: '.detail-box',
				item: 'div.detail-info-item',
				cur: 'active'
			};
			//noinspection JSDuplicatedDeclaration
			var options = options || {},
				opt = e.extend({}, e.fn.scrollPos.defaults, options),
				_this = this,
				status,
				_thisT = _this.offset().top;

			_this.find(opt.holder).find(opt.trigger).click(function() {
				$(this).stop(true);
				var _index = $(this).index();
				var _top = $(opt.container).find(opt.item).eq(_index).offset().top - 40;
				$('html, body').animate({
					scrollTop: _top
				}, 500);
				return false;
			});

			if ($.browser.msie && $.browser.version == "6.0") {
				return false;
			} else {
				$(window).scroll(tool.event.debounce(function() {
					var _rect = $(opt.container)[0].getBoundingClientRect();
					var _items;
					if (_rect.top > 50) {
						status = undefined;
						$(opt.holder).css({
							"position": "static"
						})
					} else {
						_items = $(opt.container).find(opt.item);
						$.each(_items, function(i, item) {
							if (status === i) return;
							if (item.getBoundingClientRect().top < 50) {
								if (!_items[i + 1] || _items[i + 1].getBoundingClientRect().top > 50) {
									$(opt.holder).find(opt.trigger).siblings().removeClass(opt.cur);
									$(opt.holder).find(opt.trigger).eq(i).addClass(opt.cur);
									var s_top = $(window).scrollTop();
									autopos($(opt.holder));
									status = i;
									return false;
								}
							}
						});
					}
				}));
			}
			function autopos(p_h) {
				if ($.browser.msie && $.browser.version == "6.0") {
					return false;
				} else {
					p_h.css({
						"position": "fixed",
						"top": 0
					})
				}
			}

		}
})(jQuery);
/* 倒计时 */
!function($){
	$.fn.countDown = function timer(intDiff, obj){
		window.setInterval(function(){
			var day=0,
				hour=0,
				minute=0,
				second=0;//时间默认值
			if(intDiff > 0){
				day = Math.floor(intDiff / (60 * 60 * 24));
				hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
				minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
				second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
			}
			if (minute <= 9) minute = '0' + minute;
			if (second <= 9) second = '0' + second;
			obj.html('<s>'+ day +'</s>天' + '<s id="h">'+hour+'</s>时' + '<s>'+minute+'</s>分' + '<s>'+second+'</s>秒');
			intDiff --;
		}, 1000);
	}
}(jQuery);

(function(){
	var $container = $('#J_SaleLists').find('li'),
		$countDown,
		dbStartDate,
		dbEndDate,
		startDateStr,
		endDateStr;
	$.each($container, function(i, saleLi){
		$countDown = $(saleLi).find('.j_countDownTime');
		dbStartDate = $(saleLi).find('.j_startTime').text();
		dbEndDate = $(saleLi).find('.j_endTime').text();
		startDateStr = dbStartDate.replace(/-/g,"/");
		endDateStr = dbEndDate.replace(/-/g,"/");
		var startDateObj = new Date(),
			endDateObj = new Date(endDateStr),
			millisecondDifference =  endDateObj.getTime() - startDateObj.getTime(),
			secondDifference = millisecondDifference / 1000;
		$.fn.countDown(secondDifference, $countDown);
	});

}());

!function(a){a.fn.slide=function(b){return a.fn.slide.defaults={type:"slide",effect:"fade",autoPlay:!1,delayTime:500,interTime:2500,triggerTime:150,defaultIndex:0,titCell:".hd li",mainCell:".bd",targetCell:null,trigger:"mouseover",scroll:1,vis:1,titOnClassName:"on",autoPage:!1,prevCell:".prev",nextCell:".next",pageStateCell:".pageState",opp:!1,pnLoop:!0,easing:"swing",startFun:null,endFun:null,switchLoad:null,playStateCell:".playState",mouseOverStop:!0,defaultPlay:!0,returnDefault:!1},this.each(function(){var c=a.extend({},a.fn.slide.defaults,b),d=a(this),e=c.effect,f=a(c.prevCell,d),g=a(c.nextCell,d),h=a(c.pageStateCell,d),i=a(c.playStateCell,d),j=a(c.titCell,d),k=j.size(),l=a(c.mainCell,d),m=l.children().size(),n=c.switchLoad,o=a(c.targetCell,d),p=parseInt(c.defaultIndex),q=parseInt(c.delayTime),r=parseInt(c.interTime);parseInt(c.triggerTime);var Q,t=parseInt(c.scroll),u=parseInt(c.vis),v="false"==c.autoPlay||0==c.autoPlay?!1:!0,w="false"==c.opp||0==c.opp?!1:!0,x="false"==c.autoPage||0==c.autoPage?!1:!0,y="false"==c.pnLoop||0==c.pnLoop?!1:!0,z="false"==c.mouseOverStop||0==c.mouseOverStop?!1:!0,A="false"==c.defaultPlay||0==c.defaultPlay?!1:!0,B="false"==c.returnDefault||0==c.returnDefault?!1:!0,C=0,D=0,E=0,F=0,G=c.easing,H=null,I=null,J=null,K=c.titOnClassName,L=j.index(d.find("."+K)),M=p=-1==L?p:L,N=p,O=p,P=m>=u?0!=m%t?m%t:t:0,R="leftMarquee"==e||"topMarquee"==e?!0:!1,S=function(){a.isFunction(c.startFun)&&c.startFun(p,k,d,a(c.titCell,d),l,o,f,g)},T=function(){a.isFunction(c.endFun)&&c.endFun(p,k,d,a(c.titCell,d),l,o,f,g)},U=function(){j.removeClass(K),A&&j.eq(N).addClass(K)};if("menu"==c.type)return A&&j.removeClass(K).eq(p).addClass(K),j.hover(function(){Q=a(this).find(c.targetCell);var b=j.index(a(this));I=setTimeout(function(){switch(p=b,j.removeClass(K).eq(p).addClass(K),S(),e){case"fade":Q.stop(!0,!0).animate({opacity:"show"},q,G,T);break;case"slideDown":Q.stop(!0,!0).animate({height:"show"},q,G,T)}},c.triggerTime)},function(){switch(clearTimeout(I),e){case"fade":Q.animate({opacity:"hide"},q,G);break;case"slideDown":Q.animate({height:"hide"},q,G)}}),B&&d.hover(function(){clearTimeout(J)},function(){J=setTimeout(U,q)}),void 0;if(0==k&&(k=m),R&&(k=2),x){if(m>=u)if("leftLoop"==e||"topLoop"==e)k=0!=m%t?(0^m/t)+1:m/t;else{var V=m-u;k=1+parseInt(0!=V%t?V/t+1:V/t),0>=k&&(k=1)}else k=1;j.html("");var W="";if(1==c.autoPage||"true"==c.autoPage)for(var X=0;k>X;X++)W+="<li>"+(X+1)+"</li>";else for(var X=0;k>X;X++)W+=c.autoPage.replace("$",X+1);j.html(W);var j=j.children()}if(m>=u){l.children().each(function(){a(this).width()>E&&(E=a(this).width(),D=a(this).outerWidth(!0)),a(this).height()>F&&(F=a(this).height(),C=a(this).outerHeight(!0))});var Y=l.children(),Z=function(){for(var a=0;u>a;a++)Y.eq(a).clone().addClass("clone").appendTo(l);for(var a=0;P>a;a++)Y.eq(m-a-1).clone().addClass("clone").prependTo(l)};switch(e){case"fold":l.css({position:"relative",width:D,height:C}).children().css({position:"absolute",width:E,left:0,top:0,display:"none"});break;case"top":l.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+u*C+'px"></div>').css({top:-(p*t)*C,position:"relative",padding:"0",margin:"0"}).children().css({height:F});break;case"left":l.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+u*D+'px"></div>').css({width:m*D,left:-(p*t)*D,position:"relative",overflow:"hidden",padding:"0",margin:"0"}).children().css({"float":"left",width:E});break;case"leftLoop":case"leftMarquee":Z(),l.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+u*D+'px"></div>').css({width:(m+u+P)*D,position:"relative",overflow:"hidden",padding:"0",margin:"0",left:-(P+p*t)*D}).children().css({"float":"left",width:E});break;case"topLoop":case"topMarquee":Z(),l.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+u*C+'px"></div>').css({height:(m+u+P)*C,position:"relative",padding:"0",margin:"0",top:-(P+p*t)*C}).children().css({height:F})}}var $=function(a){var b=a*t;return a==k?b=m:-1==a&&0!=m%t&&(b=-m%t),b},_=function(b){var c=function(c){for(var d=c;u+c>d;d++)b.eq(d).find("img["+n+"]").each(function(){var b=a(this);if(b.attr("src",b.attr(n)).removeAttr(n),l.find(".clone")[0])for(var c=l.children(),d=0;d<c.size();d++)c.eq(d).find("img["+n+"]").each(function(){a(this).attr(n)==b.attr("src")&&a(this).attr("src",a(this).attr(n)).removeAttr(n)})})};switch(e){case"fade":case"fold":case"top":case"left":case"slideDown":c(p*t);break;case"leftLoop":case"topLoop":c(P+$(O));break;case"leftMarquee":case"topMarquee":var d="leftMarquee"==e?l.css("left").replace("px",""):l.css("top").replace("px",""),f="leftMarquee"==e?D:C,g=P;if(0!=d%f){var h=Math.abs(0^d/f);g=1==p?P+h:P+h-1}c(g)}},ab=function(a){if(!A||M!=p||a||R){if(R?p>=1?p=1:0>=p&&(p=0):(O=p,p>=k?p=0:0>p&&(p=k-1)),S(),null!=n&&_(l.children()),o[0]&&(Q=o.eq(p),null!=n&&_(o),"slideDown"==e?(o.not(Q).stop(!0,!0).slideUp(q),Q.slideDown(q,G,function(){l[0]||T()})):(o.not(Q).stop(!0,!0).hide(),Q.animate({opacity:"show"},q,function(){l[0]||T()}))),m>=u)switch(e){case"fade":l.children().stop(!0,!0).eq(p).animate({opacity:"show"},q,G,function(){T()}).siblings().hide();break;case"fold":l.children().stop(!0,!0).eq(p).animate({opacity:"show"},q,G,function(){T()}).siblings().animate({opacity:"hide"},q,G);break;case"top":l.stop(!0,!1).animate({top:-p*t*C},q,G,function(){T()});break;case"left":l.stop(!0,!1).animate({left:-p*t*D},q,G,function(){T()});break;case"leftLoop":var b=O;l.stop(!0,!0).animate({left:-($(O)+P)*D},q,G,function(){-1>=b?l.css("left",-(P+(k-1)*t)*D):b>=k&&l.css("left",-P*D),T()});break;case"topLoop":var b=O;l.stop(!0,!0).animate({top:-($(O)+P)*C},q,G,function(){-1>=b?l.css("top",-(P+(k-1)*t)*C):b>=k&&l.css("top",-P*C),T()});break;case"leftMarquee":var c=l.css("left").replace("px","");0==p?l.animate({left:++c},0,function(){l.css("left").replace("px","")>=0&&l.css("left",-m*D)}):l.animate({left:--c},0,function(){l.css("left").replace("px","")<=-(m+P)*D&&l.css("left",-P*D)});break;case"topMarquee":var d=l.css("top").replace("px","");0==p?l.animate({top:++d},0,function(){l.css("top").replace("px","")>=0&&l.css("top",-m*C)}):l.animate({top:--d},0,function(){l.css("top").replace("px","")<=-(m+P)*C&&l.css("top",-P*C)})}j.removeClass(K).eq(p).addClass(K),M=p,y||(g.removeClass("nextStop"),f.removeClass("prevStop"),0==p&&f.addClass("prevStop"),p==k-1&&g.addClass("nextStop")),h.html("<span>"+(p+1)+"</span>/"+k)}};A&&ab(!0),B&&d.hover(function(){clearTimeout(J)},function(){J=setTimeout(function(){p=N,A?ab():"slideDown"==e?Q.slideUp(q,U):Q.animate({opacity:"hide"},q,U),M=p},300)});var bb=function(a){H=setInterval(function(){w?p--:p++,ab()},a?a:r)},cb=function(a){H=setInterval(ab,a?a:r)},db=function(){z||(clearInterval(H),bb())},eb=function(e){if(e && e.currentTarget && e.currentTarget.nodeName === 'A'){e.stopPropagation();e.preventDefault();}(y||p!=k-1)&&(p++,ab(),R||db())},fb=function(e){if(e && e.currentTarget && e.currentTarget.nodeName === 'A'){e.stopPropagation();e.preventDefault();}(y||0!=p)&&(p--,ab(),R||db())},gb=function(){clearInterval(H),R?cb():bb(),i.removeClass("pauseState")},hb=function(){clearInterval(H),i.addClass("pauseState")};if(v?R?(w?p--:p++,cb(),z&&l.hover(hb,gb)):(bb(),z&&d.hover(hb,gb)):(R&&(w?p--:p++),i.addClass("pauseState")),i.click(function(){i.hasClass("pauseState")?gb():hb()}),"mouseover"==c.trigger?j.hover(function(){var a=j.index(this);I=setTimeout(function(){p=a,ab(),db()},c.triggerTime)},function(){clearTimeout(I)}):j.click(function(){p=j.index(this),ab(),db()}),R){if(g.mousedown(eb),f.mousedown(fb),y){var ib,jb=function(){ib=setTimeout(function(){clearInterval(H),cb(0^r/10)},150)},kb=function(){clearTimeout(ib),clearInterval(H),cb()};g.mousedown(jb),g.mouseup(kb),f.mousedown(jb),f.mouseup(kb)}"mouseover"==c.trigger&&(g.hover(eb,function(){}),f.hover(fb,function(){}))}else g.click(eb),f.click(fb)})}}(jQuery),jQuery.easing.jswing=jQuery.easing.swing,jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(a,b,c,d,e){return jQuery.easing[jQuery.easing.def](a,b,c,d,e)},easeInQuad:function(a,b,c,d,e){return d*(b/=e)*b+c},easeOutQuad:function(a,b,c,d,e){return-d*(b/=e)*(b-2)+c},easeInOutQuad:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b+c:-d/2*(--b*(b-2)-1)+c},easeInCubic:function(a,b,c,d,e){return d*(b/=e)*b*b+c},easeOutCubic:function(a,b,c,d,e){return d*((b=b/e-1)*b*b+1)+c},easeInOutCubic:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b+c:d/2*((b-=2)*b*b+2)+c},easeInQuart:function(a,b,c,d,e){return d*(b/=e)*b*b*b+c},easeOutQuart:function(a,b,c,d,e){return-d*((b=b/e-1)*b*b*b-1)+c},easeInOutQuart:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b*b+c:-d/2*((b-=2)*b*b*b-2)+c},easeInQuint:function(a,b,c,d,e){return d*(b/=e)*b*b*b*b+c},easeOutQuint:function(a,b,c,d,e){return d*((b=b/e-1)*b*b*b*b+1)+c},easeInOutQuint:function(a,b,c,d,e){return(b/=e/2)<1?d/2*b*b*b*b*b+c:d/2*((b-=2)*b*b*b*b+2)+c},easeInSine:function(a,b,c,d,e){return-d*Math.cos(b/e*(Math.PI/2))+d+c},easeOutSine:function(a,b,c,d,e){return d*Math.sin(b/e*(Math.PI/2))+c},easeInOutSine:function(a,b,c,d,e){return-d/2*(Math.cos(Math.PI*b/e)-1)+c},easeInExpo:function(a,b,c,d,e){return 0==b?c:d*Math.pow(2,10*(b/e-1))+c},easeOutExpo:function(a,b,c,d,e){return b==e?c+d:d*(-Math.pow(2,-10*b/e)+1)+c},easeInOutExpo:function(a,b,c,d,e){return 0==b?c:b==e?c+d:(b/=e/2)<1?d/2*Math.pow(2,10*(b-1))+c:d/2*(-Math.pow(2,-10*--b)+2)+c},easeInCirc:function(a,b,c,d,e){return-d*(Math.sqrt(1-(b/=e)*b)-1)+c},easeOutCirc:function(a,b,c,d,e){return d*Math.sqrt(1-(b=b/e-1)*b)+c},easeInOutCirc:function(a,b,c,d,e){return(b/=e/2)<1?-d/2*(Math.sqrt(1-b*b)-1)+c:d/2*(Math.sqrt(1-(b-=2)*b)+1)+c},easeInElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return-(h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g))+c},easeOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return h*Math.pow(2,-10*b)*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(2==(b/=e/2))return c+d;if(g||(g=e*.3*1.5),h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return 1>b?-.5*h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+c:.5*h*Math.pow(2,-10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*(b/=e)*b*((f+1)*b-f)+c},easeOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*((b=b/e-1)*b*((f+1)*b+f)+1)+c},easeInOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),(b/=e/2)<1?d/2*b*b*(((f*=1.525)+1)*b-f)+c:d/2*((b-=2)*b*(((f*=1.525)+1)*b+f)+2)+c},easeInBounce:function(a,b,c,d,e){return d-jQuery.easing.easeOutBounce(a,e-b,0,d,e)+c},easeOutBounce:function(a,b,c,d,e){return(b/=e)<1/2.75?d*7.5625*b*b+c:2/2.75>b?d*(7.5625*(b-=1.5/2.75)*b+.75)+c:2.5/2.75>b?d*(7.5625*(b-=2.25/2.75)*b+.9375)+c:d*(7.5625*(b-=2.625/2.75)*b+.984375)+c},easeInOutBounce:function(a,b,c,d,e){return e/2>b?.5*jQuery.easing.easeInBounce(a,2*b,0,d,e)+c:.5*jQuery.easing.easeOutBounce(a,2*b-e,0,d,e)+.5*d+c}});

/*
* ! layer-v2.2 弹层组件 License LGPL  http://layer.layui.com/ By 贤心
*/

;!function(a,b){"use strict";var c,d,e={getPath:function(){var a=document.scripts,b=a[a.length-1],c=b.src;if(!b.getAttribute("merge"))return c.substring(0,c.lastIndexOf("/")+1)}(),enter:function(a){13===a.keyCode&&a.preventDefault()},config:{},end:{},btn:["&#x786E;&#x5B9A;","&#x53D6;&#x6D88;"],type:["dialog","page","iframe","loading","tips"]},f={v:"2.2",ie6:!!a.ActiveXObject&&!a.XMLHttpRequest,index:0,path:e.getPath,config:function(a,b){var d=0;return a=a||{},f.cache=e.config=c.extend(e.config,a),f.path=e.config.path||f.path,"string"==typeof a.extend&&(a.extend=[a.extend]),f.use("skin/layer.css",a.extend&&a.extend.length>0?function g(){var c=a.extend;f.use(c[c[d]?d:d-1],d<c.length?function(){return++d,g}():b)}():b),this},use:function(a,b,d){var e=c("head")[0],a=a.replace(/\s/g,""),g=/\.css$/.test(a),h=document.createElement(g?"link":"script"),i="layui_layer_"+a.replace(/\.|\//g,"");return f.path?(g&&(h.rel="stylesheet"),h[g?"href":"src"]=/^http:\/\//.test(a)?a:f.path+a,h.id=i,c("#"+i)[0]||e.appendChild(h),function j(){(g?1989===parseInt(c("#"+i).css("width")):f[d||i])?function(){b&&b();try{g||e.removeChild(h)}catch(a){}}():setTimeout(j,100)}(),this):void 0},ready:function(a,b){var d="function"==typeof a;return d&&(b=a),f.config(c.extend(e.config,function(){return d?{}:{path:a}}()),b),this},alert:function(a,b,d){var e="function"==typeof b;return e&&(d=b),f.open(c.extend({content:a,yes:d},e?{}:b))},confirm:function(a,b,d,g){var h="function"==typeof b;return h&&(g=d,d=b),f.open(c.extend({content:a,btn:e.btn,yes:d,cancel:g},h?{}:b))},msg:function(a,d,g){var i="function"==typeof d,j=e.config.skin,k=(j?j+" "+j+"-msg":"")||"layui-layer-msg",l=h.anim.length-1;return i&&(g=d),f.open(c.extend({content:a,time:3e3,shade:!1,skin:k,title:!1,closeBtn:!1,btn:!1,end:g},i&&!e.config.skin?{skin:k+" layui-layer-hui",shift:l}:function(){return d=d||{},(-1===d.icon||d.icon===b&&!e.config.skin)&&(d.skin=k+" "+(d.skin||"layui-layer-hui")),d}()))},load:function(a,b){return f.open(c.extend({type:3,icon:a||0,shade:.01},b))},tips:function(a,b,d){return f.open(c.extend({type:4,content:[a,b],closeBtn:!1,time:3e3,shade:!1,maxWidth:210},d))}},g=function(a){var b=this;b.index=++f.index,b.config=c.extend({},b.config,e.config,a),b.creat()};g.pt=g.prototype;var h=["layui-layer",".layui-layer-title",".layui-layer-main",".layui-layer-dialog","layui-layer-iframe","layui-layer-content","layui-layer-btn","layui-layer-close"];h.anim=["layui-anim","layui-anim-01","layui-anim-02","layui-anim-03","layui-anim-04","layui-anim-05","layui-anim-06"],g.pt.config={type:0,shade:.3,fix:!0,move:h[1],title:"&#x4FE1;&#x606F;",offset:"auto",area:"auto",closeBtn:1,time:0,zIndex:19891014,maxWidth:360,shift:0,icon:-1,scrollbar:!0,tips:2},g.pt.vessel=function(a,b){var c=this,d=c.index,f=c.config,g=f.zIndex+d,i="object"==typeof f.title,j=f.maxmin&&(1===f.type||2===f.type),k=f.title?'<div class="layui-layer-title" style="'+(i?f.title[1]:"")+'">'+(i?f.title[0]:f.title)+"</div>":"";return f.zIndex=g,b([f.shade?'<div class="layui-layer-shade" id="layui-layer-shade'+d+'" times="'+d+'" style="'+("z-index:"+(g-1)+"; background-color:"+(f.shade[1]||"#000")+"; opacity:"+(f.shade[0]||f.shade)+"; filter:alpha(opacity="+(100*f.shade[0]||100*f.shade)+");")+'"></div>':"",'<div class="'+h[0]+" "+(h.anim[f.shift]||"")+(" layui-layer-"+e.type[f.type])+(0!=f.type&&2!=f.type||f.shade?"":" layui-layer-border")+" "+(f.skin||"")+'" id="'+h[0]+d+'" type="'+e.type[f.type]+'" times="'+d+'" showtime="'+f.time+'" conType="'+(a?"object":"string")+'" style="z-index: '+g+"; width:"+f.area[0]+";height:"+f.area[1]+(f.fix?"":";position:absolute;")+'">'+(a&&2!=f.type?"":k)+'<div id="'+(f.id||"")+'" class="layui-layer-content'+(0==f.type&&-1!==f.icon?" layui-layer-padding":"")+(3==f.type?" layui-layer-loading"+f.icon:"")+'">'+(0==f.type&&-1!==f.icon?'<i class="layui-layer-ico layui-layer-ico'+f.icon+'"></i>':"")+(1==f.type&&a?"":f.content||"")+'</div><span class="layui-layer-setwin">'+function(){var a=j?'<a class="layui-layer-min" href="javascript:;"><cite></cite></a><a class="layui-layer-ico layui-layer-max" href="javascript:;"></a>':"";return f.closeBtn&&(a+='<a class="layui-layer-ico '+h[7]+" "+h[7]+(f.title?f.closeBtn:4==f.type?"1":"2")+'" href="javascript:;"></a>'),a}()+"</span>"+(f.btn?function(){var a="";"string"==typeof f.btn&&(f.btn=[f.btn]);for(var b=0,c=f.btn.length;c>b;b++)a+='<a class="'+h[6]+b+'">'+f.btn[b]+"</a>";return'<div class="'+h[6]+'">'+a+"</div>"}():"")+"</div>"],k),c},g.pt.creat=function(){var a=this,b=a.config,g=a.index,i=b.content,j="object"==typeof i;if(!c("#"+b.id)[0]){switch("string"==typeof b.area&&(b.area="auto"===b.area?["",""]:[b.area,""]),b.type){case 0:b.btn="btn"in b?b.btn:e.btn[0],f.closeAll("dialog");break;case 2:var i=b.content=j?b.content:[b.content||"http://layer.layui.com","auto"];b.content='<iframe scrolling="'+(b.content[1]||"auto")+'" allowtransparency="true" id="'+h[4]+g+'" name="'+h[4]+g+'" onload="this.className=\'\';" class="layui-layer-load" frameborder="0" src="'+b.content[0]+'"></iframe>';break;case 3:b.title=!1,b.closeBtn=!1,-1===b.icon&&0===b.icon,f.closeAll("loading");break;case 4:j||(b.content=[b.content,"body"]),b.follow=b.content[1],b.content=b.content[0]+'<i class="layui-layer-TipsG"></i>',b.title=!1,b.fix=!1,b.tips="object"==typeof b.tips?b.tips:[b.tips,!0],b.tipsMore||f.closeAll("tips")}a.vessel(j,function(d,e){c("body").append(d[0]),j?function(){2==b.type||4==b.type?function(){c("body").append(d[1])}():function(){i.parents("."+h[0])[0]||(i.show().addClass("layui-layer-wrap").wrap(d[1]),c("#"+h[0]+g).find("."+h[5]).before(e))}()}():c("body").append(d[1]),a.layero=c("#"+h[0]+g),b.scrollbar||h.html.css("overflow","hidden").attr("layer-full",g)}).auto(g),2==b.type&&f.ie6&&a.layero.find("iframe").attr("src",i[0]),c(document).off("keydown",e.enter).on("keydown",e.enter),a.layero.on("keydown",function(a){c(document).off("keydown",e.enter)}),4==b.type?a.tips():a.offset(),b.fix&&d.on("resize",function(){a.offset(),(/^\d+%$/.test(b.area[0])||/^\d+%$/.test(b.area[1]))&&a.auto(g),4==b.type&&a.tips()}),b.time<=0||setTimeout(function(){f.close(a.index)},b.time),a.move().callback()}},g.pt.auto=function(a){function b(a){a=g.find(a),a.height(i[1]-j-k-2*(0|parseFloat(a.css("padding"))))}var e=this,f=e.config,g=c("#"+h[0]+a);""===f.area[0]&&f.maxWidth>0&&(/MSIE 7/.test(navigator.userAgent)&&f.btn&&g.width(g.innerWidth()),g.outerWidth()>f.maxWidth&&g.width(f.maxWidth));var i=[g.innerWidth(),g.innerHeight()],j=g.find(h[1]).outerHeight()||0,k=g.find("."+h[6]).outerHeight()||0;switch(f.type){case 2:b("iframe");break;default:""===f.area[1]?f.fix&&i[1]>=d.height()&&(i[1]=d.height(),b("."+h[5])):b("."+h[5])}return e},g.pt.offset=function(){var a=this,b=a.config,c=a.layero,e=[c.outerWidth(),c.outerHeight()],f="object"==typeof b.offset;a.offsetTop=(d.height()-e[1])/2,a.offsetLeft=(d.width()-e[0])/2,f?(a.offsetTop=b.offset[0],a.offsetLeft=b.offset[1]||a.offsetLeft):"auto"!==b.offset&&(a.offsetTop=b.offset,"rb"===b.offset&&(a.offsetTop=d.height()-e[1],a.offsetLeft=d.width()-e[0])),b.fix||(a.offsetTop=/%$/.test(a.offsetTop)?d.height()*parseFloat(a.offsetTop)/100:parseFloat(a.offsetTop),a.offsetLeft=/%$/.test(a.offsetLeft)?d.width()*parseFloat(a.offsetLeft)/100:parseFloat(a.offsetLeft),a.offsetTop+=d.scrollTop(),a.offsetLeft+=d.scrollLeft()),c.css({top:a.offsetTop,left:a.offsetLeft})},g.pt.tips=function(){var a=this,b=a.config,e=a.layero,f=[e.outerWidth(),e.outerHeight()],g=c(b.follow);g[0]||(g=c("body"));var i={width:g.outerWidth(),height:g.outerHeight(),top:g.offset().top,left:g.offset().left},j=e.find(".layui-layer-TipsG"),k=b.tips[0];b.tips[1]||j.remove(),i.autoLeft=function(){i.left+f[0]-d.width()>0?(i.tipLeft=i.left+i.width-f[0],j.css({right:12,left:"auto"})):i.tipLeft=i.left},i.where=[function(){i.autoLeft(),i.tipTop=i.top-f[1]-10,j.removeClass("layui-layer-TipsB").addClass("layui-layer-TipsT").css("border-right-color",b.tips[1])},function(){i.tipLeft=i.left+i.width+10,i.tipTop=i.top,j.removeClass("layui-layer-TipsL").addClass("layui-layer-TipsR").css("border-bottom-color",b.tips[1])},function(){i.autoLeft(),i.tipTop=i.top+i.height+10,j.removeClass("layui-layer-TipsT").addClass("layui-layer-TipsB").css("border-right-color",b.tips[1])},function(){i.tipLeft=i.left-f[0]-10,i.tipTop=i.top,j.removeClass("layui-layer-TipsR").addClass("layui-layer-TipsL").css("border-bottom-color",b.tips[1])}],i.where[k-1](),1===k?i.top-(d.scrollTop()+f[1]+16)<0&&i.where[2]():2===k?d.width()-(i.left+i.width+f[0]+16)>0||i.where[3]():3===k?i.top-d.scrollTop()+i.height+f[1]+16-d.height()>0&&i.where[0]():4===k&&f[0]+16-i.left>0&&i.where[1](),e.find("."+h[5]).css({"background-color":b.tips[1],"padding-right":b.closeBtn?"30px":""}),e.css({left:i.tipLeft,top:i.tipTop})},g.pt.move=function(){var a=this,b=a.config,e={setY:0,moveLayer:function(){var a=e.layero,b=parseInt(a.css("margin-left")),c=parseInt(e.move.css("left"));0===b||(c-=b),"fixed"!==a.css("position")&&(c-=a.parent().offset().left,e.setY=0),a.css({left:c,top:parseInt(e.move.css("top"))-e.setY})}},f=a.layero.find(b.move);return b.move&&f.attr("move","ok"),f.css({cursor:b.move?"move":"auto"}),c(b.move).on("mousedown",function(a){if(a.preventDefault(),"ok"===c(this).attr("move")){e.ismove=!0,e.layero=c(this).parents("."+h[0]);var f=e.layero.offset().left,g=e.layero.offset().top,i=e.layero.outerWidth()-6,j=e.layero.outerHeight()-6;c("#layui-layer-moves")[0]||c("body").append('<div id="layui-layer-moves" class="layui-layer-moves" style="left:'+f+"px; top:"+g+"px; width:"+i+"px; height:"+j+'px; z-index:2147483584"></div>'),e.move=c("#layui-layer-moves"),b.moveType&&e.move.css({visibility:"hidden"}),e.moveX=a.pageX-e.move.position().left,e.moveY=a.pageY-e.move.position().top,"fixed"!==e.layero.css("position")||(e.setY=d.scrollTop())}}),c(document).mousemove(function(a){if(e.ismove){var c=a.pageX-e.moveX,f=a.pageY-e.moveY;if(a.preventDefault(),!b.moveOut){e.setY=d.scrollTop();var g=d.width()-e.move.outerWidth(),h=e.setY;0>c&&(c=0),c>g&&(c=g),h>f&&(f=h),f>d.height()-e.move.outerHeight()+e.setY&&(f=d.height()-e.move.outerHeight()+e.setY)}e.move.css({left:c,top:f}),b.moveType&&e.moveLayer(),c=f=g=h=null}}).mouseup(function(){try{e.ismove&&(e.moveLayer(),e.move.remove(),b.moveEnd&&b.moveEnd()),e.ismove=!1}catch(a){e.ismove=!1}}),a},g.pt.callback=function(){function a(){var a=g.cancel&&g.cancel(b.index);a===!1||f.close(b.index)}var b=this,d=b.layero,g=b.config;b.openLayer(),g.success&&(2==g.type?d.find("iframe").on("load",function(){g.success(d,b.index)}):g.success(d,b.index)),f.ie6&&b.IE6(d),d.find("."+h[6]).children("a").on("click",function(){var e=c(this).index();g["btn"+(e+1)]&&g["btn"+(e+1)](b.index,d),0===e?g.yes?g.yes(b.index,d):f.close(b.index):1===e?a():g["btn"+(e+1)]||f.close(b.index)}),d.find("."+h[7]).on("click",a),g.shadeClose&&c("#layui-layer-shade"+b.index).on("click",function(){f.close(b.index)}),d.find(".layui-layer-min").on("click",function(){f.min(b.index,g),g.min&&g.min(d)}),d.find(".layui-layer-max").on("click",function(){c(this).hasClass("layui-layer-maxmin")?(f.restore(b.index),g.restore&&g.restore(d)):(f.full(b.index,g),g.full&&g.full(d))}),g.end&&(e.end[b.index]=g.end)},e.reselect=function(){c.each(c("select"),function(a,b){var d=c(this);d.parents("."+h[0])[0]||1==d.attr("layer")&&c("."+h[0]).length<1&&d.removeAttr("layer").show(),d=null})},g.pt.IE6=function(a){function b(){a.css({top:f+(e.config.fix?d.scrollTop():0)})}var e=this,f=a.offset().top;b(),d.scroll(b),c("select").each(function(a,b){var d=c(this);d.parents("."+h[0])[0]||"none"===d.css("display")||d.attr({layer:"1"}).hide(),d=null})},g.pt.openLayer=function(){var a=this;f.zIndex=a.config.zIndex,f.setTop=function(a){var b=function(){f.zIndex++,a.css("z-index",f.zIndex+1)};return f.zIndex=parseInt(a[0].style.zIndex),a.on("mousedown",b),f.zIndex}},e.record=function(a){var b=[a.outerWidth(),a.outerHeight(),a.position().top,a.position().left+parseFloat(a.css("margin-left"))];a.find(".layui-layer-max").addClass("layui-layer-maxmin"),a.attr({area:b})},e.rescollbar=function(a){h.html.attr("layer-full")==a&&(h.html[0].style.removeProperty?h.html[0].style.removeProperty("overflow"):h.html[0].style.removeAttribute("overflow"),h.html.removeAttr("layer-full"))},a.layer=f,f.getChildFrame=function(a,b){return b=b||c("."+h[4]).attr("times"),c("#"+h[0]+b).find("iframe").contents().find(a)},f.getFrameIndex=function(a){return c("#"+a).parents("."+h[4]).attr("times")},f.iframeAuto=function(a){if(a){var b=f.getChildFrame("html",a).outerHeight(),d=c("#"+h[0]+a),e=d.find(h[1]).outerHeight()||0,g=d.find("."+h[6]).outerHeight()||0;d.css({height:b+e+g}),d.find("iframe").css({height:b})}},f.iframeSrc=function(a,b){c("#"+h[0]+a).find("iframe").attr("src",b)},f.style=function(a,b){var d=c("#"+h[0]+a),f=d.attr("type"),g=d.find(h[1]).outerHeight()||0,i=d.find("."+h[6]).outerHeight()||0;(f===e.type[1]||f===e.type[2])&&(d.css(b),f===e.type[2]&&d.find("iframe").css({height:parseFloat(b.height)-g-i}))},f.min=function(a,b){var d=c("#"+h[0]+a),g=d.find(h[1]).outerHeight()||0;e.record(d),f.style(a,{width:180,height:g,overflow:"hidden"}),d.find(".layui-layer-min").hide(),"page"===d.attr("type")&&d.find(h[4]).hide(),e.rescollbar(a)},f.restore=function(a){var b=c("#"+h[0]+a),d=b.attr("area").split(",");b.attr("type");f.style(a,{width:parseFloat(d[0]),height:parseFloat(d[1]),top:parseFloat(d[2]),left:parseFloat(d[3]),overflow:"visible"}),b.find(".layui-layer-max").removeClass("layui-layer-maxmin"),b.find(".layui-layer-min").show(),"page"===b.attr("type")&&b.find(h[4]).show(),e.rescollbar(a)},f.full=function(a){var b,g=c("#"+h[0]+a);e.record(g),h.html.attr("layer-full")||h.html.css("overflow","hidden").attr("layer-full",a),clearTimeout(b),b=setTimeout(function(){var b="fixed"===g.css("position");f.style(a,{top:b?0:d.scrollTop(),left:b?0:d.scrollLeft(),width:d.width(),height:d.height()}),g.find(".layui-layer-min").hide()},100)},f.title=function(a,b){var d=c("#"+h[0]+(b||f.index)).find(h[1]);d.html(a)},f.close=function(a){var b=c("#"+h[0]+a),d=b.attr("type");if(b[0]){if(d===e.type[1]&&"object"===b.attr("conType")){b.children(":not(."+h[5]+")").remove();for(var g=0;2>g;g++)b.find(".layui-layer-wrap").unwrap().hide()}else{if(d===e.type[2])try{var i=c("#"+h[4]+a)[0];i.contentWindow.document.write(""),i.contentWindow.close(),b.find("."+h[5])[0].removeChild(i)}catch(j){}b[0].innerHTML="",b.remove()}c("#layui-layer-moves, #layui-layer-shade"+a).remove(),f.ie6&&e.reselect(),e.rescollbar(a),c(document).off("keydown",e.enter),"function"==typeof e.end[a]&&e.end[a](),delete e.end[a]}},f.closeAll=function(a){c.each(c("."+h[0]),function(){var b=c(this),d=a?b.attr("type")===a:1;d&&f.close(b.attr("times")),d=null})},e.run=function(){c=jQuery,d=c(a),h.html=c("html"),f.open=function(a){var b=new g(a);return b.index}},"function"==typeof define?define(function(){return e.run(),f}):function(){e.run(),f.use("skin/layer.css")}()}(window);

$(function(){
	$('#J_signIn').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	$('#J_switchCity').click(function(e) {
		e.preventDefault();
		var allCities = '';
		var $textarea = $('textarea.storedata');
		if ($textarea[0]) {
			var allCities = $textarea.text();
		}
		HUI.PopOut.alert(["\u9009\u62e9\u533a\u57df",allCities ,""], 3);
	});
});
$(function(){
	var h = $("#uc_main").height();
	if(h>560){
		$(".UC_nav").css({
			'min-height': h
		});
	}

});

/* http://mths.be/placeholder v2.0.7 by @mathias */
(function(K, E, D) {
	var C = "placeholder" in E.createElement("input"), A = "placeholder" in E.createElement("textarea"), I = D.fn, B = D.valHooks, H, L;
	if (C && A) {
		L = I.placeholder = function() {
			return this;
		};
		L.input = L.textarea = true;
	} else {
		L = I.placeholder = function() {
			var M = this;
			M.filter((C ? "textarea" :":input") + "[placeholder]").not(".placeholder").bind({
				"focus.placeholder":F,
				"blur.placeholder":G
			}).data("placeholder-enabled", true).trigger("blur.placeholder");
			return M;
		};
		L.input = C;
		L.textarea = A;
		H = {
			get:function(N) {
				var M = D(N);
				return M.data("placeholder-enabled") && M.hasClass("placeholder") ? "" :N.value;
			},
			set:function(O, M) {
				var N = D(O);
				if (!N.data("placeholder-enabled")) {
					return O.value = M;
				}
				if (M == "") {
					O.value = M;
					if (O != E.activeElement) {
						G.call(O);
					}
				} else {
					if (N.hasClass("placeholder")) {
						F.call(O, true, M) || (O.value = M);
					} else {
						O.value = M;
					}
				}
				return N;
			}
		};
		C || (B.input = H);
		A || (B.textarea = H);
		D(function() {
			D(E).delegate("form", "submit.placeholder", function() {
				var M = D(".placeholder", this).each(F);
				setTimeout(function() {
					M.each(G);
				}, 10);
			});
		});
		D(K).bind("beforeunload.placeholder", function() {
			D(".placeholder").each(function() {
				this.value = "";
			});
		});
	}
	function J(N) {
		var M = {}, O = /^jQuery\d+$/;
		D.each(N.attributes, function(P, Q) {
			if (Q.specified && !O.test(Q.name)) {
				M[Q.name] = Q.value;
			}
		});
		return M;
	}
	function F(O, M) {
		var N = this, Q = D(N);
		if (N.value == Q.attr("placeholder") && Q.hasClass("placeholder")) {
			if (Q.data("placeholder-password")) {
				Q = Q.hide().data("brother").show();
				if (O === true) {
					return Q[0].value = M;
				}
				Q.focus();
			} else {
				N.value = "";
				Q.removeClass("placeholder");
				try {
					N == E.activeElement && N.select();
				} catch (P) {}
			}
		}
	}
	function G() {
		var M, N = this, R = D(N), P = R, Q = this.id;
		if (N.value == "") {
			if (N.type == "password") {
				if (!R.data("placeholder-textinput")) {
					try {
						M = R.clone().attr({
							type:"text"
						});
					} catch (O) {
						M = D("<input>").attr(D.extend(J(this), {
							type:"text"
						}));
					}
					M.removeAttr("name").removeAttr("id").data({
						"placeholder-password":true,
						"placeholder-id":Q,
						brother:R
					}).bind("focus.placeholder", F);
					R.data({
						"placeholder-textinput":M,
						"placeholder-id":Q,
						brother:M
					}).before(M);
				}
				R = R.hide().data("brother").show();
			}
			R.addClass("placeholder");
			R[0].value = R.attr("placeholder");
		} else {
			R.removeClass("placeholder");
		}
	}
})(this, document, jQuery);

;(function($, window, document,undefined) {
	//定义Comment的构造函数
	var Comment = function(ele, opt) {
		this.$element = ele,
			this.defaults = {
				'Commentbox': 'textarea',
				'btn': '.j_commentBtn',
				'url': '/index.php/Carlife/post_add',
				'bbsid': this.$element.data('bbsid'),
				'commentNumber': '.j_commentNumber',
				'commentLister': '.j_commentList>ul',
				'callback': function(){}
			},
			this.options = $.extend({}, this.defaults, opt)
	}

	//定义Beautifier的方法
	Comment.prototype = {
		init: function() {
			var that = this,
				$btn = this.$element.find(this.options.btn),
				$number = $(this.options.commentNumber),
				dbNumber = parseInt($.trim($number.text())),
				$txtArea = this.$element.find(this.options.Commentbox);
			$txtArea.on('focus', function(e){
				if (!$.G.getUSERID()) {
					HUI.PopOut.login();
					return false;
				}
				$(this).css("height", "80");
				$btn.show();
			});
			$txtArea.on('blur', function(e){
				if($.trim($(this).val()) === ''){
					$(this).css("height", "32");
					$btn.hide();
				}
			});
			$btn.on('click', function(event){
				event.stopPropagation();
				event.preventDefault();

				var $btn = $(this);
				var dbCommentCon = $txtArea.val();
				$btn.hasClass('G_btn_a_disable') || ($btn.text('提交中...').addClass('G_btn_a_disable'), $.post(that.options.url,
					{bbs_id: that.options.bbsid, pid:0, rid:0, comment: dbCommentCon},
					function(db){
						//							生成的日期
							var now = new Date();
							var month = now.getMonth() + 1;     //月
							var day = now.getDate();            //日

							var hh = now.getHours();            //时
							var mm = now.getMinutes();          //分

							var clock = "";

							if(month < 10)
								clock += "0";

							clock += month + "-";

							if(day < 10)
								clock += "0";

							clock += day + " ";

							if(hh < 10)
								clock += "0";

							clock += hh + ":";
							if (mm < 10) clock += '0';
							clock += mm;
						var 	$li = '<li class="comments-item"> ' +
							'<div class="comments-item-bd"> ' +
							'<div class="comments-img"> ' +
							'<img src="'+ USER.face +'"> ' +
							'</div> ' +
							'<div class="comments-comment"> ' +
							'<div class="comments-name"> ' +
							'<a href="javascript:">'+ USER.nick  +'</a>：'+ dbCommentCon +' ' +
							'</div> ' +
							'<div class="comments-op">' +
							'<span class="comments-date G_fl">'+ clock +'</span>' +
							'</div> ' +
							'</div> ' +
							'</li>';

						if(db.status = 1){
							$number.text(dbNumber + 1);
							$(that.options.commentLister).prepend($li);
							$txtArea.val('');
							$txtArea.trigger('blur');
							layer.msg(db.msg, {icon: 1});
						}else{
							layer.msg(db.msg, {icon: 2});
						}

					}
				).always(function(){
						$btn.text('发表').removeClass('G_btn_a_disable');
					}));
			});
		}
	}
	//在插件中使用Beautifier对象
	$.fn.CarLifeComment = function(options) {
		//创建Beautifier的实体
		var Commenter = new Comment(this, options);
		//调用其方法
		return Commenter.init();
	}
})(jQuery, window, document);

/* 筛选器 */
(function ($) {
	$.CommentList = {};
	$.extend($.CommentList, {
		page: 1, //当前页
		pageSize: 10, //每页个数
		commentLister: $('.j_commentList'),
		pager:  $('.j_commentList').find('.j_pager'),
		bbsId: $('.j_commentList').data('bbsid'),
		ajaxListUrl: '/index.php/Carlife/post_list',
		ajaxPostUrl: '/index.php/Carlife/post_add',
		ajaxZanUrl: '/index.php/Carlife/thumbs_comment',
		init: function (options) {
			$.extend($.bcfilter, options);
			$.CommentList.defInit.init();
		},
		defInit: {
			init:function(){
				$.CommentList._refeshList();
			}
		},
		_createListContent: function(db){
			var commentLi = "",

				lauder = '';
			if(parseInt(db.count) > 0){
				$.each(db.list, function(i, comment){
					var replay = '', laudSta = '';
					if(comment.thumbs_amount != null){
						lauder = '<span class="laud-num">'+ comment.thumbs_amount +'</span>';
					}
					if(comment.reply.length > 0){
						$.each(comment.reply, function(i, rep){
							replay += '<div class="j_commentItem comments-item"> ' +
								'<div class="comments-item-bd"> ' +
								'<div class="comments-img"> ' +
								'<img src="'+ rep.phead_url +'"/>' +
								'</div> ' +
								'<div class="comments-comment"> ' +
								'<div class="comments-name"> ' +
								'<a href="javascript:" class="j_commentUserName">'+ rep.pnick +'</a>' +
									' 回复 '  +
								'<a href="javascript:" class="j_commentUserName">'+ comment.nick +'</a>：'+ rep.comment +'</div> ' +
								'<div class="comments-op"> ' +
								'<span class="comments-date G_fl">'+
									rep.createtime
								+'</span> ' +
								'</div> ' +
								'</div> ' +
								'</div> ' +
								'</div>';
						});

					}
					if(comment.thumbs_status == 1){
						laudSta = '<a href="#" class="j_laud isZan">已赞</a>';
					}else{
						laudSta = '<a href="#" class="j_laud">赞</a>';
					}

					commentLi += '<li class="j_commentItem comments-item" data-id="'+ comment.id +'"  data-rid="'+ comment.rid +'" data-pid="'+ comment.pid +'" data-uid="'+ comment.user_id +'"> ' +
						'<div class="comments-item-bd"> ' +
						'<div class="comments-img"> ' +
						'<img src="'+ comment.head_url +'"/>' +
						'</div> ' +
						'<div class="comments-comment"> ' +
						'<div class="comments-name"> ' +
						'<a href="javascript:" class="j_commentUserName">'+ comment.nick +'</a>：'+ comment.comment +'</div> ' +
						'<div class="comments-op"> ' +
						'<div class="comments-good G_fr"> ' +
						'<a href="javascript:" class="comments-reply j_commentReplay">回复</a> ' +
						'<b></b> ' +
						laudSta +
						lauder +
						'</div> ' +
						'<span class="comments-date G_fl">'+
						comment.createtime
						+'</span> ' +
						'</div> ' +
						'</div> ' +
						replay +
						'</div> ' +
						'</li>';
				});
				$.CommentList.commentLister.find('ul').html(commentLi);
				$.CommentList.pager.html(db.pagelist);
				$.CommentList.pager.find('a').off().on('click', function(e){
					e.stopPropagation();
					e.preventDefault();
					var $this = $(this),
						dbPage = $this.data('page');
					$.CommentList.page = dbPage;
					$.CommentList._refeshList();
				});
				$.CommentList.commentLister.on('click', '.j_laud', function(e){
					e.stopPropagation();
					e.preventDefault();
					if (!$.G.getUSERID()) {
						HUI.PopOut.login();
						return false;
					}
					var $this = $(this),
						$commentItem = $this.closest('.j_commentItem '),
						$laudNum = $commentItem.find('.laud-num')
						dbCount = parseInt($.trim($laudNum.text())),
						dbId = $commentItem.data('id');

					$.post($.CommentList.ajaxZanUrl,
						{comment_id: dbId},
						function(db){
							if(db.status == 1){
								if($this.hasClass('isZan')){
									$this.removeClass('isZan');
									$laudNum.text(dbCount - 1);
									$this.text(db.str);
								}else{
									$this.addClass('isZan');
									$laudNum.text(dbCount + 1);
									$this.text(db.str);
								}
							}else{
								layer.msg(db.msg, {icon: 2});
							}

						});
				});
				$.CommentList.commentLister.on('click', '.j_commentReplay', function(e){
					e.stopPropagation();
					e.preventDefault();
					if (!$.G.getUSERID()) {
						HUI.PopOut.login();
						return false;
					}
					var $this = $(this),
						$commentItem = $this.closest('.j_commentItem '),
						dbId = $commentItem.data('id'),
						$replay = $commentItem.find('.j_commentBox');

					if($replay.length > 0){
						if($replay.is(":hidden")){
							$replay.show();
						}else{
							$replay.hide();
						}
					}else{
						$commentItem.find('.comments-comment').eq(0).append('<div data-bbsid="d5a73c66f68d3bc2598cadceb70bb47f" class="comments-box comments-text comments-box-text clearfix j_commentBox"> <div class="input"><textarea name="" rows="" cols="" placeholder="发表评论..." style="height: 32px;"></textarea> </div> <div class="clearfix"> <button class="G_btn_a j_commentBtn G_fr" >发表</button> </div> </div>');
					}
					$commentItem.find('.j_commentBtn').on('click', function(e){
						e.stopPropagation();
						e.preventDefault();
						var $btn = $(this);
						var rName = $commentItem.find('.j_commentUserName').text();
						var dbCommentCon = $commentItem.find('textarea').val();
						$btn.hasClass('G_btn_a_disable') || ($btn.text('提交中...').addClass('G_btn_a_disable'), $.post($.CommentList.ajaxPostUrl,
							{bbs_id: $.CommentList.bbsId, pid: dbId, rid: dbId, comment: dbCommentCon},
							function(db){
								var now = new Date();
								var month = now.getMonth() + 1;     //月
								var day = now.getDate();            //日

								var hh = now.getHours();            //时
								var mm = now.getMinutes();          //分

								var clock = "";

								if(month < 10)
									clock += "0";

								clock += month + "-";

								if(day < 10)
									clock += "0";

								clock += day + " ";

								if(hh < 10)
									clock += "0";

								clock += hh + ":";
								if (mm < 10) clock += '0';
								clock += mm;

								if(db.status == 1){
									$btn.closest('.comments-comment').after('<div class="j_commentItem comments-item"> <div class="comments-item-bd"> <div class="comments-img"> <img src="'+  USER.face +'"></div> <div class="comments-comment"> <div class="comments-name"> <a href="javascript:" class="j_commentUserName">'+ db.nick +'</a> 回复 <a href="javascript:" class="j_commentUserName">'+ db.pnick +'</a>：'+ dbCommentCon +'</div> <div class="comments-op"> <span class="comments-date G_fl">'+ clock +'</span> </div> </div> </div> </div>');
								}else{
									layer.msg(db.msg, {icon: 2});
								}

							}
						).always(function(){
								$btn.text('发表').removeClass('G_btn_a_disable');
							}));
					});

				});

			}else{
				$.CommentList.commentLister.find('ul').html('<li class="null_list">暂无评论！</li>');
			}
		},
		_refeshList: function () {
			var $loader = $('<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>');
			$.ajax({
				type: "post",
				url: $.CommentList.ajaxListUrl,
				data: {pageNum: $.CommentList.pageSize, p: $.CommentList.page, bbs_id: $.CommentList.bbsId},
				beforeSend: function () {
					$.CommentList.commentLister.append($loader);
				},
				success: function(db){
					$.CommentList._createListContent(db);
				},
				complete: function () {
					$loader.remove();
				},
				error: function() {
					this.errormsg()
				}
			});
		}
	});
})(jQuery);

/* ajax列表和分页 */
;(function($, window, document,undefined) {
	//定义AjaxList的构造函数
	var AjaxList = function(ele, opt) {
		this.$element = ele,
			this.defaults = {
				page: 1, //当前页
				pageSize: 10, //每页个数
				lister: '.j_list', //list容器
				tpler:'tplItem', //模板名
				pager:  '.j_pager', //page容器
				pagerItem: 'a', //page选择器
				loader: '<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>',
				nuller: '<div class="null_list">您暂无任何订单！快去 <a href="http://shop.gouchehui.com" target="_blank">购买</a>你喜欢的商品吧！</div>',
				ajaxUrl: '/index.php/Carlife/post_list',
				allRecord: $('.j_allRecord'),
				dataObj: {},
				'callback': function(){}
			},
			this.options = $.extend({}, this.defaults, opt);
			this.dataObj = $.extend({}, this.defaults.dataObj);
	}

	//定义AjaxList的方法
	AjaxList.prototype = {
		init: function() {
			var that = this;
			that.getDate(this.options.page);
		},
		getDate: function(page){
			var that = this,
				_ajaxUrl = this.options.ajaxUrl,
				setDb = this.dataObj,
				_loader = $(this.options.loader),
				$lister = this.$element.find(this.options.lister);
			setDb.p = page;
			setDb.pagesize = this.options.pageSize;
			$.ajax({
				type: "post",
				data: setDb,
				url: _ajaxUrl,
				beforeSend: function () {
					$lister.prepend(_loader);
				},
				success: function(db){
					that.createLister(db);
				},
				complete: function () {
					_loader.remove();
				},
				error: function() {
					this.errormsg()
				}
			});
		},
		createLister: function(db){
			var that = this,
				$liser = this.$element.find(this.options.lister),
				$nuller = this.options.nuller,
				$allReacord = this.$element.find(this.options.allRecord),
				$pager = this.$element.find(this.options.pager);

			if(db && db.list && db.list.length > 0){
				if(db.count){
					$allReacord.text(db.count);
				}else{
					$allReacord.text(db.list.length);
				}

				var html = template(this.options.tpler, db);
				$liser.html(html);
				$pager.html(db.pagelist);
				$pager.find(this.options.pagerItem).on('click', function(event){
					event.stopPropagation();
					event.preventDefault();
					var pageNum = $(this).data('page');
					that.getDate(pageNum);
				});
			}else{
				$liser.html($nuller);
				$allReacord.text("0");
			}
		}
	}
	//在插件中使用AjaxLister对象
	$.fn.AjaxListerContainer = function(options) {
		//创建AjaxLister的实体
		var AjaxLister = new AjaxList(this, options);
		//调用其方法
		return AjaxLister.init();
	}
})(jQuery, window, document),(function($){
	$.fn.autoMail = function(options){
		var opts = $.extend({}, $.fn.autoMail.defaults, options),
			__staticInpVal = '';
		isNewdbFlag = true;
		return this.each(function(){
			var $this = $(this),
				$form = $this.closest('form');
			var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
			var top = $this.outerHeight(true);
			var $mailBox = $('<div class="search-box-result" id="mailBox"></div>');
			$form.css('position', 'relative');
			$mailBox.css({
				'position': 'absolute',
				'top': top + 'px',
				'left': '0',
				'width': $this.outerWidth(true) + 'px'
			});
			$form.append($mailBox);

			//设置高亮li
			function setEmailLi(index){
				$('#mailBox li').removeClass('cmail').eq(index).addClass('cmail');
			}
			//初始化邮箱列表
			var emails = o.emails;
			var init = function(input){
				var inpVal = $.trim(input.val());
				//取消浏览器自动提示
				input.attr('autocomplete','off');

				//添加提示邮箱列表
				if(inpVal != '' && __staticInpVal != inpVal){
					$.ajax({
						type: "POST",
						url: "/index.php/api/carSearch",
						dataType: "json",
						data: {
							select: inpVal,
						},
						success:function(data){
							if(data.status == 1){
								var emailList = '<ul>';
								$.each(data.list, function(i, v){
									emailList += '<li><a href="'+ v.url +'" title="'+ v.carstyle + v.car_name +'">'+ v.carstyle + v.car_name + '</li>';
								});
								emailList += '</ul>';
								$mailBox.html(emailList).show(0);
								$mailBox.find('li').hover(function(){
									$(this).addClass('hover').siblings().removeClass('hover');
								});

								__staticInpVal = inpVal;
							}else{
								$mailBox.hide(0);
							}
						}
					});
				}else{
					$mailBox.hide(0);
				}

				//添加鼠标事件
				$('#mailBox li').hover(function(){
					$('#mailBox li').removeClass('cmail');
					$(this).addClass('cmail');
				},function(){
					$(this).removeClass('cmail');
				}).click(function(){
					input.val($(this).html());
					$mailBox.hide(0);
				});
			}
			//当前高亮下标
			var eindex = -1;
			//监听事件
			$this.focus(function(){
				$mailBox.show();
			}).blur(function(){
				$mailBox.hide(0);
			});
			$this.keyup(function(event){
				//var re = /[^\u4e00-\u9fa5]/;
				//if(!re.test($.trim($this.val()))){
				//	init($this);
				//}
				init($this);
				//如果在表单中，防止回车提交
			}).keydown(function(event){
				//if(event.keyCode == 13){
				//    return false;
				//}
			});
		});
	}
	$.fn.autoMail.defaults = {
		emails:[]
	}
})(jQuery);

$(document).ready(function() {

	//$('#mq').autoMail();

	$("#J_backTop").on("click",function(e){
		e.stopPropagation();
		e.preventDefault();
		$('body,html').animate({ scrollTop: 0 }, 600);
		$("#J_bar").animate({opacty:0},1);
	});

	$('.j_shareBtn').on('click', function(event){
		var shareBox = $('.j_shareBox');
		event.stopPropagation();
		event.preventDefault();
		if(shareBox.is(":hidden")){
			shareBox.show();
		}else{
			shareBox.hide();
		}
	});
	$("*[placeholder]").each(function() {
		$(this).placeholder();
	});
});

$(function(){
	// $.getJSON("/footer-link.json",function(data){
	// 	var $footerLinker = $('#J_FooterLink'), aLinker = '';
	// 	$.each(data, function(i, arr){
	// 		aLinker += '<a href="'+ arr.url +'" title="'+ arr.name +'" target="_blank">'+ arr.name +'</a><span>|</span>';
	// 	});
	// 	$footerLinker.html(aLinker);
	// 	$footerLinker.find('span:last').remove();
	// });

	$(".home_main_nav li.lh").hover(function(){
		$(this).find("a .nav-sign").css("visibility","visible");
		$(this).find(".navigation-sub").css("visibility","visible");
		$(this).parents(".main_container").siblings(".nav-bott").css("visibility","visible");
	},function(){
		$(this).find("a .nav-sign").css("visibility","hidden");
		$(this).find(".navigation-sub").css("visibility","hidden");
		$(this).parents(".main_container").siblings(".nav-bott").css("visibility","hidden");
	})
});
