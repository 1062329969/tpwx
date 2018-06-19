<?php
/**
 *
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 14:38
 */

namespace app\admin\model;


use think\Model;

class PanoramaModel extends Model
{
	protected $name='panorama';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
        return self::paginate($page);
	}

}