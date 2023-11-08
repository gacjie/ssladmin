<?php

namespace app\admin\model;

use think\Model;

class SslCa extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    //protected $updateTime = 'updatetime';


    
public function getSslCaIdAttr($value,$data)
{
    $this->set('sslCaIdList',\think\facade\Db::name('ssl_ca')->field('id,name')->where('id',$value)->find()) ;
    $this->append(array_merge($this->append,['sslCaIdList']));
    return $value;
}
public function getEndtimeAttr($value)
{
    if($value){
        return date('Y-m-d H:i:s',$value);
    }else{
        return null;
    }
   
}
public function setEndtimeAttr($value)
{
    return strtotime($value);
}

    
public function scopeDateRange($query,$field,$data)
{
    if(is_string($data)){
        $arr  =explode(' - ',$data);
        if(count($arr)==2){
            $query->whereTime($field, 'between', $arr) ;
        }
    }
}
}