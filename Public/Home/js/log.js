$(function(){
//	登录页面
	$(".itxt").on("focus",function(){
		var a = $(this);
		a.closest(".item").removeClass("item-off").addClass("item-on");
	});
	$(".itxt").on("blur",function(){
		var a = $(this);
		a.closest(".item").removeClass("item-on");
	});
	$(".item li input[type=radio]").on("click",function(){
		$(this).parents("li").siblings("li").children("input[type=radio]").removeAttr("checked");
		$(this).attr('checked',true);
	});
	
	$("#J_Submit").on("click",function(){
		var name = $("#J_logname").val(),
		    pwd = $("#J_logpwd").val();
		
		var errorbox = $('<div class="login-msg"><i class="login-icon G_fl"></i></div>');
		$('.login-form').find('#J_Message').empty();
		
		var regtel=/^1[34578]\d{9}$/;
		
		if(name=="" && pwd==""){
			$("#J_logname").closest(".item").addClass("item-off");
			$("#J_logpwd").closest(".item").addClass("item-off");
			$("#J_Message").append(errorbox);
			errorbox.append($('<p class="error G_f12">您的账号和密码为空</p>'));
			return false;
		}else if(name==""){
			$("#J_logname").closest(".item").addClass("item-off");
			$("#J_Message").append(errorbox);
			errorbox.append($('<p class="error G_f12">您的账号为空</p>'));
			return false;
		}else if(pwd==""){
			$("#J_logpwd").closest(".item").addClass("item-off");
			$("#J_Message").append(errorbox);
			errorbox.append($('<p class="error G_f12">请填写您的密码</p>'));
			return false;
		}else if(!regtel.test(name) && $("#J_role1").attr('checked')){
			$("#J_logname").closest(".item").addClass("item-off");
			$("#J_Message").append(errorbox);
			errorbox.append($('<p class="error G_f12">您的手机号格式有误</p>'));
			return false;
		}else{
			$('form').submit();
		}
	});
	
//	注册页面
	$(".pwd-inp-text").on("focus",function(){
		$(this).removeClass("pwd-red").addClass("pwd-green");
	});
	$(".pwd-inp-text").on("blur",function(){
		$(this).removeClass("pwd-green");
	});
	$("#J_reglog").on("click",function(){
		var tel = $("#J_regtel"),
		    pwd = $("#J_regPwd"),
		    pwdre = $("#J_regPwdRe"),
		    code = $("#J_regCode"),
		    chec = $("#J_agreementChk"),
		    telval = tel.val(),
		    pwdval = pwd.val(),
		    pwdreval = pwdre.val(),
		    codeval = code.val(),
		    checval = chec.val();
		
		$('.pwd-action').find('.pwd-error').empty();
		
		var regtel=/^1[34578]\d{9}$/;
		
		if(telval==""){
			tel.addClass("pwd-red");
			tel.closest(".pwd-action-tel").append($('<div class="pwd-error">请输入您的手机号</div>'));
			return false;
		}else if(!regtel.test(telval)){
			tel.addClass("pwd-red");
			tel.closest(".pwd-action-tel").append($('<div class="pwd-error">您输入的手机号有误</div>'));
			return false;
		}else if(pwdval==""){
			pwd.addClass("pwd-red");
			pwd.closest(".pwd-action-tel").append($('<div class="pwd-error">请输入您的密码</div>'));
			return false;
		}else if(pwdreval==""){
			pwdre.addClass("pwd-red");
			pwdre.closest(".pwd-action-tel").append($('<div class="pwd-error">请再次确认您的密码</div>'));
			return false;
		}else if(pwdval!=pwdreval){
			pwdre.addClass("pwd-red");
			pwdre.closest(".pwd-action-tel").append($('<div class="pwd-error">您两次输入的密码不一致</div>'));
			return false;
		}else if(codeval==""){
			code.addClass("pwd-red");
			code.closest(".pwd-action-tel").append($('<div class="pwd-error">请输入您的验证码</div>'));
			return false;
		}else{
			$('form').submit();
		}
	});
	
})
