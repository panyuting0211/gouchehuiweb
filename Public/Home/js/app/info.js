$(function(){
    var $leftUserInfo = $("#J_LeftUserInfo");
        $leftUserInfo.on("load", function(e, db){
        var html = template('tplLeftUserInfo', db);
        $leftUserInfo.html(html);
        
    });
    $.ajax({
				type: "POST",
				async: false,
				url: "/member.php/MemberGeneral/member_accinfo",
				dataType: "json",
				success: function(db) {
					userInfoDb = db;
        			$leftUserInfo.trigger("load", db);
				}
			});    
});

