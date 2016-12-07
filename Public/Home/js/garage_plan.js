$(function(){
    var qianzhui = '/member.php';
    !function(){
            $("#J_AddMyCarForm").validate({
                debug: false, //调试模式取消submit的默认提交功能   
                focusInvalid: false, //当为false时，验证无效时，没有焦点响应  
                onkeyup: false,   
                submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                    var $form = $(form);

                         $.ajax({
                                    type: "POST",
                                    url: qianzhui+"/MemberGeneral/carplan_add",
                                    dataType: "json",
                                    data: {
                                        car_id: $form.find("[name=car]").val(),
                                        province_id: $form.find("[name=province]").val(),
                                        city_id: $form.find("[name=city]").val(),
                                        buy_time: $form.find("[name=buy_time]").val()
                                    },
                                    success:function(data){
                                        if(data.status == 1){
                                            layer.msg("添加爱车失败！");
                                        }else if(data.status == 2){
                                            location.reload();
                                        }
                                     }
                                            });
                },   
                rules:{
                    brand:{
                        required:true
                    },
                    model:{
                        required:true
                    },
                    car:{
                        required:true
                    },
                    province:{
                        required:true
                    },
                    city:{
                        required:true
                    },
                    bug_time:{
                        required:true
                    }                     
                },
                messages:{
                    brand:{
                        required:"请选择品牌"
                    },
                    model:{
                        required:"请选择车型"
                    },
                    car:{
                        required:"请选择车款"
                    },
                    province:{
                        required:"请选择省份"
                    },
                    city:{
                        required:"请选择城市"
                    },
                    bug_time:{
                        required:"请选择购车时间"
                    }                              
                }         
            });  

    $('#J_DatetimePicker').datetimepicker({
            lang:"ch",
            timepicker:false,
            format:'Y-m-d',
            maxDate: '+0' //当前时间以后的不能选中
            //minDate:'-1970/01/01'  当前时间以前的不能选中
        });
    $('#J_citySelector').cxSelect({
      selects: ['province', 'city'],
      emptyStyle: 'none',
      parentSeletor: 'li', 
      jsonName: 'name',
      jsonValue: 'value'
    });

    /* 品牌车款联动 */
    $('#J_carSelector').cxSelect({
      selects: ['brand', 'model', 'car'],
      emptyStyle: 'none',
      parentSeletor: 'li',
      jsonName: 'name',
      jsonValue: 'value'
    }); 

    }();
    

    //生成我的爱车列表
    !function(){
        var html = null,
            allNum = 3,
            adderNum = 0,
            remainingNum = 3,
            $gar_myCarList = $('#J_GarMyCarList'),
            $loader = $('<div class="uc_loading"><i></i>数据加载中...</div>'),
            $remainingNumSel = $('.j_remainingNum');
            $gar_myCarList.find(".uc_loading").length || ($gar_myCarList.append($loader));
        $.post(qianzhui+"/MemberGeneral/carplan", function(db){
            adderNum = db.list.length
            remainingNum = allNum - adderNum
            $remainingNumSel.html(remainingNum);    
            $gar_myCarList.find('.uc_loading').remove();

            if(adderNum){
                html = template('tplGarBuyPlanList', db);
                $gar_myCarList.html(html); 
            }
            if(remainingNum == 0){
                $('#J_AddMyCarForm').remove();
            }
            //绑定删除按钮
            $gar_myCarList.find('.j_delBtn').on('click', function(){
                var $this = $(this),
                    $thisP = $this.closest('.car_box'),
                    _id = $thisP.data('id');
                    layer.confirm('是否确定删除！', {
                              btn: ['是','否'] //按钮
                            }, function(){
                                $.get(qianzhui+"/MemberGeneral/carplan_delete?id=" + _id, function(db){
                                    if(db.status == 1){
                                        layer.msg(db.msg);
                                    }else if(db.status == 2){
                                        location.reload();
                                    }else{
                                        layer.msg(db.msg);
                                    }
                                });
                              
                            }, function(){
                                layer.closeAll();
                            });
                    
                    
            });
            //绑定编辑按钮
            $gar_myCarList.find('.j_editBtn').on('click', function(){
                var $this = $(this),
                    $thisP = $this.closest('.car_box'),
                    _id = $thisP.data('id'),
                    _tit = $thisP.find('h5').html(),
                    _imgUrl = $thisP.find('img').attr('src'),
                    _provinceid = $thisP.data('provinceid'),
                    _cityid = $thisP.data('cityid'),
                    _brandid = $thisP.data('brandid'),
                    _carmodelid = $thisP.data('carmodelid'),
                    _carid = $thisP.data('carid'),
                    _bigBox = "",
                    _buytime = $thisP.data('buytime');

            });

            });
    }();
    

     
});