requirejs.config({
    baseUrl:"/Public/Home/js",
    paths:{
        "jquery": "lib/jquery-1.8.1.min"
    }
});

requirejs(['jquery', 'app/tpl'], function($, tpl){
    var content = $("#J_LeftUserInfo");
    content.on("load", function(e, db){
        var html = tpl('tplLeftUserInfo', db);
        content.html(html);
    });

    $.when($.ajax("http://member.gouchehui.local/index.php/MemberGeneral/member_accinfo"), $.ajax("http://member.gouchehui.local/index.php/MemberGeneral/sysmessage")).done(function(basicInfo, sysMess){
        console.log(sysMess);
        console.log(basicInfo);
    });
    
    // $.post("http://member.gouchehui.local/index.php/MemberGeneral/member_accinfo", function(db){
    //     content.trigger("load", db);
    //     sysmessage
    // });
});