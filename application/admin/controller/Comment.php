<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 评论管理控制器
 */
namespace app\admin\controller;

use app\admin\model\CommentModel;
use think\Db;
use think\Loader;

class Comment extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }


    public function index()
    {

        $comment = CommentModel::getAll();
        $this->assign('comment', $comment);

        return $this->fetch();
    }


    public function isstatus()
    {
        $type = input('param.type');
        if ($type == 1) {
            $id = input('param.id');
            Db::name('comment')->where('id', $id)->update(array('status' => 0));
            return $this->success('审核成功');
        } else {
            $id = input('param.id');
            Db::name('comment')->where('id', $id)->update(array('status' => 1));
            return $this->success('取消审核成功');
        }

    }

    //删除评论
    public function del()
    {
        $id = input('param.id');
        if (isset($_REQUEST['type'])) {
            $com_ids_str = $this->get_del_connection($id);
            if (Db::name('comment')->where(['id' => ['in', $com_ids_str]])->update(array('del' => 2))) {
                json_str(1);
            } else {
                json_str(0);
            }
        }
        //根据传递id查找该评论下的所有子数据并且删除
        $com_id = intval($id);
        if ($com_id > 0) {
            $com_ids_str = $this->get_del_connection($com_id);

            try {
                if (Db::name('comment')->where(['id' => ['in', $com_ids_str]])->update(array('del' => 2))) {
                    $this->success('删除成功', url('admin/ask/usercomment', ['id' => $id]));
                    dump(6666);exit;
                } else {
                    $this->success('删除失败', url('admin/ask/usercomment', ['id' => $id]));
                }
            } catch (\Extension_$e) {
                $this->success('删除成功', url('admin/ask/usercomment', ['id' => $id]));
                dump($com_ids_str);exit;
            }

        } else {
            $this->error('删除失败', url('admin/ask/usercomment', ['id' => $id]));
        }

    }

    //获取所有要删除父评论子评论id
    public function get_del_connection($com_id)
    {
        $com_id = (int)$com_id;
        if ($com_id > 0) {
            $categorys = Db::name('comment')->where(['del' => 0])->select();
            if (!empty($categorys)) {
                $comment_lists = getSubs($categorys, $catId = $com_id, $level = 1);
                foreach ($comment_lists as $key => $value) {
                    $com_ids[] = $value['id'];
                }
                if (isset($com_ids)) {
                    $com_ids = array_merge([$com_id], $com_ids);
                    return $com_ids;
                }
            }
        }
        return [$com_id];
    }

    //就诊导出
    public function visit_export(){

        $data = input('param.');
        //根据传递过来的参数 查找要导出的用户
        $user = Db::name('comment')->order('id desc')->select();
        if(empty($user)){
            $this->error('所属案例为空',url('index'));
        }

        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
        $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('15px');
        $PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('15px');
        $PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('15px');
        $PHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('26px');
        $PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('30px');
        $PHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('15px');
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称
        $PHPSheet
            ->setCellValue("A1","自增ID")
            ->setCellValue("B1","评论的用户ID")
            ->setCellValue("C1","评论类型")
            ->setCellValue("D1","评论的内容ID")
            ->setCellValue("E1","评论内容")
            ->setCellValue("F1","点赞数")
            ->setCellValue("G1","回复的评论ID")
            ->setCellValue("H1","回复评论的回复用户的ID")
            ->setCellValue("I1","创建时间")
            ->setCellValue("J1","更新时间")//表格数据
            ->setCellValue("K1","审核状态")//表格数据
            ->setCellValue("L1","是否删除");
        $ks = 2;

        foreach ($user as $k=>$v){
            $type=[1=>'视频评论', 2=>'问答', 3=>'案例', 4=>'帖子评论', 5=>'头条'];
            $status=[0=>'已经审核', 1=>'未审核'];
            $del=[0=>'未删除', 1=>'已删除',2=>'异常'];
            $PHPSheet
                ->setCellValue("A".$ks,$v['id'])
                ->setCellValue("B".$ks,$v['uid'])
                ->setCellValue("C".$ks,$type[$v['type']])
                ->setCellValue("D".$ks,$v['aid'])
                ->setCellValue("E".$ks,$v['text'])
                ->setCellValue("F".$ks,$v['praise'])
                ->setCellValue("G".$ks,$v['plid'])
                ->setCellValue("H".$ks,$v['reuid'])
                ->setCellValue("I".$ks,date('Y-m-d H:i:s',$v['create_time']))
                ->setCellValue("J".$ks,date('Y-m-d H:i:s',$v['update_time']))
                ->setCellValue("K".$ks,$status[$v['status']])
                ->setCellValue("L".$ks,$del[$v['del']]);
            $ks++;
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="'.date('Y-m-d H-i-s').'.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

}