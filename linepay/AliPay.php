<?php
/**
 * Created by PhpStorm.
 * User: line@lineage.com
 * Date: 2018-12-26
 * Time: 16:17
 */
namespace linepay;

use linepay\alipay\AliPayApi;

class AliPay
{
    /**
     * 发起电脑网站支付
     * @param string $payAmount                                         支付金额单位: 元
     * @param string $outTradeNo                                        商品订单号
     * @param string $orderName                                         支付标题
     * @return array                                                   返回html需要导入网页
     */
    public static function ComputerPay($payAmount = '',$outTradeNo = '',$orderName = ''){
        return self::Pay($payAmount,$outTradeNo,$orderName,true);
    }

    /**
     * 发起手机网站支付
     * @param string $payAmount                                         支付金额单位: 元
     * @param string $outTradeNo                                        商品订单号
     * @param string $orderName                                         支付标题
     * @return array                                                    返回html需要导入网页
     */
    public static function MobilePay($payAmount = '',$outTradeNo = '',$orderName = ''){
        return self::Pay($payAmount,$outTradeNo,$orderName,false);
    }

    /**
     * @param string $payAmount
     * @param string $outTradeNo
     * @param string $orderName
     * @param bool $isPc
     * @return array
     */
    private static function Pay($payAmount = '',$outTradeNo = '',$orderName = '',$isPc = true){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);//应用ID
        $aliPay->setReturnUrl($config->AliPay()['ReturnUrl']);//同步回调地址
        $aliPay->setNotifyUrl($config->AliPay()['NotifyUrl']);//异步回调地址
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);//商户私钥
        $aliPay->setTotalFee($payAmount);//支付金额 单位 元
        $aliPay->setOutTradeNo($outTradeNo);//商品订单号
        $aliPay->setOrderName($orderName);//支付标题
        if ($isPc) $result = $aliPay->doPay_Pc(); else $result = $aliPay->doPay_wap();
        if($result){
            return [true,$result];
        }
        return [false,$result];
    }

    /**
     * 查询转账订单
     * 商户转账唯一订单号（商户转账唯一订单号、支付宝转账单据号 至少填一个）
     * @param string $outBizBo                                      商户转账唯一订单号
     * @param string $orderId                                       支付宝转账单据号
     * @return array
     */
    public static function TransferQueryOrder($outBizBo = '',$orderId = ''){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);//应用ID
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);//商户私钥
        $result = $aliPay->doQuery($outBizBo,$orderId);
        $result = $result['alipay_fund_trans_order_query_response'];
        if($result['code'] && $result['code']=='10000'){
            return [true,$result];
        }else{
            return [false,$result];
        }
    }

    /**
     * 单笔转账到支付宝账户
     * @param string $payAmount                                     单笔转账到支付宝账户
     * @param string $outTradeNo                                    商户转账唯一订单号
     * @param string $account                                       收款方账户（支付宝登录号，支持邮箱和手机号格式。）
     * @param string $realName                                      收款方真实姓名
     * @param string $remark                                        转帐备注
     * @return array
     */
    public static function TransferPay($payAmount,$outTradeNo,$account,$realName,$remark){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);//应用ID
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);//商户私钥
        $result = $aliPay->doPay_transfer($payAmount,$outTradeNo,$account,$realName,$remark);
        $result = $result['alipay_fund_trans_toaccount_transfer_response'];
        if($result['code'] && $result['code']=='10000'){
            return [true,$result];
        }else{
            return [false,$result];
        }
    }

    /**
     * 同步回调数据
     * @param $params
     * @return bool
     */
    public static function AliPayReturn($params){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAlipayPublicKey($config->AliPay()['alipayPublicKey']);//支付公钥
        $result = $aliPay->rsaCheck($params,$params['sign_type']);
        if($result === true){
            //同步回调一般不处理业务逻辑，显示一个付款成功的页面，或者跳转到用户的财务记录页面即可。
            return true;
        }
        return false;
    }

    /**
     * 异步回调数据
     * @param $params
     */
    public static function AliPayNotify($params){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAlipayPublicKey($config->AliPay()['alipayPublicKey']);//支付公钥
        $result = $aliPay->rsaCheck($params,$params['sign_type']);
        if($result === true){
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，
            //直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）
            echo 'success';exit();
        }
        echo 'error';exit();
    }

    /**
     * 发起退款
     * @param string $tradeNo                       在支付宝系统中的交易流水号。最短 16 位，最长 64 位。和out_trade_no不能同时为空
     * @param string $outTradeNo                    订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
     * @param float $refundAmount                   需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
     * @return array
     */
    public static function ReFund($tradeNo,$outTradeNo,$refundAmount){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);
        $aliPay->setTradeNo($tradeNo);//在支付宝系统中的交易流水号。最短 16 位，最长 64 位。和out_trade_no不能同时为空
        $aliPay->setOutTradeNo($outTradeNo);//订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
        $aliPay->setRefundAmount($refundAmount);//需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
        $result = $aliPay->doRefund();
        $result = $result['alipay_trade_refund_response'];
        if($result['code'] && $result['code']=='10000'){
            return [true,$result];
        }else{
            return [false,$result];
        }
    }

    /**
     * 查询订单状态
     * @param string $outTradeNo                        要查询的商户订单号。注：商户订单号与支付宝交易号不能同时为空
     * @param string $tradeNo                           要查询的支付宝交易号。注：商户订单号与支付宝交易号不能同时为空
     * @return array
     */
    public static function QueryStaticOrder($outTradeNo,$tradeNo){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);
        $aliPay->setOutTradeNo($outTradeNo);//要查询的商户订单号。注：商户订单号与支付宝交易号不能同时为空
        $aliPay->setTradeNo($tradeNo);//要查询的支付宝交易号。注：商户订单号与支付宝交易号不能同时为空
        $result = $aliPay->doQuery();
        if($result['alipay_trade_query_response']['code']!='10000'){
            return [false,$result];
        }else{
            switch($result['alipay_trade_query_response']['trade_status']){
                case 'WAIT_BUYER_PAY':
                    $result['alipay_trade_query_response']['trade_status_text']='交易创建，等待买家付款';
                    break;
                case 'TRADE_CLOSED':
                    $result['alipay_trade_query_response']['trade_status_text']='未付款交易超时关闭，或支付完成后全额退款';
                    break;
                case 'TRADE_SUCCESS':
                    $result['alipay_trade_query_response']['trade_status_text']='交易支付成功';
                    break;
                case 'TRADE_FINISHED':
                    $result['alipay_trade_query_response']['trade_status_text']='交易结束，不可退款';
                    break;
                default:
                    $result['alipay_trade_query_response']['trade_status_text']='未知状态';
                    break;
            }
            return [true,$result];
        }
    }

    /**
     * 当面付 （扫码支付）
     * @param string $payAmount                             支付金额 元
     * @param string $outTradeNo                            商户订单号
     * @param string $orderName                             支付信息
     * @return array
     */
    public static function QrCodePay($payAmount = '',$outTradeNo = '',$orderName = ''){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);
        $aliPay->setNotifyUrl($config->AliPay()['NotifyUrl']);
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);
        $aliPay->setTotalFee($payAmount);
        $aliPay->setOutTradeNo($outTradeNo);
        $aliPay->setOrderName($orderName);
        $result = $aliPay->QrCodePay();
        $result = $result['alipay_trade_precreate_response'];
        if($result['code'] && $result['code']=='10000'){
            return [true,$result];
        }else{
            return [false,$result];
        }
    }

    /**
     * 交易关闭接口
     * @param string $tradeNo 在支付宝系统中的交易流水号。最短 16 位，最长 64 位。和out_trade_no不能同时为空
     * @param string $outTradeNo 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
     * @return array
     */
    public static function DoCloseOrder($tradeNo,$outTradeNo){
        $aliPay = new AliPayApi();
        $config = new PayConfig();
        $aliPay->setAppid($config->AliPay()['Appid']);
        $aliPay->setRsaPrivateKey($config->AliPay()['RsaPrivateKey']);
        $aliPay->setTradeNo($tradeNo);
        $aliPay->setOutTradeNo($outTradeNo);
        $result = $aliPay->CloseOrder();
        $result = $result['alipay_trade_close_response'];
        if($result['code'] && $result['code']=='10000'){
            return [true,$result];
        }else{
            return [false,$result];
        }
    }


}