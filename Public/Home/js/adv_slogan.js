/**
 * Created by Administrator on 2016/6/24.
 */
$(function(){
    var $body = $("body"),
        $advSlogan = $('<div class="adv-slogan" style="    width: 100%;height: 123px;position: fixed;left: 0;bottom: 0;background: #403d83;z-index: 9999;"><div class="adv-slogan-con" style="width: 1200px;height:144px;position: absolute;left: 50%;margin: 0 0 0 -600px;bottom: 0;background: url(//www.gouchehui.com/Public/Home/images/Subject/sloganPop-pic.png)  no-repeat center center;"><i class="j_closeAdvPopBtn" style="display: block;cursor:pointer;width:38px;height:38px;position: absolute; left: 80px;top: 54px;"><img src="http://www.gouchehui.com/Public/Home/images/Subject/sloganPop-close-btn.png" /></i><a href="http://www.gouchehui.com/index.php/subject/slogan" target="_blank" style="display: block;width: 231px;height: 72px;position: absolute;left: 50%;top: 50%;margin-top: -27px;" title="查看详情"><img src="http://www.gouchehui.com/Public/Home/images/Subject/sloganPop-btn.png" /></a></div></div>');
    $body.append($advSlogan);
    $advSlogan.find('.j_closeAdvPopBtn').on('click', function(){
        $advSlogan.hide();
    });
});