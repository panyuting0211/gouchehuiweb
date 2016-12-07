/**
 * Created by Administrator on 2015/12/16.
 */

;(function(e){
	/* 
	 /*Author：Yu
	 /* Create the defaults once
	 */

	"use strict";
	//上牌地插件 & 底价城市 & 询价提交合集
	e.fn.gbDetail = function(options){
		//初始化颜色
		function initializationColor(obj, colorObjStr){
			var $colorObj = obj.find('.j_' + colorObjStr),
				$colorObjDd = $colorObj.find('dd'),
				$bigPicObj = e('#J_detailPic').find('.j_bigPic'),
				$smallPicList = e('#J_detailPic').find('.j_smallPic').find('li'),
				$curColorObj = $colorObjDd.find('.active'),
				$defaultExColorId,
				$source;
			if($curColorObj.length){
				$defaultExColorId = $curColorObj.data('color');
				$source = e("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>" + $curColorObj.attr('title') + "</em></span>");
				$source.appendTo($colorObjDd);
				//if(colorObjStr == 'excolor'){
				//	e.each($smallPicList, function(i, o){
				//		if(e(o).data('id') == $defaultExColorId) {
                //
				//			$bigPicObj.attr('src', e(o).find('img').attr('src'));
				//		}
				//	});
				//}
			}else{
				$curColorObj = $colorObjDd.find('li:first');
				$curColorObj.addClass('active').siblings().removeClass('active');
				$defaultExColorId = $curColorObj.data('color');
				$source = e("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>" + $curColorObj.attr('title') + "</em></span>");
				$source.appendTo($colorObjDd);
				if(colorObjStr == 'excolor') {
					e.each($smallPicList, function (i, o) {
						if (e(o).data('id') == $defaultExColorId) {
							$bigPicObj.attr('src', e(o).find('img').attr('src'));
						}
					});
				}
			}
			return $defaultExColorId;

		}
		//重置上牌地
		function resetUCC(){
			var _ucpPopCon = $('#J_cardCityCon'), 
			_ucpFilterCondDb = {}, 
			_newUcpStr = '';
			_ucpFilterCondDb.incolor = opt.inColorId;
			_ucpFilterCondDb.excolor = opt.exColorId;
			_ucpFilterCondDb.id = opt.detailId;
			_ucpFilterCondDb.bpcities = opt.bpCities;
			$('#J_cardCityCon').find('.curCardCity').find('a').addClass('active');
			opt.upCardCityID = 0;
			opt.upCardCityName = '\u4e0d\u9650';
			$('#J_cardCity').find('span').text(opt.upCardCityName);
			$('#J_cardCity').attr('data-ucname', opt.upCardCityName);
			$('#J_cardCity').attr('data-ucid', opt.upCardCityID);
			_ucpPopCon.find('.allCardCity').html('<div class="loadingCon"><i></i>正在加载中...</div>');
				$.ajax({
					url: qianzui+"/Car/ajax_shangpai",
					data: _ucpFilterCondDb,
					async: false,
					success: function(a) {
						if(a.uccities && a.uccities.length > 0){
							$.each(a.uccities, function(i, o){
								_newUcpStr += '<a href="javascript:;" title="'+ o +'" data-ucid="'+ o +'" data-ucname="'+ o +'">'+ o +'</a>';
							});
						}else{
							_newUcpStr = "<div class=\"no-city\">\u6682\u65e0\u5176\u4ed6\u4e0a\u724c\u5730\u57ce\u5e02\u007e<\/div>";
						}
						_ucpPopCon.find('.allCardCity').html(_newUcpStr);
						uccFirstFlag = !1;
						bpCitesChangeFlag = !1;
						colorChangeUccFlag = !1;
					},
					error: function() {
						this.errormsg()
					}
				});
		}
		//获取默认底价城市列表
		function getDefaultBpcities(obj){
			var defaultBpcities = obj.find('.j_cities .j_city'),
				defaultBpcitiesArr = [];
			$.each(defaultBpcities, function(i,o){
				defaultBpcitiesArr[i] = $(o).data('cid');
			});
			return defaultBpcitiesArr;
		}
		//获取底价城市列表
		function getBpCities(){
			var db = {},  newCitiesDb = null;
			db['excolor'] = opt.exColorId;
			db['incolor'] = opt.inColorId;
			db['id'] = opt.detailId;
			$.ajax({
				url: qianzui+"/Car/ajax_city_select",
				data: db,
				async: false,
				success: function(a) {
					if(a.status == 2 && a.next) {window.location.href = a.next;}else{
						newCitiesDb = a;
					}
					
				},
				error: function(){
					this.errormsg()
				}
			});
			return newCitiesDb;
		}
		//默认参数
		e.fn.gbDetail.defaults = {
			exColorId: initializationColor(this, 'excolor'),
			inColorId: initializationColor(this, 'incolor'),
			detailId: this.data('id'),
			bpCities: getDefaultBpcities(this),
			upCardCityID: this.find('#J_cardCity').data('ucid'),
			upCardCityName: this.find('#J_cardCity').data('ucname')
		};

		var options = options || {},
			opt = e.extend({}, e.fn.gbDetail.defaults, options),
			gbThis = this,
			bpcFlag = !0,
			uccFirstFlag = !0,
			colorChangeFlag = !1,
			colorChangeUccFlag = !1,
			bpCitesChangeFlag = !1,
			ajaxReturnCities,
			$inquiryContainer = gbThis.find('#J_inquiryFee'),
			$uccContainer = gbThis.find('#J_cardCity'),
			$uccContainerBox = gbThis.find('#J_cardCityCon'),
			$colorContainer = gbThis.find('.j_carColor'),
			_eventType = "click";
		//ajaxReturnCities = {'curCityPrice':{'isBool':0, 'id':15, 'name': "北京"}, 'cities': [{'id': 10, 'city_name':'合肥'},{'id':11, 'city_name': '扬州'}, {'id': 132, 'city_name':'南京'}]}

		ajaxReturnCities = getBpCities();
		$colorContainer.on('click', 'a', function(event){
			var curCityStyle = '',
				_exColorId,
				_inColorId,
				$p = $(this).parent(),
				$citiesContainer = $('.j_cities'),
				$smallPicList = e('#J_detailPic').find('.j_smallPic').find('li'),
				$bigPicObj = e('#J_detailPic').find('.j_bigPic'),
                $selectedColor = $p.find('.j_slcColor'),
                $source = $("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>"+ $(this).attr('title') +"</em></span>");

			$(this).addClass('active').siblings().removeClass('active');
            if($selectedColor.length){
                $selectedColor.find("em").html($(this).attr("title"));
            }
            else {
                $source.appendTo($p);
            };
			if(e(event.delegateTarget).data('type') == 'excolor'){
				e.each($smallPicList, function(i, o){
					if(e(o).data('id') == gbThis.find('.j_excolor').find('.active').data('color')) {
						$bigPicObj.attr('src', e(o).find('img').attr('src'));
					}
				});
			}

			_exColorId = gbThis.find('.j_excolor .active').data('color');
			_inColorId = gbThis.find('.j_incolor .active').data('color');
			if(opt.exColorId != _exColorId || opt.inColorId != _inColorId){
				$citiesContainer.find('span').remove();
				$citiesContainer.find('.loadingCon').length || $citiesContainer.prepend('<span class="loadingCon"><i></i>\u6b63\u5728\u52a0\u8f7d\u4e2d...</span>');
				opt.exColorId = _exColorId;
				opt.inColorId = _inColorId;
				ajaxReturnCities = getBpCities();
				ajaxReturnCities && ajaxReturnCities['curCityPrice'] && ajaxReturnCities['curCityPrice']['isBool'] && (curCityStyle = "j_city");
				$citiesContainer.find('.loadingCon').length && $citiesContainer.find('.loadingCon').remove();
				ajaxReturnCities && ajaxReturnCities['curCityPrice'] && $citiesContainer.prepend('<span class="'+ curCityStyle +'" data-cid="'+ ajaxReturnCities['curCityPrice']['id'] +'">'+ ajaxReturnCities['curCityPrice']['name'] +'</span>');
				if(!ajaxReturnCities['curCityPrice']['isBool']){
					$inquiryContainer.html('0');
				}else{
					$inquiryContainer.html('99.00');
				}
				colorChangeFlag = !0;
				resetUCC();
			}else{
				colorChangeFlag = !1;
			}

		});

		gbThis.on('click', '.j_selectArea', function(){
			var _this = $(this),
				source = $('#pop_bp_Area'),
				newCitiesStr = '',
				$curCitiesBox = _this.closest('.j_cities');
			source.find(".j_popCity").removeClass('active');

			if(bpcFlag || colorChangeFlag){
				bpcFlag = !1;
				colorChangeFlag = !1;
				if(!source.length){
					source = $('<div id="pop_bp_Area"><div class="con"></div><div class="G_tc btn"><a href="javascript:;" class="G_btn_a btn_34px j_areaSaveBtn">\u786e\u5b9a</a><a href="javascript:;" onclick="HUI.PopOut.closeMask();" class="G_btn_c btn_34px">\u53d6\u6d88</a></div></div>');
				}
				if(ajaxReturnCities.cities.length > 0){
					newCitiesStr = '<a href="javascript:;" class="j_popCity " data-cid="001">全国<i></i></a>';
					$.each(ajaxReturnCities.cities, function(i, o){
						var c = '';
						if(o['id'] == ajaxReturnCities.curCityPrice.id && ajaxReturnCities.curCityPrice.isBool){
							c = "active";
						}
						newCitiesStr += '<a href="javascript:;" class="j_popCity '+ c +'" data-cid="'+ o['id'] +'">'+ o['city_name'] +'<i></i></a>';
					});
				}else{
					newCitiesStr = "<div class=\"no-city\">\u6682\u65e0\u5176\u4ed6\u5e95\u4ef7\u57ce\u5e02\u007e</div>"
				}
				source.find('.con').html(newCitiesStr);
			}

			HUI.PopOut.alert(["\u9009\u62e9\u66f4\u591a\u0020\uff08<span class=\"j_limitTip inp_err\">\u6700\u591a\u9009\u62e9\u0033\u4e2a</span>\uff09", source, ""], "m");

			$.each($curCitiesBox.find('.j_city'), function(i, o){
				$.each(source.find(".j_popCity"), function(j, b){
					$(o).data('cid') == $(b).data('cid') && $(b).addClass('active')
				});
			});


			source.find('.j_popCity').off().on("click", function(event){
				event.preventDefault();
				$(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
			});

			source.find('.j_areaSaveBtn').off().on('click', function(){
				var i = 0,
					t = null,
					_popCurCitiesArr = [],
					_bol = !0,
					_allBol,
					$maxNumTip = $(this).closest(".mask-body").find(".j_limitTip"),
					$bpCitiesBox = gbThis.find('.j_cities');
				clearInterval(t);
				if(source.find('.active').length > 3){
					t = setInterval(function(){
						i++;
						$maxNumTip.fadeOut(100).fadeIn(100);
						i >= 2 && clearInterval(t);
					}, 200);
					return false;
				}

				$bpCitiesBox.find('span').remove();
				$.each(source.find('.active'), function(i,o){
					_popCurCitiesArr[i] = $(o).data('cid');
					$bpCitiesBox.prepend('<span class="j_city" data-cid="'+ $(o).data('cid') +'">'+ $(o).text() +'</span>');
				});
				if(opt.bpCities.length != source.find('.active').length){
					resetUCC();
				}else{
					$.each(_popCurCitiesArr.sort(), function(i, o){
						$.each(opt.bpCities.sort(), function(j, b){
							_allBol = _bol && o == b;
						});
					});
					if(!_allBol){
						resetUCC();
					};
				}
				//$.each(source.find('.active'), function(i,o){
				//    opt.bpCities[i] = $(o).data('cid');
				//    $bpCitiesBox.prepend('<span class="j_city" data-cid="'+ $(o).data('cid') +'">'+ $(o).text() +'</span>');
				//});
				opt.bpCities = _popCurCitiesArr;
				HUI.PopOut.closeMask();
				$('#J_inquiryFee').empty().addClass('loading');
				$.ajax({
					url: qianzui+"/Car/ajax_price",
					data: {cities: opt.bpCities},
					success: function(a) {
						if(a['newPrice'] && a['newPrice'] != ''){
							$inquiryContainer.removeClass('loading').html(a.newPrice);
						}else{
							$inquiryContainer.removeClass('loading').html('0');
						}
					},
					error: function() {
						this.errormsg()
					}
				})
			});
		});

//上牌地

		// $(document).click(function(e) {
		// if(!(e.target == $uccContainer[0] || $.contains($uccContainer[0], e.target))) {
		// 		$uccContainerBox.hide();
		// 	}
		// });

		$uccContainer.on('click', function(){
			var _ucpPopCon = $('#J_cardCityCon');
			$(this).hasClass('card_city_active') ? $(this).removeClass('card_city_active') : $(this).addClass('card_city_active');
			_ucpPopCon.toggle();

			
			if(uccFirstFlag){
				resetUCC();
			}
		});

		gbThis.find('#J_cardCityCon').on('click', 'a', function(){
			$('#J_cardCityCon').find('a').removeClass('active');
			$(this).addClass('active');
			var ucname = $(this).data('ucname'),
				ucid = $(this).data('ucid'),
				curUcCard = $('#J_cardCity');
			curUcCard.find('span').text(ucname);
			curUcCard.attr('data-ucname', ucname);
			curUcCard.attr('data-ucid', ucid);
			opt.upCardCityID = ucid;
			opt.upCardCityName = ucname;
			curUcCard.trigger('click');
		});
		//提交询价
		//生成支付弹窗
		//$('<div class="pay_pop" id="J_payOrder"><form action="'+qianzui+'/Pay/index" method="POST"><h3>\u652f\u4ed8\u91d1\u989d\uff1a\u8be2\u4ef7\u8d39<span class="inp_err">'+ t +'\u5143</span><input name="pay_id" type="hidden" value="'+ p +'"></h3><div class="pay_container"><div class="pay_nav clearfix j_payNav"><a href="#" class="active">第三方支付</a><a href="#">银行卡支付</a></div><div class="pav_con j_payCon"><ul class="hide j_payItem" data-paynumber="1" style="display:block;"><li><input type="radio" id="alipay" checked="checked"  value="alipay" name="defaultbank" class="alipay"><label for="alipay"><img src="/Public/Home/images/zhifubao.jpg" alt="支付宝支付"></label></li><li><input type="radio" id="wechat"  value="weixin" name="defaultbank" class="alipay"><label for="wechat"><img src="/Public/Home/images/icon_wechat.jpg" alt="微信支付"></label></li></ul><ul class="hide j_payItem" data-paynumber="2"><li><input type="radio" id="gonghang" value="ICBCB2C" name="defaultbank"><label for="gonghang"><img src="/Public/Home/images/bank_1.jpg" alt="工行支付"></label></li><li><input type="radio" id="nonghang" value="ABC" name="defaultbank"><label for="nonghang"><img src="/Public/Home/images/bank_2.jpg" alt="农行支付"></label></li><li><input type="radio" id="jianhang" value="CCB" name="defaultbank"><label for="jianhang"><img src="/Public/Home/images/bank_3.jpg" alt="建行支付"></label></li><li><input type="radio" id="shanghaipudongfazhan" value="SPDB" name="defaultbank"><label for="shanghaipudongfazhan"><img src="/Public/Home/images/bank_4.jpg" alt="浦东发展银行支付"></label></li><li><input type="radio" id="zhonghang" value="BOCB2C" name="defaultbank"><label for="zhonghang"><img src="/Public/Home/images/bank_5.jpg" alt="中行支付"></label></li><li><input type="radio" id="zhaohang" value="CMB" name="defaultbank"><label for="zhaohang"><img src="/Public/Home/images/bank_6.jpg" alt="招行支付"></label></li><li><input type="radio" id="xingyeyinhang" value="CIB" name="defaultbank"><label for="xingyeyinhang"><img src="/Public/Home/images/bank_7.jpg" alt="兴业银行支付"></label></li><li><input type="radio" id="guangfayinghang" value="GDB" name="defaultbank"><label for="guangfayinghang"><img src="/Public/Home/images/bank_8.jpg" alt="广发银行支付"></label></li><li><input type="radio" id="hangzhouyinhang" value="HZCBB2C" name="defaultbank"><label for="hangzhouyinhang"><img src="/Public/Home/images/bank_9.jpg" alt="杭州银行支付"></label></li><li><input type="radio" id="fudianyinhang" value="FDB" name="defaultbank"><label for="fudianyinhang"><img src="/Public/Home/images/bank_10.jpg" alt="富滇银行支付"></label></li><li><input type="radio" id="shanghaiyinhang" value="SHBANK" name="defaultbank"><label for="shanghaiyinhang"><img src="/Public/Home/images/bank_11.jpg" alt="上海银行支付"></label></li><li><input type="radio" id="ningboyinhang" value="NBBANK" name="defaultbank"><label for="ningboyinhang"><img src="/Public/Home/images/bank_12.jpg" alt="宁波银行支付"></label></li><li><input type="radio" id="pinganyinhang" value="SPABANK" name="defaultbank"><label for="pinganyinhang"><img src="/Public/Home/images/bank_13.jpg" alt="平安银行支付"></label></li><li><input type="radio" id="zhongguoyouzhen" value="POSTGC" name="defaultbank"><label for="zhongguoyouzhen"><img src="/Public/Home/images/bank_14.jpg" alt="中国邮政支付"></label></li></ul></div><div class="pay_btn"><input type="submit" class="btn_100P G_btn_a" value="立即支付"><input type="hidden" name="pay_number" value="1"><input name="id" id="id-value" type="hidden" value="113"></div></div></form></div>');
		function p(t, p){
			var souce = $("#J_payOrder");
			souce.length || (souce = $('<div class="pay_pop" id="J_payOrder"><form action="'+qianzui+'/Pay/index" method="POST"><h3>\u652f\u4ed8\u91d1\u989d\uff1a\u8be2\u4ef7\u8d39<span class="inp_err">'+ t +'\u5143</span><input name="pay_id" type="hidden" value="'+ p +'"></h3><div class="pay_container"><div class="pay_nav clearfix j_payNav"><a href="#" class="active">第三方支付</a></div><div class="pav_con j_payCon"><ul class="hide j_payItem" data-paynumber="1" style="display:block;"><li><input type="radio" id="alipay" checked="checked"  value="alipay" name="defaultbank" class="alipay"><label for="alipay"><img src="/Public/Home/images/zhifubao.jpg" alt="支付宝支付"></label></li><li><input type="radio" id="wechat"  value="weixin" name="defaultbank" class="alipay"><label for="wechat"><img src="/Public/Home/images/icon_wechat.jpg" alt="微信支付"></label></li></ul></div><div class="pay_btn"><input type="submit" class="btn_100P G_btn_a" value="立即支付"><input type="hidden" name="pay_number" value="1"><input name="id" id="id-value" type="hidden" value="113"></div></div></form></div>'));
			HUI.PopOut.alert(["\u8bf7\u9009\u62e9\u652f\u4ed8\u65b9\u5f0f\u007e", souce, ""], 3);
			$('.j_payNav a').on('click', function(event){
				event.preventDefault();
				var i = $(this).index();
				$(this).addClass('active').siblings().removeClass('active');
				$('.j_payCon ul').hide().eq(i).show();
			});
			souce.find("input[name=defaultbank]").on("click", function(event){
					var _this = $(this),
						$pay = _this.closest(".j_payItem"),
						payNum = parseInt($pay.data("paynumber"));
					$("input[name=pay_number]").val(payNum);
				});
		}
		//提交数据获取生成的价格
		function getPrice(dataDb){
			$.ajax({
				url: qianzui+"/Car/ajax_xunjia/",
				data: dataDb,
				success: function(a){
					if(a.status == 1){
						dataDb.upCardCity == 0 ? p(a.price,a.pay_id) : uccTips(dataDb, a.price, a.pay_id);
					}else if(a.status == 2){
						window.location.href=a.url;
					}else{
						HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>'+ a.info +'</h3></div>'), $({}).delay(2e3).queue(function() {
							HUI.PopOut.closeMask()
						}));
					}
				},
				error: function() {
					this.errormsg()
				}
			});
		}
		//生成城市是否可以上牌提示
		function uccTips(dataDb, price, payId){
			var souce = $('<div id="J_uccTipsPop" class="ucctips_pop"><div class="con"><h3>你选择的上牌地为：<span class="j_ucCity yes">'+ dataDb.upCardCityName +'</span></h3><p>购车惠为您找到的底价信息并不能全部满足可在'+dataDb.upCardCityName+'上牌</p><ul class="tips"></ul><p>对于不满足上牌地的底价，在底价购车时，上牌地无法选择'+dataDb.upCardCityName+'，如果您只是想了解底价信息，您可以选择继续询价。</p><div class="G_tc btn"><a href="javascript:;" onclick="HUI.PopOut.closeMask();" class="G_btn_c btn_34px">重新选择</a><a href="javascript:;" class="G_btn_a btn_34px j_SaveBtn">继续询价</a></div></div></div>');
			var cityTip ='';
			$.ajax({
				url: qianzui+"/Car/ajax_xunjia_tixing",
				data: dataDb,
				async: false,
				success: function(a) {
					if(a.status == 1){
						$.each(a.citiesList, function(i, o){
							if(o.isYes == 1){
								cityTip += '<li class="tit"><span>'+ o.cityName +'底价</span></li><li class="yes"><span>可以在'+ dataDb.upCardCityName +'上牌</span></li>';
							}else{
								cityTip += '<li class="tit"><span>'+ o.cityName +'底价</span></li><li><span>不能在'+ dataDb.upCardCityName +'上牌</span></li>';
							}
						});
					}
				},
				error: function() {
					this.errormsg()
				}
			});
			souce.find('.tips').html(cityTip);
			HUI.PopOut.alert(["\u8bf7\u9009\u62e9\u652f\u4ed8\u65b9\u5f0f\u007e", souce, ""], 3);
			souce.on('click','.j_SaveBtn', function(){
				p(price, payId);
			});
		}
		//询价
		function n(event) {
			var n = e(this),
				excolorFlag = !0,
				incolorFlag = !0,
				bpcitiesFlag = !1,
				curWrap = gbThis,
				dataObj = {};
			event.stopPropagation();
			event.preventDefault();
			var getCookieVisited = $.Bom.getCookie("visited");

			//if(!getCookieVisited){
			//	HUI.PopOut.register();
			//	return false;
			if(!e.G.getUSERID()){
				HUI.PopOut.register();
				return false;
			}else{
				opt.bpCities = getDefaultBpcities(gbThis);
				dataObj.car = curWrap.find(".car_type").val();
				if(curWrap.find('.j_excolor a.active').length < 1){
					HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>\u8bf7\u9009\u62e9\u5916\u89c2\u989c\u8272\u007e</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
					return false;
				}
				if(curWrap.find('.j_incolor a.active').length < 1){
					HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>\u8bf7\u9009\u62e9\u5185\u9970\u989c\u8272\u007e</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
					return false;
				}
				if(opt.bpCities.length < 1){
					HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>\u5e95\u4ef7\u57ce\u5e02\u4e0d\u80fd\u4e3a\u7a7a\u007e</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
					return false;
				}
				dataObj.excolor = opt.exColorId;
				dataObj.incolor = opt.inColorId;
				dataObj.bpcities = opt.bpCities;
				dataObj.price = curWrap.find('#J_inquiryFee').text();
				dataObj.upCardCity = opt.upCardCityID;
				dataObj.upCardCityName = opt.upCardCityName;

				getPrice(dataObj);
			}
		}
		gbThis.on('click', '.j_inquiryBtn', n);
	}

})(jQuery);

$(document).ready(function(){
	$("#J_carDetail").gbDetail();
	$("#J_detailQuestion").setAjaxComment();
	$("#J_commentContainer").getAjaxComment();
	$(".j_attention").Attention();
	$('#J_detail_pos').scrollPos();
	//详情页图片切换
	;(function(e){
		var $detailPic = e('#J_detailPic'),
			$bigPic = $detailPic.find('.j_bigPic'),
			$smallPic = $detailPic.find('.j_smallPic');

		$smallPic.slide({mainCell:".bd ul",prevCell:".prev",nextCell:".next", autoPage:true,effect:"left",scroll:3,vis:3,pnLoop:false});
		$smallPic.on('hover', 'li', function(){
			e(this).addClass('active').siblings().removeClass('active');
			$bigPic.attr('src', e(this).find('img').attr('src'));
		});
	})(jQuery);

	/* 外层tab切换 */
	;(function(e){
		e("#J_CarImg").slide({titCell:"#J_carImg-nav li",mainCell:"#J_carImg-bd .bd",effect:"top"});
		e(".J_carItemSlider").slide({mainCell:".bd-con ul",autoPage:true,effect:"top",prevCell:".btn-up",nextCell:".btn-down",trigger:"click",vis:4});

		e(".J_carItemSlider-nav li").click(function(){
			var me = $(this),
				parentObj = me.closest(".J_carItemSlider"),
				index = me.index(),
				minImgObj = parentObj.find(".J_carItemSlider-nav li"),
				bigImgObj = parentObj.find(".J_carItemSlider-bd li"),
				curBigImg = bigImgObj.eq(index);
			if(minImgObj.length != bigImgObj.length){
				alert("小图和大图数量不一致！");
				return;
			}
			me.siblings().removeClass("on");
			me.addClass("on");
			bigImgObj.hide();
			curBigImg.show();
		});
	})(jQuery);

	//车款切换
	$( '.car_type').on('change', function(){
		window.location.href = $(this).find("option:selected").data('link');
	});

	//车型图片
	(function(){

		var $up = $(".smallpic .up"),
			$ul = $(".smallpic ul"),
			$smallpic = $(".smallpic ul li a img"),
			$datu = $("#J_datu");

		$up.click(function(){
			$ul.top = $ul.offsetTop - 80 + 'px';
		});

		$smallpic.click(function(){
			var href = $(this).attr("src");
			$(this).parent().addClass("selected").siblings().removeClass("active");
			$datu.attr("src",href);
		});
	})();

});









