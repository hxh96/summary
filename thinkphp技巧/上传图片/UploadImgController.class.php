<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;

/**
 * 上传图片类
 */
class UploadImgController extends Controller {

    private $_uploadObj;

    public function __construct() {

    }

    public function index() {

        $upload = D("UploadImg");

        $res = $upload->imgUpload();

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