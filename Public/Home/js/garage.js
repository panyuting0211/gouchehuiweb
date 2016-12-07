$(function(){
    var qianzhui='/member.php';
    !function(){
    $('#J_citySelector').cxSelect({
      selects: ['province', 'city'],
      emptyStyle: 'none',
      parentSeletor: 'li',
      jsonName: 'name',
      jsonValue: 'value'
    });

    // 品牌车款联动
    $('#J_carSelector').cxSelect({
      selects: ['brand', 'model', 'car'],
      emptyStyle: 'none',
      parentSeletor: 'li',
      jsonName: 'name',
      jsonValue: 'value'
    });

     $.validator.setDefaults({
        errorElement:'span'
        });

    $("#J_AddMyCarForm").validate({ 
        debug: true, //调试模式取消submit的默认提交功能
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
                    }                     
                },
                messages:{
                    brand:{
                        required:"请选择您爱车的品牌"
                    },
                    model:{
                        required:"请选择您爱车的系列车型"
                    },
                    car:{
                        required:"请选择您爱车的车款"
                    },
                    province:{
                        required:"请选择您爱车的所在地省份"
                    },
                    city:{
                        required:"请选择您爱车的所在地城市"
                    }                              
                },submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                    var $form = $(form);
                         $.ajax({
                                    type: "POST",
                                    url: qianzhui+"/MemberGeneral/carlove_add",
                                    dataType: "json",
                                    data: {
                                        car_id: $form.find("[name=car]").val(),
                                        province_id: $form.find("[name=province]").val(),
                                        city_id: $form.find("[name=city]").val(),
                                        buy_time: $form.find("[name=buy_time]").datetimepicker().val(),
                                    },
                                    success:function(data){
                                        if(data.status == 1){
                                            layer.msg("添加爱车失败！");
                                        }else if(data.status == 2){
                                            location.reload();
                                        }
                                     }
                                            });
                }        
            });
            $('#J_DatetimePicker').datetimepicker({
            lang:"ch",
            timepicker:false,
            format:'Y-m-d',
            maxDate: '+0' //当前时间以后的不能选中
            //minDate:'-1970/01/01'  当前时间以前的不能选中
        });
}();
    //生成我的爱车列表
    !function(){
        $.validator.setDefaults({
                                    errorElement:'span',
                                });
        var html = null,
            allNum = 3,
            adderNum = 0,
            remainingNum = 3,
            $gar_myCarList = $('#J_GarMyCarList'),
            $loader = $('<div class="uc_loading"><i></i>数据加载中...</div>'),
            $remainingNumSel = $('.j_remainingNum');
            $gar_myCarList.find(".uc_loading").length || ($gar_myCarList.append($loader));
        $.post(qianzhui+"/MemberGeneral/carlove", function(db){
            adderNum = db.list.length;
            remainingNum = allNum - adderNum;
            $remainingNumSel.html(remainingNum);    
            $gar_myCarList.find('.uc_loading').remove();

            if(adderNum){
                html = template('tplGarMyCarList', db);
                $gar_myCarList.html(html); 
            }
            $gar_myCarList.find('.j_delBtn').on('click', function(){
                var $this = $(this),
                    $thisP = $this.closest('.car_box'),
                    _id = $thisP.data('id');
                    layer.confirm('是否确定删除！', {
                              btn: ['是','否'] //按钮
                            }, function(){
                                $.get(qianzhui+"/MemberGeneral/carlove_delete?id=" + _id, function(db){
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
            //绑定编辑框
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
                    bigBox = "",
                    _buytime = $thisP.data('buytime');
                    bigBox = '<div class="add_buyCar_pop">'+
                            '<div class="box">'+
                            '<div class="face G_tc">'+
                            '<div class="productImg_wrap">'+
                            '<span class="productImg">'+
                            '<img src="'+ _imgUrl
                            +'" alt="'+
                            _tit +'">'+
                            '</span>'+
                            '</div>'+
                            '</div>'+

                            '<div class="info">'+

                            '<div class="uc_g_opera_box">'+
                            '<div class="uc_formlist">'+
                            '<form action="" class="j_CarFromPop">' +
                            '<ul class="uc_carSelector j_carSelPop">'+
                                '<li>'+
                                    '<span class="hd"><em>*</em>我的爱车:</span>'+
                                    '<div class="inp_box">'+
                                        '<select data-value="'+ 
                                        _brandid
                                        +'" class="brand uc_inp190 uc_inpBorder Skin_txt3" name="brand" id="" data-url="'+qianzhui+'/MemberGeneral/brand"  style=""></select>'+'</div>'+

                                '</li>'+
                                '<li>'+
                                    '<span class="hd"></span>'+
                                    '<div class="inp_box">'+
                                        '<select data-value="'+ 
                                        _carmodelid
                                        +'" class="model uc_inp190 uc_inpBorder Skin_txt3" name="model" id="" data-url="'+qianzhui+'/MemberGeneral/car_model"  style=""></select>'+
                                    '</div>'+

                                '</li>'+
                                '<li>'+
                                    '<span class="hd"></span>'+
                                    '<div class="inp_box">'+
                                        '<select data-value="'+ 
                                        _carid
                                        +'" class="car uc_inp190 uc_inpBorder Skin_txt3" name="car" id="" data-url="'+qianzhui+'/MemberGeneral/car"  style=""></select>'+
                                    '</div>'+

                                '</li>'+
                            
                            '</ul>'+
                            '<ul class="uc_citySelector j_citySelPop">'+    
                                '<li>'+
                                    '<span class="hd"><em>*</em>所  在  地:</span>'+
                                    '<div class="inp_box">'+
                                    '<select data-value="'+ _provinceid +'"  class="province uc_inp190 uc_inpBorder Skin_txt3" name="province" id="" data-url="'+qianzhui+'/MemberGeneral/province" style=""></select>'+
                                        '</div>'+
                                '</li>'+        
                                '<li>'+
                                        '<span class="hd"></span>'+        
                                        '<div class="inp_box">'+
                                        '<select data-value="'+ _cityid +'" class="city uc_inp190 uc_inpBorder Skin_txt3" name="city" id="" data-url="'+qianzhui+'/MemberGeneral/city"></select>'+
                                    '</div>'+
                                '</li>'+
                            '</ul>'+
                            '<ul>'+   
                            '<li>'+     
                                    '<span class="hd"><em>*</em>购买时间:</span>'+
                                    '<div class="inp_box">'+
                                        '<input name="buy_time" class="j_datetimePicker dataInp uc_inpBorder Skin_txt3" type="text" value="">' +
                                    '</div>'+

                                '</li>'+
                                '<li><span class="hd"></span><div class="inp_box"><input type="submit" class="G_btn_a btn_30px" value="提交"></div></li>'+
                            '</ul>'+
                            '</form>' +
                            '</div>'+
                            '</div>'+
                            '</div>'+
                            '</div>'+
                            '</div>';
                             //生成添加到预购车库弹窗
                            layer.open({
                              type: 1,
                              area: "400px",
                              zIndex: 9996,
                              title: "爱车信息编辑框",
                              closeBtn: 1,
                              move: false,
                              shadeClose: true,
                              skin: 'addBuyCar_box',
                              content: bigBox,
                              success: function(layero, index){
                                //弹窗绑定品牌车型车款选择
                                layero.find('.j_carSelPop').cxSelect({
                                  selects: ['brand', 'model', 'car'],
                                  jsonName: 'name',
                                  jsonValue: 'value'
                                });
                                //弹窗绑定城市
                                layero.find('.j_citySelPop').cxSelect({
                                  selects: ['province', 'city'],
                                  jsonName: 'name',
                                  jsonValue: 'value'
                                });
                                //购车时间
                                layero.find('.j_datetimePicker').datetimepicker({
                                    value: _buytime,
                                    lang:"ch",
                                    timepicker:false,
                                    format:'Y-m-d',
                                    maxDate: '+0' //当前时间以后的不能选中
                                    //minDate:'-1970/01/01'  当前时间以前的不能选中
                                });

                                //绑定submit
                                layero.find(".j_CarFromPop").validate({
                                    debug: false, //调试模式取消submit的默认提交功能   
                                    focusInvalid: false, //当为false时，验证无效时，没有焦点响应  
                                    onkeyup: false,   
                                    submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                                        var $form = $(form);
                                             $.ajax({
                                                        type: "POST",
                                                        url: qianzhui+"/MemberGeneral/carlove_save",
                                                        dataType: "json",
                                                        data: {
                                                            id: _id,
                                                            car_id: $form.find("[name=car]").val(),
                                                            province_id: $form.find("[name=province]").val(),
                                                            city_id: $form.find("[name=city]").val(),
                                                            buy_time: $form.find("[name=buy_time]").val()
                                                        },
                                                        success:function(data){
                                                            if(data.status == 1){
                                                                layer.msg(data.msg);
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
                                        buy_time:{
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
                                        buy_time:{
                                            required:"请选择购车时间"
                                        }                              
                                    }         
                                });


                                }
                            });
                 });

            if(remainingNum == 0){
                $('#J_AddMyCarForm').remove();
            }

            });
    }();

             
});