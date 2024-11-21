<?php
require_once('TranslationService.php');

class TranslateController {
    public function translateTitle($title) {
        if (empty($title)) {
            error_log("TranslateTitlesCN: Empty title provided");
            return '';
        }

        $serviceType = FreshRSS_Context::$user_conf->TranslateService ?? 'google';
        $translationService = new TranslationService($serviceType);
        $translatedTitle = '';
        $attempts = 0;
        $sleepTime = 1; // 初始等待时间

        error_log("TranslateTitlesCN: Service: " . $serviceType . ", Title: " . $title);

        while ($attempts < 2) {
            try {
                $translatedTitle = $translationService->translate($title);
                if (!empty($translatedTitle)) {
                    error_log("TranslateTitlesCN: Translation successful: " . $translatedTitle);
                    break;
                }
                error_log("TranslateTitlesCN: Empty translation result on attempt " . ($attempts + 1));
            } catch (Exception $e) {
                error_log("TranslateTitlesCN: Translation error on attempt " . ($attempts + 1) . " - " . $e->getMessage());
                $attempts++;
                sleep($sleepTime);
                $sleepTime *= 2; // 每次失败后增加等待时间
            }
        }

        // 如果翻译失败且当前服务为DeeplX，则尝试使用谷歌翻译
        if (empty($translatedTitle) && $serviceType == 'deeplx') {
            error_log("TranslateTitlesCN: DeeplX failed, falling back to Google Translate");
            $translationService = new TranslationService('google');
            try {
                $translatedTitle = $translationService->translate($title);
                if (!empty($translatedTitle)) {
                    error_log("TranslateTitlesCN: Google Translate fallback successful: " . $translatedTitle);
                }
            } catch (Exception $e) {
                error_log("TranslateTitlesCN: Google Translate fallback failed - " . $e->getMessage());
            }
        }

        // 如果翻译仍然失败，使用原始标题
        if (empty($translatedTitle)) {
            error_log("TranslateTitlesCN: All translation attempts failed, returning original title");
            return $title;
        }

        return $translatedTitle;
    }
}
