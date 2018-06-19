<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
//    '[hello]'     => [
//        ':id'   => ['index/hellos', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hellos', ['method' => 'post']],
//    ],
//    'qn/:q'=>'@index/Questionnaire/index/qid/:id'
//    'home'=>'@index/Index/homes'
    //活动
    'f'=>'@index/father/index',
    'f/a'=>'@index/father/getresult'
//    '[f]'=>[
//        '/'=>'@index/father/index',
//        '/a'=>'@index/father/getresult',
//    ]
];
