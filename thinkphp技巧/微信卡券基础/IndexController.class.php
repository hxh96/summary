<?php
namespace Home\Controller;
use Think\Controller;
use Think\Barcode;


class IndexController extends Controller
{
//    public function __construct(){
//
//        parent::__construct();
//        //记录请求地址
//        session('prev',$_SERVER['REQUEST_URI']);
//        if(!session('?openid')){
//            $url = U('Wx/index');
////                     header("location: $url");
//            redirect($url);
//        }
//    }

    //获取Access_token
    public function index()
    {

//        redirect(U('Index/makeCard'));
        redirect(U('Index/makeCard'));
//        redirect(U('Index/upLogo'));
    }


    //生成卡券
    public function makeCard()
    {
        $access_token = S('access_token');
        if (empty($access_token)) {
            $access_token = $this->getAccessToken();
        }
        $url = "https://api.weixin.qq.com/card/create?access_token=" . $access_token;

        $data = file_get_contents('card.json');

        // 上传的json格式文件，文件头可能会出现多余的特殊字符
        while ($data[0] != '{') {
            $data = substr($data, 1);
        }


        $resp = $this->post_code($url, $data);

        session('card_id',$resp['card_id']);

        redirect(U('Index/makeBai'));

    }


    //设置测试白名单
    public function makeBai()
    {
        $access_token = S('access_token');
        if (empty($access_token)) {
            $access_token = $this->getAccessToken();
        }
        $url = "https://api.weixin.qq.com/card/testwhitelist/set?access_token=".$access_token;

        $data = [
                "openid"=>"oh5KhwIyDbNDpel-7BLKfKsnX_MM"
        ];
        $data = json_encode($data);
//
        $resp = $this->post_code( $url ,$data );

//        var_dump($resp);
        redirect(U('Index/up'));

    }


    //导入code  修改库存
    public function up()
    {

        $access_token = S('access_token');
        if(empty($access_token)){
            $access_token = $this->getAccessToken();
        }

        $card_id = session('card_id');
        $url = "http://api.weixin.qq.com/card/code/deposit?access_token=".$access_token;


        $data = [
            "card_id"=>$card_id,
            "code"=>[
                "510322199604038110",
                "510322199604038111",
                "510322199604038112",
                "510322199604038113"
            ]
        ];

        $data = json_encode($data);

        $resp = $this->post_code( $url ,$data );

        var_dump($resp);


        $url = "https://api.weixin.qq.com/card/modifystock?access_token=".$access_token;


        $data = [
            "card_id"=>$card_id,
            "increase_stock_value"=>3,
        ];

        $data = json_encode($data);


        $resp = $this->post_code( $url ,$data );

        redirect(U('Index/makeEr'));
//                    var_dump($resp);


    }



    //二维码
    public function makeEr()
    {
        $access_token = S('access_token');
        if(empty($access_token)){
            $access_token = $this->getAccessToken();
        }
        $url = "https://api.weixin.qq.com/card/qrcode/create?access_token=".$access_token;

        $data = [
            "action_name"=>"QR_CARD",
            "expire_seconds"=>1800,
              "action_info"=>[
                "card"=>[
                        "card_id"=> session('card_id'),
                        "code"=>"510322199604038110",
                        "openid"=>"oh5KhwIyDbNDpel-7BLKfKsnX_MM",
                        "is_unique_code"=>false ,
                        "outer_str"=>"12b",
                        "outer_id"=>1
                     ]
              ]
        ];

        $data = json_encode($data);

        $resp = $this->post_code( $url ,$data );


        session('ticket',$resp['ticket']);

        header("Location: https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$resp['ticket']);

    }


    //上传logo
    public function upLogo()
    {
        $access_token = S('access_token');
        if(empty($access_token)){
            $access_token = $this->getAccessToken();
        }
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=" . urlencode( $access_token );
        if(IS_POST){
            $img = $_FILES['img'];
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Upload/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                foreach($info as $file){
                    $comment_img = '/Upload/'.$file['savepath'].$file['savename'];
                }
            }
            $file_info = [
                'filename' => $img['name'],
                'content-type' => $img['type'],
                'filelength' => $img['size'],
            ];
//            $comment_img = $_SERVER['DOCUMENT_ROOT'].$comment_img;
            $comment_img = $_SERVER['DOCUMENT_ROOT'].$comment_img;
            $file = [
                'media' => '@'.$comment_img,
                'form-data' => $file_info,
            ];

//            var_dump($file);
            $resp = $this->post_code( $url, $file );


                        var_dump($resp);

        }
        $this->display();
    }


    function getAccessToken()
    {
        $appid = "wx84e7004a2ffd3d8b";
        $secret = "e0093232d616339ec71f07c825075c37";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid.'&secret='.$secret;
        $resp = $this->post_code( $url );

        S('access_token',$resp['access_token'],1800);
        return $resp['access_token'];

    }



        function post_code($url,$data=false){
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt( $ch, CURLOPT_SAFE_UPLOAD, FALSE);
                curl_setopt ($ch, CURLOPT_HEADER, 0);
            if($data==true){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            }
            $res=curl_exec($ch);

                $err_code = curl_errno($ch);
            curl_close($ch);
            if($err_code!=0){
                echo $err_code;exit;
            }
            $data = json_decode($res,true);
            return $data;
        }















































    //替换
    public function test()
    {
        header('Content-Type:text/html;charset=utf8');
        $num = "13966778888";
        $str = substr_replace($num, '****', 3, 4);
        echo $str;
        echo '<br/>';
        echo substr_replace('你好啊', '***', 3, 3);


        //百度寻找实用的插件嵌入测试,实现多文件上传,短语验证,移动端判断,editor百度编辑器等等,深入了解其原理并分享给大兄弟.
    }


    //大数据首页
    public function information()
    {
        $this->display();
    }

    //大数据接口地址
    public function port_ip()
    {
        return 'http://223.85.1.15:85';
    }


    //到馆数据
    public function come()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/todaycome.php");
            $a = json_decode($a, true);
            if (!$a) {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            } else {
                $return['code'] = 1;
                $return['data'] = $a;
            }
            $this->ajaxReturn($return);
        }
    }

    //馆藏数据
    public function collection()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/GuanCang_count.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a;
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }


    //馆藏分类
    public function category()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/Class_Guancang_Count.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a['data'];
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }


    //读者排行
    public function reader()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/Reader_PaiHang_Year.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a;
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }


    //年度借阅
    public function lend()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/year_lend_count.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a;
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }

    //年度归还
    public function also()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/year_huan_count.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a;
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }

    //当前借还信息
    public function borrowed()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/today_lend.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a['data'];
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }


    //新书推荐
    public function newBook()
    {
        if (IS_AJAX) {
            $a = file_get_contents($this->port_ip() . "/bigData_Weixin/newbook.php");
            $a = json_decode($a, true);
            if (isset($a['code']) && $a['code'] == 200) {
                $return['code'] = 1;
                $return['data'] = $a['data'];
            } else {
                $return['code'] = 0;
                $return['msg'] = '暂无';
            }
            $this->ajaxReturn($return);
        }
    }

    //抓取
    public function zhua()
    {
        header('Content-Type:text/html;charset=utf8');
        $z = '';
        for ($i = 1; $i < 500; $i++) {
            for ($x = 1; $x < 500; $x++) {
                $url = "http://www.mslib.cn/index.php/home/index/content/id/" . $i . "/colum_id/" . $x . ".html";
//                $url="http://www.mslib.cn/index.php/home/index/content/id/308/colum_id/18.html";
//                $url="http://www.mslib.cn/index.php/home/index/content/id/6/colum_id/16.html";
                $str = file_get_contents($url);
                if ($str) {
                    preg_match("/<div class=\"detailed_title\">(.*?)*<\/div>/si", $str, $str1);
                    preg_match_all("/<p>(.*?)<\/p>|<p ([\s\S]*)>(.*?)<\/p>/si", $str, $str2);
                    echo $str1[0];
                    for ($z = 1; $z < count($str2[0]); $z++) {
                        echo $str2[0][$z];
                    }
                }
            }
        }
//        echo htmlspecialchars($z);
    }




    public function file()
    {
        $this->display();
    }


    public function zzz()
    {
        header('Content-type: application/json');
        $data['strUserName'] = 'supervisor';
        $data['strPassword'] = '';
        $data['strParameters'] = 'type=worker,client=REST|0.01';
        $data = json_encode($data);
        $re = curlRequest('http://www.baidu.com','post',$data);
        $date = date('Y-m-d',time());
        error_log(date('Y-m-d H:i:s',time()).$re,3,$date.'.log');
    }
}