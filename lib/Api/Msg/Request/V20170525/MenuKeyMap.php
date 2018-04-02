<?php

namespace Aliyun\Api\Msg\Request\V20170525;

class MenuKeyMap
{

    private $key;

    private $code;

    private $ttsParams;

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function getTtsParams() {
        return $this->ttsParams;
    }

    public function setTtsParams($ttsParams) {
        $this->ttsParams = $ttsParams;
    }
}
