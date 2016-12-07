<?php
/*
	首页不需要询价的底价车视图
*/
namespace Home\Model;
use Think\Model\ViewModel;	


class PriceViewModel extends ViewModel

{

  public $viewFields = array( 	

  	'index_car_price'=>array('id'=>'index_id','car_id','exterior_color_id','interior_color_id','imgurl','isdelete','status','isbaojia','createtime','_type'=>'LEFT'),//不需要询价表

    'view_car_price' =>array('id','car_id','exterior_color_id','interior_color_id','price','low_price','brand_name','car_model_name','car_name','city_id','city_name','province_id','province_name','is_xunjia','isdelete','exterior_color_name','car_model_imageurl','_on'=>'view_car_price.car_id=index_car_price.car_id and view_car_price.exterior_color_id=index_car_price.exterior_color_id and view_car_price.interior_color_id=index_car_price.interior_color_id','_type'=>'RIGHT'),//底价车视图表
 ); 

}

?>