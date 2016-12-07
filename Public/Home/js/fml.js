(function(a, b) {
	function q() {
		var a = r(),
			b = a.src;
		return k[b]
	}
	function r() {
		if (m) return m;
		if (n && n.readyState === "interactive") return n;
		var a = o.getElementsByTagName("script");
		for (var b = 0; b < a.length; b++) {
			var c = a[b];
			if (c.readyState === "interactive") return n = c, c
		}
	}
	function s(a) {
		return Object.prototype.toString.call(a) == "[object Array]"
	}
	function t(a) {
		for (var b in a)"defer" == b ? h = a[b] : c[b] = a[b]
	}
	function u(a, b) {
		var e, g = v(a);
		if (d.hasOwnProperty(g)) {
			var m = d[g];
			b && b.call(m, m);
			return
		}
		var n = "",
			o, p = c.modulebase;
		if (o = a.indexOf(":")) n = a.substr(0, o), a = a.substr(o + 1), c.nsmap && c.nsmap.hasOwnProperty(n) && (p = "http://" + c.nsmap[n].host);
		h ? j.hasOwnProperty(a) || (j[a] = !0, i.push(a)) : (e = p + a + ".js?" + c.sversion, k[e] = g, A(e, function() {
			if (!l) return;
			l.id = g, l = null
		})), b && (f.hasOwnProperty(g) || (f[g] = []), f[g].push(b));
		return
	}
	function v(a) {
		if (!a) return "";
		var b = c.localNS + ":";
		return 0 === a.indexOf(b) && (a = a.substr(b.length)), a
	}
	function w(a) {
		return d[a]
	}
	function x(a, b, e) {
		function i() {
			a || g ? h() : window.setTimeout(h, 0)
		}
		switch (arguments.length) {
		case 3:
			s(b) || (b = b ? [b] : []);
			break;
		case 2:
			e = b, "string" == typeof a ? b = y(e) : (b = a, a = "");
			break;
		case 1:
			e = a, a = "", b = y(e)
		}
		a = v(a);
		if (d.hasOwnProperty(a)) {
			console.log(a + " dumb define");
			return
		}
		if (!a && document.attachEvent && !~p.indexOf("Opera")) var g = q();
		var h = function() {
				a || (a = g || h.id);
				if (a && d.hasOwnProperty(a)) return;
				var b = {},
					c = e(w, b);
				c && (b = c), z(a, b)
			};
		!a && !g && (l = h);
		if (b.length) {
			var j = null,
				k = 0,
				m = b.length;
			for (var n = 0; n < m; n++) {
				var o = b[n];
				if (d.hasOwnProperty(o)) {
					k++;
					continue
				}
				if ("~" == o.substr(0, 1)) {
					var r = o.substr(1);
					"/" != r.substr(0, 1) && "http://" != r.substr(0, 7) && (r = c.modulebase + r), A(r, function() {
						z(o, !0)
					})
				} else u(o, null);
				f[o] || (f[o] = []), f[o].push(function() {
					for (var a = 0, c = b.length; a < c; a++) if (!d.hasOwnProperty(b[a])) return;
					h()
				})
			}
			k >= m && i()
		} else i()
	}
	function y(a) {
		depencies = [];
		var b = /require\((['"])([\w\:\.\/\_\-]+)\1\)/g,
			c = a.toString().replace(/\/\*.*\*\//m, "").replace(/\/\/.*\n/g, ""),
			d;
		while (d = b.exec(c)) d = d[2], depencies.push(d);
		return depencies
	}
	function z(a, b) {
		d[a] = b, c.hasOwnProperty("enableShort") && (d[C(a)] = b);
		var e = f[a];
		if (e) {
			f[a] = [];
			for (var g = 0, h = e.length; g < h; g++) e[g].call(b, b)
		}
	}
	function A(a, b) {
		var c, d = function(b) {
				g[a].state = "loaded";
				var c;
				while (c = g[a].onload.shift()) c(w)
			};
		if (!g.hasOwnProperty(a)) g[a] = {
			state: "loading",
			onload: []
		}, b && g[a].onload.push(b), B(a, d);
		else if (b) {
			c = g[a];
			switch (c.state) {
			case "loading":
				g[a].onload.push(b);
				break;
			case "loaded":
				b(w)
			}
		}
	}
	function B(a, b, c) {
		var d = document.createElement("script");
		d.defer = "true", d.async = "async", d.type = "text/javascript", b && (d.onerror = d.onload = d.onreadystatechange = function() {
			var a = this.readyState;
			if (!a || "loaded" == a || "complete" == a) b(), o.removeChild(d)
		}), d.src = a, m = d, o.appendChild(d), m = null
	}
	function C(a) {
		var b = a.lastIndexOf("/");
		return b && (a = a.substr(b + 1)), a
	}
	function D(a, b) {
		var c = a.length,
			d = 0,
			e = {};
		for (var f = 0; f < c; f++) {
			var g = a[f];
			u(g, function(a) {
				return function(f) {
					d++, e[C(a)] = f, d >= c && b.call(e, e)
				}
			}(g))
		}
	}
	function F(a, b) {
		a in E || (E[a] = []), b && typeof b == "function" && E[a].push(b)
	}
	function G(a, b, c) {
		if (a in E) {
			var d = E[a];
			if (!d || !d.length) return;
			for (var e = 0, f = d.length; e < f; e++) d[e](b);
			return c && delete E[a], !0
		}
		return
	}
	function H(a, b) {
		var c = document.createElement("link");
		c.setAttribute("rel", "stylesheet"), c.setAttribute("rev", "stylesheet"), c.setAttribute("href", a), o.appendChild(c);
		var d = document.createElement("img");
		d.onerror = function() {
			b && b()
		}, d.src = a
	}
	window.console || (window.console = {
		log: function() {}
	});
	var c = {
		sversion: 1,
		deferHost: "/",
		localNS: "",
		modulebase: "/",
		enableShort: !0
	},
		d = {},
		e = Array.prototype,
		f = {},
		g = {},
		h = !1,
		i = [],
		j = {},
		k = {},
		l, m, n, o = document.head || document.getElementsByTagName("head")[0] || document.documentElement,
		p = navigator.userAgent,
		E = {};
	a.fml = {
		version: .8,
		vars: {},
		eventProxy: F,
		fireProxy: G,
		on: F,
		emit: G,
		debug: function() {
			window.console && window.console.log.apply && window.console.log.apply(console, arguments)
		},
		getOption: function(a) {
			return c[a]
		},
		setOptions: t,
		use: function(a, b) {
			s(a) ? D(a, b) : u(a, b)
		},
		define: x,
		iLoad: function() {
			if (!h) return;
			h = !1;
			var a = i.length;
			for (var b = 0; b < a; b++) u(i[b]);
			i = [], i = {}
		},
		loadScript: A,
		loadCss: H,
		alias: function() {
			return this
		}
	}
})(this);