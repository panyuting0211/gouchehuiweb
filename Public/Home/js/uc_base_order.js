/**
 * Created by Administrator on 2016/5/10.
 */
$(function(){
    $("#J_ucmyorder_btn").on("click",function(e){
        e.stopPropagation();
        e.preventDefault();
        console.log("Y");
        $.ajax({
            url:"/index.php/MemberGeneral/orderlist",
            success:function(data){
                console.log(data);
            }
        });
    })
});