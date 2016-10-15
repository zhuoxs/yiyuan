<?php
namespace Admin\Controller;
class LevelController extends AuthController {
    protected $onname='会员等级';
    public function index(){
        $this->check_group('site_level');
        $model=M(CONTROLLER_NAME);
        $map=array();
        if(IS_GET)
        {
            $map=I('get.');

        }
        $model=$model->where(array('source'=>'sale'))->order('level asc,sort desc,id asc')->select();
        $this->assign('list',$model);

        $this->display();
    }
    public function add(){
        $this->check_group('site_level');
        if(IS_POST)
        {
            $model=D(CONTROLLER_NAME);
            if($model->create())
            {
                $result =    $model->add();
                if($result) {
                    return $this->success('操作成功！',__CONTROLLER__);
                }else{
                    return $this->error('写入错误！');
                }
            }else
            {
                $this->error($model->getError());
            }
        }else
        {

            $this->display();
        }

    }
    public function edit(){
        $this->check_group('site_level');
        if(IS_POST)
        {
            $model =D(CONTROLLER_NAME);

            if($model->create()) {
                $result =   $model->save();

                if($result) {
                    return  $this->success('更新操作成功！');
                }else{
                    return $this->error('数据一样，暂无更新');
                }
            }else{
                return $this->error($model->getError());
            }
        }else{
            $id=I('get.uuid');
            $map=array(
                'uuid'=>$id
            );

            $model   =  M(CONTROLLER_NAME)->where($map)->find();
            if($model) {
                $this->v =  $model;// 模板变量赋值
            }else{
                return $this->error('数据错误');
            }
            $this->display();
        }
    }
    public  function del(){
        $this->check_group('message');
        $id=I('get.id');
        $map=array(
            'uuid'=>$id
        );
        $model   =   D(CONTROLLER_NAME);
        $result=$model->where($map)->delete();
        if($result)
        {
            return  $this->success('删除成功');
        }
        return $this->error('删除失败');
    }
    
}