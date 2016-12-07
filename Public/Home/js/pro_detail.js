/**
 * Created by Administrator on 2015/12/16.
 */
$(function(){
	$('.j_pos').on('click', function(event){
		event.preventDefault();
		event.stopPropagation();
		var $this = $(this),
			_href = $this.attr('href'),
			$box = $('#J_' + _href),
			_top = $box.offset().top;
			$('html, body').animate({
				scrollTop: _top
			}, 500);
		$this.addClass("active").siblings("a").removeClass("active");
	});
	
	
	$(window).scroll(function(){
		var a = $(".car_model_pic_box").offset().top,
		    b = $(".car_model_pic_box").height(),
		    c = a + b,
		    d = $(window).scrollTop(),
		    e = d - a;
        if( e > 0 && e < b){
            $(".carimg-right").css("top",e);
        }
    }); 

	
})

$(function(){
          getList(1);
    });
function getList(page) {
    $("#hotList .carbox-le-con > ul").css('display','none');
    $('#hotList .carbox-le-con > ul').empty();
    $("#hotList").prepend('<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>');
    
    $.post( "/index.php/Car/bestselling",
        	{},
            function(data,textStatus){
                $('#hotList .carbox-le-con > ul').empty();
                $("#hotList .carbox-le-con > ul").show();
                $('#hotList').find('.onload').remove();
                
				var infosale = data.sales;
                var l1div = "<div class='carbox-le-head G_f18'><i class='G_f14 G_fl'>省</i>"+infosale.discount+"万</div>";
                var l1a = "<a href='"+infosale.url+"' title='"+infosale.brand_name+'  '+infosale.car_model_name+'  '+infosale.car_name+"'><img src='"+infosale.car_model_imageurl+"'/></a>";
                var l1h3 = "<h3 class='G_f14'><a href='"+infosale.url+"' title='"+infosale.brand_name+'  '+infosale.car_model_name+'  '+infosale.car_name+"'>"+infosale.brand_name+'  '+infosale.car_model_name+"</a></h3>";
                var lip = "<p class='G_f14'>购车惠底价：<span class='G_f20'>"+infosale.low_price+"万</span></p>";
                var l1a2 = "<a href='"+infosale.url+"' class='carbox-le-link G_f12'>立即查看</a>";
                  
                $('#hotList .carbox-ac > ul').append('<li>'+l1div+l1a+l1h3+lip+l1a2+'</li>');
				
				var infohot = data.hot_model;
				$.each(infohot,function(key,val){
	                var l2a = "<a href='"+val.url+"' title='"+val.brand_name+'  '+val.car_model_name+'  '+val.car_name+"'><img src='"+val.car_model_imageurl+"'/></a>";
	                var l2h = "<h3 class='G_f14'><a href='"+val.url+"' title='"+val.brand_name+'  '+val.car_model_name+'  '+val.car_name+"'>"+val.brand_name+'  '+val.car_model_name+"</a></h3>";
	                var l2p = "<p class='G_f14'>官方指导价：<span style='text-decoration:line-through;color:#666666'>"+val.auth_price+"万</span></p>";
	                var l2a2 = "<a href='"+val.url+"' class='carbox-le-link G_f12'>查询底价</a>";
	                
	                  
	                $('#hotList .carbox-hot > ul').append('<li>'+l2a+l2h+l2p+l2a2+'</li>');
	           });
	            
		    },
            "json");
  }


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
			if(souce.length){
				souce.find('.j_xunjiaPriceBox').html(t);
			}else{
				souce = $('<div class="pay_pop" id="J_payOrder"><form action="'+qianzui+'/Pay/index" method="POST"><h3>\u652f\u4ed8\u91d1\u989d\uff1a\u8be2\u4ef7\u8d39<span class="inp_err"><span class="j_xunjiaPriceBox">'+ t +'</span>\u5143</span><input name="pay_id" type="hidden" value="'+ p +'"></h3><div class="pay_container"><div class="pay_nav clearfix j_payNav"><a href="#" class="active">第三方支付</a></div><div class="pav_con j_payCon"><ul class="hide j_payItem" data-paynumber="1" style="display:block;"><li><input type="radio" id="alipay" checked="checked"  value="alipay" name="defaultbank" class="alipay"><label for="alipay"><img src="/Public/Home/images/zhifubao.jpg" alt="支付宝支付"></label></li><li><input type="radio" id="wechat"  value="weixin" name="defaultbank" class="alipay"><label for="wechat"><img src="/Public/Home/images/icon_wechat.jpg" alt="微信支付"></label></li></ul></div><div class="pay_btn"><input type="submit" class="btn_100P G_btn_a" value="立即支付"><input type="hidden" name="pay_number" value="1"><input name="id" id="id-value" type="hidden" value="113"></div></div></form></div>')
			}

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
						dataDb.upCardCity == 0 ? p(a.price, a.pay_id) : uccTips(dataDb, a.price, a.pay_id);
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


//一键询价方式
$(function(){
	$("#J_FreeInquiryBtn").on("click",function(e){
		e.preventDefault();
		e.stopPropagation();
		var md5code = "";
		var popCon ="";
		popCon = '<div class="freePop">'+
				 '<form class="j_freePop" action="#" novalidate="novalidate">'+
				 '<ul>'+
				 '<li class="clearfix">'+
				 '<dl>'+
				 '<dt class="G_fl"><i>*</i>订车人</dt>'+
				 '<dd class="G_fl">'+
				 '<input type="text" name="name">'+
				 '<label for="name" class="error"></label>'+
				 '</dd>'+
				 '</dl>'+
				 '</li>'+
				 '<li class="clearfix">'+
				 '<dl>'+
				 '<dt class="G_fl"><i>*</i>手机号</dt>'+
				 '<dd class="G_fl">'+
				 '<input type="text" name="tel" class="j_tel">'+
				 '<label for="tel" class="error"></label>'+
				 '</dd>'+
				 '</dl>'+
				 '</li>'+
				 '<li class="clearfix">'+
				 '<dl>'+
				 '<dt class="G_fl"><i>*</i>验证码</dt>'+
				 '<dd class="G_fl">'+
				 '<input type="text" name="code" class="codeInput">'+
				 '<a href="javascript:;" class="pwd-send" id="J_CreateMobileCode">获取验证码</a>'+
				 '<label for="code" class="error"></label>'+
				 '</dd>'+
				 '</dl>'+
				 '</li>'+
				 '<li class="clearfix">'+
				 '<dl>'+
				 '<dt class="G_fl">备注</dt>'+
				 '<dd class="G_fl">'+
				 '<textarea name="" rows="" cols=""></textarea>'+
				 '</dd>'+
				 '</dl>'+
				 '</li>'+
				 '</ul>'+
				 '<div class="freePopBox G_f14">'+
				 '<input type="submit" class="freebtn-submit j_freeSubmitBtn" value="提交">'+
				 '</div>'+
				 '</form>'+
				 '</div>';
		var firstlevel = layer.open({
			type: 1,
			area: ['550px', '460px'],
			zIndex: 9996,
			title: "免费询价",
			closeBtn: 1,
			move: false,
			shadeClose: false,
			skin: 'addBuyCar_box',
			content: popCon,
			success: function(){
				//  用户登录就获取用户的手机号
				USER && USER.tel && ($(".freePop").find('input[name=tel]').val(USER.tel), $(".freePop").find('input[name=tel]').attr('disabled', 'true'));
				//	点击提交
				$(".j_freeSubmitBtn").on("click",function(){
					//	jquery.validate.js插件验证提交的信息
					jQuery.validator.addMethod("isName", function(value, element) {
                        var length = value.length;
                        return this.optional(element) || (length > 0 && length < 11);
                    }, "联系人由1-10个数字、字符、下划线、中文组成");
                    
                    jQuery.validator.addMethod("isMobile", function(value, element) {
                        var length = value.length;
                        var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
                        return this.optional(element) || (length == 11 && mobile.test(value));
                    }, "请正确填写手机号");
                    
                    $(".freePop").find(".j_freePop").validate({
                            debug: false, //调试模式取消submit的默认提交功能
                            rules:{
                                name:{
                                    required:true,
                                    isName : true
                                },
                                tel:{
                                    required:true,
                                    isMobile : true
                                },
                                code:{
                                    required:true
                                }
                            },
                            messages:{
                                name:{
                                    required:"姓名必填"
                                },
                                tel:{
                                    required:"手机号必填"
                                },
                                code:{
                                    required:"验证码必填"
                                }
                            },submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                                var $form = $(form);
                                var cityLength = $(".j_cities").find(".j_city").length;
								var spanValues=[];    //询价地区是个数组
								for (var i =0;i < cityLength;i++) {
									var spanValue = $(".j_cities").find(".j_city").eq(i).data("cid");
									spanValues.push(spanValue);
								}
                                var laySec = layer.load();
                                $.ajax({
                                    type: "POST",
                                    url: qianzui + "/Car/free_xunjia",
                                    dataType: "json",
                                    data: {
                                    	car:$(".carinfo_wrap dl.name select").val(),
                                    	excolor:$(".carinfo_wrap .j_excolor a.active").data("color"),
                                    	incolor:$(".carinfo_wrap .j_incolor a.active").data("color"),
										bpcities:spanValues,
                                    	price:0,
                                    	upCardCity:$(".carinfo_wrap dl.upCardCity dd a").data("ucid"),
                                    	upCardCityName:$(".carinfo_wrap dl.upCardCity dd a").data("ucname"),
                                    	buyer_name:$form.find('[name=name]').val(),
                                    	buyer_tel:$form.find("[name=tel]").val(),
                                    	code:$form.find('[name=code]').val(),
                                    	user_remark:$form.find('textarea').val(),
                                    	md5code:md5code
                                    },
                                    success:function(data){
                                    	layer.close(laySec);
                                        if(data.status === 1){
                                            var sucCon ="";
											sucCon = '<div class="freeSuccess">'+
													 '<dl>'+
													 '<dt class="G_f16"><span><img src="/Public/Home/images/orderstatus_01.png"/></span>信息已提交</dt>'+
													 '<dd class="G_f16">稍后会有购车惠专属客服与您联系</dd>'+
													 '</dl>'+
													 '<p><a href="javascript:;" class="j_freeKnow">我知道了</a></p>'+
													 '</div>';
											var secondlevel = layer.open({
												type: 1,
												area: ['320px', '200px'],
												zIndex: 9998,
												title: "提交成功",
												closeBtn: 0,
												move: false,
												shadeClose: false,
												skin: 'addBuyCar_box',
												content: sucCon,
												success:function(){
													$(".j_freeKnow").on("click",function(e){
														layer.close(secondlevel);
														layer.close(firstlevel);
														history.go(0);
													});
												}
											});
                                            
                                        }else{
                                            layer.msg(data.msg);
                                        }
                                    }
                                });

                            }
                        });

				});
				
				//	发送验证码之后的60s倒计时
				function intervalTime(num) {
                    var num = parseInt(num);
                    var i = 0;
                    $("#J_CreateMobileCode").removeClass("pwd-send");
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
                            $("#J_CreateMobileCode").addClass("pwd-send");
                            $("#J_CreateMobileCode").html("重新发送").bind("click", sendCode);
                        }
                    }, 1000);
                }
				
				//   调用接口请求验证码
				function sendCode() {
					var telVal = $(".freePop").find('.j_tel').val();
					var reg = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
                    if (telVal == '') {
                        layer.msg("手机号不能为空！");
                        return false;
                    }else if(!reg.test(telVal)){
                    	layer.msg("手机号格式不对！");
                        return false;
                    }

                    var postData = {
                        mobile: telVal,
                        tpl_id:5774
                    }

                    $.ajax({
                        type: "POST",
//                      url: "/member.php/Public/note",
						url:qianzui+"/Api/sendMessages",
                        data: postData,
                        success: function(data) {
                            if (data.code == 0) {
                            	md5code = data.md5code;
                                $("#J_CreateMobileCode").unbind("click");
                                intervalTime(60);
                            }else if(data.code == 205405){
                                layer.msg("号码异常/同一号码发送次数过于频繁");
                            }else{
                                layer.msg(data.msg);
                            }
                        }
                    });
                }
				
				//	给发送验证码绑定事件
				$('#J_CreateMobileCode').bind("click", sendCode);
				
			}
		});

	});
	
	
});
