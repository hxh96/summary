<?php
namespace Home\Model;
use Think\Model;

/**
 * 上传文件类(支持上传文件格式较多，包括视频、音频、图片、文档等文件)
 */
class UploadFileModel extends Model {

    private $_uploadObj       = '';

    private $_uploadVideoData = '';

    const UPLOAD = 'Upload';

    public function __construct() {

        $this->_uploadObj = new  \Think\Upload();

        $this->_uploadObj->rootPath = './'.self::UPLOAD.'/';

        $this->_uploadObj->subName = date(Y) . '/' . date(m) .'/' . date(d);

        $this->_uploadObj->maxSize = 26214400;

        $this->_uploadObj->exts = array('doc','xls','xlsx','txt','pdf','jpg','gif','png','mp3','wav','avi','flv','mp4','docx','ppt','html','htm', 'zip', 'rar', 'gz', 'bz2','wmv','wma','mid','mpg','asf','rm','rmvb','swf','jpeg','bmp');
    }

    

    public function fileUpload() {

        $res = $this->_uploadObj->upload();

        if($res) {

            return '/' .self::UPLOAD . '/' . $res['uploadfile']['savepath'] . $res['uploadfile']['savename'];
        }else{
            
            return false;
        }
    }
}
