<?php
namespace Home\Controller;

use Think\Crypt\Driver\Base64;
use Think\Controller;
use ThinkOauth;

/**
 * Class PayController 支付控制器
 * @package Home\Controller
 */
class PayController extends CommonController
{

    /**
     *生成支付宝需要的订单信息并发送到支付宝
     */
    public function index()
    {
        if (IS_POST) {
            if (!isset($_SESSION['user_id'])) {
                $this->error('您还没有登录,请先登录', U('Public/login'));
            }
            //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
            if (I('post.pay_number') == 1) {
                $paytype = I('post.defaultbank');
            } else {
                $paytype = 'alibankpay';
                $defaultbank = I('post.defaultbank');
            }
            $pay = new \Think\Pay($paytype, C('payment.' . $paytype));
            $vo = M('pay')->where(array('id' => I('pay_id')))->find();
            if ($vo['pay_obj'] == 1) {
                $vo['title'] = "咨询底价支付";
            } elseif ($vo['pay_obj'] == 2) {
                $vo['title'] = "订金支付";
            }elseif($vo['pay_obj']==4)
            {
                $vo['title'] = "一元抢";
            }

            $vo['defaultbank'] = $defaultbank;
            $info = $pay->buildRequestForm($vo);
            if ($paytype == 'weixin') {
                if ($info['return_code'] == 'FAIL' && $info['result_code'] == 'SUCCESS') {
                    $this->error($info['return_msg'] . '，请选择另一种支付方式或重新生成订单！', U('Order/order_pay', array('pay_id' => $vo['id'])));

                } else {
                    if ($info['result_code'] == 'FAIL') {
                        $this->error($info['err_code_des'] . '，请选择另一种支付方式或重新生成订单！', U('Order/order_pay', array('pay_id' => $vo['id'])));
                    } else {
                        $this->redirect('weixinpay', array('url' => urlencode($info['code_url']), 'out_trade_no' => $vo['out_trade_no']));
                    }

                }

            } else {
                echo $info;
            }

        }
        $this->display();
    }

    /**
     *微信二维码生成
     * 查询订单状态
     */
    public function weixinpay()
    {
        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $qrcode_path = '';
        $file_tmp_name = '';
        $errors = array();
        $url = I('url');
        $content = urldecode($url);
        $tpgs = 'jpg';//图片格式
        $qrcode_bas_path = 'Images/qrcode/';
        if (!is_dir($qrcode_bas_path)) {
            mkdir($qrcode_bas_path, 0777, true);
        }
        $uniqid_rand = date("Ymdhis") . uniqid() . rand(1, 1000);
        $qrcode_path_new = $qrcode_bas_path . $uniqid_rand . "_2." . $tpgs;//二维码图片路径
        $errorCorrectionLevel = 'H';//容错级别
        $matrixPointSize = 7;//生成图片大小
        $matrixMarginSize = 3;//边距大小
        //生成二维码图片
        $object::png($content, $qrcode_path_new, $errorCorrectionLevel, $matrixPointSize, $matrixMarginSize);
        $data = '/' . $qrcode_path_new;
        $this->assign('data', $data);
        $out_trade_no = I('out_trade_no');
        $pay_info = M('pay')->where(array('out_trade_no' => $out_trade_no))->find();
        $this->assign('pay_info', $pay_info);
        $this->display();

    }

    /**
        * @Description:微信支付每隔5s请求订单状态
        * @Return:
        * @Author: 潘玉婷 @panyuting
        * @Date: 2016/9/20 10:23
        * @Version 2.0
        */
    public function orderstatus()
    {
        $out_trade_no=I('out_trade_no');
        $pay_info = M('pay')->where(array('out_trade_no' => $out_trade_no))->find();
        $info = $this->orderQuery($out_trade_no);
        if ($info['trade_state'] == 'SUCCESS') {
            $data['status']=1;
            $data['url']=$pay_info['url'];
        }else
        {
            $data['status']=2;
        }
        $this->ajaxReturn($data);
    }

    /**
     * @Description:微信支付回调地址
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/19 14:04
     * @Version 2.0
     */
    public function wxnotify()
    {
        $xml = file_get_contents("php://input"); //接收POST数据
        $arr = $this->FromXml($xml);
        $pay_info = M('pay')->where(array('out_trade_no' => $arr['out_trade_no']))->find();
        if ($arr['return_code'] == 'SUCCESS' && $arr['result_code'] == 'SUCCESS' && $pay_info['status']==0) {
            //更改数据库
            $z = M("pay")->where(array('out_trade_no' => $arr['out_trade_no']))->setField(array('updateuser' => session('user_id'), 'updatetime' => time(), 'status' => 1, 'pay_way' => '微信支付'));
            if ($z) {
                //订单支付成功后给客服发送邮箱提醒
                $user_name = M('user')->where(array('id' => $_SESSION['user_id']))->getField('user_name');//用户名
                $subject = "用户订单信息";//邮件主题
                $content = $user_name . " ：于" . date('Y-m-d H:i', $pay_info['updatetime']) . '成功支付订单请及时与其联系';//邮件内容
                sendMail(KEFU_EMAIL, $subject, $content);


                //成功询价后给一张优惠券
                if ($pay_info['pay_obj'] == 1) {
                    $db = M('');
                    $db->execute("call ticket_user(1,'" . $pay_info['user_id']. "','车款询价',12)");
                }

                if ($pay_info['pay_obj'] == 2) {
                    //下完订单可售汽车的总数量减一
                    if (isset($pay_info['car_special_id']) and $pay_info['car_special_id'] != '') {
                        M('special_price_car')->where(array('id' => $pay_info['car_special_id']))->setDec('number');
                    }
                    //订车支付成功删除ticket_user表中的优惠券
                    if (!empty($pay_info['ticket_number'])) {
                        $dataticket['status'] = 2;
                        $dataticket['pay_id'] = $pay_info['id'];
                        $dataticket['ramark'] = $pay_info['out_trade_no'] . '订单使用了该优惠券';
                        $dataticket['updatetime'] = date('Y-m-d H:i:s', time());
                        $dataticket['updateuser'] = session('user_id');
                        M('ticket_user')->where(array('ticket_number' => $pay_info['ticket_number']))->save($dataticket);
                    }
                }

                $return['return_code'] = 'SUCCESS';
                $return['return_msg'] = 'OK';

            }
        }

//        $ret=$this->ToXml($return);
//        $info=self::postXmlCurl($ret,'https://api.mch.weixin.qq.com/pay/unifiedorder');


    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return bool|\mix|mixed|string
     */
    public function FromXml($xml)
    {
        if (!$xml) {
            E("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($arr)
    {
        if(!is_array($arr) || count($arr) <= 0)
        {
            E("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public function orderQuery($out_trade_no, $timeOut = 30)
    {
        $weixin = new \Think\Pay\Driver\Weixin();
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        $param = array(
            'appid' => 'wxe8a079d4b6a764cc',
            'mch_id' => '1269870801',
            'nonce_str' => rand(),
            'out_trade_no' => $out_trade_no,
        );
        $param['sign'] = $weixin->MakeSign($param);
        $xml = $weixin->ToXml($param);
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $array = $weixin->FromXml($response);
        return $array;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @return mixed
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if (C('CURL_PROXY_HOST') != "0.0.0.0"
            && C('CURL_PROXY_PORT') != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, C('CURL_PROXY_HOST'));
            curl_setopt($ch, CURLOPT_PROXYPORT, C('CURL_PROXY_PORT'));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, C('SSLCERT_PATH'));
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, C('SSLKEY_PATH'));
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            E("curl出错，错误码:$error");
        }
    }

    /**
     * 订单支付成功
     * @param type $money
     * @param type $param
     */
    public function payback($money, $param)
    {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
            //处理goods1业务订单、改名good1业务订单状态
        } else {
            E("Access Denied");
        }
    }

    /**
     * @Description:支付结果返回（支付宝）
     * @Return:
     * @Author: 潘玉婷 @panyuting
     * @Date: 2016/9/19 11:21
     * @Version 2.0
     */
    public function notify()
    {
        $apitype = I('apitype');
        $pay = new \Think\Pay($apitype, C('payment.' . $apitype));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();
            if ($info['status']) {
                $payinfo = M("pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 0) {
                    session("pay_verify", true);
                    //支付成功后修改我们的数据表
                    $z = M("pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('updateuser' => session('user_id'), 'updatetime' => time(), 'status' => 1, 'pay_way' => $apitype));
                    if ($z) {
                        //订单支付成功后给客服发送邮箱提醒
                        $user_name = M('user_general')->where(array('id' => $_SESSION['user_id']))->getField('user_name');//用户名
                        $subject = "用户订单信息";//邮件主题
                        $content = $user_name . " ：于" . date('Y-m-d H:i', $payinfo['updatetime']) . '成功支付订单请及时与其联系';//邮件内容
                        sendMail(KEFU_EMAIL, $subject, $content);

                        //成功询价后给一张优惠券
                        if ($payinfo['pay_obj'] == 1) {
                            $db = M('');
                            $db->execute("call ticket_user(1,'" . session('user_id') . "','车款询价',12)");

                        }

                        if ($payinfo['pay_obj'] == 2) {
                            //下完订单可售汽车的总数量减一
                            if (isset($payinfo['car_special_id']) and $payinfo['car_special_id'] != '') {
                                M('special_price_car')->where(array('id' => $payinfo['car_special_id']))->setDec('number');
                            }
                            //订车支付成功删除ticket_user表中的优惠券
                            if (!empty($payinfo['ticket_number'])) {
                                $dataticket['status'] = 2;
                                $dataticket['pay_id'] = $payinfo['id'];
                                $dataticket['ramark'] = $payinfo['out_trade_no'] . '订单使用了该优惠券';
                                $dataticket['updatetime'] = date('Y-m-d H:i:s', time());
                                $dataticket['updateuser'] = session('user_id');
                                M('ticket_user')->where(array('ticket_number' => $payinfo['ticket_number']))->save($dataticket);
                            }

                        }
                    }
                    $this->success('支付成功', $payinfo['url']);
                }

                /*if (I('get.method') == "return") {
                    redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                    redirect($payinfo['url']);
                }*/
            } else {
                $this->error("支付失败！");
            }
        } else {
            $this->error("验证失效!", U('Index/index'));
        }
    }

    /**
     *回调函数
     */
    public function callback()
    {
        $data = file_get_contents("php://input");
        var_dump($data);
        var_dump($_REQUEST);
        exit();
    }
}