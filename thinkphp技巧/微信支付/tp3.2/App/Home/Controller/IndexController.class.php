<?php
namespace Home\Controller;
use Think\Controller;

/**
* @author CalvinPPD <www.5iweb.net>
* @version 1.0
* @create 2017/5/31
* @upd 2017/6/27
*/
class IndexController extends Controller {
	
	public function wxpay(){
			Vendor('lib.JSSDK');
			Vendor('lib.WxPayJsApiPay');
			Vendor('lib.log');
			Vendor('lib.WxPayApi');
			
			//实例化JSSDK
			$jssdk=new \JSSDK(C('appid'),C('appSecret'));
			//获取数据
			$signPackage =$jssdk->GetSignPackage();
			//初始化日志
			$logHandler= new \CLogFileHandler("./logs/".date('Y-m-d').'.log');
			$loginfo=\Log::Init($logHandler, 15);
			
			//①获取用户openid
			$tools=new \JsApiPay();
			$openId = $tools->GetOpenid();
			
			
			
			//②统一下单
			$input=new \WxPayUnifiedOrder();
			$input->SetBody("5iweb.net");
			$input->SetAttach("5iweb.net");
			$input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
			$input->SetTotal_fee("1");
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis",time()+ 600));
			$input->SetGoods_tag("5iweb.net");
			$input->SetNotify_url("http://www.5iweb.net");
			$input->SetTrade_type("JSAPI");
			$input->SetOpenid($openId);
			$order = \WxPayApi::unifiedOrder($input);
			$jsApiParameters=$tools->GetJsApiParameters($order);
	
			$this->assign('jsApiParameters',$jsApiParameters);
			$this->assign('signPackage',$signPackage);
			$this->display();
		
	}
	
	
}
