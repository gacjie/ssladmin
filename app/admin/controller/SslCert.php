<?php
namespace app\admin\controller;

use app\common\controller\AddonBase;
use think\facade\View;
use think\facade\Db;

class SslCert extends AdminBase
{

    public function initialize(){
		parent::initialize();
        $this->model = new \app\admin\model\SslCert();
    }
    public function index(){
		if(!$this->request->isAjax()){
        	return View::fetch();
		}else{
			return $this->getList();
		}
    }

   public function getList(){
   		$page = $this->request->param('page',1,'intval');
   		$limit = $this->request->param('limit',10,'intval');
   		$count = $this->model->count();
   		$data = $this->model->with([])
		   ->where(function($query){
            $query->dateRange('endtime',$this->request->param('endtime',null));
            
                    $id = $this->request->param('id',null);
                    if($id){
                        $query->whereLike('id',"%{$id}%");
                    }
                    

                    $ssl_ca_id = $this->request->param('ssl_ca_id',null);
                    if($ssl_ca_id){
                        $query->where('ssl_ca_id',$ssl_ca_id);
                    }
                    

                    $serial = $this->request->param('serial',null);
                    if($serial){
                        $query->whereLike('serial',"%{$serial}%");
                    }
                    

                    $name = $this->request->param('name',null);
                    if($name){
                        $query->whereLike('name',"%{$name}%");
                    }
                    
        })
           ->order('id','desc')
		   ->page($page,$limit)->select();
   		return json([
				'code'=> 0,
				'count'=> $count,
   				'data'=>$data,
   				'msg'=>__('Search successful')
   		]);
   }
   public function getSslCaList(){
    $data =  \think\facade\Db::name('ssl_ca')->field('id,name')->select();
    return json([
            'code'=> 0,
            'count'=> count($data),
            'data'=>$data,
            'msg'=>__('Search successful')
   	]);
}

   public function add(){
	   	if($this->request->isPost()){
	   		$data = $this->request->post();
            $data['name'] = $data['commonName'];
            $data['class'] = '/server/';
            OpenSSL($data);
            $data['endtime'] =time()+($data['day']*86400);
            $SslCert = Db::name('ssl_cert')->strict(false)->insert($data);
	   		if($SslCert){
	   			$this->success(__('Add successful'));
	   		}else{
	   			$this->error(__('Add failed'));
	   		}
	   	}
		$sslCas = \think\facade\Db::name('ssl_ca')->where('switch', 'on')->field('id,name')->select();
        View::assign('sslCas',$sslCas);
	   	return View::fetch('edit');
   }

   public function leading(){
   	   	if($this->request->isPost()){
   	   		$file = $_FILES['file'];
   	   		$inputFileName = $file['tmp_name'];
            try {
                ob_end_clean();//清除缓冲区,避免乱码
                $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);

                $objReader  = \PHPExcel_IOFactory::createReader($inputFileType);

                $objPHPExcel = $objReader->load($inputFileName);
            } catch(\Exception $e) {
                 die('加载文件发生错误：”'.pathinfo($inputFileName,PATHINFO_BASENAME).'”: '.$e->getMessage());
            }
            //形成数组
             $excel_data = $objPHPExcel->getSheet(0)->toArray();


            $insert_data = array();
            foreach($excel_data as $k=>$v){
        
                
        if($k>0){
           
        $insert_data[$k]['ssl_ca_id'] = isset($v[0]) ? $v[0] : '';
$insert_data[$k]['serial'] = isset($v[1]) ? $v[1] : '';
$insert_data[$k]['name'] = isset($v[2]) ? $v[2] : '';
}
            }


   	   		if( $this->model->saveAll($insert_data,false)){
   	   			$this->success(__('Add successful'));
   	   		}else{
   	   			$this->error(__('Add failed'));
   	   		}
   	   	}
   		$sslCas = \think\facade\Db::name('ssl_ca')->field('id,name')->select();
View::assign('sslCas',$sslCas);
   	   	return View::fetch('leading');
     }

//   public function edit(){
// 	   	if($this->request->isPost()){
// 	   		$data = $this->request->post();
            
//             if( $this->model->find($data['id'])->save($data)){
// 	   			$this->success(__('Editor successful'));
// 	   		}else{
// 	   			$this->error(__('Editor failed'));
// 	   		}
// 	   	}
// 	   	$id = $this->request->param('id');
// 	   	if(!$id){
// 	   		$this->success(__('Parameter error'));
// 	   	}
// 	   	$info =  $this->model->where('id',$id)->find();
//   		if(!$info){
// 	   		$this->success(__('Parameter error'));
// 	   	}
// 		$sslCas = \think\facade\Db::name('ssl_ca')->field('id,name')->select();
// View::assign('sslCas',$sslCas);
// 	   	View::assign('ssl_cert',$info);
//         return View::fetch('edit');
//   }

   public function delete(){
   		$idsStr = $this->request->param('idsStr');
   		if(!$idsStr){
   			$this->success(__('Parameter error'));
   		}
   		if( $this->model->where('id','in',$idsStr)->delete()){
   			$this->success(__('Delete successful'));
   		}else{
   			$this->error(__('Delete error'));
   		}
   }

   public function sw(){
      	$data = $this->request->param();
            if( $this->model->where('id',$data['id'])->update($data)){
                 $this->success(__('Editor successful'));
            }else{
                 $this->error(__('Editor failed'));
            }
      }
    public function down(){
      	$data = $this->request->param();
      	    $set=config('site');
      	    //var_dump($data);exit;
      	    $path=root_path().$set['document'].'/server/'.md5($data['name']);
      	    $filename = root_path().$set['document'].'/tmp/certificate.zip';
      	    if(file_exists($filename)){
      	        unlink($filename);
      	    }
            $zip = new \ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释  
            if ($zip->open($filename, \ZIPARCHIVE::CREATE)!==TRUE) {  
                exit('无法打开文件，或者文件创建失败');
            }  
            $zip->addFile( $path."/certificate.key", 'certificate.key');
            $zip->addFile( $path."/certificate.crt", 'certificate.crt');
            $zip->addFile( $path."/certificate.csr", 'certificate.csr');
            $zip->addFile( $path."/certificate.pem", 'certificate.pem');
            $zip->close();//关闭  
            return download($filename, 'certificate.zip')->force(false);
      }
}
