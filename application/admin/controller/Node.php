<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 左侧菜单管理
 */


namespace app\admin\controller;
use think\Db;
use tree\Tree;

class Node extends Admin
{
    private $module_id = [
        1=>'首页',
        2=>'商城管理',
        3=>'项目&预约',
        4=>'营销中心',
        5=>'数据中心',
        6=>'帮助中心'
    ];

	protected function _initialize()
	{
		$this->checkAdminLogin();
	}

	//节点管理
    public function index()
    {
        $node = Db::name('node')->order('sort asc')->select();
        $array = array();
        //构建生成树中所需的数据
        foreach($node as $k => $r)
        {
            $r['id']      = $r['id'];
            $r['title']   = $r['title'];
            $r['name']    = $r['name'];
            $r['status']  = $r['status']==1 ? '<font color="red">√</font>' :'<font color="blue">×</font>';
            $r['submenu'] = $r['level']==3 ? '<font color="#cccccc">添加子菜单</font>' : "<a href='".url('nodeAdd',array('pids'=>$r['id']))."'>添加子菜单</a>";
            $r['edit']    = $r['level']==1 ? '<font color="#cccccc">修改</font>' : "<a href='".url('nodeEdit',array('id'=>$r['id'],'pids'=>$r['pid']))."'>修改</a>";
            $r['del']     = $r['level']==1 ? '<font color="#cccccc">删除</font>' : "<a onClick='del(\"".$r['id']."\")' href='javascript:void(0)'>删除</a>";
            switch ($r['display']) {
                case 0:
                    $r['display'] = '不显示';
                    break;
                case 1:
                    $r['display'] = '主菜单';
                    break;
                case 2:
                    $r['display'] = '子菜单';
                    break;
            }
            switch ($r['level']) {
                case 0:
                    $r['level'] = '非节点';
                    break;
                case 1:
                    $r['level'] = '应用';
                    break;
                case 2:
                    $r['level'] = '模块';
                    break;
                case 3:
                    $r['level'] = '方法';
                    break;
            }
            $array[]      = $r;
        }

        $str  = "<tr class='tr'>
                    <td align='center'><input type='text' value='\$sort' size='3' name='sort[\$id]'></td>
                    <td align='center'>\$id</td> 
                    <td style='text-align:left'>\$spacer \$title (\$name)</td> 
                    <td align='center'>\$level</td> 
                    <td align='center'>\$status</td> 
                    <td align='center'>\$display</td> 
                    <td align='center'>
                        \$submenu | \$edit | \$del
                    </td>
                  </tr>";

        $Tree = new Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $Tree->init($array);
        $html_tree = $Tree->get_tree(0, $str);
        $this->assign('html_tree',$html_tree);
        $this->assign('title','功能节点管理');
        return $this->fetch();
    }

    //对功能节点进行排序
    public function nodeSort()
    {
        $sorts = input('param.json');
        $sorts=json_decode($sorts);
        if(!is_array($sorts)) $this->error('参数错误!');
        foreach ($sorts as $id => $sort)
        {
            //print_r($sort);
			//echo $sort->name;
			$sort->name=str_replace('sort[','',$sort->name);
			$sort->name=str_replace(']','',$sort->name);
			
            Db::name('node')->where('id',intval($sort->name))->update(array('sort' =>intval($sort->value)));
        }
        $this->success('更新完成！');
    }
    
    //添加功能节点
    public function nodeAdd()
    {
        $request = request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $data['status']=1;
            
            if(empty($data['title']))
            {
                $this->error('节点名称不能为空!'); 
            }
            //根据表单提交的POST数据创建数据对象
            if(Db::name('node')->insert($data))
            {
                $this->success('添加成功！');
            }
            else
            {
                $this->error('添加失败!');
            }
        }
        else
        {
            //选择子菜单
            $pid = input('param.pids');
            $pid = isset($pid)?$pid:0;
            //判断pid 如果是1  则显示所有模块 如果不是1 则显示对应模块
            if($pid==1){
                $this->assign('module_id',$this->module_id);
            }else{
                $module_id = Db::name('node')->find($pid)['module_id'];
                $this->assign('module_id',['name'=>$this->module_id[$module_id],'id'=>$module_id]);
            }
            $str  = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($this->selectNode());
            $select_categorys = $Tree->get_tree(0, $str, $pid);
            $this->assign('title','添加');
            $this->assign('pid',$pid);
            $this->assign('select_categorys',$select_categorys);
            return $this->fetch();
        }
        
    }

    //编辑功能节点
    public function nodeEdit(){
        $request = request();

        if($request->method()=='POST'){
            $data=input('post.');
            $data['status']=1;
            
            if(empty($data['title'])){
                $this->error('节点名称不能为空!'); 
            }
            unset($data['module_name']);
            //根据表单提交的POST数据创建数据对象
            if(Db::name('node')->update($data)){
                $va = Db::name('node')->where(['pid'=>$data['id']])->update(['module_id'=>$data['module_id']]);
                $this->success('修改成功！');
            }else{
                $this->error('修改失败!');
            }
        }else{
            $id = input('param.id');
            $pid= input('param.pids');

            if(!$id || !$pid) $this->error('参数错误!');
            $str  = "<option value='\$id' \$selected \$disabled >\$spacer \$title</option>";
            $Tree = new Tree();
            $Tree->init($this->selectNode());
            $select_categorys = $Tree->get_tree(0, $str, $pid);

            $info  = Db::name('node')->where('id',$id)->find();

            $this->assign('module_id',$this->module_id);
            $this->assign('title','编辑');
            $this->assign('select_categorys',$select_categorys);
            $this->assign('info', $info);
            $this->assign('pid', $pid);
            return $this->fetch();
        }
    }

    //删除功能节点
    public function nodeDel()
    {
        $id = input('param.id');
        $node  = Db::name('node')->delete($id);
        if($node)
        {
            $this->success('删除成功!');
        }
        else
        {
            $this->error('删除失败!');
        }
    }
    
    //添加修改的时候调用的下拉选择节点
    protected function selectNode()
    {
       $allNode  = Db::name('node')->order('sort desc')->select();
       $array = array();
       foreach($allNode as $k => $r)
       {
            $r['id']         = $r['id'];
            $r['title']      = $r['title'];
            $r['name']       = $r['name'];
            $r['disabled']   = $r['level']==3 ? 'disabled' : '';
            $array[$r['id']] = $r;
        }
        return  $array;
    }

    public function modulechange(){
        $pids = input('param.pids');
        $data = Db::name('node')->find($pids);
        $data['module_name'] = $this->module_id[$data['module_id']];
        return $data;
    }
}
