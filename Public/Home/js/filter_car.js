/* 筛选器 */
(function ($) {
    $.brand = {};
    $.extend($.brand, {
        page: 1, //当前页
        maxPage: 1, //最多加载页数
        pageSize: 20, //每页个数
        currentBrandId: "", //当前品牌ID 
		extractCityId: "", //提车城市id
        filterClassifyStatus: "",
        filterServiceStatus: "end_date",
        filterServiceVal: "DESC",
		extractCityName: "", //提车城市name
        currentBrandName: "", //当前品牌名称
        prinvceId: 0, // 0:全国
        isLoading: false, //ajax数据是否loading
        minPrice: -1, 
        maxPrice: 10000,
        topPrice: 10000, //最大价钱
        carLevel: 0,
        loader: $("#J_loading"),
        init: function (options) {
            $.extend($.brand, options);	
            $(window).scroll(function () {
                if ($.brand.loader && $.brand.loader.is(":visible")) {
                    var pageH = $(document.body).height(); //页面总高度 
                    var scrollT = $(window).scrollTop(); //滚动条top 
                    var winH = 500;
                    var aa = (pageH - scrollT) / winH;
                    if (!$.brand.isLoading && aa < 2) {
                        $.brand.loader.click();
                    }
                }
            });

            $.brand.loader.click(function () {
                if ($.brand.page < $.brand.maxPage) {
                    $.brand.isLoading = true;
                    $.brand.page++;
                    if ($.brand.currentmasterBrandId == 0) {
                        $.brand.loadMore();
                    } else {
                        $.brand._refeshList();
                    }
                }
            });
			$.brand.hotBrands.init();
            $.brand.priceSelector.init();
			$.brand.filterClassify.init();
            $.brand.filterService.init();
            $.brand.filters.init();	
        },
		hotBrands: {
			selector: $("#J_hotBrands"),
			seletedFlag: false,
			init: function(){
			$.brand.hotBrands.selector.find("a").on("click", function (event) {
                    event.preventDefault();
                if($(this).data('nh')){
                    $.brand.filters.del($("ul a", $.brand.filters.filters_div).eq(0));
                    $.brand.hotBrands.clear();
                    return false;
                }
					$.brand.hotBrands.seletedFlag = true;
					$(this).addClass('active').siblings().removeClass('active');
					$.brand.page = 1;
					$.brand.currentBrandId = $.trim($(this).data('id'));
					$.brand.currentBrandName = $.trim($(this).data('name'));
                    $.Bom.setCookie("specialcar_curbrand", $.trim($("#J_hotBrands>a.active").data('name')), {'expires':7, 'path':"/"});
					$.brand.filters.setItems(true);
                });
            
            var oldTab = $.Bom.getCookie("specialcar_curbrand");
			},   
			clear:function(){
                $('#J_hotBrands a').removeClass('active').eq(0).addClass('active');
				$.brand.currentmasterBrandName = '';
				$.brand.currentBrandId = '';
				$.brand.hotBrands.seletedFlag = false;
				$.brand.filters.setItems(true);
			}
			
        },
        priceSelector: {
            selector: $("#J_priceRange"),
            seletedFlag: false,
            init: function () {
                $.brand.priceSelector.selector.find("a").click(function () {
                    if($(this).data('nh')){
                        $.brand.filters.del($("ul a", $.brand.filters.filters_div).eq(1));
                        $.brand.priceSelector.clear();
                        return false;
                    }
						$.brand.priceSelector.seletedFlag = true;
						$(this).addClass('active').siblings().removeClass('active');
						$.brand.minPrice = $(this).data('minp');
						$.brand.maxPrice = $(this).data('maxp');
						$('[name="price1"]', $('#J_CustomPriceBox')).val($.brand.minPrice);
						$('[name="price2"]', $('#J_CustomPriceBox')).val($.brand.maxPrice);

						$.brand.page = 1;
                        $.brand.filters.setItems(true);
						return false;
                    });

				$("#J_CustomPriceSubmit").on('click', function(e){
					var p1 = $('[name="price1"]').val(),
						p2 = $('[name="price2"]').val();
					if(p1 == '' && p2 == ''){
						alert("输入框不能同时为空！");
						return false;
					}else if(p1 == ''){
						p1 = 0;
					}else if(isNaN(parseInt(p2))){
						p2 = 10000;
					}else{
						
					}
					$.brand.priceSelector.seletedFlag = true;
					$.brand.minPrice = p1;
					$.brand.maxPrice = p2;
					$.brand.page = 1;
					$.brand.filters.setItems(true);
					return false;
				});		
				$("#J_CustomPriceBox").on('keyup', 'input', function(e) {
								var anode = $(this),
										a = $(this).val(),
										floatReg = /^[0-9]*[1-9][0-9]*$/;
									if(a == ""){
										return false;	
									}else{
									    a = parseInt(a);
										if (isNaN(a)) {
												a = 0;
											}
										if (a <= 0) {
												a = Math.abs(a);
											}	
										if ($(this).val() != a) {
												$(this).val(a);
											}
									}
									
								if (e.keyCode === 13) {
									$('#J_CustomPriceSubmit').trigger("click");
								}
								
							});
                if ($.brand.currentmasterBrandId > 0) {
                    //品牌加载更新
                    $.brand.filters.setItems(true);
                }
            },
            clear: function () {
				$('#J_priceRange>a').removeClass('active').eq(0).addClass('active');
                $.brand.minPrice = -1;
                $.brand.maxPrice = 10000;
				$('#J_CustomPriceBox').find('input').val('');
                $.brand.priceSelector.seletedFlag = false;
                $.brand.page = 1;
                $.brand.filters.setItems(true);
            }
        },
        filterClassify:{
			selector: $("#J_FilterClassify"),
            seletedFlag: false,
			init: function(){
			$.brand.filterClassify.selector.find("a").each(function () {
                    $(this).click(function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        $('body,html').animate({ scrollTop: $('#J_FilterClassify').offset().top }, 1000);
						$.brand.filterClassify.seletedFlag = true;
						$(this).addClass('active').siblings().removeClass('active');
						$.brand.filterClassifyStatus = $.trim($(this).data('status'));
                        $.brand.filters.setItems(true);
						return false;
                    });
                });
			}
		},
        filterService:{
            selector: $("#J_FiltService"),
            seletedFlag: false,
            init: function(){
                $.brand.filterService.selector.find("a").each(function () {
                    $(this).click(function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        $('body,html').animate({ scrollTop: $('#J_FilterClassify').offset().top }, 1000);
                        $.brand.filterService.seletedFlag = true;
                        $(this).addClass('active').siblings().removeClass('active');
                        $.brand.filterServiceStatus = $.trim($(this).data('type'));
                        $.brand.filterServiceVal = $.trim($(this).data('val'));
                        $.brand.filters.setItems(true);
                        return false;
                    });
                });
            }
        },
        filters: {
            filters_div: $("#J_selectedCondition"),
            init: function () {
                var items = $("ul a", $.brand.filters.filters_div);
                $.brand.filters.setItems(true);
                $(items[0]).on("click", function () {
                    $.brand.hotBrands.clear();
                    $.brand.filters.del($(this));
                });
                $(items[1]).on("click", function () {
                    $.brand.priceSelector.clear();
                    $.brand.filters.del($(this));
                });
                $(items[2]).on("click", function () {
                    $.brand.extractCity.clear();
                    $.brand.filters.del($(this));
                });

                /*$("#J_cleanAll").click(function () {
                    $.brand.filters.clear();
                });*/
            },
            clear: function () {
                $("ul a", $.brand.filters.filters_div).each(function () {
                    $.brand.filters.del($(this));
                });
                $.brand.priceSelector.clear();
				$.brand.hotBrands.clear();
				$.brand.extractCity.clear();
            },
            del: function (obj) {
                obj.hide();
                var filters_div = $.brand.filters.filters_div;
                if ($("ul a:visible", filters_div).length == 0) {
                    filters_div.hide();
                }
            },
            setItems: function (reflashList) {
                var filters_div = $.brand.filters.filters_div;
                var items = $("ul a", filters_div);
                if ($.brand.priceSelector.seletedFlag || $.brand.hotBrands.seletedFlag) {
                    filters_div.show();
						
                    if ($.brand.hotBrands.seletedFlag) {
                        items.eq(0).html("品牌：" + $.brand.currentBrandName + "<b></b>").show();
                    }
					if($.brand.priceSelector.seletedFlag){
					var priceShow = "";
                        if ($.brand.maxPrice  === $.brand.topPrice) {
                            if ($.brand.minPrice  === -1) {
                                priceShow = "全部价格";
								$('[name="price1"]').val('不限');
                            } else {
                                priceShow = $.brand.minPrice + "万以上";
								$('[name="price2"]').val('不限');
                            }
                        } else {
                            priceShow = $.brand.minPrice + "到" + $.brand.maxPrice + "万";
							$('[name="price1"]').val($.brand.minPrice);
							$('[name="price2"]').val($.brand.maxPrice);
                        }
                        items.eq(1).html("价格：" + priceShow + "<b></b>").show();
					}
                    if($.brand.extractCityName){
						items.eq(2).html($.brand.extractCityName + "<b></b>").show();
					}    
						
                }

                if (reflashList) {
						$.brand._refeshList();
                }
            }
        },
        _createListContent: function (db) {
            var $container,
                $countDown,
                dbStartDate,
                dbEndDate,
                startDateStr,
                endDateStr,
                html = '',
                $null = '<li class="null_list">你选择的条件里暂无特价车!</li>';

            if(db.list.length == 0){
                html = $null;
                $('#J_SaleCarBd').html(html);
            }else{
                $('.j_allNum').html(db.count);
                html = template('tplLi', db);

                $('#J_Pager').html(db.pagelist);
                $('#J_Pager').find('a').off().on('click', function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    var $this = $(this),
                        dbPage = $this.data('page');
                    $.brand.page = dbPage;
                    $.brand._refeshList();
                });
                $('#J_SaleCarBd').html(html);
                $('#J_SaleCarBd').find('li')

                $container = $('#J_SaleCarBd').find('li');

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
            }

        },
        _refeshList: function () {
            var loader = '<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>';
            $('#J_SaleCarBd').html(loader);
            $('#J_Pager').html("");
            $('.j_allNum').html(0);
            var db = {
                brandid: $.brand.currentBrandId,
                minprice: $.brand.minPrice,
                maxprice: $.brand.maxPrice,
                status: $.brand.filterClassifyStatus,
                pageNum: $.brand.pageSize,
                p: $.brand.page
            };
            db[$.brand.filterServiceStatus] = $.brand.filterServiceVal;

           $.post(qianzui+"/Car/ajax_special_price_car",
               db,
			function(data){
				$.brand._createListContent(data);
			});
        }
    });
})(jQuery);