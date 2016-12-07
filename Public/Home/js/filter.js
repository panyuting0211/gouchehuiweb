/* 筛选器 */
(function ($) {
    $.bcfilter = {};
    $.extend($.bcfilter, {
        page: 1, //当前页
        maxPage: 1, //最多加载页数
        pageSize: 20, //每页个数
        currentBrandId: '', //当前品牌ID 
        currentBrandName: '', //当前品牌名称
		currentTypeId: '',
		currentTypeName: '',		
        isLoading: false, //ajax数据是否loading
		minPrice: 0, 
        maxPrice: 10000,
        topPrice: 10000, //最大价钱
        loader: $("#J_loading"),
        init: function (options) {
            $.extend($.bcfilter, options);	
            $(window).scroll(function () {
                if ($.bcfilter.loader && $.bcfilter.loader.is(":visible")) {
                    var pageH = $(document.body).height(); //页面总高度 
                    var scrollT = $(window).scrollTop(); //滚动条top 
                    var winH = 500;
                    var aa = (pageH - scrollT) / winH;
                    if (!$.bcfilter.isLoading && aa < 2) {
                        $.bcfilter.loader.click();
                    }
                }
            });

            $.bcfilter.loader.click(function () {
                if ($.bcfilter.page < $.bcfilter.maxPage) {
                    $.bcfilter.isLoading = true;
                    $.bcfilter.page++;
                    if ($.bcfilter.currentmasterBrandId == 0) {
                        $.bcfilter.loadMore();
                    } else {
                        $.bcfilter._refeshList();
                    }
                }
            });
			$.bcfilter.cartype.init();
			$.bcfilter.priceSelector.init();
			$.bcfilter.disBrands.init();
			$.bcfilter.filters.init();
        },
		cartype: {
            //车型
			seletedFlag: false,
			init: function(){
				$("#J_carTypes>a").on('click',function(e){
					$(this).addClass('active').siblings().removeClass('active');
					if($(this).data('nh')){
						$("#J_carTypes>a").removeClass('active');
						$(this).addClass('active');
						$.bcfilter.cartype.clear();
						$.bcfilter.filters.del($("ul a", $.bcfilter.filters.filters_div).eq(0));
						return false;
					}	
				$.bcfilter.currentTypeId = $(this).data('type');
				$.bcfilter.currentTypeName = $(this).data('name');
				$.bcfilter.cartype.seletedFlag = true;
				$.bcfilter.page = 1;
                $.bcfilter.filters.setItems(true);
				return false;
				});
				$.bcfilter.currentTypeId != '' && $.each($('#J_carTypes>a'), function(i,o){
					if($(o).data('type') == $.bcfilter.currentTypeId){
						$(o).trigger("click");
					}
				});	
			},
			clear: function () {
						$('#J_carTypes>a').removeClass('active').eq(0).addClass('active');
						$.bcfilter.currentTypeId = '';
						$.bcfilter.cartype.seletedFlag = false;
						$.bcfilter.page = 1;
						$.bcfilter.filters.setItems(true);
			}			
        },
		priceSelector: {
            selector: $("#J_priceRange"),
            seletedFlag: false,
            init: function () {
                $.bcfilter.priceSelector.selector.find("a").click(function () {
						if($(this).data('nh')){
							$.bcfilter.priceSelector.selector.find('a').removeClass('active');
							$(this).addClass('active');
							$.bcfilter.priceSelector.clear();
							$.bcfilter.filters.del($("ul a", $.bcfilter.filters.filters_div).eq(1));
							return false;
						}
						$.bcfilter.priceSelector.seletedFlag = true;
						$(this).addClass('active').siblings().removeClass('active');
						$.bcfilter.minPrice = $(this).data('minp');
						$.bcfilter.maxPrice = $(this).data('maxp');
						$('[name="price1"]', $('#J_CustomPriceBox')).val($.bcfilter.minPrice);
						$('[name="price2"]', $('#J_CustomPriceBox')).val($.bcfilter.maxPrice);

						$.bcfilter.page = 1;
                        $.bcfilter.filters.setItems(true);
						return false;
                    });

				$("#J_CustomPriceSubmit").on('click', function(e){
					var p1 = $('[name="price1"]').val(),
						p2 = $('[name="price2"]').val();
					if(p1 == '' && p2 == ''){
						alert("输入框不能同时为空！");
						return false;
					}else if(isNaN(parseInt(p1))){
						p1 = 0;
					}else if(isNaN(parseInt(p2))){
						p2 = 10000;
					}else{
						
					}
					$.bcfilter.priceSelector.seletedFlag = true;
					$.bcfilter.minPrice = p1;
					$.bcfilter.maxPrice = p2;
					$.bcfilter.page = 1;
					$.bcfilter.filters.setItems(true);
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
				$.each($.bcfilter.priceSelector.selector.find("a"), function(i,o){
					var _minp = $(o).data('minp'),
						_maxp = $(o).data('maxp');

						if(_minp == $.bcfilter.minPrice && _maxp == $.bcfilter.maxPrice){
							$(o).trigger('click');
						}
				});
				
            },
            clear: function () {
				$.bcfilter.priceSelector.selector.find('a').removeClass('active').eq(0).addClass('active');
                $.bcfilter.minPrice = 0;
                $.bcfilter.maxPrice = 10000;
				$('#J_CustomPriceBox').find('input').val('');
                $.bcfilter.priceSelector.seletedFlag = false;
                $.bcfilter.page = 1;
				$.bcfilter.filters.setItems(true);
            }
        },
		disBrands: {
            //隐藏品牌的字母
			seletedFlag: false,
			init: function(){
				
				$("#J_selectFirstL>a").on('click',function(e){
					var _index = $(this).index();
					$(this).addClass('hover').siblings().removeClass('hover');
					if($(this).data('nh')){
						$(this).removeClass('hover').addClass('active');
						$('.j_bcBrand a').removeClass('active');
						$.bcfilter.disBrands.clear();
						$.bcfilter.filters.del($("ul a", $.bcfilter.filters.filters_div).eq(2));
					}
					$('.j_bcBrand').hide();
					$('.j_bcBrand').eq(_index).show()
				});
				$('.j_bcBrand a').click(function(e){
						$('.j_bcBrand a').removeClass('active');
						$('#J_selectFirstL>a').removeClass('active');
						$(this).addClass('active');
						$.bcfilter.currentBrandId = $.trim($(this).data('bid'));
						$.bcfilter.currentBrandName = $.trim($(this).text());
						$.bcfilter.disBrands.seletedFlag = true;
						$.bcfilter.page = 1;
                        $.bcfilter.filters.setItems(true);
						return false;
					});
				$.bcfilter.currentBrandId != '' && $.each($('.j_bcBrand>li>a'), function(i,o){
					var $p, _index;
					if($(o).data('bid') == $.bcfilter.currentBrandId){
						$p = $(o).closest('ul');
						_index = $(".j_bcBrand").index($p);
						$("#J_selectFirstL>a").eq(_index).trigger("click");
						$(o).trigger("click");
					}
				});	
			},
			clear: function () {
						$('#J_selectFirstL>a').removeClass('hover').eq(0).addClass('active');
						$('.j_bcBrand').hide().eq(0).show;
						$('.j_bcBrand a').removeClass('active');
						$.bcfilter.currentBrandId = '';
						$.bcfilter.currentBrandName = '';
						$.bcfilter.disBrands.seletedFlag = false;
						$.bcfilter.page = 1;
						$.bcfilter.filters.setItems(true);
			}			
        },
        filters: {
            filters_div: $("#J_selectedCondition"),
            init: function () {
                var items = $("ul a", $.bcfilter.filters.filters_div);
                $.bcfilter.filters.setItems(false);
				$(items[0]).on("click", function () {
                    $.bcfilter.cartype.clear();
                    $.bcfilter.filters.del($(this));
                });
                $(items[1]).on("click", function () {
                    $.bcfilter.priceSelector.clear();
                    $.bcfilter.filters.del($(this));
                });
				$(items[2]).on("click", function () {
                    $.bcfilter.disBrands.clear();
                    $.bcfilter.filters.del($(this));
                });

            },
            clear: function () {
                $("ul a", $.bcfilter.filters.filters_div).each(function () {
                    $.bcfilter.filters.del($(this));
                });
                $.bcfilter.priceSelector.clear();
				$.bcfilter.disBrands.clear();
            },
            del: function (obj) {
                obj.hide();
                var filters_div = $.bcfilter.filters.filters_div;
                if ($("ul a:visible", filters_div).length == 0) {
                    filters_div.hide();
                }
            },
            setItems: function (reflashList) {
                var filters_div = $.bcfilter.filters.filters_div;
                var items = $("ul a", filters_div);
                if ($.bcfilter.disBrands.seletedFlag || $.bcfilter.priceSelector.seletedFlag || $.bcfilter.cartype.seletedFlag) {
                    filters_div.show();
					if($.bcfilter.cartype.seletedFlag){
						items.eq(0).html('车型：' + $.bcfilter.currentTypeName + "<b></b>").show();
					}		
                    
					if($.bcfilter.priceSelector.seletedFlag){
					var priceShow = "";
                        if ($.bcfilter.maxPrice  === $.bcfilter.topPrice) {
                            if ($.bcfilter.minPrice  === 0) {
                                priceShow = "全部价格";
								$('[name="price1"]').val('不限');
                            } else {
                                priceShow = $.bcfilter.minPrice + "万以上";
								$('[name="price2"]').val('不限');
                            }
                        } else {
                            priceShow = $.bcfilter.minPrice + "到" + $.bcfilter.maxPrice + "万";
							$('[name="price1"]').val($.bcfilter.minPrice);
							$('[name="price2"]').val($.bcfilter.maxPrice);
                        }
                        items.eq(1).html('价格：' + priceShow + "<b></b>").show();
					}
					if ($.bcfilter.disBrands.seletedFlag) {
                        items.eq(2).html('品牌：' + $.bcfilter.currentBrandName + "<b></b>").show();
                    }
                }
                if (reflashList) {
						$.bcfilter._refeshList();
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
				$null = '<li class="null_list">抱歉，现在暂无任何车辆！</li>';

			if(db.list.length == 0){
				html = $null;
			}else{
				html = template('tplLi', db);
			}

			$('#J_Pager').html(db.pagelist);
			$('#J_Pager').find('a').off().on('click', function(e){
				e.stopPropagation();
				e.preventDefault();
				var $this = $(this),
					dbPage = $this.data('page');
				$.bcfilter.page = dbPage;
				$.bcfilter._refeshList();
			});

			$('#J_SearchCon').html(html);
        },
        _refeshList: function () {
			var loader = '<div class="onload"><img src="/Public/Home/images/common/5-121204193R7.gif" alt=""/>数据加载中...</div>';
			$('#J_SearchCon').html(loader);
           $.post("/index.php/Car/ajax_price_car_xunjia",
             {
			 brandTypeId: $.bcfilter.currentTypeId,
			 brandId: $.bcfilter.currentBrandId,
			 minprice: $.bcfilter.minPrice,
			 maxprice: $.bcfilter.maxPrice,
				 pageNum: $.bcfilter.pageSize,
				 p: $.bcfilter.page
            },
			function(data){
				$.bcfilter._createListContent(data);
			});
        }
    });
})(jQuery);