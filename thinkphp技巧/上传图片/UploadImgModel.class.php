<?php
namespace Home\Model;
use Think\Model;

/**
 * 上传图片类
 */
class UploadImgModel extends Model {

    private $_uploadObj     = '';

    private $_uploadImgData = '';

    const UPLOAD = 'Upload';

    public function __construct() {

        $this->_uploadObj = new  \Think\Upload();

        $this->_uploadObj->rootPath = './'.self::UPLOAD.'/';

        $this->_uploadObj->subName = date(Y) . '/' . date(m) .'/' . date(d);

        $this->_uploadObj->exts = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        $this->_uploadObj->maxSize = 3145728;
    }

    

    public function imgUpload() {

        $res = $this->_uploadObj->upload();

        if($res) {

            return '/' .self::UPLOAD . '/' . $res['file']['savepath'] . $res['file']['savename'];
        }else{
            
            return false;
        }
    }
}
