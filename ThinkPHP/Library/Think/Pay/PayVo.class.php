<?php

/**
 * 订单数据模型
 */

namespace Think\Pay;

class PayVo {

    protected $_orderNo;
    protected $_fee;
    protected $_title;
    protected $_body;
    protected $_callback;
    protected $_url;
    protected $_param;
    protected $_defaultbank;

    /**
     * 设置订单号
     * @param type $order_no
     * @return \Think\Pay\PayVo
     */
    public function setOrderNo($order_no) {
        $this->_orderNo = $order_no;
        return $this;
    }

    /**
     * 设置商品价格
     * @param type $fee
     * @return \Think\Pay\PayVo
     */
    public function setFee($fee) {
        $this->_fee = $fee;
        return $this;
    }

    /**
     * 设置商品名称
     * @param type $title
     * @return \Think\Pay\PayVo
     */
    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    /**
     * 设置商品描述
     * @param type $body
     * @return \Think\Pay\PayVo
     */
    public function setBody($body) {
        $this->_body = $body;
        return $this;
    }

    /**
     * 设置支付完成后的后续操作接口
     * @param type $callback
     * @return \Think\Pay\PayVo
     */
    public function setCallback($callback) {
        $this->_callback = $callback;
        return $this;
    }

    /**
     * 设置支付完成后的跳转地址
     * @param type $url
     * @return \Think\Pay\PayVo
     */
    public function setUrl($url) {
        $this->_url = $url;
        return $this;
    }

    /**
     * 设置订单的额外参数
     * @param type $param
     * @return \Think\Pay\PayVo
     */
    public function setParam($param) {
        $this->_param = $param;
        return $this;
    }
    
    /**
     * 设置汽车ID
     * @param type $carid
     * @return \Think\Pay\PayVo
     */
    public function setCarId($carid) {
    	$this->_carid = $carid;
    	return $this;
    }
    
    /**
     * 设置购车方式
     * @param type $BuyWay
     * @return \Think\Pay\PayVo
     */
    public function setBuyWay($BuyWay) {
    	$this->_buyway = $BuyWay;
    	return $this;
    }
    
    /**
     * 设置购车人姓名
     * @param type $BuyerName
     * @return \Think\Pay\PayVo
     */
    public function setBuyerName($BuyerName) {
    	$this->_buyername = $BuyerName;
    	return $this;
    }
    
    /**
     * 设置购车人手机
     * @param type $BuyerTel
     * @return \Think\Pay\PayVo
     */
    public function setBuyerTel($BuyerTel) {
    	$this->_buyertel = $BuyerTel;
    	return $this;
    }
    
    /**
     * 设置购车人IP
     * @param type $PayIp
     * @return \Think\Pay\PayVo
     */
    public function setPayIp($PayIp) {
    	$this->_payip = $PayIp;
    	return $this;
    }
    
    /**
     * 设置购车支付方式(支付宝,微信等)
     * @param type $PayWay
     * @return \Think\Pay\PayVo
     */
    public function setPayWay($PayWay) {
    	$this->_payway = $PayWay;
    	return $this;
    }
    
    /**
     * 设置提车方式
     * @param type $CarMode
     * @return \Think\Pay\PayVo
     */
    public function setCarMode($CarMode) {
    	$this->_carmode = $CarMode;
    	return $this;
    }
    /**
     * 购车时间
     * @param type $buyTime
     * @return \Think\Pay\PayVo
     */
    public function setbuyTime($buyTime) {
    	$this->_buytime = $buyTime;
    	return $this;
    }
    /**
     * 购车城市
     * @param type $buyAddr
     * @return \Think\Pay\PayVo
     */
    public function setbuyAddr($buyAddr) {
    	$this->_buyaddr = $buyAddr;
    	return $this;
    }

      /**
     * 设置支付银行网关
     * @param type $defaultbank
     * @return \Think\Pay\PayVo
     */
    public function setDefaultbank($defaultbank) {
        $this->_defaultbank = $defaultbank;
        return $this;
    }

    /**
     * 获取订单号
     * @return type
     */
    public function getOrderNo() {
        return $this->_orderNo;
    }

    /**
     * 获取商品价格
     * @return type
     */
    public function getFee() {
        return $this->_fee;
    }

    /**
     * 获取商品名称
     * @return type
     */
    public function getTitle() {
        return $this->_title;
    }

    /**
     * 获取支付完成后的后续操作接口
     * @return type
     */
    public function getCallback() {
        return $this->_callback;
    }

    /**
     * 获取支付完成后的跳转地址
     * @return type
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * 获取商品描述
     * @return type
     */
    public function getBody() {
        return $this->_body;
    }

    /**
     * 获取订单的额外参数
     * @return type
     */
    public function getParam() {
        return $this->_param;
    }
    
    /**
     * 获得汽车ID
	 * @return type
     */
    public function getCarId() {
    	return $this->_carid;
    }
    
    /**
     * 获得购车方式
	 * @return type
     */
    public function getBuyWay() {
    	return $this->_buyway;
    }
    
   /**
     * 获得购车人姓名
	 * @return type
     */
    public function getBuyerName() {
    	return $this->_buyername;
    }
    
  	/**
     * 获得购车人手机
	 * @return type
     */
    public function getBuyerTel() {
    	return $this->_buyertel;
    }
    
	/**
     * 获得支付IP
	 * @return type
     */
    public function getPayIp() {
    	return $this->_payip;
    }
    
	/**
     * 获得支付方式
	 * @return type
     */
    public function getPayWay() {
    	return $this->_payway;
    }
    
	/**
     * 获得购车方式
	 * @return type
     */
    public function getCarMode() {
    	return $this->_carmode;
    }
    
    /**
     * 获得购车时间
	 * @return type
     */
    public function getbuyTime() {
    	return $this->_buytime;
    }
    /**
     * 获得购车城市
	 * @return type
     */
    public function getbuyAddr() {
    	return $this->_buyaddr;
    }

         /**
     * 获取支付银行网关
     * @return type
     */
    public function getDefaultbank() {
        return $this->_defaultbank;
    }


}
