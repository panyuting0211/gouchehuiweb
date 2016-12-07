<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class NodeModel extends RelationModel{
	
	protected $_link = array(
				'Node'=>array(
					'mapping_type'=>self::HAS_MANY,
					'mapping_name'=>'node',
					'mapping_order'=>'sort',
					'condition'=>'display=1',
					'parent_key'=>'pid'					
				),
			);
}

?>