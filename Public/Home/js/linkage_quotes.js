$(function(){
    $("#brand").change(function(){
        var $obj=$('#brand').find('option:selected');
        $("#model").empty();
        $("#model").append("<option value=''>请选择车型</option>");

        $.post( "DR_ajax_quotes_model",
            {brand_id : $obj.val()},
            function(data,textStatus){
                $.each(data,function(key,val){
                    $("#model").append("<option value='"+val.car_model_id+"'>"+val.car_model_name+"</option>");
                });

            },
            "json");
    })
});

$(function(){
    $("#model").change(function(){
        var $obj=$('#model').find('option:selected');
        $("#style").empty();
        $("#style").append("<option value=''>请选择车款</option>");

        $.post( "DR_ajax_quotes_style",
            {car_model_id : $obj.val()},
            function(data,textStatus){
                $.each(data,function(key,val){
                    $("#style").append("<option value='"+val.car_id+"'>"+val.car_name+"</option>");
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

        $.post( "DR_ajax_quotes_exterior",
            {car_id : $obj.val()},
            function(data,textStatus){
                $.each(data,function(key,val){
                    $("#exterior_color").append("<option value='"+val.exterior_color_id+"'>"+val.exterior_color_name+"</option>");
                });

            },
            "json");
    })
});

$(function(){
    $("#exterior_color").change(function(){
        var $obj  =$('#style').find('option:selected');
        $("#interior_color").empty();
        $("#interior_color").append("<option value=''>请选择内饰颜色</option>");

        $.post( "DR_ajax_quotes_interior",
            {car_id : $obj.val()},
            function(data,textStatus){
                $.each(data,function(key,val){
                    $("#interior_color").append("<option value='"+val.interior_color_id+"'>"+val.interior_color_name+"</option>");
                });

            },
            "json");
    })
});
