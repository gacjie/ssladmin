<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/6/17
 * Time: 21:47
 */
use app\admin\model\SystemLog;
use think\App;
use think\facade\View;

class Adminlog extends AdminBase {
    protected $model;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new SystemLog();
    }

    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $page = $this->request->param('page', 1, 'intval');
            $limit = $this->request->param('limit', 10, 'intval');
            $count = $this->model
                ->count();
            $list = $this->model
                ->page($page, $limit)
                ->order('id desc')
                ->select();

            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        return View::fetch();
    }
}