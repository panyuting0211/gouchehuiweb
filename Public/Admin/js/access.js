$(function(){
				var qxli=$("#qxid li"),
					qxdiv=qxli.children(".span3");
				qxdiv.on("mouseover",function(){
					var divindex=$(this).parents("li").index();
					console.log(divindex);
					$(this).addClass("active").parents("li").siblings("li").children(".span3").removeClass("active");
					$(this).next(".span9").show().parents("li").siblings("li").children(".span9").hide();
				});
			})
			
			
			$(function(){
				$('#qxid li .span3 input[type="checkbox"]').click(function(){
					var liindex=$(this).parents(".span3");
					var co=liindex.next(".span9");
					var incheck = $(this).attr("checked") ;
					if(incheck=="checked"){
						co.find("input[type='checkbox']").attr('checked',true);
					}else{
						co.find("input[type='checkbox']").attr('checked',false);
					}
				});
			})
			
			$(function(){
				$("#qxid li .span9 input[type='checkbox']").click(function(){
					var qxin=$(this).parents(".span9");
					var liin=qxin.siblings(".span3").find("input[type='checkbox']");
					var inlen = qxin.find("input[type='checkbox']:checked").length; 
					if(inlen>0){
						liin.attr('checked',true);
					}else{
						liin.attr('checked',false);
					}
				});
			})