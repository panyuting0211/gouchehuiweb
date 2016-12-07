<?php 
namespace Admin\Controller;
use Think\Controller;
	/**
	* Excel到出
	*/
	class ExcelController extends Controller
	{
		
		/**方法**/
		public function  index(){
		        $this->display();
		    }
		public function exportExcel($expTitle,$expCellName,$expTableData){
		        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
		        $fileName = $_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
		        $cellNum = count($expCellName);
		        $dataNum = count($expTableData);
		        vendor("PHPExcel.PHPExcel");
		       
		        $objPHPExcel = new \PHPExcel();
		        $cellName = array('A','B','C','D','E','F','G','H','I','J');
		        
		        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
		       // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));  
		        for($i=0;$i<$cellNum;$i++){
		            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
		        } 
		          // Miscellaneous glyphs, UTF-8   
		        for($i=0;$i<$dataNum;$i++){
		          for($j=0;$j<$cellNum;$j++){
		            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
		          }             
		        }  
		        
		        header('pragma:public');
		        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
		        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
		        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
		        $objWriter->save('php://output'); 
		        exit;   
		    }
		/**
		     *
		     * 导出Excel
		     */
		    function expCarPrice(){//导出Excel
		        $xlsName  = "CarList";
		        $xlsCell  = array(
		        array('','国系'),
		        array('brand_name','品牌名称'),
		        array('car_type_name','车型分类'),
		        array('car_model_name','车型名称'),
		        array('car_name','车款名称'),
		        array('car_status','状态'),
		        array('exterior_color_name','外观'),
		        array('interior_color_name','内饰'),
		        array('auth_price','官方指导价'),
		        array('low_price','底价'),  
		        );
		        $xlsModel = M('view_car_price');
		    
		        $xlsData  = $xlsModel->where(array('isdelete'=>0))->Field('id,brand_name,car_model_name,car_type_name,car_name,car_status,exterior_color_name,interior_color_name,auth_price,low_price')->order('brand_alif,car_model_name,createtime')->select();       
		        $this->exportExcel($xlsName,$xlsCell,$xlsData);  
		    }

		    /**
		     *
		     * 导出Excel
		     */
		 public   function expCar(){//导出Excel
		        $xlsName  = "CarList";
		        $xlsCell  = array(
					array('brand_name','品牌名称'),
					array('car_model_name','车型名称'),
					array('car_name','车款名称'),
					array('car_status','状态'),
					array('displacement','排量')
		        );
		        $xlsModel = M('view_car_plan');
		    	$data['isdelete'] = 0;
		    	$data['car_status'] = 1;
		        $xlsData  = $xlsModel->where($data)->Field('brand_name,car_model_name,car_name,car_status,displacement')->select();
		        $this->exportExcel($xlsName,$xlsCell,$xlsData);
		    }

		    /**
		     *
		     * 导出用户喜好Excel
		     */
			 public   function expUserLove(){//导出Excel
			        $xlsName  = "UserLove";
			        $xlsCell  = array(
						array('user_name','用户名'),
						array('from_plan','页面入口'),
						array('brand_name','关注品牌'),
						array('car_model_name','关注车型'),
						array('car_name','关注车款'),
						array('createtime','提交时间')
			        );
			        $xlsModel = M('view_car_plan');
			    	$data['isdelete'] = 0;
			        $xlsData  = $xlsModel->where($data)->Field('user_name,from_plan,brand_name,car_model_name,car_name,createtime')->select();
			        $this->exportExcel($xlsName,$xlsCell,$xlsData);
			}
			/**
		     *
		     * 导出品牌维护Excel
		     */
			 public   function expBrandManager(){//导出Excel
			        $xlsName  = "BrandManager";
			        $xlsCell  = array(
						array('manager','公关人员姓名'),
						array('tel','联系方式'),
						array('brand_name','品牌')
			        );
			        $xlsModel = M('brand_manager');
			    	$data['isdelete'] = 0;
			        $xlsData  = $xlsModel->where($data)->Field('manager,tel,brand_name,brand_name')->select();
			        $this->exportExcel($xlsName,$xlsCell,$xlsData);
			}

			/**
		     *
		     * 导出4S店用户名
		     */
			 public   function exp4sManager(){//导出Excel
			        $xlsName  = "4sManager";
			        $xlsCell  = array(
						array('user_name','用户名'),
						array('tel','联系方式'),
						array('name_4S',"店名"),
						array('contacts',"联系人"),
			        );
			        $xlsModel = M('user_4s');
			    	$data['isdelete'] = 0;
			        $xlsData  = $xlsModel->where($data)->Field('user_name,tel,name_4S,contacts')->select();
			        $this->exportExcel($xlsName,$xlsCell,$xlsData);
			}

		    function impUser(){
		        if (!empty($_FILES)) {
		            import("@.ORG.UploadFile");
		            $config=array(
		                'allowExts'=>array('xlsx','xls'),
		                'savePath'=>'./Public/upload/',
		                'saveRule'=>'time',
		            );
		            $upload = new UploadFile($config);
		            if (!$upload->upload()) {
		                $this->error($upload->getErrorMsg());
		            } else {
		                $info = $upload->getUploadFileInfo();
		                
		            }
		        
		            vendor("PHPExcel.PHPExcel");
	                $file_name=$info[0]['savepath'].$info[0]['savename'];
	                $objReader = PHPExcel_IOFactory::createReader('Excel5');
	                $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
	                $sheet = $objPHPExcel->getSheet(0);
	                $highestRow = $sheet->getHighestRow(); // 取得总行数
	                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	                for($i=3;$i<=$highestRow;$i++)
	                {   
	                   $data['account']= $data['truename'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();  
	                    $sex = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
	                   // $data['res_id']    = $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
	                    $data['class'] = $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
	                    $data['year'] = $objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
	                    $data['city']= $objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue();
	                    $data['company']= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
	                    $data['zhicheng']= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
	                    $data['zhiwu']= $objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue();
	                    $data['jibie']= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
	                    $data['honor']= $objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();
	                    $data['tel']= $objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
	                    $data['qq']= $objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
	                    $data['email']= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
	                    $data['remark']= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
	                    $data['sex']=$sex=='男'?1:0;
	                    $data['res_id'] =1;
	                    
	                    $data['last_login_time']=0;
	                    $data['create_time']=$data['last_login_ip']=$_SERVER['REMOTE_ADDR'];
	                    $data['login_count']=0;
	                    $data['join']=0;
	                    $data['avatar']='';
	                    $data['password']=md5('123456');              
	                    M('Member')->add($data);
	         
	                } 
	                 $this->success('导入成功！');
		        }else
		            {
		                $this->error("请选择上传的文件");
		            }
    		}
}
 ?>