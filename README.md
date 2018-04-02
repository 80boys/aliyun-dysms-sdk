# aliyun-dysms-sdk
阿里云语音服务

实用范例


 use Aliyun\Core\Config;
 use Aliyun\Core\Exception\ClientException;
 use Aliyun\Core\Profile\DefaultProfile;
 use Aliyun\Core\DefaultAcsClient;
 use Aliyun\Api\Msg\Request\V20170525\SingleCallByVoiceRequest;
 use Aliyun\Api\Msg\Request\V20170525\SingleCallByTtsRequest;
 use Aliyun\Api\Msg\Request\V20170525\IvrCallRequest;
 use Aliyun\Api\Msg\Request\V20170525\MenuKeyMap;
 use AliyunMNS\Exception\MnsException;
 use AliyunMNS\TokenGetterForAlicom;

Config::load();

class VoiceReportlib
{

    private $tokenGetter = null;
    public  $acsClient   = null;
    private $appid       = '';
    private $secret      = '';

    /**
     * 取得AcsClient
     * @return DefaultAcsClient
     */
    public  function __construct($config = [])
    {
        $this->appid = $config['appid'];
        $this->secret= $config['secret'];
    }

	// 初始化 SDK
    public  function getAcsClient() {

		//产品名称
        $product         = "Dyvmsapi";

        //产品域名
        $domain          = "dyvmsapi.aliyuncs.com";


        $accessKeyId     = $this->appid;

        $accessKeySecret = $this->secret;

		//服务节点
        $region          = "cn-hangzhou";

		//服务节点
        $endPointName    = "cn-hangzhou";


        if($this->acsClient == null) {

            //初始化acsClient
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            //增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            //初始化AcsClient用于发起请求
            $this->acsClient = new DefaultAcsClient($profile);
        }

        return $this->acsClient;
    }

	//获取签名
    public function getTokenGetter() {

        $accountId 		 = "1943695596114318";

        $accessKeyId 	 = $this->appid;

        $accessKeySecret = $this -> secret;

        if ($this->tokenGetter == null) {
            $this->tokenGetter = new TokenGetterForAlicom(
                $accountId,
                $accessKeyId,
                $accessKeySecret
			);
        }
        return $this->tokenGetter;
    }

    /**
     * 语音文件外呼
     * @return stdClass
     * @throws ClientException
     */
    public function singleCallByVoice() {

        $acsClient = $this->getAcsClient();

        //组装请求对象
        $request = new SingleCallByVoiceRequest();

        //必填-被叫显号
        $request->setCalledShowNumber("400100000");

        //必填-被叫号码
        $request->setCalledNumber("13700000000");

        //必填-语音文件Code
        $request->setVoiceCode("c2e99ebc-2d4c-4e78-8d2a-afbb06cf6216.wav");

        //选填-外呼流水号
        $request->setOutId("1234");

        //hint 此处可能会抛出异常，注意catch
        $response = $acsClient->getAcsResponse($request);

        return $response;
    }


    /**
     * 获取消息
     *
     * @param string $messageType 消息类型
     * @param string $queueName Alicom-Queue-xxxxxx-xxxxxReport
     * @param callable $callback
     * 回调仅接受一个消息参数;
     * 回调返回true，则工具类自动删除已拉取的消息;
     * 回调返回false,消息不删除可以下次获取.
     */
    public function receiveMsg( $config = [] )
    {
        $i = 0;
        // 取回执消息失败3次则停止循环拉取
        while ( $i < 3)
        {
            try
            {
                // 取临时token
                $tokenForAlicom = $this->getTokenGetter()->getTokenByMessageType( $config['type'], $config['queue'] );

                // 使用MNSClient得到Queue
                $queue = $tokenForAlicom->getClient()->getQueueRef($config['queue']);

                // 接收消息，并根据实际情况设置超时时间
                $res = $queue->receiveMessage(30);

                // 计算消息体的摘要用作校验
                $bodyMD5 = strtoupper(md5(base64_encode($res->getMessageBody())));

                // 比对摘要，防止消息被截断或发生错误
                if ($bodyMD5 == $res->getMessageBodyMD5())
                {
                    // 执行回调
                    if( call_user_func( $config['callback'], json_decode($res->getMessageBody()) ) )
                    {
                        // 当回调返回真值时，删除已接收的信息
                        $receiptHandle = $res->getReceiptHandle();

                        $queue->deleteMessage($receiptHandle);
                    }
                }

                return; // 整个取回执消息流程完成后退出
            }
            catch (MnsException $e)
            {
                $i++;
            }
        }
    }
	/**
	 * 交互外呼
	 */
	public function ivrCall($config = [])
	{
        if ( !empty($config['from_tel']) && !empty($config['call_tel']) &&
            !empty($config['menukey']) && !empty($config['start_code']) ) {
    		$acsClient = $this->getAcsClient();
            //组装请求对象-具体描述见控制台-文档部分内容
            $request = new IvrCallRequest();
            //必填-被叫显号
            $request->setCalledShowNumber($config['from_tel']);
            //必填-被叫号码
            $request->setCalledNumber($config['call_tel']);
            //选填-播放次数
            if ( empty($config['count'])) {
                $config['count'] = 3;
            }
            $request->setPlayTimes($config['count']);

            //必填-语音文件ID或者tts模板的模板号,有参数的模板需要设置模板变量的值
            $request->setStartCode($config['start_code']);
            if ( !empty($config['start_parm']) ) {
                $request->setStartTtsParams($config['start_parm']);
            }

    		//这只按键信息
            $menuKeyMaps = array();
    		foreach ($config['menukey'] as $key => $value) {
    			$menuKeyMap = new MenuKeyMap();
    	        $menuKeyMap->setKey($value['key']);
    	        $menuKeyMap->setCode($value['tts']);
                if ( !empty($value['params']) ) {
    	            $menuKeyMap->setTtsParams($value['params']);
                }
    	        $menuKeyMaps[] = $menuKeyMap;
    		}
            $request->setMenuKeyMaps($menuKeyMaps);

            //选填-等待用户按键超时时间，单位毫秒
            if ( empty($config['timeout'])) {
                $config['timeout'] = 3;
            }
            $request->setTimeout($config['timeout']);

            //选填-播放结束时播放的结束提示音,支持语音文件和Tts模板2种方式,但是类型需要与StartCode一致，即前者为Tts类型的，后者也需要是Tts类型的
            if (!empty($config['bye'])) {
                $request->setByeCode($config['bye']);
            }
            //Tts模板变量替换JSON,当ByeCode为Tts时且Tts模板中带变量的情况下此参数必填
            if (!empty($config['byeparams'])) {
                $request->setByeTtsParams($config['byeparams']);
            }
            //选填-外呼流水号
            if (!empty($config['outid'])) {
                $request->setOutId($config['outid']);
            }
            //hint 此处可能会抛出异常，注意catch
            $response = $acsClient->getAcsResponse($request);

            return $response;
        }
        return false;
    }


    /**
     * 文本转语音外呼
     * @return stdClass
     * @throws ClientException
	 * $args = [
	 *	'from_tel'  => '01086394488',
	 *		'call_tel'  => '13836159637',
	 *		'json_data' => '['name':'武恒']',
	 *		'out_id'    => '123',
	 *		'tpl_id'    => 'TTS_109390174'
	 *	]
     */
    public  function singleCallByTts( $config = [] ) {

        $acsClient = $this->getAcsClient();

        //组装请求对象
        $request = new SingleCallByTtsRequest();

        //必填-被叫显号
        $request->setCalledShowNumber($config['from_tel']);

        //必填-被叫号码
        $request->setCalledNumber($config['call_tel']);

        //必填-Tts模板Code
        $request->setTtsCode($config['tpl_id']);

        //选填-Tts模板中的变量替换JSON
        $request->setTtsParam($config['json_data']);

        //选填-外呼流水号
        $request->setOutId($config['out_id']);

        //hint 此处可能会抛出异常
        $response = $acsClient->getAcsResponse($request);

        return $response;
    }

}


