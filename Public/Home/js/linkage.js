$(function(){
    $("#model").change(function(){
        var $obj=$('#model').find('option:selected');
        $("#style").empty();
        $("#interior_color").empty();
        $("#exterior_color").empty();
        $("#style").append("<option value=''>请选择车款</option>");
        $("#exterior_color").append("<option value=''>选择外观颜色</option>");
        $("#interior_color").append("<option value=''>选择内饰颜色</option>");
        $.post( "FS_ajax_style",
            {model_id : $obj.val()},
            function(data,textStatus){
                $.each(data,function(key,val){
                    $("#style").append("<option value='"+val.id+"'>"+val.car_name+"</option>");
                });

            },
            "json");
    })
});

$(function(){
    $("#style").change(function(){
        var $obj=$('#style').find('option:selected');
        $("#exterior_color").empty();
        $("#exterior_color").append("<option value=''>请选择外观颜色</option>");
        $("#interior_color").empty();
        $("#interior_color").append("<option value=''>选择内饰颜色</option>");

        $.post( "FS_ajax_colors",
            {car_id : $obj.val()},
            function(data){
                $.each(data["ex_info"],function(key,val){
                    $("#exterior_color").append("<option value='"+val.id+"'>"+val.exterior_color_name+"</option>");
                });
                $.each(data["in_info"],function(key,val){
                    $("#interior_color").append("<option value='"+val.id+"'>"+val.interior_color_name+"</option>");
                });
            },
            "json");
    })
});

//$(function(){
//    $("#exterior_color").change(function(){
//        var $obj  =$('#style').find('option:selected');
//        $("#interior_color").empty();
//        $("#interior_color").append("<option value=''>请选择内饰颜色</option>");
//
//        $.post( "FS_ajax_interior_color",
//            {car_id : $obj.val()},
//            function(data,textStatus){
//                $.each(data,function(key,val){
//                    $("#interior_color").append("<option value='"+val.id+"'>"+val.interior_color_name+"</option>");
//                });
//
//            },
//            "json");
//    })
//});
