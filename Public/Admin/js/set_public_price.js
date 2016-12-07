$(function(){
    $("#pinpai").change(function(){
      var $obj=$("#pinpai").find('option:selected');
      $("#chexing").empty();
      $("#chexing").append("<option value=''>请选择车型</option>");
      // alert($obj.val());
      $.post( "/admin.php/Ajax/addModelHandle",
                  {brand_id : $obj.val()},
                  function(data,textStatus){
                             $.each(data,function(key,val){
                             $("#chexing").append("<option value='"+val.id+"'>"+val.car_model_name+"</option>");
                           });                     
                  },
                  "json");      
    }); 

    $("#chexing").change(function(){
      var $obj=$('#chexing').find('option:selected');
      $("#chekuang").empty();
      $("#chekuang").append("<option value=''>请选择车款</option>");

        $.post( "/admin.php/Ajax/addModelHandle",
                  {car_model_id : $obj.val()},
                  function(data,textStatus){
                             $.each(data,function(key,val){
                             $("#chekuang").append("<option value='"+val.id+"'>"+val.car_name+"</option>");
                           });               
                  },
                  "json");      
    }); 

    $("#chekuang").change(function(){
      var $obj=$('#chekuang').find('option:selected');
      $("#exterior_color").empty();
      $("#exterior_color").append("<option value=''>外观颜色</option>");
      $.post( "/admin.php/Ajax/addModelHandle",
              {car_id : $obj.val()},
              function(data,textStatus){
                         $.each(data,function(key,val){
                         $("#exterior_color").append("<option value='"+val.id+"'>"+val.color_name+"</option>");
                       });
              },
              "json");  
    }); 
    $("#exterior_color").change(function(){
      var $obj=$('#chekuang').find('option:selected');
      $("#interior_color").empty();
      $("#interior_color").append("<option value=''>内饰颜色</option>");
      $.post( "/admin.php/Ajax/addModelHandle",
              {car_id1 : $obj.val()},
              function(data,textStatus){
                         $.each(data,function(key,val){
                         $("#interior_color").append("<option value='"+val.id+"'>"+val.color_name+"</option>");
                       });
              },
              "json");  

    });
    
    // $("input[id='ajax_price1']").click(function(){
    //   var val=$(this).val();
    //   $.post("/admin.php/carmanage/addModelHandle",
    //           {id:val},
    //           function(data,textStatus){
    //           }
    //     )
    // });

    // $("input[id='ajax_price2']").click(function(){
    //   var val=$(this).val();
    //   $.post("/admin.php/carmanage/addModelHandle",
    //           {id2:val},
    //           function(data,textStatus){
    //           }
    //     )
    // });


    $("#brand").change(function(){
      var $obj=$("#brand").find('option:selected');
      $("#model").empty();
      $("#model").append("<option value=''>请选择车型</option>");
      $("#car").empty();
      $("#car").append("<option value=''>选择车款</option>");
      $("#exterior").empty();
      $("#exterior").append("<option value=''>外观颜色</option>");
      $("#interior").empty();
      $("#interior").append("<option value=''>内饰颜色</option>");
      // alert($obj.val());
      $.post( "/admin.php/Ajax/addModelHandle2",
                  {brand_id : $obj.val()},
                  function(data,textStatus){
                             $.each(data,function(key,val){
                             $("#model").append("<option value='"+val.model_id+"'>"+val.model_name+"</option>");
                           });                     

                  },
                  "json");      
    });

    $("#model").change(function(){
      var $obj=$('#model').find('option:selected');
      $("#car").empty();
      $("#car").append("<option value=''>请选择车款</option>");
      $("#exterior").empty();
      $("#exterior").append("<option value=''>外观颜色</option>");
      $("#interior").empty();
      $("#interior").append("<option value=''>内饰颜色</option>");
      $.post( "/admin.php/Ajax/addModelHandle2",
                  {car_model_id : $obj.val()},
                  function(data,textStatus){
                             $.each(data,function(key,val){
                             $("#car").append("<option value='"+val.car_id+"'>"+val.car_name+"</option>");
                           });                     

                  },
                  "json");      
      });   

    $("#car").change(function(){
      var $obj=$('#car').find('option:selected');
      $("#exterior").empty();
      $("#exterior").append("<option value=''>外观颜色</option>");
      $("#interior").empty();
      $("#interior").append("<option value=''>内饰颜色</option>");
      $.post( "/admin.php/Ajax/addModelHandle2",
              {car_id : $obj.val()},
              function(data,textStatus){
                  $.each(data,function(key,val){
                      if(val.exterior_color_name){
                        $("#exterior").append("<option value='"+val.exterior_color_id+"'>"+val.exterior_color_name+"</option>");
                      }
                      if(val.interior_color_name){
                        $("#interior").append("<option value='"+val.interior_color_id+"'>"+val.interior_color_name+"</option>");
                      }

                  });
              },
              "json");  

    });

});



$(function(){
    $("#chexing").attr("disabled", 'false');
    $("#chekuang").attr("disabled", 'false');
    $("#exterior_color").attr("disabled", 'false');
    $("#interior_color").attr("disabled", 'false');

    $("#pinpai").change(function(){
      $("#chexing").removeAttr("disabled"); 
    }); 

    $("#chexing").change(function(){
      $("#chekuang").removeAttr("disabled"); 
    });

    $("#chekuang").change(function(){
      $("#exterior_color").removeAttr("disabled"); 
    });
    $("#exterior_color").change(function(){
      $("#interior_color").removeAttr("disabled"); 
    }); 



var url_ajax = "/admin.php/Ajax/public_carprice_ajax";
  
$("#ajax_lists").delegate(".pager a", "click", function() {
    var page = $(this).attr("data-page");
    getPage(page);
})
getPage(1);

});


 function getPage(page) {
  $("thead").css('display','none');
  $('tbody').empty();
  $("#ajax_lists").prepend("<div class='onload' style='text-align:center'><img src='/public/admin/images/loading.gif' alt='loading'>数据加载中....</div>");
  var brand    =$('#brand').find('option:selected').val();
  var model    =$('#model').find('option:selected').val();
  var car      =$('#car').find('option:selected').val();
  var exterior =$('#exterior').find('option:selected').val();
  var interior =$('#interior').find('option:selected').val();
  var index_status =$('#index_status').find('option:selected').val();
  
  $.post( "/admin.php/Ajax/public_carprice_ajax",
      {brand:brand,model:model,car:car,exterior:exterior,interior:interior,index_status:index_status, p:page},
      function(data,textStatus){
          $('tbody').empty();
          $('#ajax_lists > .onload').remove();
          $("thead").css('display','');
          var pagelist=data[0];          
          $('#ajax_lists .pager').html(pagelist);
          var info=data[1];

          $.each(info,function(key,val){
              var td1="<td>"+val.brand_name+"</td>";
              var td2="<td>"+val.car_model_name+"</td>";
              var td3="<td>"+val.car_name+"</td>";
              var td4="<td>"+val.exterior_color_name+"</td>";
              var td5="<td>"+val.interior_color_name+"</td>"
              if(val.status==1){
                var td6="<td><label class='radio inline'><input id='ajax_price1' type='radio' value="+val.id+" name='hide"+key+"' />不显示</label><label class='radio inline'><input type='radio' id='ajax_price2' value="+val.id+" checked='checked' name='hide"+key+"' />显示</label></td>";
              }else{
                var td6="<td><label class='radio inline'><input id='ajax_price1' type='radio' value="+val.id+" checked='checked' name='hide"+key+"' />不显示</label><label class='radio inline'><input type='radio' id='ajax_price2' value="+val.id+" name='hide"+key+"' />显示</label></td>";
              }
              if(val.client==2){
                  var td7="<td><label class='radio inline'><input id='ajax_client1' type='radio' value="+val.id+"  checked='checked' name='client"+key+"' />手机WEB端</label><label class='radio inline'><input type='radio' id='ajax_client2' value="+val.id+"  name='client"+key+"' />PC端</label></td>";
              }else{
                  var td7="<td><label class='radio inline'><input id='ajax_client1' type='radio' value="+val.id+"  name='client"+key+"' />手机WEB端</label><label class='radio inline'><input type='radio' id='ajax_client2' value="+val.id+" checked='checked' name='client"+key+"' />PC端</label></td>";
              }
              if(val.isbaojia==1){
                var td8="<td><font color='green'>有报价<font></td>";
              }else{
                var td8="<td><font color='red'>无报价<font></td>";
              }
              var td9="<td><a title='编辑' href='/admin.php/carmanage/car_index_image/id/"+val.id+"'>首页图片</a><a title='删除' href='javascript:;' onclick='{ if(confirm(\"您确定要删除吗？\")) {window.location.href=\"/admin.php/carmanage/del_index_car_price/id/"+val.id+"\"}}'> 删除</a></td>";

              $all=$("<tr>"+td1+td2+td3+td4+td5+td6+td7+td8+td9+"</tr>");
              $('tbody').append($all);
              $all.find("input[id='ajax_price1']").click(function(){
                var val=$(this).val();
                $.post("/admin.php/Ajax/addModelHandle",
                        {id:val},
                        function(data,textStatus){
                        }
                  )
              });
              $all.find("input[id='ajax_price2']").click(function(){
                var val=$(this).val();
                $.post("/admin.php/Ajax/addModelHandle",
                        {id2:val},
                        function(data,textStatus){
                        }
                  )
              });
              $all.find("input[id='ajax_client1']").click(function(){
                  var val=$(this).val();
                  $.post("/admin.php/Ajax/setClient",
                      {id:val,table:"index_car_price",client:2},
                      function(data,textStatus){
                      }
                  )
              });
              $all.find("input[id='ajax_client2']").click(function(){
                  var val=$(this).val();
                  $.post("/admin.php/Ajax/setClient",
                      {id:val,table:"index_car_price",client:1},
                      function(data,textStatus){
                      }
                  )
              });
          });

      },
      "json");
  }
   