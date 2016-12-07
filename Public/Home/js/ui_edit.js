(function ($) {
	var qianzui = '/member.php';
	/* 筛选&喜好 */
	$.uccarFilterLike = function(options) {
			var defaults = {
				ModelsSelector: "J_models",
				carSelector: "J_car",
				ExColorSelector: "J_exColor",
				inColorSelector: "J_inColor",
				isModelFlag: !0,
				getUrl: "/Member4s/fs_ajax_car_prefer",
				brand_id: "",
				opareUrl : "/Member4s/fs_ajax_car_prefer_edit"
			};
			var opt = $.extend({}, defaults, options);
			var $modelsSelector = $("#" + opt.ModelsSelector),
				$carSelector = $("#" + opt.carSelector),
				$ExColorSelector = $("#" + opt.ExColorSelector),
				$inColorSelector = $("#" + opt.inColorSelector),
				CarModelId = 0,
				CarModelName = 0,
				CarId = "",
				CarName = "",
				moreStr = "<a href=\"#\" class=\"more\">查看其它更多<i></i></a>",

				nullStr = "<li class=\"null_li\">请添加自己喜好!</li>",
				db = {type:"get_model", brand_id: opt.brand_id},
				style = "";

			$(document).click(function () {
				$(".j_linkSelecter").find(".drop-down-sub-wrap").hide();
			});

			$modelsSelector.unbind("click").click(function(e) {
				$(".drop-down-sub-wrap").hide();
				var $that = $(this);
				var $container = $(this).find(".drop-down-sub-wrap");
				if ($container.size() > 0 && opt.isModelFlag) {
					$container.show();
					e.stopPropagation()
				} else {
					//获取生成车型
					$.ajax({url: qianzui + opt.getUrl,
						data: db,
						type: "GET",
						success: function(data){
							var modelDropWrapStr = '<div ' + style + ' class="drop-down-sub-wrap">',
								modelDropUl = "<div class=\"drop-down-sub-ul j_likeList\"><ul class=\"page-ul\">";
							var	modelLikeLis = "<li data-code=\"0\" data-codename=\"选择全部车型\"><p class=\"opt\"><i class=\"icon-nike\"></i>选择全部车型</p></li>",
								modelUnLikeLis = "",
							_moreStr = "<a href=\"#\" class=\"more\">查看其它更多<i></i></a>";

							if(data.status == 0){ //如果状态为0，表示有数据
							if(data["list"] && data["list"]["like_model_list"] && data["list"]["like_model_list"].length <= 0){
								modelLikeLis = nullStr;
								$.each(data["list"]["unlike_model_list"], function(i, o){
									modelUnLikeLis += "<li data-code=\"" + o.car_model_id +"\" data-codename=\"" + o.car_model_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_model_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
								});
							}else if(data["list"] && data["list"]["unlike_model_list"] && data["list"]["unlike_model_list"].length <= 0){
								$.each(data["list"]["like_model_list"], function(i, o){
									modelLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.car_model_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_model_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
								});
								_moreStr = "<a href=\"#\" style=\"display:none;\" class=\"more\">查看其它更多<i></i></a>";
							}else{
								$.each(data["list"]["like_model_list"], function(i, o){
									modelLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.car_model_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_model_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
								});
								$.each(data["list"]["unlike_model_list"], function(i, o){
									modelUnLikeLis += "<li data-code=\"" + o.car_model_id +"\" data-codename=\"" + o.car_model_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_model_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
								});
							}
								modelDropWrapStr += modelDropUl + modelLikeLis + "</ul></div>"+ _moreStr +"<div class=\"drop-down-sub-ul brother-drop-down-sub j_unLikeList hide\"><ul class=\"page-ul\">" + modelUnLikeLis + "</ul></div></div>";
							}else if(data.status == 1){ //如果状态为1，表示无数据
								modelDropWrapStr += data.info + "</div>";
							}

							$that.append(modelDropWrapStr);
							$that.find(".more").on("click", function(e){
								e.stopPropagation();
								e.preventDefault();
								$that.find(".j_unLikeList").toggle();
							});


							$that.find(".drop-down-sub-wrap").on("click",".opt", function(e){

								var curName = $.trim($(this).parent().data("codename"));
								var curId = $.trim($(this).parent().data("code"));
								CarModelId = curId;
								CarModelName = curName;
								var $modelsSpan = $that.find("span.drop-down-menu-result");
								if ($modelsSpan.size() > 0) {
									$modelsSpan.css("color", "#333");
									$modelsSpan.html(curName).attr("data-code", curId);
								}
								$that.find(".drop-down-sub-wrap").hide();
								$that.find("li.current").removeClass("current");
								$(this).closest("li").addClass("current");
								e.stopPropagation();
								var $carSpan = $carSelector.find("span.drop-down-menu-result");
								if ($carSpan.size() > 0) {
									$carSpan.css("color", "#333");
									$carSpan.html("请选择车款").attr("data-code", 0);
								}
								$carSelector.removeClass("no-select");
								$carSelector.find(".drop-down-sub-wrap").remove();
								$ExColorSelector.find("span.drop-down-menu-result").html("请选择外观颜色").attr("data-code", 0);
								$ExColorSelector.find(".drop-down-sub-wrap").remove();
								$inColorSelector.find("span.drop-down-menu-result").html("请选择内饰颜色").attr("data-code", 0);
								$inColorSelector.find(".drop-down-sub-wrap").remove();
								if(curId == 0){
									return false;
								}
								//获取生成车款
								$.ajax({url: qianzui + opt.getUrl,
									data:{type:"get_car", car_model_id: curId, brand_id: opt.brand_id},
									type: "GET",
									success: function(data){
										var cardropWrapStr = "<div " + style + " class=\"drop-down-sub-wrap\">",
											cardropWrapUl = "<div class=\"drop-down-sub-ul j_likeList\"><ul class=\"page-ul\">";
										var carLikeLis = "<li data-code=\"0\" data-codename=\"选择全部车款\"><p class=\"opt\"><i class=\"icon-nike\"></i>选择全部车款</p></li>",
											carUnLikeLis = "",
											_moreStr = "<a href=\"#\" class=\"more\">点击更多&gt;</a>";

										if(data.stutas == 0){
										if(data["list"] && data["list"]["like_car_list"] && data["list"]["like_car_list"].length <= 0){
											carLikeLis = nullStr;
											$.each(data["list"]["unlike_car_list"], function(i, o){
												carUnLikeLis += "<li data-code=\"" + o.car_id +"\" data-codename=\"" + o.car_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
											});
										}else if(data["list"] && data["list"]["unlike_car_list"] && data["list"]["unlike_car_list"].length <= 0){
											$.each(data["list"]["like_car_list"], function(i, o){
												carLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.car_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
											});
											_moreStr = "<a href=\"#\" style=\"display:none;\" class=\"more\">点击更多&gt;</a>";

										}else{
											$.each(data["list"]["like_car_list"], function(i, o){
												carLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.car_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
											});
											$.each(data["list"]["unlike_car_list"], function(i, o){
												carUnLikeLis += "<li data-code=\"" + o.car_id +"\" data-codename=\"" + o.car_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.car_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
											});
										}
											cardropWrapStr += cardropWrapUl + carLikeLis + "</ul></div>"+ _moreStr +"<div class=\"drop-down-sub-ul brother-drop-down-sub j_unLikeList hide\"><ul class=\"page-ul\">" + carUnLikeLis + "</ul></div></div>";

										}else if(data.stutas === 1){
											cardropWrapStr += "<p class=\"all-list-null\">" + data.info + "</p></div>";
										}


										var $parent = $("#" + opt.carSelector);
										var $con = $parent.find(".drop-down-sub-wrap");
										if ($con.size() > 0) {$con.remove()}
										$parent.append(cardropWrapStr);

										$parent.find(".more").on("click", function(e){
											e.stopPropagation();
											e.preventDefault();
											$parent.find(".j_unLikeList").toggle();
										});

										//车款绑定选中
										$parent.find(".drop-down-sub-wrap").on("click",".opt",function(e){

											var carName = $.trim($(this).parent().data("codename"));
											var carId = $.trim($(this).parent().data("code"));
											CarId = carId;
											CarName = carName;
											$parent.find("span.drop-down-menu-result").html(carName).attr("data-code", carId);
											$parent.find(".drop-down-sub-wrap").hide();
											$parent.find("li.current").removeClass("current");
											$(this).closest("li").addClass("current");
											e.stopPropagation();
											var $exColorSpan = $ExColorSelector.find("span.drop-down-menu-result");
											if ($exColorSpan.size() > 0) {
												$exColorSpan.css("color", "#333");
												$exColorSpan.html("请选择外观颜色").attr("data-code", 0);
											}
											$ExColorSelector.removeClass("no-select");
											$ExColorSelector.find(".drop-down-sub-wrap").remove();
											var $inColorSpan = $inColorSelector.find("span.drop-down-menu-result");
											if ($inColorSpan.size() > 0) {
												$inColorSpan.css("color", "#333");
												$inColorSpan.html("请选择内饰颜色").attr("data-code", 0);
											}
											$inColorSelector.removeClass("no-select");
											$inColorSelector.find(".drop-down-sub-wrap").remove();
											if(carId == 0){
												return false;
											}
											//获取生成内外饰颜色
											$.ajax({url: qianzui + opt.getUrl,
												data:{type:"get_color", car_model_id: curId, car_id: carId, brand_id: opt.brand_id},
												type: "GET",
												success: function(data){
													var colorExDropWrapStr = colorInDropWrapStr = '<div ' + style + ' class="drop-down-sub-wrap hide">',
														colorExDropWrapUl = colorInDropWrapUl = "<div class=\"drop-down-sub-ul j_likeList\"><ul class=\"page-ul\">";
													var exLikeLis = "<li data-code=\"0\" data-codename=\"选择全部外饰颜色\"><p class=\"opt\"><i class=\"icon-nike\"></i>全选外饰颜色</p></li>";
													var inLikeLis = "<li data-code=\"0\" data-codename=\"选择全部内饰颜色\"><p class=\"opt\"><i class=\"icon-nike\"></i>全选内饰颜色</p></li>";
													var	exUnlikeLis = inUnlikeLis  = "";
													var _moreStr = _inMoreStr = "<a href=\"#\" class=\"more\">点击更多&gt;</a>";

													//生成外饰颜色
													if(data["list"] && data["list"]["like_exterior_list"] && data["list"]["like_exterior_list"].length <= 0){
														exLikeLis = nullStr;

														$.each(data["list"]["unlike_exterior_list"], function(i, o){
															exUnlikeLis += "<li data-code=\"" + o.exterior_color_id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
														});

													}else if(data["list"] && data["list"]["unlike_exterior_list"] && data["list"]["unlike_exterior_list"].length <= 0){
														$.each(data["list"]["like_exterior_list"], function(i, o){
															exLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
														});
														_moreStr = "<a href=\"#\" style=\"display:none;\" class=\"more\">点击更多&gt;</a>";
													}else{
														$.each(data["list"]["like_exterior_list"], function(i, o){
															exLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
														});
														$.each(data["list"]["unlike_exterior_list"], function(i, o){
															exUnlikeLis += "<li data-code=\"" + o.exterior_color_id +"\" data-codename=\"" + o.exterior_color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.exterior_color_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
														});
													}

													colorExDropWrapStr += colorExDropWrapUl + exLikeLis + "</ul></div>"+ _moreStr +"<div class=\"drop-down-sub-ul brother-drop-down-sub j_unLikeList hide\"><ul class=\"page-ul\">" + exUnlikeLis + "</ul></div></div>";

													var $exColor = $("#" + opt.ExColorSelector);
													var $exConcar = $exColor.find(".drop-down-sub-wrap");
													if ($exConcar.size() > 0) {
														$exConcar.remove();
													}
													$exColor.append(colorExDropWrapStr);
													$exColor.find(".more").on("click", function(e){
														e.stopPropagation();
														e.preventDefault();
														$exColor.find(".j_unLikeList").toggle();
													});


													$exColor.find(".drop-down-sub-wrap").on("click",".opt",function(e){
														var colorName = $.trim($(this).parent().data("codename"));
														var colorId = $.trim($(this).parent().data("code"));
														$exColor.find("span.drop-down-menu-result").html(colorName).attr("data-code", colorId);
														$exColor.find(".drop-down-sub-wrap").hide();
														$exColor.find("li.current").removeClass("current");
														$(this).closest("li").addClass("current");
														e.stopPropagation();
													});

													//绑定外饰颜色删减喜好
													$exColor.find(".drop-down-sub-wrap").click(function(e) {
														var $cur = $(e.target),
															_data = {},
															$Like = $exColor.find(".j_likeList"),
															$unLike = $exColor.find(".j_unLikeList"),
															$li = $cur.closest("li");

														e.stopPropagation();
														if(e.target.className == "icon-opera icon-add" || e.target.className == "icon-opera icon-minus"){
															_data.action = $cur.data("type");
															_data.car_model_id = CarModelId;
															_data.car_model_name = CarModelName
															_data.car_id = CarId;
															_data.car_name = CarName;
															_data.exterior_color_id = $li.data("code");
															_data.exterior_color_name = $li.data("codename");
															_data.type = "get_exterior_color";
															_data.brand_id = opt.brand_id;

															$.ajax({
																url: qianzui + opt.opareUrl,
																data: _data,
																type: "POST",
																success: function(e){
																	console.log(_data)
																	if(e.stutas == 0 && $cur.data("type") == "add"){
																		if($Like.find(".null_li").length){
																			$Like.find(".null_li").remove();
																		}
																		$Like.find("li:first").after("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>");
																		$li.remove();

																	}else if(e.stutas == 0 && $cur.data("type") == "del"){
																		$unLike.find("ul").prepend("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>");
																		$exColor.find(".drop-down-sub-wrap").find(".more").show();
																		$unLike.show();
																		$li.remove();
																	}else{
																		HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
																	}
																}
															});
														}
													});

													//生成内饰颜色
													if(data["list"] && data["list"]["like_interior_list"] && data["list"]["like_interior_list"].length <= 0){
														inLikeLis = nullStr;
														$.each(data["list"]["unlike_interior_list"], function(i, o){
															inUnlikeLis += "<li data-code=\"" + o.interior_color_id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
														});

													}else if(data["list"] && data["list"]["unlike_interior_list"] && data["list"]["unlike_interior_list"].length <= 0){
														$.each(data["list"]["like_interior_list"], function(i, o){
															inLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
															_inMoreStr = "<a href=\"#\" style=\"display:none;\" class=\"more\">点击更多&gt;</a>";
														});

													}else{
														$.each(data["list"]["like_interior_list"], function(i, o){
															inLikeLis += "<li data-code=\"" + o.id +"\" data-codename=\"" + o.color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.color_name +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>";
														});
														$.each(data["list"]["unlike_interior_list"], function(i, o){
															inUnlikeLis += "<li data-code=\"" + o.interior_color_id +"\" data-codename=\"" + o.interior_color_name +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ o.interior_color_name +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>";
														});
													}

													colorInDropWrapStr += colorInDropWrapUl + inLikeLis + "</ul></div>"+ _inMoreStr +"<div class=\"drop-down-sub-ul brother-drop-down-sub j_unLikeList hide\"><ul class=\"page-ul\">" + inUnlikeLis + "</ul></div></div>";

													var $inColor = $("#" + opt.inColorSelector);
													var $inConcar = $inColor.find(".drop-down-sub-wrap");
													if ($inConcar.size() > 0) {
														$inConcar.remove();
													}
													$inColor.append(colorInDropWrapStr);
													$inColor.find(".more").on("click", function(e){
														e.stopPropagation();
														e.preventDefault();
														$inColor.find(".j_unLikeList").toggle();
													});
													$inColor.find(".drop-down-sub-wrap").hide();
													$inColor.find(".drop-down-sub-wrap").on("click",".opt",function(e){
														var colorName = $.trim($(this).parent().data("codename"));
														var colorId = $.trim($(this).parent().data("code"));
														$inColor.find("span.drop-down-menu-result").html(colorName).attr("data-code", colorId);
														$inColor.find(".drop-down-sub-wrap").hide();
														$inColor.find("li.current").removeClass("current");
														$(this).closest("li").addClass("current");
														e.stopPropagation();
													});
													//绑定内饰颜色删减喜好
													$inColor.find(".drop-down-sub-wrap").click(function(e) {
														var $cur = $(e.target),
															_data = {},
															$Like = $inColor.find(".j_likeList"),
															$unLike = $inColor.find(".j_unLikeList"),
															$li = $cur.closest("li");

														e.stopPropagation();
														if(e.target.className == "icon-opera icon-add" || e.target.className == "icon-opera icon-minus"){
															_data.action = $cur.data("type");
															_data.car_model_id = CarModelId;
															_data.car_model_name = CarModelName
															_data.car_id = CarId;
															_data.car_name = CarName;
															_data.interior_color_id = $li.data("code");
															_data.interior_color_name = $li.data("codename");
															_data.type = "get_interior_color";

															$.ajax({
																url: qianzui + opt.opareUrl,
																data: _data,
																type: "POST",
																success: function(e){
																	if(e.stutas == 0 && $cur.data("type") == "add"){
																		if($Like.find(".null_li").length){
																			$Like.find(".null_li").remove();
																		}
																		$Like.find("ul").find("li:first").after("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>");
																		$li.remove();
																	}else if(e.stutas == 0 && $cur.data("type") == "del"){
																		$unLike.find("ul").prepend("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>");
																		$inColor.find(".drop-down-sub-wrap").find(".more").show();
																		$unLike.show();
																		$li.remove();
																	}else{
																		HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
																	}
																}
															});
														}
													});

												}});

										});
										//绑定 车款删减喜好
										$parent.find(".drop-down-sub-wrap").click(function(e) {
											var $cur = $(e.target),
												_data = {},
												$Like = $parent.find(".j_likeList"),
												$unLike = $parent.find(".j_unLikeList"),
												$li = $cur.closest("li");

											e.stopPropagation();
											if(e.target.className == "icon-opera icon-add" || e.target.className == "icon-opera icon-minus"){
												_data.action = $cur.data("type");
												_data.car_model_id = CarModelId;
												_data.car_model_name = CarModelName;
												_data.car_id = $li.data("code");
												_data.car_name = $li.data("codename");
												_data.type = "get_car";
												_data.brand_id = opt.brand_id;

												$.ajax({
													url: qianzui + opt.opareUrl,
													data: _data,
													type: "POST",
													success: function(e){
														if(e.stutas == 0 && $cur.data("type") == "add"){
															if($Like.find(".null_li").length){
																$Like.find(".null_li").remove();
															}
															$Like.find("ul").find("li:first").after("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>");
															$li.remove();

														}else if(e.stutas == 0 && $cur.data("type") == "del"){
															$unLike.find("ul").prepend("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>");
															$parent.find(".drop-down-sub-wrap").find(".more").show();
															$unLike.show();
															$li.remove();
														}else{
															HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
														}
													}
												});
											}
										});

									}});

							});
							//绑定 车型删减喜好
							$that.find(".drop-down-sub-wrap").click(function(e) {
								var $cur = $(e.target),
									_data = {},
									$Like = $that.find(".j_likeList"),
									$unLike = $that.find(".j_unLikeList"),
									$li = $cur.closest("li");

								e.stopPropagation();
								if(e.target.className == "icon-opera icon-add" || e.target.className == "icon-opera icon-minus"){
									_data.action = $cur.data("type");
									_data.car_model_id = $li.data("code");
									_data.car_model_name = $li.data("codename");
									_data.type = "get_car_model";
									_data.brand_id = opt.brand_id;

									$.ajax({
										url: qianzui + opt.opareUrl,
										data: _data,
										type: "POST",
										success: function(e){
											if(e.stutas == 0 && $cur.data("type") == "add"){
												if($Like.find(".null_li").length){
													$Like.find(".null_li").remove();
												}
												$Like.find("ul").find("li:first").after("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"del\" class=\"icon-opera icon-minus\">-</i></li>");
												$li.remove();

											}else if(e.stutas == 0 && $cur.data("type") == "del"){
												$unLike.find("ul").append("<li data-code=\"" + $li.data("code") +"\" data-codename=\"" + $li.data("codename") +"\"><p class=\"opt\"><i class=\"icon-nike\"></i>"+ $li.data("codename") +"</p><i data-type=\"add\" class=\"icon-opera icon-add\">+</i></li>");
												$that.find(".drop-down-sub-wrap").find(".more").show();
												$unLike.show();
												$li.remove();
											}else{
												HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>'+ e.info +'</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
											}
										}
									});
								}
							});
						}});


				}

				e.stopPropagation();
				return false
			});
			$carSelector.unbind("click").click(function(e) {
				$(".drop-down-sub-wrap").hide();
				var $container = $(this).find(".drop-down-sub-wrap");
				if ($container.size() > 0) {
					$container.show()
				}

				e.stopPropagation();
				return false
			});
			$ExColorSelector.unbind("click").click(function(e) {
				$(".drop-down-sub-wrap").hide();
				var $container = $(this).find(".drop-down-sub-wrap");
				if ($container.size() > 0) {
					$container.show()
				}

				e.stopPropagation();
				return false
			});
			$inColorSelector.unbind("click").click(function(e) {
				$(".drop-down-sub-wrap").hide();
				var $container = $(this).find(".drop-down-sub-wrap");
				if ($container.size() > 0) {
					$container.show()
				}

				e.stopPropagation();
				return false
			})


		}
	/* 报价管理插件 */
	$.fn.uiOfferManage = function (options) {
		$.fn.uiOfferManage.defaults = {
			head: true,
			noeditcol: [0, 1, 2, 3, 4, 5, 7],
			editcol: [{ colindex: 6, css: "j_priceHook edit_input"},{ colindex: 7, css: "edit_input"},{ colindex: 8, css: "edit_input"},{ colindex: 9, css: "edit_input"}],
			onok: function () {
				return true;
			},
			ondel: function () {
				return true;
			},
			editcss: ["j_btnEdit", ".j_btnEdit"],
			cities: ".j_cities",
			onokcss: ["j_btnSave", ".j_btnSave"],
			canclcss: ["j_btnCancel", ".j_btnCancel"]
		};
		var options = options || {},
			opt = $.extend({}, $.fn.uiOfferManage.defaults, options),
			_btnList,
			_cityObj,
			_selectCities,
			_allBtnList = opt.onokcss[1] + "," + opt.canclcss[1] +","+ opt.editcss[1],
			trs = [],
			$trs, bjUrl,btnType,curBtn,
			orginCities = [],
			discountFlag = false,
			$loader = $("<div class=\"subdata-loading-overlay\"></div><div class=\"subdata-loading\"><p><i class=\"ui_icon\"></i>数据提交中，请稍等...</p></div>");

		function filterYesSelect(){
			$('#J_curAllArea').off('click').on('click','.yes_select', function(e){
				var _this = $(this),
					selectArea = $('#J_selectedArea'),
					selectAreaArr = selectArea.find('a'),
					pid = _this.data('pid'),
					mid = _this.data('id'),
					_flag = true,
					_p,
					_id,
					_lvl = 0,
					areaName = _this.data('name'),
					newArea = '';
				e.stopPropagation();
				e.preventDefault();
				if(pid == 1){
					_this.attr("class","selected");
					_this.siblings().attr("class","no_select");
					$.each(selectAreaArr, function(i, o){
						if($(o).data('pid') == mid){
							$(o).remove();
						}
					});
					_lvl = 2;
				}else{
					_this.attr("class","selected");
					_lvl = 3;
				}
				$.each(_this.closest("div").find("a:gt(0)"), function(i, o){
					_flag = _flag && $(o).hasClass("selected");
				});
				if(_flag){
					_p = _this.closest("div").find("a:first");
					_id = _this.closest("div").find("a:first").data("id");
					_p.attr("class","selected");
					_p.siblings().attr("class","no_select");
					$.each(selectAreaArr, function(i, o){
						if($(o).data('pid') == _id){
							$(o).remove();
						}
					});
					_lvl = 2;
					areaName = _p.data('name');
					pid = _p.data('pid');
					mid = _id;
				}

				newArea = $('<a href="javascript:void(0);" title="'+ areaName +'" data-lvl="'+ _lvl +'"  data-name="'+ areaName +'" data-pid="'+ pid +'" data-id="'+ mid +'"><abbr>'+ areaName +'<abbr><i title="\u5220\u9664' + areaName + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');

				selectArea.prepend(newArea);
			});

		}

		/**
		 * 格式化价格数值
		 *
		 * @param {String} 价格字符串
		 * @param {Number} 是否带小数点，（0：不带小数点）
		 * @return {String} 格式化后的字符串
		 **/

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
			if (type == 0) {// 不带小数位(默认是有小数位)
				var a = s.split(".");
				if (a[1] == "00") {
					s = a[0];
				}
			}
			return s;
		}
		/**
		 * 格式化价格数值
		 *
		 * @param {String} 价格字符串
		 * @param {Number} 是否带小数点，（0：不带小数点）
		 * @return {String} 格式化后的字符串
		 **/
        function buildSalesArea(selectedArr, curTr, batchCallback){
            var salesArea = $("#J_salesArea"),
                citiesList = '',
                provinceList = '',
                allAreaBox = '',
                AreaShowFlag = '',
                J_allArea = '',
				_newCitiesArr = [],
                changeCurAllArea;
            provinceList = '<select name="province" class="province_list" id="J_province"><option value="\u8bf7\u9009\u62e9\u7701\u4efd" data-id="0" >\u8bf7\u9009\u62e9\u7701\u4efd</option>';
            province && $.each(province, function(j, p){
                provinceList += '<option value="'+ p.name +'" data-id="'+ p.id +'" >'+ p.letter +' '+ p.name +'</option>';
                allAreaBox += '<div class="hide" id="J_allArea'+ p.id +'">';
                allAreaBox += '<a href="javascript:;" id="j_city'+ p.id +'" data-pid="1" data-id="'+ p.id +'" data-name="'+ p.name +'" title="选择'+ p.name +'">'+ p.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
                p.city && $.each(p.city, function(k, c){
                    allAreaBox += '<a href="javascript:;" id="j_city'+ c.id +'" data-pid="'+ p.id +'" data-id="'+ c.id +'" data-name="'+ c.name +'" title="选择'+ c.name +'">'+ c.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
                });
                allAreaBox += '</div>';
            });
            provinceList += '</select>';

            salesArea.length || (salesArea = $('<div class="sales_area" id="J_salesArea"><dl class="selected_area clearfix"><dt class="G_f16">\u5df2\u9009\u533a\u57df：</dt><dd id="J_selectedArea">'+ citiesList  +'</dd></dl><dl class="all_area clearfix"><dt class="G_f16">\u5168\u56fd\u533a\u57df：</dt><dd class="all_area_province"><a href="javascript:;" title="\u9009\u62e9\u5168\u56fd">\u5168\u56fd<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a></dd>  </dl><dl class="all_area clearfix"><dt class="G_f16">\u9009\u62e9\u9500\u552e\u533a\u57df\u0028\u7701\u4efd\u0029：</dt><dd class="all_area_province">'+ provinceList +'<span class="G_f16"><b class="ftx08">\u53cb\u60c5\u63d0\u793a：</b>\u5982\u679c\u4f60\u9009\u62e9\u7701\u4efd\uff0c\u65e2\u5305\u62ec\u4e86\u8be5\u7701\u6240\u6709\u57ce\u5e02\uff01</span></dd><dd class="all_area_city" id="J_curAllArea">' + allAreaBox + '</dd></dl><div class="btn_box"><a href="javascript:;" class="G_btn_b btn_34px j_saveAreas">\u4fdd\u5b58</a></div></div>'));

            HUI.PopOut.alert(['\u8bf7\u9009\u62e9\u60a8\u7684\u9500\u552e\u533a\u57df<a class="mask-close" target="_self" href="javascript:;" onclick="HUI.PopOut.closeMask();">关闭</a>', salesArea ,""], 3, !0, "30%"); //区域弹窗

            $('#J_curAllArea').find('a').attr("class","yes_select");
            $('.all_area_province').find('a').attr("class","yes_select");
            selectedArr.length && $.each(selectedArr, function(i, s){
                if($.trim(s) == "全国"){
                    $('#J_curAllArea').find('a').attr("class","no_select");
                    $('.all_area_province a').attr("class","selected");
                    citiesList = '<a href="javascript:void(0);" title="\u5168\u56fd" data-name="\u5168\u56fd" data-pid="0" data-lvl="1" data-id="1"><abbr>\u5168\u56fd</abbr><i title="\u5220\u9664\u5168\u56fd" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';
                    return false;
                }
                province && $.each(province, function(j, p){
                    if($.trim(s) === $.trim(p.name)){
                        citiesList += '<a href="javascript:void(0);" title="'+ p.name +'" data-lvl="2" data-name="'+ p.name +'" data-pid="1" data-id="'+ p.id +'"><abbr>'+ p.name +'</abbr><i title="\u5220\u9664' + p.name + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';
                        J_allArea = '#j_city' + p.id;
                        $(J_allArea).attr("class","selected");
                        $(J_allArea).siblings().attr("class","no_select");

                    }else{
                        p.city && $.each(p.city, function(k, c){
                            if(c.name === s){
                                J_allArea = '#j_city' + c.id;
                                $(J_allArea).attr("class","selected");
                                citiesList += '<a href="javascript:void(0);" title="'+ p.name +'" data-lvl="3" data-name="'+ c.name +'" data-pid="'+ p.id +'" data-id="'+ c.id +'"><abbr>'+ c.name +'</abbr><i title="\u5220\u9664' + c.name + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>';

                            }
                        });
                    }
                });
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

                var newArea = $('<a href="javascript:void(0);" title="\u5168\u56fd" data-lvl="1" data-name="\u5168\u56fd"  data-id="1">\u5168\u56fd<i title="\u5220\u9664\u5168\u56fd" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');
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

            $('#J_salesArea').off().on('click', '.j_saveAreas',function(e){
                curTr.find('.j_cities').find('.j_delCity').remove();
                var newCity = '';

                $.each($('#J_selectedArea').find('a'), function(i, m){
					_newCitiesArr[i] = {};
					_newCitiesArr[i]["lvl"] = $(m).data('lvl');
					_newCitiesArr[i]["name"] = $(m).data('name');
                    newCity += '<span data-name="'+ $(m).data('name') +'" data-lvl="' + $(m).data('lvl') + '" class="city j_delCity" title="'+ $(m).data('name') +'">'+ $(m).data('name') +'</span>';

                });
                $.each(curTr, function(i, o){
                            $(o).find('.j_citiesInner').html(newCity);
                });

                $('#J_curAllArea a.yes_select').off("click");
                HUI.PopOut.closeMask();
				if(batchCallback && typeof batchCallback == "function"){
					batchCallback(curTr, _newCitiesArr);
				}
            });

        }
		/*
		* 作用：创建批量区域弹窗的销售区域
		* */
		function beoBuildSalesArea(source){
			var provinceList = "",
				_newCitiesArr = [],
				J_allArea = '',
				overly = $(".BEO_area_popOverly"),
				overlyArrow = $(".BEO_area_popArrow"),
				salesArea = $(".BEO_area_pop"),
				allAreaBox = '';
			HUI.PopOut.closeMask();
			provinceList = '<select name="province" class="province_list j_beoProvince"><option value="\u8bf7\u9009\u62e9\u7701\u4efd" data-id="0" >\u8bf7\u9009\u62e9\u7701\u4efd</option>';
			province && $.each(province, function(j, p){
				provinceList += '<option value="'+ p.name +'" data-id="'+ p.id +'" >'+ p.letter +' '+ p.name +'</option>';
				allAreaBox += '<div class="hide j_beoArea'+ p.id +'">';
				allAreaBox += '<a href="javascript:;" id="j_c'+ p.id +'" class="yes_select" data-pid="1" data-id="'+ p.id +'" data-name="'+ p.name +'" title="选择'+ p.name +'">'+ p.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
				p.city && $.each(p.city, function(k, c){
					allAreaBox += '<a href="javascript:;" id="j_c'+ c.id +'" class="yes_select" data-pid="'+ p.id +'" data-id="'+ c.id +'" data-name="'+ c.name +'" title="选择'+ c.name +'">'+ c.name +'<i class="ui_icon ui_icon_16 ui_icon_wb_suc_16"></i></a>';
				});
				allAreaBox += '</div>';
			});
			provinceList += '</select>';


			if(salesArea.length && overly.length && overlyArrow.length){
				salesArea.animate({left:"50%"}, 500);
				overlyArrow.animate({left:"50%"}, 500);
				overly.show();
			}else{
				salesArea = $("<div class=\"BEO_area_pop\">"+
					"<div class=\"sales_area\">"+
					"<dl class=\"selected_area clearfix\">"+
					"<dt>已选区域：</dt>"+
					"<dd class=\"j_selectedAreas\">" +

					"</dd>" +
					"</dl>" +
					"<dl class=\"all_area clearfix\">" +
					"<dt>全国区域：</dt>" +
					"<dd class=\"all_area_province j_nationwide\"><a href=\"javascript:;\" title=\"选择全国\" class=\"yes_select\">全国<i class=\"ui_icon ui_icon_16 ui_icon_wb_suc_16\"></i></a></dd>"  +
					"</dl>" +
					"<dl class=\"all_area clearfix\">" +
					"<dt>销售区域：</dt>" +
					"<dd class=\"all_area_province\">" +
					provinceList +
					"<span><b class=\"ftx08\">友情提示：</b>省份包括了该省所有城市！</span></dd>"	+

					"<dd class=\"all_area_city j_curAllArea\">" + allAreaBox + "</dd>" +
					"</dl>" +
					"<div class=\"btn_box\">" +
					"<a href=\"javascript:;\" class=\"G_btn_b btn_34px j_saveAreas\">保存</a>" +
					"</div>"+
					"</div>" +
					"</div>");
				overly = $("<div class=\"BEO_area_popOverly\"></div>");
				overlyArrow = $("<div class=\"BEO_area_popArrow\"><i class=\"icon_left\"></i></div>");
				$("body").append(salesArea);
				$("body").append(overlyArrow);
				$("body").append(overly);
				salesArea.animate({left:"50%"},500);
				overlyArrow.animate({left:"50%"},500);
				overly.show();
				overlyArrow.find(".icon_left").on("click", function(){
					salesArea.animate({left:"-335px"},500);
					overlyArrow.animate({left:"100%"},500);
					overly.hide();

					HUI.PopOut.alert(["\u6279\u91cf\u4fee\u6539\u62a5\u4ef7", source ,""], 3, !0);
				});
				salesArea.find(".j_beoProvince").change(function(e){
					var _this = $(this),
						changeCurAllArea,
						curOpt = _this.find("option:selected"),
						curID = $.trim(curOpt.data('id'));
					changeCurAllArea = '.j_beoArea' + curID;
					$(changeCurAllArea).siblings().hide();
					$(changeCurAllArea).show();

				});
				salesArea.find('.j_nationwide').on('click','a', function(e){
					e.stopPropagation();
					e.preventDefault();
					var _this = $(this),
						selectArea = salesArea.find('.j_selectedAreas'),
						selectAreaArr = selectArea.find('a');
					_this.attr("class","selected");
					selectAreaArr.remove();
					var newArea = $('<a href="javascript:void(0);" title="\u5168\u56fd" data-lvl="1" data-name="\u5168\u56fd"  data-id="1">\u5168\u56fd<i title="\u5220\u9664\u5168\u56fd" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');
					selectArea.prepend(newArea);
					salesArea.find('.j_curAllArea').find('a').attr("class","no_select");
				});
				salesArea.find('.j_selectedAreas').on('click', 'a', function(e){
					var _this = $(this),
						allArea = $('.j_curAllArea'),
						allAreaObjs = allArea.find('a'),
						_thisId = _this.data('id'),
						_thisPid = _this.data('pid'),
						_thisAllArea = '.j_beoArea' + _thisId,
						_thisArea = '#j_c' + _thisId;
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
				});

				salesArea.find('.j_curAllArea').on('click','.yes_select', function(e){
					var _this = $(this),
						selectArea = $('.j_selectedAreas'),
						selectAreaArr = selectArea.find('a'),
						pid = _this.data('pid'),
						mid = _this.data('id'),
						_flag = true,
						_p,
						_id,
						_lvl = 0,
						areaName = _this.data('name'),
						newArea = '';
					e.stopPropagation();
					e.preventDefault();
					if(pid == 1){
						_this.attr("class","selected");
						_this.siblings().attr("class","no_select");
						$.each(selectAreaArr, function(i, o){
							if($(o).data('pid') == mid){
								$(o).remove();
							}
						});
						_lvl = 2;
					}else{
						_this.attr("class","selected");
						_lvl = 3;
					}
					$.each(_this.closest("div").find("a:gt(0)"), function(i, o){
						_flag = _flag && $(o).hasClass("selected");
					});
					if(_flag){
						_p = _this.closest("div").find("a:first");
						_id = _this.closest("div").find("a:first").data("id");
						_p.attr("class","selected");
						_p.siblings().attr("class","no_select");
						$.each(selectAreaArr, function(i, o){
							if($(o).data('pid') == _id){
								$(o).remove();
							}
						});
						_lvl = 2;
						areaName = _p.data('name');
						pid = _p.data('pid');
						mid = _id;
					}

					newArea = $('<a href="javascript:void(0);" title="'+ areaName +'" data-lvl="'+ _lvl +'"  data-name="'+ areaName +'" data-pid="'+ pid +'" data-id="'+ mid +'"><abbr>'+ areaName +'<abbr><i title="\u5220\u9664' + areaName + '" class="ui_icon ui_icon_16 ui_icon_wb_err_16"></i></a>');

					selectArea.prepend(newArea);
				});
				salesArea.find('.j_saveAreas').on('click', function(){
					var newCity = "", $sleAreas = salesArea.find(".j_selectedAreas").find("a");

					$.each($sleAreas, function(i, m){
						_newCitiesArr[i] = {};
						_newCitiesArr[i]["lvl"] = $(m).data('lvl');
						_newCitiesArr[i]["name"] = $(m).data('name');
						newCity += '<span data-name="'+ $(m).data('name') +'" data-lvl="' + $(m).data('lvl') + '" class="city j_delCity" title="'+ $(m).data('name') +'">'+ $(m).data('name') +'</span>';
					});

					salesArea.animate({left:"-335px"},500);
					overlyArrow.animate({left:"100%"},500);
					overly.hide();
					HUI.PopOut.alert(["\u6279\u91cf\u4fee\u6539\u62a5\u4ef7", source ,""], 3, !0);
					source.find(".j_beoCities").html(newCity);

				});

			}

		}
		/**
		 * 数据验证
		 *
		 *@param {Object} 传入需要验证的表单DOM对象
		 *@return {Boolean} 是否验证通过
		 **/
		function checkDb(a) {
			if (!a) {
				return false;
			}
			var value = $.trim(a.value),
				formatVal = '',
				j = 0,
				_aParent = $(a).closest('td'),
				tips = ["该编辑框不能空！",
					"底价或优惠请至少填写一个！",
					"如果没有该车库存，可填写：0",
					"如果该车没有在途，可填写：0",
					"优惠不能大于官方报价"
				]
				;
			switch (a.name) {
				case "discount":
					var _curTr = $(a).closest('tr'),
						_curSecond = _curTr.find('[data-tpn="low_price"]'),
						_curFirstPri = parseInt($.trim(_curTr.find('[data-tpn="price"]').text())),
						_thirdPri,
						qmTip;

					console.log(value);

					if (value === '') {
						var msg = '<div class="qmTip">'+ tips[0] +'<i></i></div>',
							qmTip = $(msg);
						_aParent.find('.qmTip').length > 0 ? _aParent.find('.qmTip').show() : _aParent.append(qmTip);
						discountFlag = false;
					}else if(value > _curFirstPri){
						var msg = '<div class="qmTip">'+ tips[4] +'<i></i></div>',
							qmTip = $(msg);
						_aParent.find('.qmTip').length > 0 ? _aParent.find('.qmTip').show() : _aParent.append(qmTip);
						discountFlag = false;
					}else{
						$(".qmTip").hide();
						$(a).removeClass("G_input_error2");
						_curSecondPri = _curFirstPri - parseInt(value);
						_curSecond.text(_curSecondPri);
						discountFlag = true;
					}
					return discountFlag;
					break;
				case "stock":
					if (!value) {
						var msg='<div class="qmTip">'+ tips[2] +'<i></i></div>',
							qmTip = $(msg);
						_aParent.find('.qmTip').length > 0 ? _aParent.find('.qmTip').show() : _aParent.append(qmTip);
						$(a).focus();
						$(a).addClass("G_input_error2");
						return false;
					}else{
						$(".qmTip").hide();
						$(a).val(value);
						$(a).removeClass("G_input_error2");
						return true;
					}
					break;
				case "onway":
					if (!value) {
						var msg='<div class="qmTip">'+ tips[3] +'<i></i></div>',
							qmTip = $(msg);
						_aParent.find('.qmTip').length > 0 ? _aParent.find('.qmTip').show() : _aParent.append(qmTip);
						$(a).focus();
						$(a).addClass("G_input_error2");
						return false;
					}else{
						$(".qmTip").hide();
						$(a).val(value);
						$(a).removeClass("G_input_error2");
						return true;
					}
					break;
				default:
					return false;
					break;
			}

		}

		$.each(this, function () {
			if (this.tagName.toString().toLowerCase() == "table") {
				$(this).find("tr").each(function () {
					trs.push(this);
				});
			}
			else if (this.tagName.toString().toLowerCase() == "tr") {
				trs.push(this);
			}
		});

		$trs = $(trs);
		if ($trs.size() == 0 || (opt.head && $trs.size() == 1)){
			return false;
		}

		var _button = "<a href='javascript:;' class='G_btn_a " + opt.onokcss[0] + "'>保存</a> <a href='javascript:;' class='G_btn_c " + opt.canclcss[0] + "'>取消</a>";
		$trs.each(function (i, tr) {
			if (opt.head && i == 0) {
				return true;
			}
			$(tr).find('.j_abtnInner').append(_button);
		});
		_btnList = opt.onokcss[1] + "," + opt.canclcss[1];
		$trs.find(_btnList).hide();

	//全选
        this.find("input[type=checkbox]").on("click", function (e) {
            var $inpItems = $("#J_QmTable").find("tbody").find("input"),
                $inpTrItems = $inpItems.closest("tr"),
                _thisVal = $(this).val();
            if(_thisVal == "all"){
                if(this.checked){
                    $inpItems.attr("checked","checked");
                    $inpTrItems.addClass("checked");
                }else{
                    $inpItems.removeAttr("checked");
                    $inpTrItems.removeClass("checked");
                }
            }else{
                if(this.checked){
                    $(this).closest("tr").addClass("checked");
                }else{
                    $(this).closest("tr").removeClass("checked");
                }
            }
        });
		//批量修改报价
		$("#J_BatchEditOfferBtn").on("click", function(){
			if($(this).hasClass('G_btn_disabled')){
				HUI && (HUI.PopOut.alert('<div class="prompt prompt-fail"><h3>批量添加报价，需要筛选指定车款后才能使用！</h3></div>'), $({}).delay(3e3).queue(function() {
					HUI.PopOut.closeMask()
				}));
				return false;
			}
			var $checkedInp = $("#J_QmTable tbody").find("input[type='checkbox']:checked"),
				$checkedTrs = $checkedInp.closest("tr"),
				sourceTable = "",
				operaType = $(this).data("type"),
				operaUrl = "",
				lowPrice = 0,
				_maxHeight = ($(window).height() - 167) + "px",
				newTr = "",
				btns = "<div class=\"button_box G_tc clearfix\"><input type=\"button\" class=\"j_determine G_btn_a btn_32px\" value=\"确定\"><input type=\"button\" class=\"G_btn_c btn_32px cancel\" onclick=\"HUI.PopOut.closeMask();\" value=\"取消\"> </div>",
				source = $("#J_BatchEditOffer");

			if($checkedTrs.length){
				$checkedTrs.each(function(i, o){
					var _this = $(o);
					newTr += "<tr data-id=\"" + _this.data("id") + "\" data-carid=\""+
						_this.find(".j_carId").data("carid") +
					"\">" +
							"<td data-excid=\""+
						_this.find(".j_ecColor").data("excid") +
					"\">"+
						$.trim(_this.find(".j_ecColor").text()) +
							"</td>" +
						"<td data-incid=\""+
						_this.find(".j_inColor").data("incid") +
						"\">"+
						$.trim(_this.find(".j_inColor").text()) +
						"</td>" +
							"<td class=\"half\">" +
							"<div class=\"inp_box\">" +
							"<input type=\"text\" name=\"stock\" maxlength=\"7\">" +
							"<span class=\"icon-yuan-change\">辆</span>"+
							"</div>"+
							"</td>"+
							"<td class=\"half\">"+
							"<div class=\"inp_box\">"+
							"<input type=\"text\" name=\"onway\" maxlength=\"7\" value=\"0\">"+
							"<span class=\"icon-yuan-change\">辆</span>"+
							"</div>"+
							"</td>"+
							"</tr>";
					lowPrice = _this.find(".j_price").text();
				});
				sourceTable = "<div class=\"batch_edit_offer_content\" style=\"max-height:" + _maxHeight + "\">" +
					"<div class=\"hd\">" +
					"<table class=\"grid_bundle\">" +
					"<thead>"+
					"<tr class=\"col_name\">" +
					"<th>外观颜色</th>" +
					"<th>内饰颜色</th>" +
					"<th class=\"half\">库存</th>" +
					"<th class=\"half\">在途</th>" +
					"<th>市场价</th>" +
					"<th>优惠</th>" +
					"<th>底价</th>" +
					"<th>销售区域</th>" +
					"</tr>" +
					"</thead>" +
					"</table>" +
					"</div>" +
					"<div class=\"batch_edit_offer_bd\">" +
					"<table class=\"grid_bundle\">" +
					"<tbody class=\"bd G_f14 ftx02\">" +
					"<tr>" +
					"<td colspan=\"4\">"+
					"<table class=\"j_selTable\">"+
					newTr +
					"</table>"+
					"</td>"+
					"<td>" +
					"<div class=\"inp_box\">" +
					"<input type=\"text\" name=\"price\"  value=\""+ lowPrice + "\" disabled=\"true\" data-code=\"price\" maxlength=\"8\">" +
					"<span class=\"icon-yuan-change\">元</span>" +
					"</div>" +
					"</td>" +
					"<td>" +
					"<div class=\"inp_box\">" +
					"<input type=\"text\" name=\"discount\" data-code=\"price\" maxlength=\"8\">" +
					"<span class=\"icon-yuan-change\">元</span>" +
					"</div>"+
					"</td>"+
					"<td>" +
					"<div class=\"inp_box\">" +
					"<input type=\"text\" name=\"low_price\" disabled=\"true\" data-code=\"price\" maxlength=\"8\">" +
					"<span class=\"icon-yuan-change\">元</span>" +
					"</div>" +
					"</td>" +
					"<td>" +
					"<div class=\"area_box\">" +
					"<div class=\"j_beoCities beoCities\">"+
					"</div>" +
					"<a href=\"javascript:;\" class=\"G_btn_a j_batchEditOfferArea batchEditOfferAreaBtn\">添加区域</a>" +
					"</div>" +
					"</td>" +
					"</tr>" +
					"</tbody>" +
					"</table>" +
					"</div>" +
					"</div>" +
					btns;
				if(source.length){
					source.html(sourceTable);
				}else{
					source = $("<div id=\"J_BatchEditOffer\" class=\"batch_edit_offer\">" + sourceTable +"</div>");
				}
				HUI.PopOut.alert(["\u6279\u91cf\u4fee\u6539\u62a5\u4ef7", source ,""], 3, !0);

				source.find(".j_batchEditOfferArea").on("click", function(){
					beoBuildSalesArea(source);
				});
				source.find('input:text').keyup(function(e){
					var anode = $(this),
						$anodeParent  = anode.parent(),
						$anodeTip = $anodeParent.find(".BEO_tips"),
						a = $(this).val(),
						_formatMoneyStr,
						_priceNumber,
						_lowPriceNumber,
						_discountNumber,
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
					if(anode.data("code") == "price"){
						_formatMoneyStr = formatMoney(a, 0);
						$anodeTip.length ? ($anodeTip.find("p").html("<span class=\"money-format\">" + _formatMoneyStr +"</span><span>元</span>")) : ($anodeTip = $("<div class=\"BEO_tips airBubble\"><div class=\"arrow\"><em></em><span></span></div><p><span class=\"money-format\">" + _formatMoneyStr + "</span><span>元</span></p></div>"),$anodeParent.append($anodeTip));
						$anodeTip.addClass("BEO_tips_price").show();

					}
					if(this.name == "low_price"){
						_priceNumber = parseInt(source.find('input[name=price]').val());
						_discountNumber = _priceNumber - a;
						source.find("input[name=discount]").val(_discountNumber);
					}
					if(this.name == "discount"){
						_priceNumber = parseInt(source.find('input[name=price]').val());
						_discountNumber = _priceNumber - a;
						source.find("input[name=low_price]").val(_discountNumber);
					}
				}).blur(function(){
					var $this = $(this),
						$thisParent = $this.parent(),
						$thisParentWidth = $thisParent.width(),
						$thisTipMl = "-"+ $thisParentWidth / 2 + "px",
						_thisVal = $.trim($this.val()),
						$thisTip = $thisParent.find(".BEO_tips");

					$thisTip.length || ($thisTip = $("<div class=\"BEO_tips airBubble\"><div class=\"arrow\"><em></em><span></span></div><p>该项不能为空！</p></div>"), $thisTip.css("marginLeft", $thisTipMl), $thisParent.append($thisTip));
					if(_thisVal === ""){
						$thisTip.find("p").html("该项不能为空！");
						$thisTip.removeClass("BEO_tips_price").show();
						$thisParent.addClass("G_input_error");
					}else{
						$thisParent.removeClass("G_input_error");
						$thisTip.hide();
						$this.val(parseInt(_thisVal));
					}
				});
				source.find(".j_determine").on("click",function(e){
					var _flag = true,
						data = {},
						$car_list = source.find(".j_selTable").find("tr"),
						$cities = source.find(".j_beoCities ").find("span");
					data["car_list"] = [];
					if(operaType == "edit"){
						$car_list.each(function(i,o){
							data["car_list"][i] = {};
							data["car_list"][i]["id"] = $(o).data("id");
							data["car_list"][i]["stock"] = parseInt($(o).find("input[name=stock]").val());
							data["car_list"][i]["onway"] = parseInt($(o).find("input[name=onway]").val());
						});
						operaUrl = qianzui+"/Member4s/fs_quotation_all_save";
					}else{
						$car_list.each(function(i,o){
							data["car_list"][i] = {};
							data["car_list"][i]["car_id"] = $(o).data("carid");
							data["car_list"][i]["exterior_color_id"] = $(o).find("td[data-excid]").data("excid");
							data["car_list"][i]["interior_color_id"] = $(o).find("td[data-incid]").data("incid");
							data["car_list"][i]["stock"] = parseInt($(o).find("input[name=stock]").val());
							data["car_list"][i]["onway"] = parseInt($(o).find("input[name=onway]").val());
						});
						operaUrl = qianzui+"/Member4s/FS_Quotation_add_all";
					}

					data["price"] = parseInt(source.find("input[name=price]").val());
					data["low_price"] = parseInt(source.find("input[name=low_price]").val());
					data["discount"] = parseInt(source.find("input[name=discount]").val());
					data["cities"] = [];
					$cities.each(function(i, o){
						data["cities"][i] = {};
						data["cities"][i]["lvl"] = $(o).data("lvl");
						data["cities"][i]["name"] = $(o).data("name");
					});

					source.find("input[type=text]").each(function(i, o){
							_flag = _flag && $(o).val() != "";
					});
					$(".BEO_area_pop").remove();
					$(".BEO_area_popArrow").remove();
					$(".BEO_area_popOverly").remove();

					if(_flag){
						$.ajax({
							type: "post",
							url: operaUrl,
							data: data,
							beforeSend: function () {
								$("body").append($loader);
							},
							success: function(e){
								if(e.status == 0){ //成功
									HUI && (HUI.PopOut.alert('<div class="G_succ"><i class="icon_warn"></i>提交成功！</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
									if(operaType == "edit"){
										$.each($checkedTrs, function(i, o){
											$.each(data.car_list, function(j, db){
												if(db.id == $(o).data("id")){
													$(o).find("td[data-tpn=price]").html(data.price).attr("data-md", data.price);
													$(o).find("td[data-tpn=low_price]").html(data.low_price).attr("data-md", data.low_price);
													$(o).find("td[data-tpn=discount]").html(data.discount).attr("data-md", data.discount);
													$(o).find("td[data-tpn=stock]").html(db.stock).attr("data-md", db.stock);
													$(o).find("td[data-tpn=onway]").html(db.onway).attr("data-md", db.onway);
													$(o).find(".j_citiesInner").html($cities.clone());
												}
											});
										});
									}else{
										$.each($checkedTrs, function(i, o){
											$.each(data.car_list, function(j, db){
												if(db.car_id == $(o).find(".j_carId").data("carid") && db.exterior_color_id == $(o).find(".j_ecColor").data("excid") && db.interior_color_id == $(o).find(".j_inColor").data("incid")){
													$(o).find("td[data-tpn=price]").html(data.price).attr("data-md", data.price);
													$(o).find("td[data-tpn=low_price]").html(data.low_price).attr("data-md", data.low_price);
													$(o).find("td[data-tpn=discount]").html(data.discount).attr("data-md", data.discount);
													$(o).find("td[data-tpn=stock]").html(db.stock).attr("data-md", db.stock);
													$(o).find("td[data-tpn=onway]").html(db.onway).attr("data-md", db.onway);
													$(o).find(".j_citiesInner").html($cities.clone());
												}
											});
										});
									}
								}else{//失败
									HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>提交失败！</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
								}
							},
							complete: function () {
								$loader.remove();
							},
							error: function() {
								this.errormsg()
							}
						});
					}else{
						source.find(".batch_edit_offer_bd").prepend("<p class=\"G_wrong j_chckTip\"><i class=\"icon_warn\"></i>请完善数据！</p>");
						$({}).delay(2e3).queue(function(){source.find(".j_chckTip").remove()});
					}

				});
			}else{
				HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>您还未选中任何有效的报价记录！~</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
			}
		});
		// 批量修改区域
		$("#J_ModifiedRegion").on("click",function(){
			var $checkedInp = $("#J_QmTable tbody").find("input[type='checkbox']:checked"),
				$checkedTrs = $checkedInp.closest("tr[data-id]");
			if($checkedTrs.length){
				function callbackFuc(checkedTrs, cities){
					var data = {};
					data.id_list = [];
					$.each(checkedTrs, function(i, o){
						var $curTr = $(o);
						data["id_list"][i] = $curTr.data("id");
					});
					data.cities = cities;
					$.ajax({
						type: "post",
						url: qianzui+"/Member4s/FS_Area_save_all",
						data: data,
						beforeSend: function () {
							$("body").append($loader);
						},
						success: function(e){
							if(e.status == 0){ //成功
								HUI && (HUI.PopOut.alert('<div class="G_succ"><i class="icon_warn"></i>提交成功！</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
							}else{//失败
								HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>提交失败！</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
							}
						},
						complete: function () {
							$loader.remove();
						},
						error: function() {
							this.errormsg()
						}
					});
				}
				buildSalesArea([], $checkedTrs, callbackFuc);

			}else{
				HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>您还未选中任何有效的报价记录！~</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
			}
		});
		/*批量取消车款报价*/
		$("#J_CancelQuote").on("click",function(){
			var data = {},
				_lenFlag = 0,
				souce = $("#J_BatchCancelPop"),
				$checkedInp = $("#J_QmTable tbody").find("input[type='checkbox']:checked"),
				$checkedTrs = $checkedInp.closest("tr");
			$.each($checkedTrs, function(i, o){
				var $curTr = $(o);
				if ($curTr.data("id") !== undefined){
					data[i] = $curTr.data("id");
					_lenFlag += 1;
				}
			});

			if(_lenFlag != 0){
				if(souce.length){
					souce.find(".num").text(_lenFlag);
				}else{
					souce = $("<div class='batchCancelPop' id=\"J_BatchCancelPop\"><div class=\"tips G_f14\"></div><div class=\"msg G_f16\"><p>您已选中<span class=\"num\">"+ _lenFlag +"</span>条有效的报价记录。</p><p>是否确定要取消？</p><div class='button_box clearfix'><input type='button' class='j_determine G_btn_a G_fl' value='\u786e\u5b9a'><input type='button' class='G_btn_c cancel' onclick=\"HUI.PopOut.closeMask();\" value='不确定'> </div></div>");
				}
				HUI && (HUI.PopOut.alert(["\u6279\u91cf\u53d6\u6d88", souce, ""], "m"));
				souce.find(".tips").hide();
				souce.find(".j_determine").off().on("click",function(){
					$.ajax({
						url: qianzui+"/Member4s/FS_Quotation_deletes",
						data: data,
						success:function(e){
							var $tips = souce.find(".tips"),
								tips_suc,
								tips_err;
							if(e.status == 1){
								$tips.removeClass("err").addClass("suc");
								tips_suc = "<i class=\"ui_icon ui_icon_wb_suc_16  ui_icon_16\"></i>" + e.info;
								$tips.html(tips_suc).show();
								$.each($checkedTrs, function(i, o){
									$.each(data, function(j, _id){
										$(o).data("id") == _id && ($(o).removeClass().addClass("tr-cancel-offer"), $(o).find("td:last").append("<i class='icon-cancel-offer'></i>"),$(o).find("input[type=checkbox]").remove(), $(o).find(".j_btnEdit").remove());
									});
								});
								$({}).delay(2e3).queue(function() {
									HUI.PopOut.closeMask();
								});
							}else{
								$tips.removeClass("suc").addClass("err");
								tips_err  = "<i class=\"ui_icon ui_icon_wb_err_16  ui_icon_16\"></i>" + e.info;
								$tips.html(tips_err).show();
							}
						},
						error: function() {
							this.errormsg();
						}
					});
				});
			}else{
				HUI && (HUI.PopOut.alert('<div class="G_wrong"><i class="icon_warn"></i>您还未选中任何有效的报价记录！~</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
				return false;
			}
		});
//编辑按钮
		$trs.find(opt.editcss[1]).click(function () {
			var $tr = $(this).closest("tr"),
				$cities = $tr.find('.j_delCity'),
				$tds = $tr.find("td");
			curBtn = $(this);
			$.each($cities, function(i, city){
				orginCities[i] = $(city).data('name');
			});
            if(opt.cities){
                $tr.find('.j_addCities').click(function(e){
                    e.preventDefault();
                    var _selectCityArrs = [],
                        $tr = $(this).closest('tr'),
                        curCities = $(this).closest('.j_cities').find('.j_delCity');
                    $.each(curCities, function(j, p){
                        _selectCityArrs[j] = $(p).data('name');
                    });
                    buildSalesArea(_selectCityArrs, $tr);
                });
            }
			$.each($tds.filter(":lt(" + ($tds.size() - 2) + ")"), function (i, td) {
				if ($.inArray(i, opt.noeditcol) != -1)
					return true;

				var t = $.trim($(td).text()),
					newTdWidth = ($(td).width() - 2) + "px",
					_inputTpn = $.trim($(td).data('tpn'));

				if (opt.editcol != undefined) {
					$.each(opt.editcol, function (j, obj) {
						if (obj.colindex == i) {
							var cssClass = obj.css ? "class='" + obj.css + "'" : "";

							if (obj.edittype == undefined || obj.edittype == 0) {
								$(td).html('<input type="text" name="'+ _inputTpn +'" value="' + t + '" ' + cssClass + ' style="width:'+ newTdWidth +'"/>');
							}
							else if (obj.edittype == 2) { //select
								if (obj.ctrid == undefined) {
									console.log('请指定select元素id ctrid');
									return;
								}
								$(td).empty().append($("#" + obj.ctrid).clone().show());
								$(td).find("option").filter(":contains('" + t + "')").attr("selected", true);
							}
							/* 可以在此处扩展input、select以外的元素编辑行为 */
						}
					});
				}
				else {
					$(td).html('<input type="text" name="'+ _inputTpn +'" value="' + t + 'style="width:'+ newTdWidth + '" />');
				}
				$(td).find('input:text').keyup(function(e){
					var anode = $(this),
						a = $(this).val(),
						floatReg = /^[0-9]*[1-9][0-9]*$/;
					if(a == ""){
						return false;
					}else{
						a = parseInt(a);
						if (isNaN(a)) {
							a = 1;
						}
						if ($(this).val() != a) {
							$(this).val(a);
						}
					}
					checkDb($(this).get(0));
				});
			});
			if(opt.cities){
				_cityObj = $tr.find(opt.cities);
				_cityObj.addClass("city_editing");
			}
			$tr.find(_allBtnList).toggle();
			return false;
		});

		$trs.find(opt.onokcss[1]).click(function () {
			var $tr = $(this).closest("tr"),
				$tds = $tr.find("td"),
				_editBtn = $(this).closest("td").find(".j_btnEdit"),
				_btnType = _editBtn.hasClass("add"),
				$curCities = $tr.find('.j_delCity'),
				noChangeFlag = true,
				noNullFlag = true,
				newDB = {},
				msg,
				curVal;
			newDB.cities = [];
			newDB.id = $tr.data("id");
			newDB.car_id = $tr.find('.j_carId').data('carid');
			newDB.exterior_color_id = $tr.find('.j_ecColor').data('excid');
			newDB.interior_color_id = $tr.find('.j_inColor').data('incid');
			newDB.low_price = parseInt($.trim($tr.find('[data-tpn="low_price"]').text()), 10);

			if (opt.onok()) {
				$.each(opt.editcol,function(j, obj){
					var _thisTd = $($tds.get(obj.colindex)),
						_thisMD = $.trim(_thisTd.data('md')),
						_thisTpn = $.trim(_thisTd.data('tpn')),
						c = _thisTd.children().get(0);

					if (c != null){
						if (c.tagName.toLowerCase() == "select") {
							curVal = $.trim(c.options[c.selectedIndex].text);
							noChangeFlag = noChangeFlag && curVal == _thisMD;
						}
						else if (c.tagName.toLowerCase() == "input") {
							curVal = $.trim(c.value);
							noNullFlag = noNullFlag && checkDb(c);
							noChangeFlag = noChangeFlag && curVal == _thisMD;
						}
						newDB[_thisTpn] = parseInt(curVal);
					}

				});
				$.each($curCities, function(i, city){
					newDB['cities'][i] = {};
					newDB['cities'][i]['name'] = $(city).data('name');
					newDB['cities'][i]['lvl'] = $(city).data('lvl');
				});
				if(_btnType){
					bjUrl = qianzui + '/Member4s/FS_Quotation_add';
				}else{
					bjUrl = qianzui + '/Member4s/FS_Quotation_save';
				}
				if( noNullFlag){
					$.ajax({
						type: "post",
						url: bjUrl,
						data: newDB,
						beforeSend: function () {
							$("body").append($loader);
						},
						success: function(e){
							if(e.status == 0){ //成功
								HUI && (HUI.PopOut.alert('<div class="G_succ"><i class="icon_warn"></i>\u7f16\u8f91\u6210\u529f！</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
								$.each(opt.editcol,function(j, obj){
									var _thisTd = $($tds.get(obj.colindex)),_thisMD = $.trim(_thisTd.data('md')),_thisTpn = $.trim(_thisTd.data('tpn')),c = _thisTd.children().get(0);
									if (c != null){
										if (c.tagName.toLowerCase() == "select") {
											_thisTd.html($.trim(c.options[c.selectedIndex].text));
										}
										else if (c.tagName.toLowerCase() == "input") {
											_thisTd.html($.trim(c.value));
										}
									}
								});
								$tr.find('.j_cities').removeClass('city_editing');
								$tr.find(_allBtnList).toggle();
								if(_btnType){
									_editBtn.removeClass("add");
									_editBtn.text("修改报价");
								}
							}else{//失败
								HUI && (HUI.PopOut.alert('<div class="G_succ"><i class="icon_warn"></i>'+ e.info +'</div>'), $({}).delay(2e3).queue(function(){HUI.PopOut.closeMask()}));
							}
						},
						complete: function () {
							$loader.remove();
						},
						error: function() {
							this.errormsg()
						}
					});
				}

			}
			return false;
		});

		$trs.find(opt.canclcss[1]).click(function () {
			$tr = $(this).closest("tr");
			$tds = $tr.find("td");

			$.each($tds.filter(":lt(" + ($tds.size() - 2) + ")"), function (i, td) {
				$(td).html($(td).data('md'));
				/* 可以在此处扩展input、select以外的元素取消行为 */
			});
			$tr.find('.j_delCity').remove();
			$.each(orginCities, function(j, city){
				$tr.find('.j_citiesInner').prepend('<span data-name="'+ city +'" class="city j_delCity" title="'+ city +'">'+ city +'</span>');
			});
			$tr.find('.j_cities').removeClass('city_editing');
			$tr.find(_allBtnList).toggle();
			return false;
		});
	}
	/* 报价管理分页插件 */
	$.ucPager = function(options){
		var defaults = {
			wrapper: "J_QmTable",
			pager: "J_uiOfferPager",
			page: 1,
			pageSize: 10,
			brand_id: "",
			model: 0,
			style: 0,
			exterior_color: 0,
			interior_color: 0,
			asynUrl: qianzui+"/Member4s/FS_Quotation_ajax"
		};


		var opt = $.extend({}, defaults, options);
		var $pager = $("#" + opt.pager),
			$wrapper = $("#" + opt.wrapper),
				postDb = {
					car_model_id: opt.model,
					car_id: opt.style,
					brand_id: opt.brand_id,
					exterior_color_id: opt.exterior_color,
					interior_color_id: opt.interior_color,
					pagesize: opt.pageSize,
					p: opt.page};

		$wrapper.find('tbody').prepend("<tr class='onload'><td colspan='12'><img src='/Public/Admin/images/loading.gif' alt='loading'>数据加载中....</td></tr>");
		$wrapper.find('tbody').find("tr").remove();
		$.ajax({
			url: opt.asynUrl,
			data: postDb,
			success: function(data) {
				$wrapper.find('tbody').empty();
				var pagelist=data[0];
				$pager.html(pagelist);
				var info = data[1],
					newOfferCon,
					offerSalesBtn = "",
					offerBtn = "";
				if(info.length == 0){
					$wrapper.find("tbody").append("<tr><td colspan=\"12\" class=\"null_list\">暂无任何记录！</td></tr>");
					return false;
				}

				if(postDb.car_id != 0 && info.length > 0){
					$("#J_BatchEditOfferBtn").removeClass('G_btn_disabled');
				}

				$.each(info,function(key,val){
					var _salesArea = '';
					val.sales_area_name && ($.each(val.sales_area_name, function(key,val1){
						_salesArea += "<span data-name=\""+
							val1.sales_area_name +
							"\" data-lvl=\"" +
							val1.sales_area_name +
							"\" class=\"city j_delCity\"  title=\"" +
							val1.sales_area_name +
							"\">"+
							val1.sales_area_name +
							"</span>";
					}));
					if(val.low_price != null){
						offerSalesBtn = "<a href=\"javascript:;\" class=\"add_cities G_btn_a j_addCities\" data-type=\"edit\" title=\"修改区域\">修改区域</a>";
						offerBtn = "<div class=\"abtn_inner j_abtnInner\"><a href=\"#\" class=\"G_btn_a btn_34px j_btnEdit\">修改报价</a></div>" +
							"<div class=\"alink_inner\">" +
							"<a href=\"FS_Quotation_history?car_price_id="+
							val.id  +
							"\" class=\"alinker\">历史报价</a>" +
							"</div>";
						newOfferCon = "<tr data-id=\""+
							val.id+
							"\">" +
							"<td><input type='checkbox' value='0'></td>" +
							"<td>" +
							val.car_model_name +
							"</td>" +
							"<td class=\"j_carId\" data-carid=\"" +
							val.car_id +
							"\">" +
							val.car_name +
							"</td>" +
							"<td class=\"j_ecColor\" data-excid=\"" +
							val.exterior_color_id +
							"\">" +
							val.exterior_color_name +
							"</td>" +
							"<td class=\"j_inColor\" data-incid=\"" +
							val.interior_color_id +
							"\">" +
							val.interior_color_name	+
							"</td>" +
							"<td class=\"j_price\" data-md=\"" +
							val.auth_price +
							"\" data-tpn=\"price\">" +
							val.auth_price +
							"</td>" +
							"<td data-md=\"" +
							val.discount +
							"\" data-tpn=\"discount\">" +
							val.discount +
							"</td>" +
							"<td data-md=\"" +
							val.low_price +
							"\" data-tpn=\"low_price\">" +
							val.low_price +
							"</td>" +
							"<td data-md=\"" +
							val.stock +
							"\" data-tpn=\"stock\">" +
							val.stock +
							"</td>" +
							"<td data-md=\"" +
							val.onway +
							"\" data-tpn=\"onway\">" +
							val.onway +
							"</td>" +
							"<td class=\"cities j_cities\">" +
							"<div class=\"j_citiesInner\">" +
							_salesArea +
							"</div>" +
							offerSalesBtn +
							"</td>" +
							"<td>" +
							offerBtn +
							"</td>"	+
							"</tr>";
					}
					else{
						offerSalesBtn = "<a href=\"javascript:;\" class=\"add_cities G_btn_a j_addCities\" title=\"添加区域\">添加区域</a>";
						offerBtn = "<div class=\"abtn_inner j_abtnInner\"><a href=\"#\" class=\"G_btn_a btn_34px j_btnEdit add\" data-type=\"add\">添加报价</a></div>";
						newOfferCon = "<tr>" +
							"<td><input type='checkbox' value='0'></td>" +
							"<td>" +
							val.car_model_name +
							"</td>" +
							"<td class=\"j_carId\" data-carid=\"" +
							val.car_id +
							"\">" +
							val.car_name +
							"</td>" +
							"<td class=\"j_ecColor\" data-excid=\"" +
							val.exterior_color_id +
							"\">" +
							val.exterior_color_name +
							"</td>" +
							"<td class=\"j_inColor\" data-incid=\"" +
							val.interior_color_id +
							"\">" +
							val.interior_color_name	+
							"</td>" +
							"<td  class=\"j_price\" data-md=\""+ val.auth_price +"\" data-tpn=\"price\">"+ val.auth_price +"</td>" +
							"<td data-md=\"\" data-tpn=\"discount\"></td>" +
							"<td data-md=\"\" data-tpn=\"low_price\"></td>" +
							"<td data-md=\"\" data-tpn=\"stock\"></td>" +
							"<td data-md=\"\" data-tpn=\"onway\"></td>" +
							"<td class=\"cities j_cities\">" +
							"<div class=\"j_citiesInner\">" +
							_salesArea +
							"</div>" +
							offerSalesBtn +
							"</td>" +
							"<td>" +
							offerBtn +
							"</td>"	+
							"</tr>";
					}
					$wrapper.find("tbody").append(newOfferCon);
				});

				$wrapper.uiOfferManage();
			},
			error: function() {
				this.errormsg()
			}
		});
	}
})(jQuery);
