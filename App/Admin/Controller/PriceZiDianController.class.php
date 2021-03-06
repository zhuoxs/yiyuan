<?php
namespace Admin\Controller;
class PriceZiDianController extends AuthController {
    protected $onname='收费项目';
    protected $rule_qz='yushen';


    public function index(){
        //权限选择

        $this->check_group($this->rule_qz);

        $map=array();
        if(IS_GET)
        {
            $data=I('get.');
            $keyword=$data['keyword'];
            unset($data['sorttype']);
            unset($data['sort']);
            unset($data['keyword']);
            foreach ($data as $k=>$v)
            {

                if($v!='' )
                {
                    if( $v!='none')
                    {
                        $map[$k]=$v;
                    }

                }
            }
            if($keyword)
            {
                /*$map['name']=array(
                     'like','%'.$keyword.'%'
                );
                $map['ticket_name']=array(
                    'like','%'.$keyword.'%'
                );
                $map['ticket_name']=array(
                    'like','%'.$keyword.'%'
                );*/
                $map['name|ticket_name|price_code|code'] = array('like','%'.$keyword.'%');
            }
        }


        $count =  D('Price')->where($map)->count();// 查询满足要求的总记录数
        $pagesize=(C('PAGESIZE'))!=''?C('PAGESIZE'):'50';
        $pagesize=I('get.pagesize')==''?$pagesize:I('get.pagesize');
        $page=1;
        if(isset($_GET['p']))
        {
            $page=$_GET['p'];
        }
        
        $list =  D('Price')->where($map);


        if(I('sorttype'))
        {
            $sorttype=I('sorttype');
            $sort=I('sort');
            if($sort!=='none')
            {
                $list=$list->order(
                    array(
                        $sorttype=>$sort
                    )
                    
                );
            }

        }else
        {
            $list=$list->order('sort desc,id desc');
        }

        $list=$list->page( $page.','.$pagesize)->select();
        $lb=M('LanMu')->where(array('type'=>'xiaofei','checked'))->select();
        $lbarr=array();
        if(count($lb)>0)
        {
            foreach ($lb as $key => $value) {
                $lbarr[$value['id']]=$value['name'];
            }
        }

        $this->assign('list',$list);// 赋值数据集
        $rule=($this->getList());
        $menu_list= array(
            '排序',
            '编号',
            '费用名称',
            '所属类别',
            '票据名称',
            '单价',
            '单位',
            '金额修改',
            '助记符',
            '状态',
            '操作'
            );
        $this->lbarr=$lbarr;
        $this->menu_list=$menu_list;
        $this->assign("subcategory",$rule);
        $this->assign('page',page( $count ,$map,$pagesize));// 赋值分页输出
        $this->display();

    }
    public function add(){
        //权限选择
        $this->check_group($this->rule_qz);
        if(IS_POST)
        {

            $model=D("Price");

            if($model->create())
            {
              
                $data=$model->create();

                //取得最大序号
               
                $data= create_price_code($data['fid'])+$data;
               
                $result =    $model->add( $data);
                if($result) {
                    add_log($this->onname.'：'.$data['name'].'添加成功');
                    $msg=lang('添加成功','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
                }else{
                    add_log($this->onname.'：'.$data['name'].'添加失败','/Admin/add');
                    $msg=lang('添加失败','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');;parent.window.location.reload()</script>";
                }
            }else
            {
                $this->error($model->getError());
            }
        }else
        {
            $rule=get_tree_option($this->getList(),0);
            $this->assign('rule',$rule);// 赋值数据集
            $this->display();
        }

    }
    public function getList(){
        $rule=M('KeShi')->where(array('checked'=>1,'type'=>'keshi','fid'=>0))->select();
        return $rule;

    }
    public function ajaxList($id){
        $tfid=I('get.tfid');

        $rule=M('KeShi')->where(array('checked'=>1,'type'=>'keshi','fid'=>$id))->select();
        $str='';
        if(count($rule)>0)
        {
            foreach ($rule as $k=>$v)
            {
                if($tfid==$v['id'])
                {
                    $str.="<option selected='selected' value='".$v['id']."'>".$v['name']."</option>";
                }else
                {
                    $str.="<option value='".$v['id']."'>".$v['name']."</option>";
                }

            }
        }
        return $this->ajaxReturn($str);
    }
    public function excel()
    {
        if (IS_POST) {
            $file=I('post.file');
            error_reporting(E_ALL); //开启错误
            set_time_limit(0); //脚本不超时
            date_default_timezone_set('Europe/London'); //设置时间
            header('Content-type: text/html; charset=utf-8');

            vendor('PHPExcel.Classes.PHPExcel');
            $type = pathinfo($file);
            $type = strtolower($type["extension"]);
            $type=$type==='csv' ? $type : 'Excel5';
            ini_set('max_execution_time', '0');
            $file = WEB_ROOT.pic_url($file,'','file');
            // 判断使用哪种格式
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($file);
            $sheet = $objPHPExcel->getSheet(0);
            // 取得总行数
            $highestRow = $sheet->getHighestRow();
            // 取得总列数
            $highestColumn = $sheet->getHighestColumn();
            //循环读取excel文件,读取一条,插入一条
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//将，A,B,D,这些转为0,1,2,3,4
            for($j=2;$j<=$highestRow;$j++){
                //从A列读取数据
                for($k=0;$k<=$highestColumnIndex;$k++){
                    // 读取单元格
                    $strs[$k]=$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($k, $j)->getValue();
                }
                $data[] = array(
                 'a'=>"$strs[0]",
                 'b'=>"$strs[1]",
                 'c'=>"$strs[2]",
                 'd'=>"$strs[3]",
                 'e'=>"$strs[4]",
                 'f'=>"$strs[5]",
                 'g'=>"$strs[6]",
                 'h'=>"$strs[7]"
                

               
                );
            }
            foreach ($data as $k=>$v)
            {
                $dataList[] = array(
                    'ctime' => time(),
                    'uuid' => create_uuid(),
                    'name' => $v['a'],
                    'ticket_name'=>$v['b'],
                    'fid' => $v['c'],
                    'price'=>$v['d'],
                    'danwei'=>$v['e'],
                    'is_update'=>$v['f'],
                    'code'=>$v['g'],
                    'base_code'=>$v['h'],
                    'price_code'=>str_pad(get_price_type($v['c']), 2, '0', STR_PAD_LEFT).str_pad($v['h'], 5, '0', STR_PAD_LEFT),
                    'admin_id' => session('admin_id')
                );
            }




          $model = M('Price');
            $result = $model->addAll($dataList);
            if ($result) {

                $msg = lang('导入成功', 'handle');
                echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('" . $msg . "');;parent.window.location.reload()</script>";
            } else {
                $msg = lang('导入失败', 'handle');

                echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('" . $msg . "');;parent.window.location.reload()</script>";
            }




        }
        $this->display();
    }

    public function edit(){
        //权限选择

        $this->check_group('yushen');
        if(IS_POST)
        {
            $model =D('Price');

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

            $model   =   M('Price')->where($map)->find();
            $rule=get_tree_option($this->getList(),0);
            $this->rule=$rule;
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
        $this->check_group('yushen');
        $id=I('get.id');
        $map=array(
            'uuid'=>$id
        );
        $model   =   D('Price');
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
        $this->check_group('yushen');
        $model =M('Price');
        $type=I('get.type');
        if($type=='true')
        {
            $status=1;
        }else
        {
            $status=0;
        }
        $data['id'] =$id;
        $filed=I('get.filed');
        $logmsg='';
        if($filed)
        {
            $data[$filed] = $status;
            $logmsg='是否金额可修改';
        }else
        {
            $data['checked'] = $status;
            $logmsg='设置状态';
        }


        if($model->save($data)){
            add_log($this->onname.'：'.$logmsg.'成功');
            return  $this->success(lang('更新成功','handle'));
        }else
        {
            add_log($this->onname.'：'.$logmsg.'失败');
            return $this->error(lang('更新失败','handle'));
        }
    }
   
}
