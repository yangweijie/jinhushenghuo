<?php

class IndexController extends Action {

    public function _initialize(){
        $nav_tree = D('Nav')->where('status=1')->field("id,c_name,pid,e_name,ctime,link,order")->order('`order` asc')->select();
        $nav_tree = list_to_tree($nav_tree,$pk='id',$pid='pid',$child='_child',$root=1);
        $this->assign('nav',$nav_tree);
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
        import("@.ORG.Util.Page2");
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
        $p = new Page2($totalRows, $listRows, $parameter);
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
        $this->assign('start_row', ($p->nowPage- 1) * $listRows + 1);
        $this->assign('end_row', ($p->nowPage- 1) * $listRows + count($voList));
        $this->assign('currentPage',$p->nowPage);
        $this->assign('listRows',$listRows);
        return $voList;
    }


    //首页
    public function index(){
        $this->assign('banner', D('Banner')->order('`order` asc')->select());
        #欧意动态
        $dynamic_navid = D('Nav')->where("c_name='欧易动态'")->getField('id');
        $this->assign('dynamic_navid', $dynamic_navid);
        $dynamic = D('Article')->where("nav_id={$dynamic_navid}")->limit(5)->select();
        $this->assign('dynamic', $dynamic);
        #行业资讯
        $dynamic_navid2 = D('Nav')->where("c_name='欧易动态'")->getField('id');
        $this->assign('dynamic_navid2', $dynamic_navid2);
        $dynamic2 = D('Article')->where("nav_id={$dynamic_navid2}")->limit(5)->select();
        $this->assign('dynamic2', $dynamic);
        #资料下载
        $down = D('attach')->limit(5)->select();
        $this->assign('down', $down);
        $this->display();
    }

    //文章详情页
    public function detail($id){
        $navModel = D('Nav');
        $current_nav = $navModel->find($id);
        $this->assign('current_nav',$current_nav);//当前分类
        $path = $navModel->getPath($id);
        $this->assign('path', $path); // 面包屑导航
        $this->assign('siblings', $navModel->where("status=1 AND pid={$current_nav['pid']}")->order('`order` asc')->select());
        $article_id = I('article_id');
        $subnav = $navModel->where("status=1 AND pid={$id}")->order('`order` asc')->select();
        if(!$subnav)
            $subnav = D('Article')->where("nav_id={$id}")->select();
        if(count($subnav) <2)
            $article_id = D('Article')->where("nav_id={$id}")->getField('id');
        if($article_id)
            $this->view($id, $article_id);
        else
            $this->nav($subnav);
    }

    //显示分类列表
    public function nav($subnav){
        $this->assign('subnav', $subnav);//子导航分类
        $this->display('list');
    }

    //显示文章内容
    public function view($id , $article_id){
        if($article_id)
            $map['id'] = $article_id;
        else
            $map['nav_id'] = $id;
        $record = D('Article')->where($map)->find();
        $this->assign('record', $record);
        $this->display('detail');
    }

    //特殊分类页
    public function video($id = 1){
        $data = M('Video')->find($id);
        $this->assign('data', $data);
        $this->display();
    }

    //下载文件
    public function down($id){
        if($id){
            $attach = D('Attach')->find($id);
            download_file("{$attach['path']}{$attach['name']}",$attach['o_name']);
        }else{
            $attach = D('Attach')->select();
            $this->assign('down', $attach);
            $this->display();
        }
    }

    //搜索
    public function search($keyword){
        G('begin');
        $map['title|content'] = array('like',"%{$keyword}%");
        $time_limit = I('time_limit');
        if($time_limit)
            $map['ctime'] = array('egt', strtotime($time_limit));
        G('end');
        $this->assign('cost_time', G('begin', 'end',3));
        $this->_list(array('source'=>'Article','map'=>$map));
    }

    //注册用户
    public function reg(){

    }
}