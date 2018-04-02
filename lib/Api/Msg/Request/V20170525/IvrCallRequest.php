<?php

namespace Aliyun\Api\Msg\Request\V20170525;
use Aliyun\Core\RpcAcsRequest;

class IvrCallRequest extends RpcAcsRequest
{
	function  __construct()
	{
		parent::__construct("Dyvmsapi", "2017-05-25", "IvrCall");
		$this->setMethod("POST");
	}

    private  $startCode;

    private  $startTtsParams;

    private  $playTimes;

    private  $ownerId;

    private  $outId;

    private  $calledNumber;

    private  $MenuKeyMaps;

    private  $resourceOwnerAccount;

    private  $calledShowNumber;

    private  $resourceOwnerId;

    private  $timeout;

    private  $byeTtsParams;

    private  $byeCode;

    public function getStartCode() {
        return $this->startCode;
    }

    public function setStartCode($startCode) {
        $this->startCode = $startCode;
        $this->queryParameters["StartCode"]=$startCode;
    }

    public function getStartTtsParams() {
        return $this->startTtsParams;
    }

    public function setStartTtsParams($startTtsParams) {
        $this->startTtsParams = $startTtsParams;
        $this->queryParameters["StartTtsParams"]=$startTtsParams;
    }

    public function getPlayTimes() {
        return $this->playTimes;
    }

    public function setPlayTimes($playTimes) {
        $this->playTimes = $playTimes;
        $this->queryParameters["PlayTimes"]=$playTimes;
    }

    public function getOwnerId() {
        return $this->ownerId;
    }

    public function setOwnerId($ownerId) {
        $this->ownerId = $ownerId;
        $this->queryParameters["OwnerId"]=$ownerId;
    }

    public function getOutId() {
        return $this->outId;
    }

    public function setOutId($outId) {
        $this->outId = $outId;
        $this->queryParameters["OutId"]=$outId;
    }

    public function getCalledNumber() {
        return $this->calledNumber;
    }

    public function setCalledNumber($calledNumber) {
        $this->calledNumber = $calledNumber;
        $this->queryParameters["CalledNumber"]=$calledNumber;
    }

    public function getMenuKeyMaps() {
        return $this->MenuKeyMaps;
    }

    public function setMenuKeyMaps($MenuKeyMaps) {
        $this->MenuKeyMaps = $MenuKeyMaps;
        for ($i = 0; $i < count($MenuKeyMaps); $i ++) {
            $this->queryParameters['MenuKeyMap.' . ($i + 1) . '.Key'] = $MenuKeyMaps[$i]->getKey();
            $this->queryParameters['MenuKeyMap.' . ($i + 1) . '.Code'] = $MenuKeyMaps[$i]->getCode();
            $this->queryParameters['MenuKeyMap.' . ($i + 1) . '.TtsParams'] = $MenuKeyMaps[$i]->getTtsParams();

        }
    }

    public function getResourceOwnerAccount() {
        return $this->resourceOwnerAccount;
    }

    public function setResourceOwnerAccount($resourceOwnerAccount) {
        $this->resourceOwnerAccount = $resourceOwnerAccount;
        $this->queryParameters["ResourceOwnerAccount"]=$resourceOwnerAccount;
    }

    public function getCalledShowNumber() {
        return $this->calledShowNumber;
    }

    public function setCalledShowNumber($calledShowNumber) {
        $this->calledShowNumber = $calledShowNumber;
        $this->queryParameters["CalledShowNumber"]=$calledShowNumber;
    }

    public function getResourceOwnerId() {
        return $this->resourceOwnerId;
    }

    public function setResourceOwnerId($resourceOwnerId) {
        $this->resourceOwnerId = $resourceOwnerId;
        $this->queryParameters["ResourceOwnerId"]=$resourceOwnerId;
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
        $this->queryParameters["Timeout"]=$timeout;
    }

    public function getByeTtsParams() {
        return $this->byeTtsParams;
    }

    public function setByeTtsParams($byeTtsParams) {
        $this->byeTtsParams = $byeTtsParams;
        $this->queryParameters["ByeTtsParams"]=$byeTtsParams;
    }

    public function getByeCode() {
        return $this->byeCode;
    }

    public function setByeCode($byeCode) {
        $this->byeCode = $byeCode;
        $this->queryParameters["ByeCode"]=$byeCode;
    }

}