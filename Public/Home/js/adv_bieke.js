/**
 * Created by Administrator on 2016/6/28.
 */
$(function(){
    var $body = $("body"),
        $advSlogan = $('<div class="adv-slogan" style="width: 100%;height: 154px;position: fixed;left: 0;bottom: 0;background: #1d1f2e;z-index: 9999;"><div class="adv-slogan-con" style="width: 1200px;height:154px;padding-top:40px;position: absolute;left: 50%;margin: 0 0 0 -600px;bottom: 0;background: url(//www.gouchehui.com/Public/Home/images/Subject/biekePop-pic.png)  no-repeat center center;"><i class="j_closeAdvPopBtn" style="display: block;cursor:pointer;width:38px;height:38px;position: absolute; right: 80px;top: 50px;"><img src="http://www.gouchehui.com/Public/Home/images/Subject/biekePop-close-btn.png" /></i><a href="http://www.gouchehui.com/index.php/subject/bieke" target="_blank" style="    display: block;width: 185px;height: 50px;position: absolute;left: 50%;bottom: 4px;margin: 0 0 0 -119px;" title="查看详情"><img src="http://www.gouchehui.com/Public/Home/images/Subject/biekePop-btn.png" /></a></div></div>');
    $body.append($advSlogan);
    $advSlogan.find('.j_closeAdvPopBtn').on('click', function(){
        $advSlogan.hide();
    });
});