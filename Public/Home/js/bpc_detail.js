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
	$('.j_popupWrapper').on('click', function(e){
		var $con = $(this).closest('.popup-wrapper').find('.popup-content');
		e.stopPropagation();
		e.preventDefault();
		if($con.is(':hidden')){
			$con.show();
		}else{
			$con.hide();
		}

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

	var $car = $('#J_carDetail');

	$("#J_detailQuestion").setAjaxComment(); //评论
	$("#J_commentContainer").getAjaxComment(); //获取评论列表
	$(".j_attention").Attention(); //关注
	$('#J_detail_pos').scrollPos(); //滚动定位
	// 车款切换
	(function(car){
		car.on('change', '.car_type', function(event){
			window.location.href = $(this).find("option:selected").data('link');
		});
	})($car);

	/* 外层tab切换 */
	(function(e){
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



	(function($, car){
	var sourceDb = {},
		uccFlag = !0,
		isTrendChart = !1,
		$detailPic =  $('#J_detailPic'),
		$smallPicList = $detailPic.find('.j_smallPic').find('li'),
		$bigPicObj = $detailPic.find('.j_bigPic'),
		_excolor = car.find('.j_excolor').find('.active').data('color'),
		defalut4sName = $.trim(car.data("name4s")),
		curCityName = $.trim($("#J_switchCity").text()),
		_incolor = car.find('.j_incolor').find('.active').data('color'),
		_incolorName = car.find('.j_incolor').find('.active').attr("title"),
		_excolorName = car.find('.j_excolor').find('.active').attr("title"),
		_carid = $('#car_id').val();
	sourceDb.excolor = _excolor; //外饰颜色
	sourceDb.incolor = _incolor; //内饰颜色
	sourceDb.bp = $('.j_gfPrice').data('price'); //底价
	sourceDb.car_id = _carid; //车款
	sourceDb.car_price_id = car.data("carpid"); //车款
	sourceDb.ucc_id = 0;//上牌地id
	sourceDb.ucc_name = "不限";//上牌地名称
	sourceDb.deposit = parseInt($('#J_deposit').text());//定金
	// 基于准备好的dom，初始化echarts实例

	var $j_trendChartTableWrapper = car.find('.j_trendChartTableWrapper'),
		myChart = null,
		$trendChartBtn = car.find('.j_trendChartBtn');
	if(document.getElementById('J_TrendChartTable')){
		myChart = echarts.init(document.getElementById('J_TrendChartTable'));
	}
	// 指定图表的配置项和数据
	$trendChartBtn.on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		if(isTrendChart){
			$j_trendChartTableWrapper.is(":hidden") ? $j_trendChartTableWrapper.show() : $j_trendChartTableWrapper.hide();
		}else{
			$j_trendChartTableWrapper.show();
			myChart.showLoading();
			$.post("/index.php/car/get_car_trend", {car_id: _carid, exterior_color_id: _excolor, interior_color_id: _incolor},function(db){
				myChart.hideLoading();
				isTrendChart = !0;
				// 使用刚指定的配置项和数据显示图表。
				myChart.setOption({
					title: {
						text: '价格走势',
						subtext: '外观：'+ _excolorName + " " + "内观：" + _incolorName
					},
					tooltip: {
						trigger: 'axis'
					},
					color: [
						'#fa8c35', '#299ffb', '#000'
					],
					legend: {
						show: true,
						//padding: 20,
						//itemGap: 30,
						data:[
							{name:'购车惠', textStyle:{color:"#fa8c35"}},
							{name:'市场', textStyle:{color:"#299ffb"}},
							{name:'其他', textStyle:{color:"#000"}}]
					},
					toolbox: {
						show: true,
						feature: {
							magicType: {show: true, type: ['stack', 'tiled']},
							saveAsImage: {show: true}
						}
					},
					xAxis: {
						type: 'category',
						name: '日期',
						boundaryGap: false,
						data: db.date
					},
					yAxis: {
						type: 'value',
						name: '（单位：元）',
						nameLocation: 'start'
					},
					series: [{
						name: '购车惠',
						type: 'line',
						smooth: true,
						data: db.low_price,
						lineStyle: {
							normal: {
								color: '#ff8e1b',
								width: 3,
								type: 'solid'
							}
						}
					},
						{
							name: '市场',
							type: 'line',
							smooth: true,
							data: db.price,
							lineStyle: {
								normal: {
									color: '#299ffb',
									width: 2,
									type: 'solid'
								}
							}
						},
						{
							name: '其他',
							type: 'line',
							smooth: true,
							data: db.other_price,
							lineStyle: {
								normal: {
									color: '#000',
									width: 2,
									type: 'solid'
								}
							}
						}]
				});
			},"JSON");
		}


	});

	/* 价格格式化 */
	function formatMoney(s, type) {
		if (/[^0-9\.]/.test(s))
			return "0";
		if (s == null || s == "")
			return "0";
		s = s.toString().replace(/^(\d*)$/, "$1.");
		s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
		s = s.replace(".", ",");
		var re = /(\d)(\d{3},)/;
		while (re.test(s))
			s = s.replace(re, "$1,$2");
		s = s.replace(/,(\d\d)$/, ".$1");
		if (type == 0) {
			var a = s.split(".");
			if (a[1] == "00") {
				s = a[0];
			}
		}
		return s;
	}

	/* 计算保险 */
	function createPrice(wrapObj){
		function calculationSubtotal(){
			var subtotalVal = 0;
			$.each($('#J_InsuranceBox').find('.j_tr'), function(i, ele){
				var $curEle = $(ele),
					$curInp = $curEle.find('input'),
					val = 0;

				if($curEle.find('.checkFrame').hasClass('selected')){
					val = $curInp.val();
					subtotalVal += parseInt(val, 10);
					sourceDb[$curInp.data('txt')] = parseInt(val, 10);
				}else{
					sourceDb[$curInp.data('txt')] = '';
				}

			});

			$insuranceSubtotalNum.text(subtotalVal);
			allPriceVal = _valNumber + purchaseTaxVal + licenseTaxVal + usageTaxVal + subtotalVal; //价格总计

			sourceDb.cess = purchaseTaxVal;
			sourceDb.licensing_fees = licenseTaxVal;
			sourceDb.travel_tax = usageTaxVal;
			$all.html(formatMoney(allPriceVal, 0));
		}
		var $gchWrap = $(wrapObj.gchPriceWrapper),
			_gchPriceVal = $gchWrap.eq(0).text(),
			_val = $.trim(_gchPriceVal),
			defaultCoverage = 1000000, //
			defaultRate = 0.0143, //费率
			_valNumber = parseInt(_val), //购车惠价格
			$strongInsurance = $(wrapObj.strongInsuranceWrapper),//交强险 容器
			$qitaSubtotalNum = $(wrapObj.qitaWrapper),//其他 容器
			$purchaseTax = $(wrapObj.purchaseTaxWrapper), //购置税 容器
			$licenseTax = $(wrapObj.licenseTaxWrapper), //上牌费 容器
			$usageTax = $(wrapObj.usageTaxWrapper), //车船使用税 容器
			$insuranceSubtotalNum = $(wrapObj.insuranceSubtotalNumWrapper), //保险小计 容器
			$damageInsurance = $(wrapObj.damageInsuranceWrapper), //车辆损失险 容器
			$thirdInsurance = $(wrapObj.thirdInsuranceWrapper), //第三者责任险 容器
			$passengerInsurance = $(wrapObj.passengerInsuranceWrapper), //车上人员责任险 容器
			$noDeductibleInsurance = $(wrapObj.noDeductibleInsuranceWrapper), //附加不计免赔特约 容器
			$all = $(wrapObj.allPriceWrapper), //总计价格 容器
			displacement = $('#J_carDetail').data('displacement'), //排量
			displacementFormat = 0,
			fixedPremium = 603,//$(wrapObj.damageInsuranceSltWrapper).val() 家用座位数量
			purchaseTaxVal = Math.round(_valNumber/11.7), //购置税
			licenseTaxVal = 500, //上牌费用
			usageTaxVal = 30 * 12, //车船使用税
//			qitaSubtotalNum = licenseTaxVal + usageTaxVal,
			damageInsuranceVal = Math.round(parseInt(_val) * defaultRate) + parseInt(fixedPremium), //车辆损失险
			thirdInsuranceVal = 1614,//parseInt($(wrapObj.thirdInsuranceSltWrapper).val()),第三者责任险
			passengerInsuranceVal = 40 + 26 * 4, //车上人员责任险
			noDeductibleInsuranceVal = Math.round((damageInsuranceVal + thirdInsuranceVal + passengerInsuranceVal) * 0.15), //附加不计免赔特约
			insuranceSubtotalVal = 0, //保险小计
			displacementInfo = '排量在1.6以下，此购置税需减半计算',
			displacementHtml = '',
			allPriceVal = 0; //价格总计
		if(displacement != ''){
			displacementHtml = '/' + displacement;
			$('.j_plInfo').html(displacementHtml);

			displacementFormat = parseFloat(displacement.substring(0, displacement.length - 1)) * 10;
			if(displacementFormat <= 16){
				$('.j_newGzsPrice').html(purchaseTaxVal).css({textDecoration: "line-through"});
				purchaseTaxVal = Math.ceil(purchaseTaxVal / 2);
			}
			displacementInfo = '排量为'+ displacement + '，此购置税为' + purchaseTaxVal + "元";

			if(displacementFormat < 10){
				usageTaxVal = 180;
			}else if(displacementFormat > 10 && displacementFormat <= 16){
				usageTaxVal = 360;
			}else if(displacementFormat > 16 && displacementFormat <= 20){
				usageTaxVal = 420;
			}
			else if(displacementFormat > 20 && displacementFormat <= 25){
				usageTaxVal = 720;
			}
			else if(displacementFormat > 25 && displacementFormat <= 30){
				usageTaxVal = 1800;
			}
			else if(displacementFormat > 30 && displacementFormat <= 40){
				usageTaxVal = 3000;
			}else if(displacementFormat > 40){
				usageTaxVal = 4500;
			}
		}
		var qitaSubtotalNum = licenseTaxVal + usageTaxVal;
		//保险选项
		$('#J_InsuranceBox').off().on('click', '.j_checkItem', function(e){
			e.stopPropagation();
			var $this = $(this);
			if($this.hasClass('selected')){
				$this.removeClass('selected');
			}else{
				$this.addClass('selected');
			}
			calculationSubtotal();
		});

		$('.j_purchaseTaxInfo').html(displacementInfo);
		$gchWrap.html(formatMoney(_valNumber, 0));
		$strongInsurance.html(formatMoney(wrapObj.strongInsuranceNum, 0));
		$purchaseTax.html(purchaseTaxVal);
		$licenseTax.val(licenseTaxVal);
		$usageTax.val(usageTaxVal);
		$qitaSubtotalNum.html(qitaSubtotalNum);

		$damageInsurance.val(damageInsuranceVal);
		$thirdInsurance.val(thirdInsuranceVal);
		$passengerInsurance.val(passengerInsuranceVal);
		$noDeductibleInsurance.val(noDeductibleInsuranceVal);

		calculationSubtotal();

	}
	/* 获取定金和礼包 */
	function getBprice(v){
			$.ajax({
				url: "/index.php/Car/ajax_dingjin",
				data: {id: v},
				success: function(a) {
					if(a.status == 1 && a.newInquiryFee && a.newPackagePrice){
						db.inquiryFee = a.newInquiryFee;
						db.packagePrice = a.newPackagePrice;
						$('#J_inquiryFee').html(a.newInquiryFee);
						$('#J_PackagePrice').html(a.newPackagePrice);
						$('.package_picbox').html("<img src='"+ossImg+a.newUrl+"' alt='超值大礼包'>");

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
		/* 根据询价记录 获取新的礼包和定金 */
		function initPrice(ele){
			var _curId = ele.data('id'),
				_cur4sName = ele.data('city');
			ele.addClass('active').siblings().removeClass('active');
			car.attr('data-name4s', _cur4sName);
			defalut4sName = _cur4sName;
			sourceDb.bp =ele.data('bp');
			$.ajax({
				url: "/index.php/Car/ajax_dingjin",
				data: {id: _curId},
				success: function(db) {
					$('#J_PackagePriceBox').text(db.newPackagePrice);
					$('#J_deposit').text(db.newInquiryFee);
					$('#J_PackageDes').text(db.description);
					sourceDb.deposit = db.newInquiryFee;
				},
				error: function() {
					this.errormsg()
				}
			});

			$('.j_gfPrice').text(ele.data('bp'));

			createPrice({
				gchPriceWrapper:".j_gfPrice",
				damageInsuranceWrapper:".j_damageInsurance",
				thirdInsuranceWrapper:".j_thirdInsurance",
				passengerInsuranceWrapper:".j_passengerInsurance",
				strongInsuranceWrapper:".j_strongInsurance",
				noDeductibleInsuranceWrapper:".j_noDeductibleInsurance",
				damageInsuranceSltWrapper:"#J_damageInsuranceSlt",
				thirdInsuranceSltWrapper:"#J_thirdInsuranceSlt",
				purchaseTaxWrapper:".j_gzsPrice",
				licenseTaxWrapper:".j_spfPrice",
				usageTaxWrapper:".j_ccsysPrice",
				qitaWrapper:".j_qtPrice",
				insuranceSubtotalNumWrapper:".j_insuranceSubtotalNum",
				allPriceWrapper:".j_ldjPrice",
				strongInsuranceNum:950,
				additionalFlag: !0
			});
		}
	createPrice({
		gchPriceWrapper:".j_gfPrice",
		damageInsuranceWrapper:".j_damageInsurance",
		thirdInsuranceWrapper:".j_thirdInsurance",
		passengerInsuranceWrapper:".j_passengerInsurance",
		strongInsuranceWrapper:".j_strongInsurance",
		noDeductibleInsuranceWrapper:".j_noDeductibleInsurance",
		damageInsuranceSltWrapper:"#J_damageInsuranceSlt",
		thirdInsuranceSltWrapper:"#J_thirdInsuranceSlt",
		purchaseTaxWrapper:".j_gzsPrice",
		licenseTaxWrapper:".j_spfPrice",
		usageTaxWrapper:".j_ccsysPrice",
		qitaWrapper:".j_qtPrice",
		insuranceSubtotalNumWrapper:".j_insuranceSubtotalNum",
		allPriceWrapper:".j_ldjPrice",
		strongInsuranceNum:950,
		additionalFlag: !0
	});


	if($('#J_bpFilter').length){
		initPrice($('#J_bpFilter').find('a:first'));
	}

	$('#J_bpFilter').on('click', 'a', function(event) {
		event.stopPropagation();
		event.preventDefault();
		initPrice($(this));
	});
//初始化颜色
	function initializationColor(obj, colorObjStr){
		var $colorObj = obj.find('.j_' + colorObjStr),
			$colorObjDd = $colorObj.find('dd'),
			$bigPicObj = $('#J_detailPic').find('.j_bigPic'),
			$smallPicList = $('#J_detailPic').find('.j_smallPic').find('li'),
			$curColorObj = $colorObjDd.find('.active'),
			$source;
		if($curColorObj.length){
			$defaultExColorId = $curColorObj.data('color');
			$source = $("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>" + $curColorObj.attr('title') + "</em></span>");
			$source.appendTo($colorObjDd);
			//if(colorObjStr == 'excolor'){
			//	$.each($smallPicList, function(i, o){
			//		if($(o).data('id') == $defaultExColorId) {
            //
			//			$bigPicObj.attr('src', $(o).find('img').attr('src'));
			//		}
			//	});
			//}
		}else{
			$curColorObj = $colorObjDd.find('li:first');
			$curColorObj.addClass('active').siblings().removeClass('active');
			$source = $("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>" + $curColorObj.attr('title') + "</em></span>");
			$source.appendTo($colorObjDd);
		}

	}

	initializationColor($('#J_carDetail'), 'excolor');
	initializationColor($('#J_carDetail'), 'incolor');

//判断颜色是否已有底价并跳转
	$('.j_carColor').on('click', 'a', function(event){
		var $p = $(this).parent(),
			$source = $("<span class='j_slcColor'>已选择：<em class='j_slcColorTxt'>"+ $(this).attr('title') +"</em></span>"),
			$selectedColor = $p.find('.j_slcColor');
		$(this).addClass('active').siblings().removeClass('active');
		if($selectedColor.length){
			$selectedColor.find("em").html($(this).attr("title"));
		}
		else {
			$source.appendTo($p);
		}
		if($(event.delegateTarget).data('type') == 'excolor'){
			$.each($smallPicList, function(i, o){
				if($(o).data('id') == $('.j_excolor').find('.active').data('color')) {
					$bigPicObj.attr('src', $(o).find('img').attr('src'));
				}
			});
		}
		sourceDb.excolor = $('.j_excolor').find('.active').data('color'); //外饰颜色
		sourceDb.incolor = $('.j_incolor').find('.active').data('color'); //内饰颜色

		sourceDb.car_id = $('#car_id').val(); //车款
		$('.j_excolor').find('.active').length && $('.j_incolor').find('.active').length && (
			$.ajax({
				url: qianzui + "/Car/ajax_unquotes_color",
				data: sourceDb,
				success: function(a) {
					if(a.status == 1 && a.next) {window.location.href = a.next;}
					else if(a.status == 2 && a.newPrice){
						$('.j_gfPrice').text(a.newPrice);
						$('.j_gfPrice').next().show();
						$('.j_inquiryBtn').removeClass('desabled_btn');
						sourceDb.car_price_id = a.car_price_id;
						sourceDb.bp = a.newPrice;
						defalut4sName = a.name_4s;
						createPrice({
							gchPriceWrapper:".j_gfPrice",
							damageInsuranceWrapper:".j_damageInsurance",
							thirdInsuranceWrapper:".j_thirdInsurance",
							passengerInsuranceWrapper:".j_passengerInsurance",
							strongInsuranceWrapper:".j_strongInsurance",
							noDeductibleInsuranceWrapper:".j_noDeductibleInsurance",
							damageInsuranceSltWrapper:"#J_damageInsuranceSlt",
							thirdInsuranceSltWrapper:"#J_thirdInsuranceSlt",
							purchaseTaxWrapper:".j_gzsPrice",
							licenseTaxWrapper:".j_spfPrice",
							usageTaxWrapper:".j_ccsysPrice",
							qitaWrapper:".j_qtPrice",
							insuranceSubtotalNumWrapper:".j_insuranceSubtotalNum",
							allPriceWrapper:".j_ldjPrice",
							strongInsuranceNum:950,
							additionalFlag: !0
						});
					}else{
						$('.j_gfPrice').text(a.info);
						$('.j_gfPrice').next().hide();
						$('.j_inquiryBtn').addClass('desabled_btn');
						$('.j_ldjPrice').html('暂无落地价');
						sourceDb.car_price_id = "";
						defalut4sName = "";
						//HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>'+ a.info +'</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
					}
				},
				error: function() {
					this.errormsg()
				}
			})
		)
	});
//上牌地
	$('#J_cardCity').on('click', function(event){
		var $ucpPopCon = $('#J_cardCityCon'), _newUcpStr = '';
		sourceDb.excolor = $('.j_excolor .active').data('color'); //外饰颜色
		sourceDb.incolor = $('.j_incolor .active').data('color'); //内饰颜色
		sourceDb.bp = parseInt($('.j_gfPrice ').data('price')); //底价
		$(this).hasClass('card_city_active') ? $(this).removeClass('card_city_active') : $(this).addClass('card_city_active');
		$ucpPopCon.is(":hidden") ? $ucpPopCon.show() : $ucpPopCon.hide();

		if(uccFlag || (_excolor != sourceDb.excolor && _incolor != sourceDb.incolor)){
			$ucpPopCon.find('.allCardCity').html('<div class="loadingCon"><i></i>正在加载中...</div>');
			$.ajax({
				url: qianzui + "/Car/ajax_unquotes_shangpai",
				data: {car_price_id: sourceDb.car_price_id},
				async: false,
				success: function(a) {

					if(a && a.uccities && a.uccities.length > 0) {
						uccFlag = !1;
						$.each(a.uccities, function (i, val) {
							_newUcpStr += '<a href="javascript:;" title="' + val + '" data-ucname="' + val + '">' + val + '</a>';
						});
					}else{
						_newUcpStr = "<div class=\"no-city\">暂无其他上牌地城市~<\/div>";
					}
					$ucpPopCon.find('.allCardCity').html(_newUcpStr);
				},
				error: function() {
					this.errormsg()
				}
			});
		}
		$ucpPopCon.on('click', 'a', function(event){
			$('#J_cardCityCon a').removeClass('active');
			$(this).addClass('active');
			var ucname = $(this).data('ucname'),
				ucid = $(this).data('ucid'),
				$curUcCard = $('#J_cardCity');
			$curUcCard.find('span').text(ucname);
			$curUcCard.attr('data-ucname', ucname);
			$curUcCard.attr('data-ucid', ucid);
			$curUcCard.removeClass('card_city_active');
			sourceDb.ucc_id = ucid;//上牌地id
			sourceDb.ucc_name = ucname;//上牌地名称
			$ucpPopCon.hide();
		});
	});

//底价购车
	$('.j_inquiryBtn').on('click', function(event){

		var $source = $(".j_bpTips");
		event.stopPropagation();
		event.preventDefault();
		if($(this).hasClass('desabled_btn')){
			return false;
		}
		if(!$.G.getUSERID()){
			HUI.PopOut.login();
			return false;
		}

		if(defalut4sName){
			if(curCityName !== defalut4sName){
				$source.length || ($source = $("<div class=\"bpTips j_bpTips\"><p>该车款由"+ defalut4sName + "4S店，提车地方为" + defalut4sName + "；是否继续底价购车？</p><div class=\"G_tc btn\"><a href=\"javascript:;\" onclick=\"HUI.PopOut.closeMask();\" class=\"G_btn_c btn_34px\">重新选择</a><a href=\"javascript:;\" class=\"G_btn_a btn_34px j_SaveBtn\">继续定车</a></div></div>"));
				HUI.PopOut.alert(["\u63d0\u793a", $source, ""], "m");

				$source.on("click", ".j_SaveBtn", function(){
					$.ajax({
						url: qianzui + "/Car/ajax_order",
						data: sourceDb,
						success: function(a) {
							if(a.status == 1 && a.next){
								window.location.href = a.next;
							}else if(1 !== a.status){
								HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>'+ a.info +'</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
							}
						},
						error: function() {
							this.errormsg()
						}
					});
				});
			}else{
				$.ajax({
					url: qianzui + "/Car/ajax_order",
					data: sourceDb,
					success: function(a) {
						if(a.status == 1 && a.next){
							window.location.href = a.next;
						}else if(1 !== a.status){
							HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>'+ a.info +'</h3></div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
						}
					},
					error: function() {
						this.errormsg()
					}
				});
			}
		}
	});
})(jQuery, $car);

	//详情页图片切换
	(function(e){
		var $detailPic = e('#J_detailPic'),
			$bigPic = $detailPic.find('.j_bigPic'),
			$smallPic = $detailPic.find('.j_smallPic');

		$smallPic.slide({mainCell:".bd ul",prevCell:".prev",nextCell:".next", autoPage:true,effect:"left",scroll:3,vis:3,pnLoop:false});
		$smallPic.on('hover', 'li', function(){
			e(this).addClass('active').siblings().removeClass('active');
			$bigPic.attr('src', e(this).find('img').attr('src'));
		});
	})(jQuery);
});