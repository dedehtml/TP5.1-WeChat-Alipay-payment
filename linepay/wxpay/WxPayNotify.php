<?php
/**
 * Created by PhpStorm.
 * User: lineage
 * Date: 2018-12-25
 * Time: 16:44
 */

namespace linepay\wxpay;


class WxPayNotify extends WxPayNotifyReply
{
    private $config = null;

    /**
     * 回调入口
     * @param $config
     * @param bool $needSign 是否需要签名返回
     * @throws WxPayException
     */
    final public function Handle($config, $needSign = true)
    {
        $this->config = $config;
        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
        $result = WxpayApi::notify($config, array($this, 'NotifyCallBack'), $msg);
        if($result == false){
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        } else {
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        }
        $this->ReplyNotify($needSign);
    }

    /**
     * 回调方法入口，子类可重写该方法
     * 注意：
     * @param WxPayNotifyResults $objData 回调解释出的参数
     * @param WxPayConfigInterface $config
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return bool true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        //TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        return false;
    }

    /**
     *
     * 业务可以继承该方法，打印XML方便定位.
     * @param string $xmlData 返回的xml参数
     *
     **/
    public function LogAfterProcess($xmlData)
    {
        return;
    }

    /**
     *
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     * @param array $data
     * @return bool true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    final public function NotifyCallBack($data)
    {
        $msg = "OK";
        $result = $this->NotifyProcess($data, $this->config, $msg);

        if($result == true){
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } else {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
        }
        return $result;
    }

    /**
     * 回复通知
     * @param bool $needSign 是否需要签名输出
     * @throws WxPayException
     */
    final private function ReplyNotify($needSign = true)
    {
        //如果需要签名
        if($needSign == true &&
            $this->GetReturn_code() == "SUCCESS")
        {
            $this->SetSign($this->config);
        }

        $xml = $this->ToXml();
        $this->LogAfterProcess($xml);
        WxpayApi::replyNotify($xml);
    }
}