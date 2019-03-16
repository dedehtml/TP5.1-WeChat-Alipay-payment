linepay for Tp5.1
----
* 运行最底要求 TP5

功能描述
----
* 微信支付（退款、App支付、JSAPI支付、扫码支付等）
* 支付宝支付（账单、转账、App支付、扫码支付、Web支付、Wap支付等）

技术帮助
----

linepay 是基于官方接口封装，在做微信开发前，必需先阅读微信官方文档。
* 微信支付文档：https://pay.weixin.qq.com/wiki/doc/api/index.html
* 支付宝支付文档：https://docs.open.alipay.com/270

代码仓库
----
linepay 为开源项目，允许把它用于任何地方，不受任何约束，欢迎 fork 项目。
* Gitee 托管地址：https://github.com/ShmmGood/TP5.1-WeChat-Alipay-payment
* GitHub 托管地址：https://github.com/ShmmGood/TP5.1-WeChat-Alipay-payment

安装使用
----

1. 下载 linepay 并解压到项目 extend 文件夹中

2. 接口实例所需参数

    你的项目/extend/linepay/PayConfig.php
```php

    /**
     * 支付宝配置
     * @return array
     */
    public static function AliPay(){
        return [
            //应用ID
            'Appid'                     => '2016112503296230',
            //同步回调地址
            'ReturnUrl'                 => 'http://lineageja.com',
            //异步回调地址
            'NotifyUrl'                 => 'http://lineageja.com',
            //商户私钥 === 这是生成的私钥
            'RsaPrivateKey'             => '',
            //支付宝公钥 === 这是上传后的 旁边有个支付宝公钥
            'alipayPublicKey'           => '',
        ];
    }
    
    /**
     * 微信配置
     * @return array
     */
    public static function WxPay(){
        return [
            //绑定支付的APPID（必须配置，开户邮件中可查看）
            'AppId'                     => '',
            //商户号（必须配置，开户邮件中可查看）
            'MchID'                     => '',
            //支付回调url
            'NotifyUrl'                 => '',
            //签名和验证签名方式， 支持 MD5 和 HMAC-SHA256 方式
            'SignType'                  => 'MD5',
            //上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
            'ReportLevenl'              => '1',
            //KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
            'Key'                       => '',
            //APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
            'AppSecret'                 => '',
            //证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载
            'sslCertPath'               => '',
            //API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
            'sslKeyPath'                => '',
            //证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
            //建议将证书文件名改为复杂且不容易猜测的文件名；
            //商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
        ];
    }

```

3.1 实例指定接口

```php
  use linepay\AliPay; //支付宝支付   按需加载
  use linepay\WxPay;  //微信支付     按需加载
  use think\facade\Request; //获取请求的数据 按需使用
```

支付宝支付
---
```php
/**
 * 发起电脑端支付
 * $payAmount 支付金额单位: 元
 * $outTradeNo 商品订单号
 * $orderName 支付标题
 * AliPay::ComputerPay($payAmount,$outTradeNo,$orderName);
 *
 */
$alipay = AliPay::ComputerPay('0.01','20180301004','测试支付');
if($alipay[0] == true) {
    return $alipay[1];
}else{
    var_dump($alipay[1]);
    return '发起支付请求失败';
}

/**
 * 发起手机网站支付
 * $payAmount 支付金额单位: 元
 * $outTradeNo 商品订单号
 * $orderName 支付标题
 * AliPay::ComputerPay($payAmount,$outTradeNo,$orderName);
 *
 */
$alipay = AliPay::MobilePay('0.01','20180301004','测试支付');
if($alipay[0] == true) {
    return $alipay[1];
}else{
    var_dump($alipay[1]);
    return '发起支付请求失败';
}

/**
 * 查询转账订单
 * 商户转账唯一订单号（商户转账唯一订单号、支付宝转账单据号 至少填一个）
 * $outBizBo 商户转账唯一订单号
 * $orderId 支付宝转账单据号
 * AliPay::TransferQueryOrder($outBizBo,$orderId);
 */
$QueryOrder = AliPay::TransferQueryOrder('20180301004','');
if($QueryOrder[0] == true) {
    var_dump($QueryOrder[1]);
}else{
    var_dump($QueryOrder[1]);
    return '查询转账订单失败';
}

/**
 * 单笔转账到支付宝账户
 * $payAmount 单笔转账到支付宝金额（元）
 * $outTradeNo 商户转账唯一订单号
 * $account 收款方账户（支付宝登录号，支持邮箱和手机号格式。）
 * $realName 收款方真实姓名
 * $remark 转帐备注
 * AliPay::TransferPay($payAmount,$outTradeNo,$account,$realName,$remark);
 */
$TransferPay = AliPay::TransferPay('0.01','20180301005','670751110@qq.com','','测试转账');
if($TransferPay[0] == true) {
    var_dump($TransferPay[1]);
}else{
    var_dump($TransferPay[1]);
    return '单笔转账到支付宝账户失败';
}

/**
 * 同步回调数据
 * AliPay::AliPayReturn($params)
 */
$Return = AliPay::AliPayReturn(Request::post());
if($Return == true) {
    //同步回调一般不处理业务逻辑，显示一个付款成功的页面，或者跳转到用户的财务记录页面即可。
}else{
    return '验证失败';
}

/**
 * 异步回调数据
 * AliPay::AliPayNotify($params)
 */
$Notify = AliPay::AliPayNotify(Request::post());
if($Notify == true) {
    //处理你的逻辑，例如获取订单号 Request::post('out_trade_no') ，订单金额 Request::post('total_amount') 等
    //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，
    //直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）
    echo 'success';exit();
}else{
    echo 'error';exit();
}

/**
 * 发起退款
 * $tradeNo 在支付宝系统中的交易流水号。最短 16 位，最长 64 位。和out_trade_no不能同时为空
 * $outTradeNo 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
 * $refundAmount 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
 * AliPay::ReFund($tradeNo,$outTradeNo,$refundAmount)
 */
$ReFund = AliPay::ReFund('','20180301004','0.01');
if($ReFund[0] == true) {
    var_dump($ReFund[1]);
}else{
    var_dump($ReFund[1]);
    return '发起退款失败';
}

/**
 * 查询订单状态
 * $outTradeNo 要查询的商户订单号。注：商户订单号与支付宝交易号不能同时为空
 * $tradeNo 要查询的支付宝交易号。注：商户订单号与支付宝交易号不能同时为空
 * AliPay::QueryStaticOrder($outTradeNo,$tradeNo)
 */
$res = AliPay::QueryStaticOrder('20180301004','');
if($res[0] == true) {
    var_dump($res[1]);
}else{
    var_dump($res[1]);
    return '查询订单状态失败';
}

/**
 * 当面付 （扫码支付）
 * @param string $payAmount                             支付金额 元
 * @param string $outTradeNo                            商户订单号
 * @param string $orderName                             支付信息
 * AliPay::QrCodePay($payAmount,$outTradeNo,$orderName)
 */
$res = AliPay::QrCodePay('0.01','20180301006','测试扫码支付');
if($res[0] == true) {
    var_dump($res[1]);
}else{
    var_dump($res[1]);
    return '当面付 （扫码支付）失败';
}
```
* 更多功能请阅读测试代码或SDK封装源码

微信支付
---
```php


```

