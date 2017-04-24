码上赢支付SDK
==============================

## 安装

使用 [composer](http://getcomposer.org/):

```shell
$ composer require winwin/pay-sdk
```

## 配置

调用接口需要先创建 payment 对象：

```php
use winwin\pay\sdk\Config;
use winwin\pay\sdk\payment\Payment;

$payment = new Payment(new Config([
    'appid' => $appid,
    'secret' => $secret,
]));
```

appid 和 secret 需要申请获取。

## 创建订单

```php
use winwin\pay\sdk\payment\Order;

$result = $payment->prepare(new Order([
    'mch_id' => $merchant_id,
    'method' => 'pay.weixin.jsapi',
    'body' => '支付1分',
    'total_fee' => 1,
    'out_trade_no' => date('ymdHis') . mt_rand(1000, 9999),
    'notify_url' => 'http://example.org/notify',
    'openid' => $openid,
]));
```
mch_id 需要申请获取。

openid 需要网页授权获取，参考[微信官方文档](https://mp.weixin.qq.com/wiki?action=doc&id=mp1421140842&t=0.04999664287487349#1)。

## 处理支付异步通知

```php
$response = $payment->handleNotify(function($notify, $successful) {
    // 处理逻辑
    return true;
});
echo $response->getBody();
```

`handleNotify` 接收一个回调函数，该回调函数接收两个参数，这两个参数分别为：
- `$notify` 为封装了通知信息的数组对象，可以使用对象或者数组形式来读取通知内容，比如：`$notify->total_fee` 或者 `$notify['total_fee']`。
- `$successful` 用于判断用户是否付款成功了

回调函数返回 false 或者一个具体的错误消息，那么系统会在稍后再次继续通知你，直到你明确的告诉它：“我已经处理完成了”，在函数里 `return true;` 代表处理完成。

`handleNotify` 返回值 `$response` 是一个 [PSR-7](http://www.php-fig.org/psr/psr-7/) Response 对象。

## 调试

如果需要打印 http 请求日志，可使用 [PSR-3](http://www.php-fig.org/psr/psr-3/) 实现库，例如 [Monolog](https://github.com/Seldaek/monolog) ：

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$logger = new Logger('WinwinPay');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

$payment->setLogger($logger);
```
