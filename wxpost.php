
<?php
header("Content-type:text/html; charset=utf-8");
include_once("../../../database/mysql.config.php");
include_once("../moneyfunc.php");

$top_uid = $_REQUEST['top_uid'];

date_default_timezone_set('PRC');

$getwximg = false;

if(function_exists("date_default_timezone_set"))
{
	date_default_timezone_set("Asia/Shanghai");
}

//获取第三方的资料
$pay_type = $_REQUEST['pay_type'];
$params = array(':pay_type'=>$_REQUEST['pay_type']);
$sql = "select t.pay_name,t.mer_id,t.mer_key,t.mer_account,t.pay_type,t.pay_domain,t1.wy_returnUrl,t1.wx_returnUrl,t1.zfb_returnUrl,t1.wy_synUrl,t1.wx_synUrl,t1.zfb_synUrl from pay_set t left join pay_list t1 on t1.pay_name=t.pay_name where t.pay_type=:pay_type";
$stmt = $mydata1_db->prepare($sql);
$stmt->execute($params);
$row = $stmt->fetch();
$pay_mid = $row['mer_id'];
$pay_mkey = $row['mer_key'];
$pay_account = $row['mer_account'];
$return_url = $row['pay_domain'].$row['wx_postUrl'];
$merchant_url = $row['pay_domain'].$row['wx_synUrl'];
if($pay_mid == "" || $pay_mkey == "")
{
	echo "非法提交参数";
	exit;
}



$form_url = 'https://pay.swiftpass.cn/pay/gateway';  //提交地址
$service = 'pay.weixin.native';

$mch_id = $pay_mid; 	//商戶號

$out_trade_no = date("YmdHis").substr(microtime(),2,5).rand(1,9);  //隨機生成商户訂單編號

$body = '測試用支付';

$mch_create_ip = getClientIp();  //取到電腦 IP function在 moneyfunc.php 裡

$total_fee = $_GET['MOAmount']; //訂單支付金額

$notify_url = $merchant_url;  //異步回傳地址

$nonce_str = 'test123';

$key = $pay_mkey;  //商戶金鑰

$signText ='body='.$body.'&mch_create_ip='.$mch_create_ip.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&notify_url='.$notify_url.'&out_trade_no='.$out_trade_no.'&service='.$service.'&total_fee='.$total_fee.'&key='.$key;
$sign = strtoupper(md5($signText));
$data = '<xml><body><![CDATA['.$body.']]></body>\n<mch_create_ip><![CDATA['.$mch_create_ip.']]></mch_create_ip>\n<mch_id><![CDATA['.$mch_id.']]></mch_id>\n<nonce_str><![CDATA['.$nonce_str.']]></nonce_str>\n<notify_url><![CDATA['.$notify_url.']]></notify_url>\n<out_trade_no><![CDATA['.$out_trade_no.']]></out_trade_no>\n<service><![CDATA['.$service.']]></service>\n<sign><![CDATA['.$sign.']]></sign>\n<total_fee><![CDATA['.$total_fee.']]></total_fee>\n</xml>';


$bankname = $pay_type."->微信在线充值";
$payType = $pay_type."_wx";

$result_insert = insert_online_order($_REQUEST['S_Name'] , $out_trade_no , $total_fee,$bankname,$payType,$top_uid);
if ($result_insert == -1)
{
	echo "会员信息不存在，无法支付，请重新登录网站进行支付！";
	exit;
}
else if ($result_insert == -2)
{
	echo "订单号已存在，请返回支付页面重新支付";
	exit;
}

//使用cURL method
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $form_url);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, "$data");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($ch);
curl_close($ch);
$xml = (array)simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA) or die("Error: Cannot create object");
$array = json_decode(json_encode($xml), 1);



 ?>
 <html>
   <head>
     <title>跳转......</title>
     <meta http-equiv="content-Type" content="text/html; charset=utf-8" />
   </head>
   <body>
     <form action="./qrcode.php"; method="get" id="frm1" >  
         <p>正在为您跳转中，请稍候......</p>
         <input type="hidden" name="code" id="code_url" value="<?php echo $array["code_url"]; ?>"/>
     </form>
     <script language="javascript">
       document.getElementById("frm1").submit();
     </script>
   </body>
 </html>
