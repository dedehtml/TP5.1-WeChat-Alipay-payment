<?php
/**
 * Created by PhpStorm.
 * User: line
 * Date: 2019-03-13
 * Time: 16:05
 */
namespace linepay;

// 这里的配置参数要读取数据库等自己改。 ---- 返回方式是数组就好了。


class PayConfig
{
    public static function AliPay(){
        return [
            //应用ID
            'Appid'                     => '2016112503296230',
            //同步回调地址
            'ReturnUrl'                 => 'http://lineageja.com',
            //异步回调地址
            'NotifyUrl'                 => 'http://lineageja.com',
            //商户私钥 === 这是生成的私钥
            'RsaPrivateKey'             => 'MIIEpAIBAAKCAQEAt7LVot6mL8vNg/kkS2dHpZmCztG+0nFZf3uovlC3Y4+fbOlDwsqkqmAfFLKck4xNRpZn2Z4V2alSnUA2JEceipo+sJyvnscCifBjcvaNN3t90EkOcEg24GXcEtSeSfZJX+WKmAh5735tMfjHAW7czk7JI3p5YjgCngnE+Bn80i3DfW7zps1Rin8bW9c40o7aSqcAEEknIS2foZQBNEJfXJQHYvToHOmN0BoFpa890lhGNkgbj1cbf2OZnb412+XewUIwCK7+42pYpwdC5KKexewJkYSYpXTgHf+dnpGPp4D3IwuB0c7ORjD57FHW7n2/fsfDD0WyZwkGoKcYN0fkVwIDAQABAoIBAQCQ3yvWz6rWhO8mhoTWJrR9aCyeORI52wTPIlH5DaUjkrATb39uDuyAJWA9rYMIZhzHb3SlTiRDTWMG+w333FK80lpgZKGoIaDh8kAr+T9zoyOc8RC/AmSs4ggGncHb6K0DQP7lWcH4W4d4Yo9nlOv0lHPSVBOIn02JH4FEfRJDGla2WL5Nu0X/TBhq3073TAXwQj6TsbMezBlr6sz4IZJFN0Fky3lAg7CGCUwjl265dEwie6+PsX0qOF82NHWVPipiqsYuT/ulV2gn5VY2CB0fKbBwosSKKg2cSvS++bcH9CcisD0ttIIiCmPIVFGzlqeHvVdcVD40u61WuMNtfRtJAoGBAO6bgKESfcxh4KAA7ZOmu4DkJciVRrAIDDr/WgIn+Nta7x9Gm63d+PADAzsEivWuevi54BEXURiH1pSDCKvN6z/exOyhpPgxUiIbpzzeBQ1jPHptjI0JQad0gOi98d/FVgwSHZARucXfs3pHjMJ0clpHEvS5xairQKajwOqKkfqFAoGBAMUWtrOCP7OL4NwI051ctSToAI0k9BPTeB7W+cuFfIP6Tlk74WmJtj/F8AruUBcZpq6gewfhpxObGA+JoUwNrFL9TzhfHk3lBZtZGVAVHdPCpxTY6fKxCOX7yZ8olHGKabpRlcuF0M9UBwql9olONoLozKPE0pafwPmFgnKH+JArAoGBAKuUiNfK60lIbPXCFRVjBVx3MzBdH9q/vsPCEeCRC+P5LRDJfnN1tmgpUm5jIuXulSE80IKZyMgiDkz3OQoKZoukTul2h6GuHcjHJ+ieIvXwkXHcSwC0UlDvdy9mP4NRbUH62OVbycIfzfk1cGP4xq5ig6AV4qwakcAC61v9T2HdAoGAHuTVlMPo+lsFz1fu2+7pUjSvyXAudmEipirqkxWElCeLWn+BcQquL8b2PrBmi4hw0VTElFKx3ufj2KOpTgOirIuafGqklG8+9r/7sMrulwAfMqxh17Iag/p4+2LgCgA5XIRnZ7S9K5KvW+LyB0nkT5bwZQd4PzYhiLt93DgmJWsCgYApXXfXSXYKzt1+eLI6IKiXAJ7m+mabDhklZWTcbKdX1Ab1mof8QOL1OVm7r8zN1MrnWnyfCCxAeZNTMTo9hSWHPdspoJ6vBxV1AaxrvSEtSRihc03qcmFUS3xCwEYsyfFSj6+w2wTOERwOj3sPKnFYG17Ez53qDeWRwedVXIdxJw==',
            //支付宝公钥 === 这是上传后的 旁边有个支付宝公钥
            'alipayPublicKey'           => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAp0gT1P359Fl+qPPfWbMNILCXL0vxP3gNzUZD5X7b3sOqbJUXfoJ9P4NcX5G01IpJ5tZ2VuNscnidyLxuKl+2ZZrxO7PYqNLIHY7fEpK6j2B2c2L9hK68Nq/lCbWsToOvVFQs34QpLw/1s43p4ZtOETrn9lHfJvADPVKQDk5OOQ/dHRyUQqR/vX+x7QaHat/uKQvs6/8T5a6Wcl/KtfiAOeNMHrMUjA2OiqmeUgSUPfL4Xq7VLNZot8wYTpih9SNJyf1JDhM8HUjVA642itbOJK0knq15yRsmD7jgf02oJwLI5X0cirs5hSV//OLOeVPb7wY/faqXAU0FnZjvYZgEhwIDAQAB',
        ];
    }

    public static function WxPay(){
        return [
            //绑定支付的APPID（必须配置，开户邮件中可查看）
            'AppId'                     => 'wx42a1c32b8b7b2cd5',
            //商户号（必须配置，开户邮件中可查看）
            'MchID'                     => '1511682171',
            //支付回调url
            'NotifyUrl'                 => '',
            //签名和验证签名方式， 支持 MD5 和 HMAC-SHA256 方式
            'SignType'                  => 'MD5',
            //上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
            'ReportLevenl'              => '1',
            //KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
            'Key'                       => '8aeTmGGtnhJTykFWXKnWgKzbHcH3luo6',
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
}