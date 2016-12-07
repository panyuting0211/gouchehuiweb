$('#J_LifeList').slide({mainCell:".life-con-bd", titCell:".life-tit-hd li"});
$('#J_CarBan').slide({mainCell:".carban-bd ul",effect:"fold", interTime:6000, autoPlay:true});

		$(function(){
			$('.j_commentBox').CarLifeComment();
			$.CommentList.init();
			$('.j_bbsZan').on('click', function(e){
				if (!$.G.getUSERID()) {
					HUI.PopOut.login();
					return false;
				}
				var $this = $(this),
					$num = $this.find('.num'),
					dbCount = parseInt($.trim($num.text()), 10),
					dbId = $this.data('id');
				$.post('/index.php/Carlife/thumbs_bbs',
					{bbs_id: dbId},
					function(db){
						if(db.status == 1){
							if($this.hasClass('isZan')){
								$this.removeClass('isZan');
								$num.text(dbCount - 1);
							}else{
								$this.addClass('isZan');
								$num.text(dbCount + 1);
							}
						}else{
							layer.msg(db.msg, {icon: 2});
						}

					});
			});
			$(".life-con-div .life-reading li").hover(function(){
				$(this).find(".life-rewz").animate({bottom:0});
				$(this).css({"border":"1px solid #ff9921"});
			},function(){
				$(this).find(".life-rewz").animate({bottom:"-30px"});
				$(this).css({"border":"1px solid #fafafa"});
			});
			
			
		})