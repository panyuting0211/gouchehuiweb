
(function($){
    $.fn.autoMail = function(options){
        var opts = $.extend({}, $.fn.autoMail.defaults, options),
            __staticInpVal = '';
            isNewdbFlag = true;
        return this.each(function(){
            var $this = $(this),
                $form = $this.closest('form');
            var o = $.meta ? $.extend({}, opts, $this.data()) : opts;
            var top = $this.outerHeight(true);
            var $mailBox = $('<div class="search-box-result" id="mailBox"></div>');
            $form.css('position', 'relative');
            $mailBox.css({
                'position': 'absolute',
                'top': top + 'px',
                'left': '0',
                'width': $this.outerWidth(true) + 'px'
            });
            $form.append($mailBox);

            //设置高亮li
            function setEmailLi(index){
                $('#mailBox li').removeClass('cmail').eq(index).addClass('cmail');
            }
            //初始化邮箱列表
            var emails = o.emails;
            var init = function(input){
                var inpVal = $.trim(input.val());
                //取消浏览器自动提示
                input.attr('autocomplete','off');

                //添加提示邮箱列表
                if(inpVal != '' && __staticInpVal != inpVal){
                    $.ajax({
                        type: "POST",
                        url: "/index.php/api/carSearch",
                        dataType: "json",
                        data: {
                            select: inpVal,
                        },
                        success:function(data){
                            if(data.status == 1){
                                var emailList = '<ul>';
                                $.each(data.list, function(i, v){
                                    emailList += '<li><a href="'+ v.url +'" title="'+ v.carstyle + v.car_name +'">'+ v.carstyle + v.car_name + '</li>';
                                });
                                emailList += '</ul>';
                                $mailBox.html(emailList).show(0);
                                $mailBox.find('li').hover(function(){
                                   $(this).addClass('hover').siblings().removeClass('hover');
                                });

                                __staticInpVal = inpVal;
                            }else{
                                $mailBox.hide(0);
                            }
                        }
                    });
                }else{
                    $mailBox.hide(0);
                }

                //添加鼠标事件
                $('#mailBox li').hover(function(){
                    $('#mailBox li').removeClass('cmail');
                    $(this).addClass('cmail');
                },function(){
                    $(this).removeClass('cmail');
                }).click(function(){
                    input.val($(this).html());
                    $mailBox.hide(0);
                });
            }
            //当前高亮下标
            var eindex = -1;
            //监听事件
            $this.keyup(function(event){
                var re = /[^\u4e00-\u9fa5]/;
                if(!re.test($.trim($this.val()))){
                    init($this);
                }
                //如果在表单中，防止回车提交
            }).keydown(function(event){
                //if(event.keyCode == 13){
                //    return false;
                //}
            });
        });
    }
    $.fn.autoMail.defaults = {
        emails:[]
    }
})(jQuery);