/**
 * Created by Administrator on 2016/5/5.
 */
(function ($) {

    $.fn.ymdate = function (options) {

        //创建一些默认值，拓展任何被提供的选项
        var defaults = {
            startyear: 1900,
            endyear: 2100,
            year:"year",
            month:"month",
            day:"day"
        };

        var settings = $.extend({},defaults, options);//将一个空对象做为第一个参数

        var _this = $(this);
        var _year = _this.val();

        /*年份*/
        var _yearstring = '<option value="0">选择年份</option>';
        for(var i =settings.startyear ; i<= settings.endyear ;i++){
            _yearstring += '<option value="'+ i +'">'+ i +'</option>';
        };
        _this.append(_yearstring);
        //_this.val(settings.year);

        /*mouth*/
        var _mouthstring = '<option value="0">选择月份</option>';
        for(var j =1 ; j<= 12;j++){
            _mouthstring += '<option value="'+ j +'">'+ j +'</option>';
        }
        _this.on("change",function(){
            _this.next().html(_mouthstring);
        });


        function addday(n){
            var _daystring = '';
            for(var j =1 ; j<= n;j++){
                _daystring += '<option value="'+ j +'">'+ j +'</option>';
            };
            return _daystring;
        }

        _this.next().on("change",function(){
            var daunum;
            var _mouth = _this.next().val();

            if(_mouth == 2){    //2月份
                if((_year % 4 == 0 && _year%100 != 0) || _year %400 == 0){  //闰年
                    daunum = addday(29);
                }else{  // 非闰年
                    daunum = addday(28);
                }
            }else if(_mouth == 1 || _mouth == 3 || _mouth == 5 || _mouth == 7 || _mouth == 8 || _mouth == 10 || _mouth == 12){
                //31天的月份
                daunum = addday(31);
            }else{
                //30的月份
                daunum = addday(30);
            }
            _this.next().next().html(daunum);
        })

    };
})(jQuery);
