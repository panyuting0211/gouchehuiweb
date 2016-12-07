$(function(){
    var qianzhui='/member.php';
    $('#J_carSelector').cxSelect({
      selects: ['brand', 'model'],
      emptyStyle: 'none',
      parentSeletor: 'li', 
      jsonName: 'name',
      jsonValue: 'value'
    });
    //生成我的爱车列表
    !function(){
        var html = null,
            allNum = 9,
            adderNum = 0,
            remainingNum = 9,
            $gar_myCarList = $('#J_GarAttenCarList'),
            $loader = $('<div class="uc_loading"><i></i>数据加载中...</div>'),
            $remainingNumSel = $('.j_remainingNum');
            $gar_myCarList.find(".uc_loading").length || ($gar_myCarList.append($loader));
        $.post(qianzhui+"/MemberGeneral/attention_car", function(db){
            adderNum = db.list.length
            remainingNum = allNum - adderNum
            $remainingNumSel.html(remainingNum);    
            $gar_myCarList.find('.uc_loading').remove();

            if(adderNum > 0){
                html = template('tplGarAttenCarList', db);
            }else{
                html = "暂无关注车型，赶紧添加自己喜爱的车型吧！";
            }

            $gar_myCarList.html(html);

            $gar_myCarList.find('.j_addBuyCar').on('click', function(){
                var $closeCarBox = $(this).closest('.car_box'),
                    _carList = '<option value="">请选择车款</option>',
                    _imgUrl = $closeCarBox.find('img').attr('src'),
                    _tit = $closeCarBox.find('.j_tit').html(),
                    _carModelId = $closeCarBox.find('h5').data('cmid'),
                    bigBox = '';
                    //根据已知车型ID获取相应车款
                    $.get(qianzhui+"/MemberGeneral/car?model="+_carModelId, function(db){
                        if(db.length > 0){
                        $.each(db, function(i, v){
                            _carList += '<option value="'+ v.value + '">'+ v.name + '</option>';
                        });
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
                            '<h5 class="G_f18 G_tc" title="'+
                            _tit
                            +'">'+ _tit +'</h5>'+
                            '<div class="uc_g_opera_box">'+
                            '<div class="uc_formlist">'+
                            '<form action="" class="j_addMyCarFromPop">' +
                            '<ul id="J_carSelectorPop" class="uc_carSelector">'+
                                '<li>'+
                                    '<span class="hd"><em>*</em>预购车款:</span>'+
                                    '<div class="inp_box">'+
                                        '<select name="car" class="car uc_inp190 uc_inpBorder Skin_txt3 valid" style="">'+ _carList +'</select>'+
                                    '</div>'+

                                '</li>'+
                            
                            '</ul>'+
                            '<ul class="uc_citySelector j_citySelector">'+    
                                '<li>'+
                                    '<span class="hd"><em>*</em>所  在  地:</span>'+
                                    '<div class="inp_box">'+
                                        '<select data-required="false" data-first-title="请选择省份" data-first-value="" class="province uc_inp190 uc_inpBorder Skin_txt3" name="province" id="" data-url="'+qianzhui+'/MemberGeneral/province" style=""></select>'+
                                        '</div>'+
                                '</li>'+        
                                '<li>'+
                                        '<span class="hd"></span>'+        
                                        '<div class="inp_box">'+
                                        '<select data-required="false" data-first-title="请选择城市" data-first-value="" class="city uc_inp190 uc_inpBorder Skin_txt3" name="city" id="" data-url="'+qianzhui+'/MemberGeneral/city"></select>'+
                                    '</div>'+
                                '</li>'+
                            '</ul>'+
                            '<ul>'+   
                            '<li>'+     
                                    '<span class="hd"><em>*</em>购买时间:</span>'+
                                    '<div class="inp_box">'+
                                        '<select class="uc_inp190 uc_inpBorder Skin_txt3 valid" name="buy_time">'+
                                            '<option value="">请选择</option>'+
                                            '<option value="1个月内">1个月内</option>'+
                                            '<option value="3个月内">3个月内</option>'+
                                            '<option value="6个月内">6个月内</option>'+
                                            '<option value="6个月内">6个月内</option>'+
                                            '<option value="6个月以上">6个月以上</option>'+
                                        '</select>'+
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
                              closeBtn: 1,
                              move: false,
                              shadeClose: true,
                              skin: 'addBuyCar_box',
                              content: bigBox,
                              success: function(layero, index){
                                //弹窗绑定城市
                                layero.find('.j_citySelector').cxSelect({
                                  selects: ['province', 'city'],
                                  emptyStyle: 'none',
                                  parentSeletor: 'li',
                                  jsonName: 'name',
                                  jsonValue: 'value'
                                });
                                //弹窗绑定提交
                                $.validator.setDefaults({
                                    errorElement:'span',
                                });
                                layero.find(".j_addMyCarFromPop").validate({
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
                                                            console.log(data);
                                                            if(data.status == 1){
                                                                layer.msg(data.msg);
                                                            }else if(data.status == 2){
                                                                location.reload();
                                                            }
                                                         }
                                                    });
                                    },   
                                    rules:{
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

                        }else{
                            //该车型暂无车款处理
                            layer.msg("对不起，由于该车的车款暂时的缺失；无法完成添加到预购车库！");
                        }
                     });
            });
 //车库列表绑定删除           
    $gar_myCarList.find('.j_delBtn').on('click', function(){
                var $this = $(this),
                    $thisP = $this.closest('.car_box'),
                    _id = $thisP.data('id');
                    layer.confirm('是否确定删除！', {
                              btn: ['是','否'] //按钮
                            }, function(){
                                $.get(qianzhui+"/MemberGeneral/attention_delete?id=" + _id, function(db){
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

            if(remainingNum == 0){
                $('#J_AddMyCarForm').remove();
            }
            });
    }();
    

    $("#J_AddMyCarForm").validate({
                debug: false, //调试模式取消submit的默认提交功能   
                focusInvalid: false, //当为false时，验证无效时，没有焦点响应  
                onkeyup: false,   
                submitHandler: function(form){   //表单提交句柄,为一回调函数，带一个参数：form
                    var $form = $(form);
                         $.ajax({
                                    type: "POST",
                                    url: qianzhui+"/MemberGeneral/attention_add",
                                    dataType: "json",
                                    data: {
                                        car_model_id: $form.find("[name=model]").val()
                                    },
                                    success:function(data){
                                        if(data.status == 1){
                                            layer.msg("添加关注车型失败！");
                                        }else if(data.status == 2){
                                            location.reload();
                                        }else if(data.status == 3){
                                            layer.msg(data.msg);
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
                    }                   
                },
                messages:{
                    brand:{
                        required:"请选择您爱车的品牌"
                    },
                    model:{
                        required:"请选择您爱车的系列车型"
                    }                            
                }         
            });  
        
});