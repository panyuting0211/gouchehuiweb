/**
 * Created by Administrator on 2015/12/23.
 */

$(document).ready(function(){
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
	function createPrice(){
		function calculationSubtotal(ele){
			var subtotalVal = 0, baseSubtotal = 0, $orderAgencyService = $('.j_orderAgencyService'), dbAllPrice = 0, dbBalancePrice = 0, $agencyServiceAllPrice = $('.j_agencyServicePrice'), agencyServiceSubtatle = 0, agencyServiceList = "<ul>";

			$.each(ele.find('.j_agencyService'), function(i, ele){
				var $curEle = $(ele),
					$curInp = $curEle.find('.j_spanVal'),
					val = 0;
				if($curEle.find('.checkFrame').hasClass('selected')){
					val = parseInt($.trim($curInp.text()), 10);
					agencyServiceList += '<li>' + $curEle.find('.j_insuranceName').text() + '：' +  val + ' 元</li>';
					agencyServiceSubtatle = agencyServiceSubtatle + val;
					$curEle.find('.j_inpVal').val(val);
				}else{
					$curEle.find('.j_inpVal').val("");
				}
			});
			dbAllPrice = parseInt($('.j_lowPrice').text(), 10) + agencyServiceSubtatle;
			dbBalancePrice = dbAllPrice - parseInt($('.j_orderDepostPrice').text(), 10);

			$('.j_allPriceBox').text(dbAllPrice);
			$('.j_balancePrice').text(dbBalancePrice)
			$.each(ele.find('.j_tr'), function(i, ele){
				var $curEle = $(ele),
					$curInp = $curEle.find('.j_spanVal'),
					val = 0;
				if($curEle.find('.checkFrame').hasClass('selected')){
					val = parseInt($.trim($curInp.text()), 10);
					$curEle.find('.j_inpVal').val(val);
					subtotalVal += val;
				}else{
					$curEle.find('.j_inpVal').val("");
				}
			});
			agencyServiceList += "</ul>";
			$orderAgencyService.html(agencyServiceList);
			$agencyServiceAllPrice.html(agencyServiceSubtatle);
			$insuranceSubtotal.text(subtotalVal);
			//总值计算
			allPriceVal = baseSubtotalVal + subtotalVal;

			$all.text(allPriceVal);
		}

		/* 容器定义 */
		var $onTheRoad = $("#J_OrderInsuranceContenter"),
			$gchPrice = $onTheRoad.find(".j_lowPrice"), //购车惠底价容器
			$purchaseTax = $onTheRoad.find(".j_purchaseTax"), //购置税容器
			$licenseTax = $onTheRoad.find(".j_licenseTax"), //上牌费容器
			$baseSubtotal = $onTheRoad.find(".j_baseSubtotal"), //基本金额小计容器
			$insuranceSubtotal = $onTheRoad.find(".j_insuranceSubtotal"), //保险金额小计容器
			$usageTax = $onTheRoad.find(".j_usageTax"), //车船使用税容器
			$strongInsurance = $onTheRoad.find(".j_strongInsurance"),//交强险容器
			$damageInsurance = $onTheRoad.find(".j_damageInsurance"), //车辆损失险容器
			$damageInsuranceSel = $onTheRoad.find("[name=damageInsurance]"), //车船损失险选择器
			$thirdInsurance = $onTheRoad.find(".j_thirdInsurance"), //第三方责任险容器
			$thirdInsuranceSel = $onTheRoad.find("[name=thirdInsurance]"), //第三方责任险选择器
			$passengerInsurance = $onTheRoad.find(".j_passengerInsurance"), //车上人员责任险容器
			$noDeductibleInsurance = $onTheRoad.find(".j_noDeductibleInsurance"), //不计免赔容器
			$all = $onTheRoad.find(".j_alltotal"); //总价容器

		/* 数值定义 */
		var defaultCoverage = 1000000,
			defaultRate = 0.0143, //费率
			_valNumber = parseInt($.trim($gchPrice.text()), 10), //购车惠底价数值
			strongInsuranceVal = 950, //交强险
			licenseTaxVal = 500, //上牌费用
			usageTaxVal  = 30 * 12, //车船使用税
			displacement = $onTheRoad.data('displacement'),//排量数值
			displacementFormat = 0, //排量格式化
			purchaseTaxVal = Math.round(_valNumber/11.7), //购置税 数值
			fixedPremium = $onTheRoad.find("[name=damageInsurance]").val(), //车辆损失险基数值
			damageInsuranceVal = Math.round(_valNumber * defaultRate) + parseInt(fixedPremium), //车辆损失险总数
			thirdInsuranceVal = parseInt($thirdInsuranceSel.val(), 10), //第三方责任险选择器数值
			passengerInsuranceVal = 40 + 26 * 4, //车上人员责任险数值
			noDeductibleInsuranceVal = Math.round((damageInsuranceVal + thirdInsuranceVal + passengerInsuranceVal) * 0.15), //不计免赔数值
			insuranceSubtotalVal = 0, //保险小计
			baseSubtotalVal = 0, //基本小计
			allPriceVal = 0; //总价默认值

		if(displacement == ''){
			displacement = '1.8L';
		}
			displacementFormat = parseFloat(displacement.substring(0, displacement.length - 1)) * 10;
			if(displacementFormat <= 16){
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
		//购置税
		$onTheRoad.find('input[name=cess]').length ? $onTheRoad.find('input[name=cess]').val(purchaseTaxVal)  : $onTheRoad.prepend('<input type="hidden" name="cess" value="'+ purchaseTaxVal +'">');
		//车船使用税
		$onTheRoad.find('input[name=travel_tax]').length ? $onTheRoad.find('input[name=travel_tax]').val(usageTaxVal)  :$onTheRoad.prepend('<input type="hidden" name="travel_tax" value="'+ usageTaxVal +'">');
		//上牌费
		$onTheRoad.find('input[name=licensing_fees]').length ? $onTheRoad.find('input[name=licensing_fees]').val(licenseTaxVal)  :$onTheRoad.prepend('<input type="hidden" name="licensing_fees" value="'+ licenseTaxVal +'">');
		//交强险
		$onTheRoad.find('input[name=compulsory_insurance]').length ? $onTheRoad.find('input[name=compulsory_insurance]').val(strongInsuranceVal)  : $onTheRoad.prepend('<input type="hidden" name="compulsory_insurance" value="'+ strongInsuranceVal +'">');
		//车辆损失险
		$onTheRoad.find('input[name=car_loss_dang]').length ? $onTheRoad.find('input[name=car_loss_dang]').val(damageInsuranceVal)  : $onTheRoad.prepend('<input type="hidden" name="car_loss_dang" value="'+ damageInsuranceVal +'">');
		//第三者责任险
		$onTheRoad.find('input[name=liability]').length ? $onTheRoad.find('input[name=liability]').val(thirdInsuranceVal)  : $onTheRoad.prepend('<input type="hidden" name="liability" value="'+ thirdInsuranceVal +'">');
		//附加不计免赔特约
		$onTheRoad.find('input[name=deductible_special]').length ? $onTheRoad.find('input[name=deductible_special]').val(noDeductibleInsuranceVal)  : $onTheRoad.prepend('<input type="hidden" name="deductible_special" value="'+ noDeductibleInsuranceVal +'">');
		//车上人员责任险
		$onTheRoad.find('input[name=car_liability]').length ? $onTheRoad.find('input[name=car_liability]').val(passengerInsuranceVal)  : $onTheRoad.prepend('<input type="hidden" name="car_liability" value="'+ passengerInsuranceVal +'">');



		//基本小计计算
		baseSubtotalVal += _valNumber + purchaseTaxVal + usageTaxVal + licenseTaxVal; //基本小计
		//填充容器
		$purchaseTax.text(purchaseTaxVal);
		$usageTax.text(usageTaxVal);
		$licenseTax.text(licenseTaxVal);
		$baseSubtotal.text(baseSubtotalVal);

		$strongInsurance.text(strongInsuranceVal);
		$damageInsurance.text(damageInsuranceVal);
		$thirdInsurance.text(thirdInsuranceVal);
		$passengerInsurance.text(passengerInsuranceVal);
		$noDeductibleInsurance.text(noDeductibleInsuranceVal);
		//保险选项
		$onTheRoad.on('click', '.j_checkItem', function(e){
			var $this = $(this);
			if($this.hasClass('selected')){
				$this.removeClass('selected');
			}else{
				$this.addClass('selected');
			}
			calculationSubtotal($onTheRoad);
		});
		//保险小计计算
		calculationSubtotal($onTheRoad);
	}

	$("#J_damageInsuranceSlt").change(function(){
		createPrice();
	});
	$("#J_thirdInsuranceSlt").change(function(){
		createPrice();
	});
	createPrice();
	function bindSel(event){
		var $lowPrice = $('#low_price'),
			$cardPlace = $('#card_place'),
			$inpCarPriceId = $('input[name=car_price_id]'),
			$deposit = $('#deposit');
		$cardPlace.empty().addClass('loading');
		$.ajax({
			url: qianzui + "/order/price_details",
			data: {exterior_color_id: $('#exterior_color_id').val(), interior_color_id: $('#interior_color_id').val()},
			success: function(db){
				$cardPlace.removeClass('loading');
				if(db.low_price && db.deposit && db.area.length > 0){
					$lowPrice.html(db.low_price);
					$deposit.html(db.deposit);
					$(".onTheRoadDetailed").find(".j_gchPrice").html(db.low_price);
					createPrice();
					$inpCarPriceId.val(db.id);
					$.each(db.area, function(i, v){
						$cardPlace.append('<option value="'+ v.sales_area_name +'">'+ v.sales_area_name +'</option>');
					});
				}
			},
			error: function() {
				this.errormsg()
			}
		});
	}
	function setIn(wrapper, db, carId, exId){
		var optList = "";
		$.each(db, function(i, v){
			optList +=  '<option value="'+ v.id +'">'+ v.name +'</option>';
		});
		wrapper.removeClass('loading');
		wrapper.append(optList);
		wrapper.off().on('change', bindSel);
	}
	function setEx(exObj, inObj, db, carId){
		var optList = "";
		$.each(db, function(i, v){
			optList +=  '<option value="'+ v.id +'">'+ v.name +'</option>';
		});
		exObj.removeClass('loading');
		exObj.append(optList);
		exObj.off().on('change', getIn);

	}
	function getIn(){
		var _thisId = $(this).val(),
			$imgBox = $('.j_activityOrderPic'),
			$imger = $imgBox.find('img'),
		inObj = $('#interior_color_id');
		inObj.empty().removeAttr("disabled").append('<option value="0">请选择内饰颜色</option>').addClass('loading');
		$.ajax({
			url: qianzui + "/order/color",
			data: { exterior_color_id: _thisId},
			success: function(db){
				if(db.ex_img){
					$imger.attr('src', db.ex_img);
				}
				if(db.interior && db.interior.length){
					setIn(inObj, db.interior, $('#car_id').val(), _thisId);
				}
			},
			error: function() {
				this.errormsg()
			}
		});
	}

	$('#exterior_color_id').on('change', getIn);
	$('#car_id').change(function(){
		var $ex = $('#exterior_color_id'),
			_thisVal = $(this).val(),
			$in = $('#interior_color_id');
		$ex.empty().append('<option value="0">请选择外观颜色</option>').addClass('loading');
		$in.empty().attr("disabled", true).append('<option value="0">请选择内饰颜色</option>');
		$.ajax({
			url: qianzui + "/order/color",
			data: {car_id: _thisVal},
			success: function(db){
				if(db.exterior && db.exterior.length){
					setEx($ex, $in, db.exterior, _thisVal);
				}
			},
			error: function() {
				this.errormsg()
			}
		});
	});

	$("#J_damageInsuranceSlt").change(function(){
		createPrice(!0);
	});
	$(".j_usageTaxSlt").change(function(){
		createPrice(!0);
	});
	$("#J_thirdInsuranceSlt").change(function(){
		createPrice(!0);
	});

    $.each($('.j_hiddenInp'), function(i,o){
        var _ul = $(o).find('ul'),
            _type = _ul.data('type'),
			_defaultVal = $(o).data('defaultval'),
            _thisVal = _ul.find('li.active').data('msg');

        _ul.append('<input type="hidden" name="'+ _type +'" value="'+ _defaultVal +'">');
		$.each(_ul.find('li'), function(i, v){
			if(_defaultVal == $(v).data('msg')){
				$(v).addClass('active');
			}
			$(v).on('click',function(event){
				var _this = $(this),
					_v = _this.data('msg'),
					_type = _this.data('type'),
					_curInp = $('input[name="'+ _type +'"]');
				_curInp.val(_v);
				_this.addClass("active").siblings().removeClass("active");
			});
		});

    });

	$('.j_orderDetailed').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		var source = $('.onTheRoadDetailed');
		createPrice(!0);
		HUI.PopOut.alert(["保险须知", source, ""], 3);
	});

    $('#datetimepicker').datetimepicker({
        lang:"ch",
        format:'Y-m-d',
        minDate:'-1970/01/01'
    });

	$('#J_CheckBtn').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		var source = $("#J_CheckCon"), buyCarTime = $('#datetimepicker').val(), buyDateTimeObj = '', formatDate = "";
		HUI.PopOut.alert(["汽车委托定车合同", source, ""], 3);
		if(buyCarTime){
			buyDateTimeObj = new Date(buyCarTime.replace(/-/g,"/"));
			formatDate = "<s>" + buyDateTimeObj.getFullYear() + "</s>年<s>"+(buyDateTimeObj.getMonth() + 1) + "</s>月<s>" + buyDateTimeObj.getDate() + "</s>日";
		}else{
			formatDate = "<s></s>年<s></s>月<s></s>日";
		}
		source.find('.j_buyCarDateTime').html(formatDate);
		source.find('.j_partyBname').html($('input[name=buyer_name]').val());
		source.find('.j_partyBtel').html($('input[name=buyer_tel]').val());
		source.find('.j_depositPrice').html($('#J_RealPaidBox').text());
	});

	!function(){
		function previewImage(file) {
        var MAXWIDTH  = 260;
        var MAXHEIGHT = 180;
        var imgBox = $(file).closest('.filebox').find('.imgbox');

        if (file.files && file.files[0])
        {
            imgBox.html('<img class="imghead">');
            var img = imgBox.find('.imghead').get(0);

            img.onload = function(){
                var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                img.width  =  rect.width;
                img.height =  rect.height;
//                 img.style.marginLeft = rect.left+'px';
                img.style.marginTop = rect.top+'px';
            }
            var reader = new FileReader();
            reader.onload = function(evt){img.src = evt.target.result;}
            reader.readAsDataURL(file.files[0]);
        }
        else //兼容IE
        {
            var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
            file.select();
            var src = document.selection.createRange().text;
            div.innerHTML = '<img id=imghead>';
            var img = document.getElementById('imghead');
            img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
            var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
            status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
            div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\"'></div>";
        }
    }
		function clacImgZoomParam( maxWidth, maxHeight, width, height ){
        var param = {top:0, left:0, width:width, height:height};
        if( width>maxWidth || height>maxHeight )
        {
            rateWidth = width / maxWidth;
            rateHeight = height / maxHeight;

            if( rateWidth > rateHeight )
            {
                param.width =  maxWidth;
                param.height = Math.round(height / rateWidth);
            }else
            {
                param.width = Math.round(width / rateHeight);
                param.height = maxHeight;
            }
        }

        param.left = Math.round((maxWidth - param.width) / 2);
        param.top = Math.round((maxHeight - param.height) / 2);
        return param;
    }

    $('input[name="cardpic_front"]').change(function(){
        previewImage($(this).get(0));
    });
    $('input[name="cardpic_back"]').change(function(){
        previewImage($(this).get(0));
    });
	}(),

	!function(){
	    var errTips = {
				buyer_name:"订车人姓名不能为空~",
				buyer_tel:"请输入正确的手机号~",
				cardpic_front: "身份证正面照不能为空~",
				cardpic_back: "身份证反面照不能为空~",
				buy_time: "提车时间不能为空~"
			},
		showTips = ["请输入您的姓名~", "请输入您的手机号，格式如：13809040655~", "请输入身份证正面照，如示例图~", "请输入身份证反面照，如示例图~", "请选择您的提车时间~"];
		//初始化提示框
		$.each($("input.j_validateInp"), function(i, o){
		  $(o).closest('.j_tipsWrapper').append('<div class="tips j_'+ $(o)[0].name +'"></div>');
		});

		//提交事件
		$(".orderbox").find("form").find("input[type=submit]").on("click", function(event) {
			event.preventDefault();
			event.stopPropagation();
			var buyerNameFlag = checkData($("input[name=buyer_name]")[0]),
			    buyerTelFlag = checkData($("input[name=buyer_tel]")[0]),
				buyTimeFlag = checkData($("input[name=buy_time]")[0]),
				isPayment = checkCustommRadio(".j_payment"),
				_this = $(event.target),
				payTxt = ["去支付","去支付..."];

			if(buyerNameFlag && buyerTelFlag && buyTimeFlag && isPayment){
				$(".orderbox").find("form").submit();
			}
		});

		function checkCustommRadio(obj){
			var isFlag = false,
				tipStr = $.trim($(obj).data('tip'));
			$(obj).find('.active').length > 0 ? (isFlag = true, $(obj).find('.tips').hide()) : $(obj).find('.tips').length ? (isFlag = false, $(obj).find('.tips').show()) : (isFlag = false, $(obj).find('dd').append('<div class="tips"><p class="G_wrong"><i class="icon_warn"></i><span>请选择一种'+ tipStr +'</span></p></div>'));

			return isFlag;
		}
		//校验数据单个或者整体
		function checkData(ele) {
			if (!ele) {
				return true;
			}
			var value = $.trim(ele.value);
			switch (ele.name) {
			  case "buyer_name":
			    if (!value){
					$(".j_" + ele.name).html("<p class=\"G_wrong\"><i class=\"icon_warn\"></i><span>"+ errTips[ele.name] +"</span></p>");
					$(ele).focus().addClass('G_input_error');
				  return false;
				}else{
					$(".j_" + ele.name).html("");
					$(ele).removeClass('G_input_error');
					return true;
				}
			  break;
			  case "buyer_tel":	
				if (!value || new RegExp("^((13[0-9])|(15[0-9])|(18[0-9])|14[0-9]|17[0-9])[0-9]{8,8}$").test(value) == false) {
						$(ele).focus().addClass('G_input_error');
						$(".j_" + ele.name).html("<p class=\"G_wrong\"><i class=\"icon_warn\"></i><span>"+ errTips[ele.name] +"</span></p>");
						return false;
					}else{
					$(".j_" + ele.name).html("");
					$(ele).removeClass('G_input_error');
					return true;
					}
			  break;
			  case "cardpic_front":
			    if(!value){
				$(ele).focus();
					$(".j_" + ele.name).html("<p class=\"G_wrong\"><i class=\"icon_warn\"></i><span>"+ errTips[ele.name] +"</span></p>");
					return false;
				}else{
					$(".j_" + ele.name).html("");
					return true;
				}
				break;
				case "cardpic_back":
			    if(!value){
				$(ele).focus();
					$(".j_" + ele.name).html("<p class=\"G_wrong\"><i class=\"icon_warn\"></i><span>"+ errTips[ele.name] +"</span></p>");
					return false;
				}else{
					$(".j_" + ele.name).html("");
					return true;
				}
				break;
				case "buy_time":
			    if(!value){
				$(ele).focus();
					$(".j_" + ele.name).html("<p class=\"G_wrong\"><i class=\"icon_warn\"></i><span>"+ errTips[ele.name] +"</span></p>");
					return false;
				}else{
					$(".j_" + ele.name).html("");
					return true;
				}
				break;
			}
        }
        //end
	
	}();
    !function(){
		function setCoupon(ele, priceEle){
			var $OrderCouponList = $('#J_OrderCouponList')
				$deposit = $('#J_DepositBox'),
				$coupon = $('#J_CouponPrice'),
				$couponBox = $('#J_CouponPriceBox'),
				dbDeposit = parseInt($deposit.text(), 10),
					dbCouponNumber = ele.data('number'),
				dbCouponPrice = parseInt(ele.find('.j_moneyBox').text(), 10) || 0,
				dbRealPrice = dbDeposit - dbCouponPrice,
				$realPaid = $('#J_RealPaidBox');
			//优惠券ID input框
			$OrderCouponList.find('input[name=ticket_number]').length ? $OrderCouponList.find('input[name=ticket_number]').val(dbCouponNumber)  : $OrderCouponList.prepend('<input type="hidden" name="ticket_number" value="'+ dbCouponNumber +'">');
			ele.addClass('cur').siblings().removeClass('cur');
			priceEle.text(dbCouponPrice);
			$coupon.show();
			$couponBox.text(dbCouponPrice);
			$realPaid.text(dbRealPrice);
		}
		$.ajax({
			url: "/index.php/Order/ticket",
			data: {},
			success: function(db) {
				var html = "";
				if(db.list.length > 0){
					html = template('tplItem', db);
					$('#J_OrderCouponListBox').show();
					$('#J_OrderCouponList').html(html);
					$('#J_OrderCouponList').find('li').on('click',function(){
						setCoupon($(this), $('#J_disMoney'));
					});
					setCoupon($('#J_OrderCouponList').find('li:first'), $('#J_disMoney'));
				}else{
					$('#J_OrderCouponList').html('<div class="null_list"><p>优惠券可以用于抵消等价定金！</p><p>询价您喜欢的车，即可得到优惠券！</p></div>');
				}

			}
		});

	}();

});

$(".invoicebox .discou li").on("click",function(){
	var li = $(this);
	$(this).addClass("active").siblings().removeClass("active");
	var a = li.children(".gift1").find("span").text();
	$("#J_disMoney i").text(a);
});





