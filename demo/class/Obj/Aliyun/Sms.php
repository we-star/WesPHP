<?php
/**
 * 短信
 */

require_once PATH_INCLUDES . '/aliyun/sms/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class Obj_Aliyun_Sms extends Obj_Aliyun {
	private $_signName = "花狸";

	private $_acsClient = "";

	public function __construct() {
		parent::__construct();
		// 短信API产品名
		$product = "Dysmsapi";

		// 短信API产品域名
		$domain = "dysmsapi.aliyuncs.com";

		// 暂时不支持多Region
		$region = "cn-hangzhou";

		// 服务结点
		$endPointName = "cn-hangzhou";

		// 初始化用户Profile实例
		$profile = DefaultProfile::getProfile($region, $this->_accessKeyId, $this->_accessKeySecret);

		// 增加服务结点
		DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

		// 初始化AcsClient用于发起请求
		$this->_acsClient = new DefaultAcsClient($profile);
	}

	public function send($mobile, $tplCode, $tplData, $outId = null) {
		// 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称
        $request->setSignName($this->_signName);

        // 必填，设置模板CODE
        $request->setTemplateCode($tplCode);

        // 可选，设置模板参数
        if($tplData) {
            $request->setTemplateParam(json_encode($tplData));
        }

        // 可选，设置流水号
        if($outId) {
            $request->setOutId($outId);
        }

        // 发起访问请求
        $acsResponse = $this->_acsClient->getAcsResponse($request);

        return $acsResponse;
	}
}