<?php
require_once('TranslationService.php');

class TranslateController {
    public function translateTitle($title) {
        $serviceType = FreshRSS_Context::$user_conf->TranslateService ?? 'google';
        $translationService = new TranslationService($serviceType);
        $translatedTitle = '';
        $attempts = 0;
        $sleepTime = 1; // 初始等待时间

        error_log("Service: " . $serviceType . ", Title: " . $title);

        while ($attempts < 2) {
            try {
                $translatedTitle = $translationService->translate($title);
                if (!empty($translatedTitle)) {
                    break;
                }
            } catch (Exception $e) {
                $attempts++;
                sleep($sleepTime);
                $sleepTime *= 2; // 每次失败后增加等待时间
            }
        }

        // 如果翻译失败且当前服务为DeeplX，则尝试使用谷歌翻译
        if (empty($translatedTitle) && $serviceType == 'deeplx') {
            $translationService = new TranslationService('google');
            $translatedTitle = $translationService->translate($title);
        }

        // 如果翻译仍然失败，使用原始标题
        if (empty($translatedTitle)) {
            return $title;
        }

        return $translatedTitle;
    }
}
