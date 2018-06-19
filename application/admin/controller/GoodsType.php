<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品分类控制器
 */

namespace app\admin\controller;
use app\admin\model\GoodsTypeModel;



class GoodsType extends Admin
{
    protected $Toptype = null;
	protected $modelName = '';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName = new GoodsTypeModel;
        if(in_array(request()->action(),array('add','alter'))){
            $this->Toptype = $this->modelName->getTop();
        }
	}

	public function index()
	{

		$cont_type = $this->modelName->getAll(15);
		$this->assign('cont_type', $cont_type);
		return $this->fetch();
	}

	public function add()
	{

		$request = request();
		if ($request->method() == 'POST') {

			$data = input('post.');
            $type = $this->modelName->where(['id'=>$data['pid']])->find();
            if($type['pid']!=0){
                $this->error('添加失败,父级分类不是顶级分类', url('index'));
            }
			$result = $this->validate($data, 'ValidateClass.CK1');
			if (true !== $result) {
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic = $this->upload($request, 'pic');
			if (!empty($pic)) {
				$data['pic'] = $pic;
			}

			if ($this->modelName->allowField(true)->save($data)) {
				$this->success('添加成功', url('index'));
			} else {
				$this->error('添加失败', url('index'));
			}
		} else {
            $this->assign('toptype', $this->Toptype);
			return $this->fetch();
		}
	}

	public function alter()
	{

		$request = request();

		if ($request->method() == 'POST') {
			$data = input('post.');
            if(isset($data['pid']) && $data['pid']!=0){
                $type = $this->modelName->where(['id'=>$data['pid']])->find();
                if($type['pid']!=0){
                    $this->error('修改失败,父级分类不是顶级分类', url('index'));
                }
            }
			$result = $this->validate($data, 'ValidateClass.CK2');
			if (true !== $result) {
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic = $this->upload($request, 'pic');
			if (!empty($pic)) {
				$data['pic'] = $pic;
			} else {
				unset($data['pic']);
			}

			if ($this->modelName->allowField(true)->save($data, ['id' => $data['id']])) {
				$this->success('修改成功', url('index'));
			} else {
				$this->error('修改失败', url('index'));
			}

		} else {
            $this->assign('toptype', $this->Toptype);
			$id = input('param.id');
			$cont_type = $this->modelName->get($id);
			$this->assign('cont_type', $cont_type);
			return $this->fetch();
		}

	}


	public function del()
	{

		$id = input('param.id');
        $type = $this->modelName->where(['pid'=>$id])->find();
        if($type){
//            echo '{"code":"1","msg":"修改失败，请先删除子分类"}';die;
            $this->error('修改失败，请先删除子分类', url('index'));
        }
		if ($this->modelName->save(['del' => 1], ['id' => $id])) {
			echo '{"code":"1"}';
		} else {
			echo '{"code":"0"}';
		}
	}
}

