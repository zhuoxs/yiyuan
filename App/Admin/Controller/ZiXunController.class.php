<?php
namespace Admin\Controller;
class ZiXunController extends AuthController {
    protected $onname='咨询工具';
    protected $rule_qz='zixun';


    public function index($type){
        //权限选择

        $this->check_group($type);
        $model=M('LanMu');
        $map=array();
        if(IS_GET)
        {
            $map=I('get.');

        }

        $count =  $model->where($map)->count();// 查询满足要求的总记录数
        $pagesize=(C('PAGESIZE'))!=''?C('PAGESIZE'):'20';
        $pagesize=I('get.pagesize')==''?$pagesize:I('get.pagesize');

        $page=1;
        if(isset($_GET['p']))
        {
            $page=$_GET['p'];
        }

        $list =  $model->where($map)->order('sort desc,id desc')->page( $page.','.$pagesize)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('onname','咨询工具');// 赋值数据集

        $this->assign('page',page( $count ,$map,$pagesize));// 赋值分页输出
        $this->display();

    }

    public function add(){
        //权限选择
        $this->check_group('zixun');
        if(IS_POST)
        {

            $model=D('LanMu');

            $name=I('post.name');
            $is_tongji=I('post.is_tongji');
            if($name=='')
            {
                $msg=lang('名字不能为空','handle');
                echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');</script>";
                exit();
            }
            $pname=explode(",",str_replace("，",",",$name));

            foreach ($pname as $k=>$v)
            {
                $dataList[]=array(
                    'ctime'=>time(),
                    'uuid'=>create_uuid(),
                    'name'=>$v,
                    'type'=>'zixun',
                    'is_tongji'=>$is_tongji
                );
            }

            $result = $model->addAll($dataList);

            if($result) {
                add_log($this->onname.'：'.$name.'添加成功');
                $msg=lang('添加成功','handle');
                echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
            }else{
                $msg=lang('添加失败','handle');
                add_log($this->onname.'：'.$name.'添加失败','/Admin/add');
                echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
            }


        }else
        {

            $this->display();
        }

    }
    public function edit(){
        //权限选择

        $this->check_group('zixun');
        if(IS_POST)
        {
            $model =D('LanMu');

            if($model->create()) {
                $data=$model->create();
                $id=$data['id'];
                if(($data['pwd'])!='')
                {
                    $data['pwd']=sha1($data['pwd']);
                }else
                {
                    unset($data['pwd']);
                }

                $result =   $model->save($data);

                if($result) {
                    add_log($this->onname.'：'.$data['name'].'更新成功');
                    $msg=lang('更新成功','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
                    //return  $this->success(lang('更新成功','handle'),'/Admin/edit',$id);
                }else{
                    $msg=lang('数据一样无更新','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
                }
            }else{
                return $this->error($model->getError());
            }
        }else{
            $id=I('get.uuid');
            $map=array(
                'uuid'=>$id
            );

            $model   =   M('LanMu')->where($map)->find();
          
            if($model) {
                $this->data =  $model;// 模板变量赋值
            }else{
                return $this->error(lang('数据错误','handle'));
            }
            $this->display();
        }
    }
    public  function del(){
        //权限选择
        $this->check_group('zixun');
        $id=I('get.id');
        $map=array(
            'uuid'=>$id
        );
        $model   =   D('LanMu');
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
        $this->check_group('zixun');
        $model =M('LanMu');
        $type=I('get.type');
        $field=I('get.field');
        if($type=='true')
        {
            $status=1;
        }else
        {
            $status=0;
        }
        $data['id'] =$id;
        if($field!='')
        {
            $data[$field]=$status;
        }else
        {
            $data['checked'] = $status;
        }
       


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
