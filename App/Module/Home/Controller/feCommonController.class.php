<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie@topthink.net <www.thinkphp.cn>
// +----------------------------------------------------------------------

class feCommonController extends Action {

	//初始化方法
    protected function _initialize() {

    }

    protected function _list($params){
        extract($params);
        if(!isset($params['source'])){
            $this->error('错误的数据');
        }else{
            if(!$listRows)
                $listRows = I('listRows');
            $params = array(
                'source'=>$source,
                'map'=>$map,
                'parameter'=>$parameter,
                'listRows'=>$listRows,
                'listvar'=>$list,
                'order'=>$order,
            );
            $this->page($params);
            $this->display($tpl);
        }
    }

    /**
     * 分页函数 支持数据库查询分页和数组分页 数据库分页直接传数据表名称
     * @access public
     * @param mixed  $source 分页用数据源，可以是数组或数据表
     * @param array  $map数据源为数据表的时候的查询条件
     * @param string $parameter  分页跳转的参数
     * @param string $listvar    赋给模板遍历的变量名 默认list
     * @param int    $listRows  每页显示记录数 默认20
     */
    protected function page($param){
        extract($param);
        import("@.ORG.Util.Page");
        $flag = !is_array($source);
        $listvar = $listvar ? $listvar : 'list';
        $listRows = $listRows? $listRows : 10;
        //总记录数
        if ($flag){//字符串
            $count = $count? $count : '*';
            $totalRows = D($source)->where($map)->count($count);
        }else{
            $totalRows = ($source) ? count($source) : 1;
        }
        //创建分页对象
        $p = new Page($totalRows, $listRows, $parameter);
        //抽取数据
        if ($flag) {
            $voList = D($source)->where($map)->bind(true)->group($group)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
            // d_f('sql',D($source)->_sql());
        } else {
            $voList = array_slice($source, $p->firstRow, $p->listRows);
        }
        $pages = array(
          'theme'=>'<div class="page">%upPage% %linkPage% %downPage%</div>',
        );//要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
        foreach ($pages as $key => $value) {
            $p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
        }
        //分页显示
        $page = $p->totalPages>1? $p->show():'';
        //模板赋值
        $this->assign($listvar, $voList);
        $this->assign("page", $page);
        $this->assign('count',$totalRows);
        $varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        $this->assign('currentPage',!empty($_GET[$varPage])?intval($_GET[$varPage]):1);
        $this->assign('listRows',$listRows);
        return $voList;
    }

    /**
     * 公共插入方法,要插入的数据模型为当前模块名，如果提价模型，则为提交的模型
     */
    public function insert() {
        $name = empty($_POST['model']) ? CONTROLLER_NAME : $_POST['model']; //添加页面提交要操作的数据模型
        $model = D($name);
        if($data = $model->create($_POST,MODEL::MODEL_INSERT)){
            //保存当前数据对象
            $list = $model->add($data);
            if ($list !== false) { //保存成功
                $jumpUrl = empty($_POST['jumpUrl']) ? $_SESSION['returnUrl'] : $_POST['jumpUrl'];
                $success = empty($_POST['success_info']) ? '新增成功' : $_POST['success_info'];
                $this->assign('jumpUrl', $jumpUrl);
                $this->success($success, $jumpUrl);
            } else {
                $error = $model->getError();
                $error = empty($error) ? '新增失败' : $model->getError();
                $this->error($error);
            }
        }else{
            $error = $model->getError();
            $this->error($error);
        }
    }

}
