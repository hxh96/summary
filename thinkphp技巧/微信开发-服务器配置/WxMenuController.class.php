<?php
namespace Run\Controller;
use Think\Controller;

/*
 *微信开发者模式
 */
class WxMenuController extends Controller {

	public function __construct(){

       parent::__construct();
    }

	 public function index(){
	     $timestamp = $_GET['timestamp'];
		 $nonce     = $_GET['nonce'];
		 $token     = $this->getToken();
		 //token自定义设置，但是一定要和设置的值相同（微信公众号开发者设置）
		 $signature = $_GET['signature'];
		 $echostr   = $_GET['echostr'];//第一次验证的时候才有，验证成功后第二次将不再发送该值
		 $arr       = array($timestamp,$nonce,$token);
		 sort($arr);
	     //2.将排序后的三个参数拼接之后用sha1加密
	     $tmpstr = implode($arr);//join
		 $tmpstr = sha1($tmpstr);
	     //3.将加密后的字符串与signature比较，判断该请求是否来自微信
	     if($tmpstr == $signature && $echostr){
			 //第一次介入weixin api接口时进行验证
			 echo $echostr;
			 
			 exit;
		 }else{ 
		      //$this->getDelete();
		 	  $this->responseMsg();
			  $this->getItem();
		 }
	   }




		//获取access_token	
		public function getWxtoken(){
				
		 if( session('access_token') && session('expires_time') > time() ){

	        $token = session('access_token');

		 }else{

			$appId    = D('System')->getByvar('appid');

            $secret   = D('System')->getByvar('secret');

		    $url   = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appId.'&secret='.$secret;

			$res   = curlRequest($url);

			session('access_token',$res['access_token']);

			session('expires_time',time()+7200);

		    $token = $res['access_token'];

		}
		
		    return $token;
						
		}

		//获取服务器IP地址
		public function getWxIP(){

			$token = $this->getWxtoken();

			$url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$token;

		    $res = curlRequest($url);

			return $res;
		}
        

		//自定义菜单
		public function getItem(){

			$token = $this->getWxtoken(); 

			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;

			$item = D('Menu')->getMenu();

			$postArr = array(
		       'button'=>array(
		       	foreach ($item as $key => $value) {
		       	 switch ($value['type']) {
		       	 	case 'click':
	       	 		 array(
	       	 			'type'=>$value['type'],
                        'name'=>urlencode($value['name']),
                        'key'=>$value['key']
	       	  	      ),
		       	 		break;
		       	 	case 'view':
	       	 		 array(
	       	 			'type'=>$value['type'],
                        'name'=>urlencode($value['name']),
                        'url'=>$value['url']
	       	  	      ),
		       	 		break;
		       	 	default:
	       	 		array(
                      'name'=>$value['name'],
                      'sub_button'=>array(
                        foreach ($value['nextmenu'] as $k => $v) {
                        	switch ($v['type']) {
                        		case 'click':
				       	 		 array(
				       	 			'type'=>$v['type'],
			                        'name'=>urlencode($v['name']),
			                        'key'=>$v['key']
				       	  	      ),
					       	 		break;
					       	 	case 'view':
				       	 		 array(
				       	 			'type'=>$v['type'],
			                        'name'=>urlencode($v['name']),
			                        'url'=>$v['url']
				       	  	      ),
					       	 		break;
                        	}
                        }
                      	)
	       	 			)
		       	 	break;
		       	 }
		       	  
		       	}
		        
		       	 )
				);

			 $postJson = urldecode(json_encode($postArr));

			 $res = curlRequest($url,'post',$postJson);
			
		}

		/*删除菜单*/
		public function getDelete(){

			$token = $this->getWxtoken();

			$url   = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$token;

			$res   = curlRequest($url);
			
		}

     //接收事件推送并回复纯文本消息
	 public function responseMsg(){
	  //1.获取微信推送过来的post数据，数据类型为xml
	  $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
	  //2.处理消息类型，并设置回复类型和内容
		
	  $postObj = simplexml_load_string($postArr);//将xml转化成对象
	  //$postObj->ToUserName   = '';//开发者微信号
	  //$postObj->FromUserName = '';//发送方微信号（一个OpenId）
      //$postObj->CreateTime   = '';//消息创建时间（为整型）
	  //$postObj->MsgType      = '';//消息类型，event
	  //$postObj->Event        = '';//事件类型，subscribe(订阅),unsubscribe(取消订阅)
	  if(strtolower($postObj->MsgType) == 'event'){//判断消息类型是事件推送
	 	 if(strtolower($postObj->Event) == 'subscribe'){//事件类型为关注/订阅
	 		 //回复用户消息
	 		 $toUser   = $postObj->FromUserName;
	 		 $fromUser = $postObj->ToUserName;
	 		 $time     = time();
	 		 $msgType  = 'text';
	 		 $content  = '欢迎关注绵阳文化馆订阅号！';
	 		/* <xml>
	 		<ToUserName><![CDATA[toUser]]></ToUserName>
 		    <FromUserName><![CDATA[fromUser]]></FromUserName>
	 		<CreateTime>12345678</CreateTime>
	 		<MsgType><![CDATA[text]]></MsgType>
	 		<Content><![CDATA[你好]]></Content>
	 		</xml>
	 		 */	
	 		 //这是回复消息的xml格式
                 $template  = "<xml>
		 						<ToUserName><![CDATA[%s]]></ToUserName>
		 						<FromUserName><![CDATA[%s]]></FromUserName>
		 						<CreateTime>%s</CreateTime>
		 						<MsgType><![CDATA[%s]]></MsgType>
		 						<Content><![CDATA[%s]]></Content>
		 						</xml>";
	 		$info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
	 		echo $info;
            }
	  }else if(strtolower($postObj->MsgType) == 'text'){
	 	     $toUser   = $postObj->FromUserName;
	 		 $fromUser = $postObj->ToUserName;
	 		 $time     = time();
	 		 $msgType  = 'text';
	 		 $template = "<xml>
	 						<ToUserName><![CDATA[%s]]></ToUserName>
	 						<FromUserName><![CDATA[%s]]></FromUserName>
	 						<CreateTime>%s</CreateTime>
	 						<MsgType><![CDATA[%s]]></MsgType>
	 						<Content><![CDATA[%s]]></Content>
	 						</xml>";
	 	 switch($postObj->Content){
	 	        case 1:
	 		    $content  = "感谢你的关注！";
	 			break;
	 			case 2:
	 			$content  = "关注绵阳文化馆订阅号，更多惊喜等着你！";
	 			break;
	 			case 3:
	 			$content  = "关注绵阳文化馆，更多精彩信息在这里！";
	 			break;
	 			default:
	 			$content  = "<a href='Http://mcc.cdphm.com'>你好像迷路了呢！去逛逛吧。</a>";
            }
	 	$info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
	 		echo $info;
	  }

	 }

	  //接收事件推送并回复纯文本消息
	 public function responseNews(){
		 //1.获取微信推送过来的post数据，数据类型为xml
		 $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
		 //2.处理消息类型，并设置回复类型和内容
		
		 $postObj = simplexml_load_string($postArr);//将xml转化成对象
		 //$postObj->ToUserName   = '';//开发者微信号
		 //$postObj->FromUserName = '';//发送方微信号（一个OpenId）
         //$postObj->CreateTime   = '';//消息创建时间（为整型）
		 //$postObj->MsgType      = '';//消息类型，event
		 //$postObj->Event        = '';//事件类型，subscribe(订阅),unsubscribe(取消订阅)
		 if(strtolower($postObj->MsgType) == 'event'){
		 	if($postObj->Event == 'CLICK'){
			     $toUser      = $postObj->FromUserName;
				 $fromUser    = $postObj->ToUserName;
				 $time        = time();
				 $msgType     = 'news';
				 $count       = 1;
				 $title       = '泗溪洛的公众号';
				 $description = '非常感谢你的关注！';
				 $picurl      = 'https://www.baidu.com/img/bd_logo1.png';
				 $template    = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<ArticleCount>%s</ArticleCount>
								<Articles>
								<item>
								<Title><![CDATA[%s]]></Title> 
								<Description><![CDATA[%s]]></Description>
								<PicUrl><![CDATA[%s]]></PicUrl>
								<Url><![CDATA[%s]]></Url>
								</item>
								</Articles>
								</xml>";
			 switch($postObj->EventKey){
			        case 'SXL_1101':
			        $url      = 'http://jylib.cdphm.com';
				    $info     = sprintf($template,$toUser,$fromUser,$time,$msgType,$count,$title,$description,$picurl,$url);
				    echo $info;
				    case 'JJ_1025':
				    $url      = 'http://mslib.cdphm.com';
				    $info     = sprintf($template,$toUser,$fromUser,$time,$msgType,$count,$title,$description,$picurl,$url);
					break;
					
            }
        }
			
		 }

	 }
    
    public function getToken(){

    	return D('System')->getData('token');
    }

}