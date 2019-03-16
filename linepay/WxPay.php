<?php
/**
 * Created by PhpStorm.
 * User: lineage
 * Date: 2018-12-25
 * Time: 17:33
 */

namespace linepay;

use linepay\wxpay\WxPayApi;
use linepay\wxpay\WxPayConfig;
use linepay\wxpay\WxPayOrderQuery;
use linepay\wxpay\WxPayRefund;
use linepay\wxpay\WxPayRefundQuery;
use linepay\wxpay\WxPayUnifiedOrder;
use think\Exception;
use think\facade\Log;

class WxPay
{

    /**
     * 微信支付 JsApi
     * @param string $openId                    用户openid 可空
     * @param string $Body                      商品简单描述
     * @param string $attach                    附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用
     * @param string $Out_trade_no              商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一
     * @param string $Total_fee                 支付金额 分
     * @param string $Goods_tag                 订单优惠标记，使用代金券或立减优惠功能时需要的参数
     * @param string $Notify_url                异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
     * @param string $Trade_type                JSAPI -JSAPI支付 NATIVE -Native支付 APP -APP支付
     * @param string $Product_id                trade_type=NATIVE时，此参数必传。此参数为二维码中包含的商品ID，商户自行定义。
     * @return array|bool                       返回数据
     */
    public static function WxPayJsApi($openId,$Body,$attach,$Out_trade_no,$Total_fee,$Goods_tag,$Notify_url,$Trade_type,$Product_id=''){
        try{
            $input = new WxPayUnifiedOrder();
            $input->SetBody(mb_substr($Body,0,30,'utf-8'));// 商品简单描述
            $input->SetAttach($attach);//附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。
            $input->SetOut_trade_no($Out_trade_no);//商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一
            $input->SetTotal_fee($Total_fee);//支付金额 分
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag($Goods_tag);//订单优惠标记，使用代金券或立减优惠功能时需要的参数
            $input->SetNotify_url($Notify_url);//异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
            $input->SetTrade_type($Trade_type);//JSAPI -JSAPI支付 NATIVE -Native支付 APP -APP支付
            $input->SetOpenid($openId);
            $input->SetProduct_id($Product_id);//扫码支付时的商品ID
            $config = new WxPayConfig();
            $order = WxPayApi::unifiedOrder($config, $input);
            //通讯成功
            if($order['return_code'] === 'SUCCESS'){
                //业务结果
                if($order['result_code'] === 'SUCCESS'){
                    return $order;
                }
            }
            //通讯失败 -- 记录错误
            Log::ERROR($order);
        } catch(Exception $e) {
            Log::ERROR(json($e));
        }
        return false;
    }

    /**
     * 订单查询
     * 微信订单号和商户订单号选少填一个，微信订单号优先
     * @param string $transaction_id                    微信订单号 可空
     * @param string $out_trade_no                      商户订单号 可空
     * @return array|bool
     */
    public static function OrderQuery($transaction_id = '',$out_trade_no = ''){
        if(!empty($transaction_id)) {
            try {
                $input = new WxPayOrderQuery();
                $input->SetTransaction_id($transaction_id);
                $config = new WxPayConfig();
                return WxPayApi::orderQuery($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }

        if(!empty($out_trade_no)){
            try{
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($out_trade_no);
                $config = new WxPayConfig();
                return WxPayApi::orderQuery($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }

        return false;
    }

    /**
     * 退款
     * 微信订单号和商户订单号选少填一个，微信订单号优先
     * @param string $transaction_id                            微信订单号 可空
     * @param string $out_trade_no                              商户订单号 可空
     * @param string $total_fee                                 退款总金额 分
     * @param string $refund_fee                                退款金额 分
     * @return array|bool
     */
    public static function ReFund($transaction_id = '',$out_trade_no = '',$total_fee='',$refund_fee = ''){
        if(!empty($transaction_id)){
            try{
                $input = new WxPayRefund();
                $input->SetTransaction_id($transaction_id);
                $input->SetTotal_fee($total_fee);
                $input->SetRefund_fee($refund_fee);
                $config = new WxPayConfig();
                $input->SetOut_refund_no(date("YmdHis").$total_fee);
                $input->SetOp_user_id($config->GetMerchantId());
                return WxPayApi::refund($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }

        if(!empty($out_trade_no)){
            try{
                $input = new WxPayRefund();
                $input->SetOut_trade_no($out_trade_no);
                $input->SetTotal_fee($total_fee);
                $input->SetRefund_fee($refund_fee);
                $config = new WxPayConfig();
                $input->SetOut_refund_no(date("YmdHis").$total_fee);
                $input->SetOp_user_id($config->GetMerchantId());
                return WxPayApi::refund($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }
        return false;
    }

    /**
     * 查退款单
     * 微信订单号、商户订单号、微信订单号、微信退款单号选填至少一个，微信退款单号优先
     * @param string $transaction_id                            微信订单号
     * @param string $out_trade_no                              商户订单号
     * @param string $out_refund_no                             商户退款单号
     * @param string $refund_id                                 微信退款单号
     * @return array|bool
     */
    public static function ReFundQuery($transaction_id = '',$out_trade_no = '',$out_refund_no='',$refund_id=''){
        if(!empty($transaction_id)){
            try{
                $input = new WxPayRefundQuery();
                $input->SetTransaction_id($transaction_id);
                $config = new WxPayConfig();
                return WxPayApi::refundQuery($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }
        if(!empty($out_trade_no)){
            try{
                $input = new WxPayRefundQuery();
                $input->SetOut_trade_no($out_trade_no);
                $config = new WxPayConfig();
                return WxPayApi::refundQuery($config,$input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }
        if(!empty($out_refund_no)){
            try{
                $input = new WxPayRefundQuery();
                $input->SetOut_refund_no($out_refund_no);
                $config = new WxPayConfig();
                return WxPayApi::refundQuery($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }
        if(!empty($refund_id)){
            try{
                $input = new WxPayRefundQuery();
                $input->SetRefund_id($refund_id);
                $config = new WxPayConfig();
                return WxPayApi::refundQuery($config, $input);
            } catch(Exception $e) {
                Log::ERROR(json($e));
            }
        }
        return false;
    }


}