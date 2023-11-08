<?php

namespace app\admin\model;


use app\common\model\TimeModel;

class SystemLog extends TimeModel
{

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->name = 'system_log_' . date('Ym');
    }

    public function setMonth($month)
    {
        $this->name = 'system_log_' . $month;
        return $this;
    }

    public function admin()
    {
        return db('admin')->where('id');
    }


}