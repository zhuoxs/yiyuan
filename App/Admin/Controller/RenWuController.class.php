<?php
namespace Admin\Controller;
class RenWuController extends AuthController {
    protected $onname='回访任务';
    protected $rule_qz='hfrenwu';



    public function getlistrenwuadd(){
        $isclose=1;
        if(IS_POST)
        {
            $this->check_group($this->rule_qz."_add");
            $this->onname='回访任务';
            $tabid=I('post.bktab');
            $model=D('RenWu');

            if($model->create())
            {

               
                
                $data=$model->create();
                $data['rtime']=strtotime($data['rtime']);
                $result =$model->add( $data);
                if($result) {
                    add_log($this->onname.'：'.$data['name'].'添加成功');
                    if($isclose)
                    {
                        $msg=lang('添加成功','handle');
                        $backurl=U('Admin/YuYue/index',array('is_huifang'=>1));
                        echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."'); parent.location.reload();;parent.layer.close(index);</script>";

                    }else
                    {
                        $url=U('Admin/HuiFang/getlist',array('uid'=>$data['user_id'],'bktab'=>$tabid));
                        return $this->success(lang('添加成功','handle'),$url);
                    }

                }else{
                    if($isclose)
                    {
                        $msg=lang('添加失败','handle');

                        echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."'); </script>";

                    }else {
                        return $this->error(lang('添加失败', 'handle'));
                    }
                }
            }else
            {
                $this->error($model->getError());
            }
        }
    }
    public function index(){
        //权限选择
        $map=array();
        $this->assign('is_search',I('get.is_search'));
        $this->check_group("huihfrenwu_list");
        $model = M('RenWu');
        $join[] = 'LEFT JOIN __USER__ u1 ON rewu.user_id = u1.id';
        $join[] = 'LEFT JOIN __ADMIN__ a1 ON rewu.admin_id = a1.id';
        $join[] = 'LEFT JOIN __ADMIN__ fp ON rewu.handle_id = fp.id';
        $filed ='
        rewu.id as id,
        rewu.status as status,
        rewu.uuid as uuid,
        rewu.name as name,
        rewu.type_text as type_text,
        rewu.ctime as ctime,
        rewu.rtime as rtime,
        a1.name as create_name,
        fp.name as handle_name,
        u1.name as user_name,
        u1.tel as tel,
        u1.id as user_id
        ';
        if(I('get.keyword')!='')
        {
             $key=I('get.keyword');
            $map['_string'] = "u1.name like '%" . $key . "%' or u1.tel like '%" . $key . "%' or rewu.name like '%" . $key . "%'";
        }
         $status='';
         if(I('get.status')!='')
         {
            $status=I('get.status');
            $map['status']=$status;
         }
         $count = $model->alias('rewu')->join($join)->where($map)->count();// 查询满足要求的总记录数
         $pagesize = (C('PAGESIZE')) != '' ? C('PAGESIZE') : '50';
         $page = 1;
         if (isset($_GET['p'])) {
             $page = $_GET['p'];
         }

         $list = $model->alias('rewu')->field($filed)->join($join)->order('rewu.ctime desc,rewu.id desc')->where($map)->page($page . ',' . $pagesize)->select();
         //print_r($list);
         $this->assign('list', $list);// 赋值数据集
         $type_arr='';
         $menu_list = $this->getFiledArray($type_arr);
         
         $this->menu_list = $menu_list;

        
        $this->assign('page', page($count, $map, $pagesize));// 赋值分页输出
        
        $this->display();

    }
    public function getFiledArray($type){
        switch ($type) {
           
            
            default:
                $menu_list = array(

                    
                    'td-1' => array('name' => lang('姓名'), 'filed'=>'user_name','diy'=>'text-blue','is_time'=>'','fun'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-2' => array('name' => lang('电话'), 'filed'=>'tel','diy'=>'', 'is_time'=>'','fun'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-3' => array('name' => lang('回访类型'), 'filed'=>'type_text','diy'=>'', 'is_time'=>'','fun'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-4' => array('name' => lang('回访状态'), 'filed'=>'status','diy'=>'', 'is_time'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    
                    'td-7' => array('name' => lang('待回访日期'), 'filed'=>'rtime','diy'=>'text-info', 'is_time'=>'1','fun'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-5' => array('name' => lang('创建日期'), 'filed'=>'ctime','diy'=>'text-blue', 'is_time'=>'1','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-6' => array('name' => lang('创建人'), 'filed'=>'create_name','diy'=>'', 'is_time'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    'td-8' => array('name' => lang('指定回访人'), 'filed'=>'handle_name','diy'=>'', 'is_time'=>'','fun'=>'','w' => '', 'h' => '', 'is_hide' => ''),
                    
                   

                );
                break;
        }
        return $menu_list;
    }

    public function add($uid){
        //权限选择
        $this->check_group($this->rule_qz."_add");
       

        $this->data=get_user($uid);
        return $this->display();



    }
    public function edit(){
        //权限选择

        $this->check_group($this->rule_qz."_edit");
        if(IS_POST)
        {
            $model =D('RenWu');

            if($model->create()) {
                $data=$model->create();
                $data['rtime']=strtotime($data['rtime']);
                $result =   $model->save($data);

                if($result) {
                    add_log($this->onname.'：'.$data['name'].'更新成功');
                    $msg=lang('更新成功','handle');
                    return '';
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');</script>";
                    //return  $this->success(lang('更新成功','handle'),'/Admin/edit',$id);
                }else{
                    $msg=lang('数据一样无更新','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');</script>";
                }
            }else{
                return $this->error($model->getError());
            }
        }else{
            $id=I('get.id');
            $map=array(
                'uuid'=>$id
            );

            $model=M('RenWu')->where($map)->find();

            if($model) {
                $this->data =  $model;// 模板变量赋值

            }else{
                return $this->error(lang('数据错误','handle'));
            }
            $user=get_user($model['user_id']);
           
            $this->assign('user_name',$user['name']);
            $this->display();
        }
    }
    public  function del(){
        //权限选择
        $this->check_group($this->rule_qz."_del");

        $id=I('get.id');
        $map=array(
            'uuid'=>$id
        );
        //自己删除自己
        if(!check_group('root'))
        {
            if(check_group('hfrenwu_only'))
            {
                $map['admin_id']=session('admin_id');
            }
        }
        $model   =   D('RenWu');
        $data=$model->where($map)->find();
        $result=$model->where($map)->delete();
        if($result)
        {
            add_log($this->onname.'：'.$data['name'].'删除成功');
            return  $this->success(lang('删除成功','handle'));;
        }
        add_log($this->onname.'：'.$data['name'].'删除失败');
        return $this->error(lang('删除失败','handle'));;
    }
    public function handle($id){
        //权限选择
        $this->check_group($this->rule_qz."_edit");;
        $model =M('RenWu');
        $type=I('get.type');
        if($type=='true')
        {
            $status=1;
        }else
        {
            $status=0;
        }
        $data['id'] =$id;
        $data['status'] = $status;


        if($model->save($data)){
            add_log($this->onname.'：设置状态成功');
            return  $this->success(lang('更新成功','handle'));
        }else
        {
            add_log($this->onname.'：设置状态失败');
            return $this->error(lang('更新失败','handle'));
        }
    }
}
