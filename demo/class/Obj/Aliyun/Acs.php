<?php
/**
 * ACS
 */
require_once PATH_INCLUDES . '/aliyun/acs/aliyun-php-sdk-core/Config.php';
use Green\Request\V20170112 as Green;

class Obj_Aliyun_Acs extends Obj_Aliyun {
	private $_acsClient = "";

	public function __construct() {
		$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $this->_accessKeyId, $this->_accessKeySecret); // TODO
		DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "Green", "green.cn-shanghai.aliyuncs.com");
		$this->_acsClient = new DefaultAcsClient($iClientProfile);
	}

	/**
	 * 文本检测接口
	 */
	public function textScan($tasks=array()) {
		$request = new Green\TextScanRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode(array("tasks" => $tasks,"scenes" => array("antispam"))));

		try {
			$response = $this->_acsClient->getAcsResponse($request);
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 图片同步检测接口
	 */
	public function imageSyncScan($tasks=array()) {
		$request = new Green\ImageSyncScanRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode(array("tasks" => $tasks,"scenes" => array("porn","terrorism"))));

		try {
			$response = $client->getAcsResponse($request);
			if(200 == $response->code){
				$taskResults = $response->data;
				foreach ($taskResults as $taskResult) {
					if(200 == $taskResult->code){
						$sceneResults = $taskResult->results;
						foreach ($sceneResults as $sceneResult) {
							$scene = $sceneResult->scene;
							$suggestion = $sceneResult->suggestion;
							//根据scene和suggetion做相关的处理
							//do something
						}
					}else{
						return "task process fail:" + $response->code;
					}
				}
			}else{
				return "detect not success. code:" + $response->code;
			}
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 图片异步检测接口
	 */
	public function imageAsyncScan($tasks=array()) {
		$request = new Green\ImageAsyncScanRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode(array("tasks" => $tasks,"scenes" => array("porn","terrorism"))));

		try {
			$response = $client->getAcsResponse($request);
			if(200 == $response->code){
				$taskResults = $response->data;
				foreach ($taskResults as $taskResult) {
					if(200 == $taskResult->code){
						$taskId = $taskResult->taskId;
						// 将taskId 保存下来，间隔一段时间来轮询结果, 参照ImageAsyncScanResultsRequest
					}else{
						return "task process fail:" + $response->code;
					}
				}
			}else{
				return "detect not success. code:" + $response->code;
			}
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 获取图片异步检测结果接口
	 */
	public function imageAsyncScanResults($taskIds=array()) {
		$request = new Green\ImageAsyncScanResultsRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode($taskIds));

		try {
			$response = $client->getAcsResponse($request);
			if(200 == $response->code){
				$taskResults = $response->data;
				foreach ($taskResults as $taskResult) {
					if(200 == $taskResult->code){
						$sceneResults = $taskResult->results;
						foreach ($sceneResults as $sceneResult) {
							$scene = $sceneResult->scene;
							$suggestion = $sceneResult->suggestion;
							//根据scene和suggetion做相关的处理
							//do something
						}
					}else{
						return "task process fail:" + $response->code;
					}
				}
			}else{
				return "detect not success. code:" + $response->code;
			}
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 视频异步检测接口
	 */
	public function videoAsyncScan($tasks=array()) {
		$request = new Green\VideoAsyncScanRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode(array("tasks" => $tasks,"scenes" => array("porn"))));

		try {
			$response = $client->getAcsResponse($request);
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 获取视频异步检测结果接口
	 */
	public function videoAsyncScanResults($taskIds=array()) {
		$request = new Green\VideoAsyncScanResultsRequest();
		$request->setMethod("POST");
		$request->setAcceptFormat("JSON");

		$request->setContent(json_encode($taskIds));

		try {
			$response = $client->getAcsResponse($request);
		} catch (Exception $e) {
			return $e;
		}
		return $response;
	}

	/**
	 * 检测结果处理
	 */
	public function scanResults($response) {
		$blocks = [];
		if(200 == $response->code){
			$taskResults = $response->data;
			foreach ($taskResults as $taskResult) {
				if(200 == $taskResult->code){
					$sceneResults = $taskResult->results;
					foreach ($sceneResults as $sceneResult) {
						$scene = $sceneResult->scene;
						$suggestion = $sceneResult->suggestion;
						//根据scene和suggetion做相关的处理
						if($suggestion == 'block'){
							$blocks[] = $sceneResult->details;
						}
					}
				}
			}
		}
		return $blocks;
	}
}