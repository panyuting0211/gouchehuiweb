/*反馈页面提示*/

$(function(){
	var email = $("#J_feedbackEmail");
		email.on('blur', function(event) {
			event.preventDefault();
			/* Act on the event */
			var txt = $(this).val();
			var reg  = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
			var alertbox = $('<div class="alertbox"><i></i></div>');
			if (!reg.test(txt)) {	
				email.css({
					"border-color":'rgb(255, 0, 0)',
					"-webkit-box-shadow":'0 0 2px rgb(255, 0, 0)',
					"-moz-box-shadow":'0 0 2px rgb(255, 0, 0)',
					"-ms-box-shadow":'0 0 2px rgb(255, 0, 0)',
					"-o-box-shadow":'0 0 2px rgb(255, 0, 0)',
					"box-shadow":'0 0 2px rgb(255, 0, 0)',
				});
				$('.feedback').find('.alertboxwrap').empty();
				alertbox.append($('<span>对不起，Email地址格式不正确，请填写正确的邮箱。</span>'));
				$('.feedback').find('.alertboxwrap').append(alertbox);
			}else{
				email.css({"border-color":'#3e933e',
					"-webkit-box-shadow":'0 0 2px #3e933e',
					"-moz-box-shadow":'0 0 2px #3e933e',
					"-ms-box-shadow":'0 0 2px #3e933e',
					"-o-box-shadow":'0 0 2px #3e933e',
					"box-shadow":'0 0 2px #3e933e',

			});

				$('.feedback').find('.alertboxwrap').empty();
				alertbox.append($('<span>邮箱可以使用！</span>'));
				alertbox.find('i').css('background-position', '-29px 3px');
				$('.feedback').find('.alertboxwrap').append(alertbox);
			}
		});
});


/*左侧滑动
*/

$(function(){
	var box = $(".ques_box");
	var a = $(".ques_navbox  a");
	// console.log(box.length);
	// console.log(a.length);
	$.each(a, function(i,o) {
		$(o).click(function(event) {
			/* Act on the event */
			event.preventDefault();	
			$(this).addClass('active').siblings('a').removeClass('active');
			$(".ques_box:lt("+ i +")").hide();
			$(".ques_box:gt("+ i + ")").show();
			$(".ques_box:eq("+ i + ")").show();

		});
	});
});