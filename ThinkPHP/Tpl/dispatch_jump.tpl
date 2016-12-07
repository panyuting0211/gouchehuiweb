<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
	@charset "utf-8";
/* 跳转页 */
body,h1,h2,h3,h4,h5,h6,hr,p,blockquote,dl,dt,dd,ul,ol,li,pre,form,fieldset,legend,button,input,textarea,th,td {
	margin: 0;
	padding: 0
}
#content{width:100%;height:1080px;}
#content{ background: url(__ROOT__/ThinkPHP/Tpl/images/login-bg.jpg) no-repeat center center;}
.content_1{height:370px;width:1036px;margin:0 auto;}
.content_2{height:325px;width:1036px;margin:0 auto;}
.content_21{width:1036px;height:151px;background:url(__ROOT__/ThinkPHP/Tpl/images/che3.png) no-repeat;}
.content_22{width:1036px;height:60px;color:#fff;text-align:center;margin-top:15px;}
.content_23{width:1036px;height:90px;color:#fff;text-align:center;}

</style>
</head>


<body>

<div id="content">
	<div class="content_1"></div>
	<div class="content_2">
		<div class="content_21"></div>

		<div class="content_22">
		<?php if(isset($message)) {?>
			<h1><?php echo($message); ?></h1>
        <?php }else{?>
            <h1><?php echo($error); ?></h1>
        <?php }?>
		</div>
	
		<div class="content_23">
			<h3>页面自动<a id="href" href="<?php echo($jumpUrl); ?>">跳转</a>等待时间：<b id="wait"><?php echo($waitSecond); ?></b></h3>
		</div>
	</div>
</div>


<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>

</body>
</html>
