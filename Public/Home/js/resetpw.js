$(function(){
	$("#form1 input:password").keyup(function(e) {
		if (e.keyCode === 13) {
			$("[name='commit']", $(this).parents("form")).trigger("click");
		}
	}).blur(function(){
	  checkData(this);
	});

		function checkData(ele, blurCheck) {
		if (!ele) {
				return true;
			}
		var value = $.trim(ele.value);
		switch (ele.name) {
			case "password":
					if (!value || value.length < 6) {
						$('#password-state').hide();
						$("#password-tip").addClass("err").show();
						$("#password-tip .input-tip-inner").html("<span class='G_wrong'><i class='icon_warn'></i>请输入正确的密码（密码长度最少6位以上）</span>");
						$(ele).addClass('G_input_error');
						return false;
					} else {
						$(ele).removeClass('G_input_error');
						$('#password-state').show();
						$("#password-tip").removeClass("err").hide();
						$("#password-tip .input-tip-inner").html("");
						var pass = $.trim($(ele).val());
						var level_score = 0;
						var rating = 0;
						var numericTest = /[0-9]/;
						var lowerCaseAlphaTest = /[a-z]/;
						var upperCaseAlphaTest = /[A-Z]/;
						var symbolsTest = /[\\.,!@#$%^&*()}{:<>|]/;
						if (numericTest.test(pass)) {
							level_score++;
						}
						if (lowerCaseAlphaTest.test(pass) || upperCaseAlphaTest.test(pass)) {
							level_score++;
						}
						if (symbolsTest.test(pass)) {
							level_score++;
						}
						if (pass.length > 0 && pass.length < 6) {
							rating = 1;
						} else if (level_score < 3 && pass.length >= 6) {
							rating = 2;
						} else if (level_score = 3 && pass.length >= 6) {
							rating = 3;
						}
						if (rating == 1) {
							$('#password-state .input-tip-inner span').attr('class', '');
							$('#password-state .input-tip-inner span').addClass('reg_mes mes_pass_weak');
						} else if (rating == 2) {
							$('#password-state .input-tip-inner span').attr('class', '');
							$('#password-state .input-tip-inner span').addClass('reg_mes mes_pass_in');
						} else if (rating == 3) {
							$('#password-state .input-tip-inner span').attr('class', '');
							$('#password-state .input-tip-inner span').addClass('reg_mes mes_pass_strong');
						}
						
						return true;
					}
					break;
		case "repassword":
					if (!ele.value || ele.value.length < 6) {
						$("#repassword-tip").addClass("err").show();
						$("#repassword-tip .input-tip-inner").html("<span class='G_wrong'><i class='icon_warn'></i>请输入正确的密码（密码长度最少6位以上）</span>");
						return false;
					} else if (ele.value != $("#password").val()) {
						$("#repassword-tip").addClass("err").show();
						$("#repassword-tip .input-tip-inner").html("<span class='G_wrong'><i class='icon_warn'></i>两次输入密码不一致！</span>");
						return false;
					} else {
						$("#repassword-tip").removeClass("err").hide();
						$("#repassword-tip .input-tip-inner").html("");
						return true;
					}
					break;
		default:
		  return false;
		  break;			
	}	
		}
	$("#J_Submit").bind("click", function() {

		var pwCode = checkData(document.getElementById("password"));
		var rePwCode = checkData(document.getElementById("repassword"));
		
		if (!pwCode || !rePwCode) {
			return false;
		}
		$('#form1').submit();
	});	
});