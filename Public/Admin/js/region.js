function loadRegion(sel,level_id,selName,url){
	jQuery("#"+selName+" option").each(function(){
		jQuery(this).remove();
	});
	jQuery("<option value=0>请选择</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==0){
		return;
	}
	jQuery.getJSON(url,{parent:jQuery("#"+sel).val(),level:level_id},
		function(data){
			if(data){
				jQuery.each(data,function(idx,item){
					jQuery("<option value="+item.id+">"+item.title+"</option>").appendTo(jQuery("#"+selName));
				});
			}else{
				jQuery("<option value='0'>请选择</option>").appendTo(jQuery("#"+selName));
			}
		}
	);
}
function loadRegion1(sel,type_id,selName,url){
	jQuery("#"+selName+" option").each(function(){
		jQuery(this).remove();
	});
	jQuery("<option value=0>请选择</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==0){
		return;
	}
	jQuery.getJSON(url,{title:jQuery("#"+sel).val(),type:type_id},
		function(data){
			if(data){
				jQuery.each(data,function(idx,item){
					jQuery("<option value="+item.color+">"+item.color+"</option>").appendTo(jQuery("#"+selName));
				});
			}else{
				jQuery("<option value='0'>请选择</option>").appendTo(jQuery("#"+selName));
			}
		}
	);
}
function loadRegion2(sel,type_bid,selName,url){
	jQuery("#"+selName+" option").each(function(){
		jQuery(this).remove();
	});
	jQuery("<option value=0>请选择</option>").appendTo(jQuery("#"+selName));
	if(jQuery("#"+sel).val()==0){
		return;
	}
	jQuery.getJSON(url,{level:jQuery("#"+sel).val(),type:type_bid},
		function(data){
			if(data){
				jQuery.each(data,function(idx,item){
					jQuery("<option value="+item.bid+">"+item.color+"</option>").appendTo(jQuery("#"+selName));
				});
			}else{
				jQuery("<option value='0'>请选择</option>").appendTo(jQuery("#"+selName));
			}
		}
	);
}