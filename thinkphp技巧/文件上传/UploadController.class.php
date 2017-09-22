<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14 0014
 * Time: 09:25
 */

namespace Home\Controller;


use Think\Controller;


class UploadController extends Controller
{
    private $_uploadObj;

    public function __construct() {

    }

    public function index() {

        $upload = D("UploadFile");

        $res = $upload->fileUpload();

        if( $res === false ) {

            $reuslt = array(
                'status'  => 0,
                'message' => '上传失败',
                'data'    => '',
            );
        }else{

            $reuslt = array(
                'status'  => 1,
                'message' => '上传成功',
                'data'    => $res,
            );
        }

        echo json_encode($reuslt);

    }
}