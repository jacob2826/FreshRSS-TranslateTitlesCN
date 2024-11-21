<?php
class TranslationService {
    private $serviceType;
    private $deeplxBaseUrl;
    private $googleBaseUrl;
    private $libreBaseUrl;
    private $libreApiKey;

    public function __construct($serviceType) {
        $this->serviceType = $serviceType;
        $this->deeplxBaseUrl = FreshRSS_Context::$user_conf->DeeplxApiUrl;
        $this->googleBaseUrl = 'https://translate.googleapis.com/translate_a/single';
        $this->libreBaseUrl = FreshRSS_Context::$user_conf->LibreApiUrl;
        $this->libreApiKey = FreshRSS_Context::$user_conf->LibreApiKey;
    }

    public function translate($text) {
        switch ($this->serviceType) {
            case 'deeplx':
                return $this->translateWithDeeplx($text);
            case 'libre':
                return $this->translateWithLibre($text);
            default:
                return $this->translateWithGoogle($text);
        }
    }

    private function translateWithLibre($text) {
        if (empty($text)) {
            return '';
        }

        // 确保 API URL 末尾没有斜杠
        $apiUrl = rtrim($this->libreBaseUrl, '/') . '/translate';
        
        $postData = array(
            'q' => $text,
            'source' => 'auto',
            'target' => 'zh',
            'format' => 'text'
        );
        
        if (!empty($this->libreApiKey)) {
            $postData['api_key'] = $this->libreApiKey;
        }

        $jsonData = json_encode($postData);
        
        // 记录请求信息用于调试
        error_log("LibreTranslate Request URL: " . $apiUrl);
        error_log("LibreTranslate Request Data: " . $jsonData);

        $options = array(
            'http' => array(
                'header' => array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData)
                ),
                'method' => 'POST',
                'content' => $jsonData,
                'timeout' => 10,
                'ignore_errors' => true // 允许获取错误响应
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $context = stream_context_create($options);

        try {
            $result = @file_get_contents($apiUrl, false, $context);
            
            // 获取响应头信息
            $responseHeaders = $http_response_header ?? array();
            $statusLine = $responseHeaders[0] ?? '';
            error_log("LibreTranslate Response Status: " . $statusLine);
            
            if ($result === FALSE) {
                error_log("LibreTranslate API request failed - No Response");
                return $text;
            }

            error_log("LibreTranslate Raw Response: " . $result);
            
            $response = json_decode($result, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("LibreTranslate JSON decode error: " . json_last_error_msg());
                return $text;
            }

            if (isset($response['translatedText'])) {
                return mb_convert_encoding($response['translatedText'], 'UTF-8', 'UTF-8');
            } else if (isset($response['error'])) {
                error_log("LibreTranslate API error: " . $response['error']);
                return $text;
            } else {
                error_log("LibreTranslate API unexpected response structure: " . print_r($response, true));
                return $text;
            }
        } catch (Exception $e) {
            error_log("LibreTranslate exception: " . $e->getMessage());
            return $text;
        }
    }

    private function translateWithGoogle($text) {
        // 谷歌翻译逻辑
        $translatedText = '';

        // 构建谷歌翻译API的查询参数
        $queryParams = http_build_query([
            'client' => 'gtx',
            'sl' => 'auto',     // 源语言设置为自动检测
            'tl' => 'zh',       // 目标语言设置为中文
            'dt' => 't',
            'q' => $text,
        ]);

        $url = $this->googleBaseUrl . '?' . $queryParams;

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'timeout' => 3,
            ],
        ];

        $context = stream_context_create($options);

        try {
            $result = @file_get_contents($url, false, $context);
            if ($result === FALSE) {
                throw new Exception("Failed to get content from Google Translate API.");
            }

            // 解析谷歌翻译的响应
            $response = json_decode($result, true);
            if (!empty($response[0][0][0])) {
                $translatedText = $response[0][0][0];
            } else {
                throw new Exception("Google Translate API returned an empty translation.");
            }

            // 记录成功的翻译
            // error_log("Translation successful for text: " . $text . "; Translated: " . $translatedText);
        } catch (Exception $e) {
            // 记录错误信息
            error_log("Error in translation: " . $e->getMessage());
        }

        return $translatedText;
    }

    private function translateWithDeeplx($text) {
        // DeeplX翻译逻辑
        $translatedText = '';

        // 增加1-3秒的随机时间间隔
        sleep(rand(1, 3));

        // 构建POST数据
        $postData = json_encode([
            'text' => $text,
            'source_lang' => 'auto',
            'target_lang' => 'ZH' // 目标语言设置为中文
        ]);

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => $postData,
                'timeout' => 3, // 设置超时时间
            ]
        ];

        $context = stream_context_create($options);

        try {
            // 发送请求到DeeplX API
            $result = file_get_contents($this->deeplxBaseUrl, false, $context);
            if ($result === FALSE) {
                throw new Exception("Failed to get content from DeeplX API.");
            }

            // 解析响应
            $response = json_decode($result, true);
            if (isset($response['data']) && !empty($response['data'])) {
                $translatedText = $response['data'];
            } else {
                throw new Exception("DeeplX API returned an empty translation. Response code: " . $response['code']);
            }

            // 记录成功的翻译
            // error_log("Translation successful for text: " . $text . "; Translated: " . $translatedText);
        } catch (Exception $e) {
            // 处理错误情况
            error_log("Error in DeeplX translation: " . $e->getMessage());
        }

        return $translatedText;
    }
}
