$(function(){
//	manual-navbar 切换
	$('#menu').metisMenu({ toggle: false });
	
//	在线反馈表单验证
	$(".J_sumbit").on("click",function(){
			var email=$('#J_feedbackEmail'),
				tel=$('#J_feedbackTel'),
				txt=$('#J_feedbackText'),
				emailval=$('#J_feedbackEmail').val(),
				telval=$('#J_feedbackTel').val(),
				txtval=$('#J_feedbackText').val();
		    
			var alertbox = $('<div class="alertbox"><i></i></div>');
		    
		    $('.feedback-form').find('.alertboxwrap').empty();
		    
		    var myreg = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]+$/;
		    if(emailval=='' || (emailval!='' && !myreg.test(emailval))){
		        email.addClass("alerterror").removeClass("alertsuccess");
				alertbox.append($('<span>对不起，您的邮箱地址有误，请填写正确的邮箱。</span>'));
				$('.feedback-form').find('.alertboxwrap').append(alertbox);
		        return false;
		    }else if(txtval==''){
		        email.removeClass("alerterror");
		        txt.addClass("alerterror").removeClass("alertsuccess");
				alertbox.append($('<span>请填写反馈信息</span>'));
				$('.feedback-form').find('.alertboxwrap').append(alertbox);
		        return false;
		    }else if(telval!=''){
		    	txt.removeClass("alerterror");
		        var regtel=/^1[3458]\d{9}$/;
		        if(!regtel.test(telval)){
		              tel.addClass("alerterror").removeClass("alertsuccess");
					  alertbox.append($('<span>请填写正确的手机号方便我们联系。</span>'));
					  $('.feedback-form').find('.alertboxwrap').append(alertbox);
		              return false;
		        }
		    }else{
		    	txt.removeClass("alerterror");
		        $('form').submit();
		    }
		    
		    
		});
	
})
