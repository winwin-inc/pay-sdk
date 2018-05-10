<?php 

namespace winwin\pay\sdk\constants;

class TradeMethod
{
    //公众号/扫码预支付
    const WEIXIN_JSAPI = 'trade.weixin.jsapi';          //微信JS支付预下单
    const WEIXIN_QR = 'trade.weixin.qr';                //微信扫码支付预下单
    const ALIPAY_JSAPI = 'trade.alipay.jsapi';          //支付宝JS支付预下单
    const ALIPAY_QR = 'trade.alipay.qr';                //支付宝扫码支付预下单
    const CLOSE = 'trade.close';                        //关闭预支付订单
    
    //刷卡支付
    const MICRO_PAY = 'trade.micropay';                 //刷卡支付
    const REVERSE = 'micropay.reverse';                 //取消订单
    
    const QUERY = 'trade.query';                        //支付订单查询
    const REFUND = 'trade.refund';                      //退款
    const REFUND_QUERY = 'trade.refund.query';          //退款查询

    const BILL_DOWNLOAD = 'bill.download';              //对账单下载
}