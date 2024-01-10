<?php
require_once('lib/TranslateController.php');

class TranslateTitlesExtension extends Minz_Extension {
    // 默认DeepLX API 地址
    private const ApiUrl = 'http://localhost:1188/translate';

    public function init() {
        $this->registerHook('feed_before_insert', array($this, 'addTranslationOption'));
        $this->registerHook('entry_before_insert', array($this, 'translateTitle'));

        if (is_null(FreshRSS_Context::$user_conf->TranslateService)) {
            FreshRSS_Context::$user_conf->TranslateService = 'google';
            FreshRSS_Context::$user_conf->save();
        }

        if (is_null(FreshRSS_Context::$user_conf->DeeplxApiUrl)) {
            FreshRSS_Context::$user_conf->DeeplxApiUrl = self::ApiUrl;
            FreshRSS_Context::$user_conf->save();
        }
    }

    public function handleConfigureAction() {
        if (Minz_Request::isPost()) {
            $translateService = Minz_Request::param('TranslateService', 'google');
            FreshRSS_Context::$user_conf->TranslateService = $translateService;

            $translateTitles = Minz_Request::param('TranslateTitles', array());
            FreshRSS_Context::$user_conf->TranslateTitles = $translateTitles;

            $deeplxApiUrl = Minz_Request::param('DeeplxApiUrl', self::ApiUrl);
            FreshRSS_Context::$user_conf->DeeplxApiUrl = $deeplxApiUrl;

            FreshRSS_Context::$user_conf->save();
        }
    }

    public function handleUninstallAction() {
        // 清除所有与插件相关的用户配置
        if (isset(FreshRSS_Context::$user_conf->TranslateService)) {
            unset(FreshRSS_Context::$user_conf->TranslateService);
        }
        if (isset(FreshRSS_Context::$user_conf->TranslateTitles)) {
            unset(FreshRSS_Context::$user_conf->TranslateTitles);
        }
        if (isset(FreshRSS_Context::$user_conf->DeeplxApiUrl)) {
            unset(FreshRSS_Context::$user_conf->DeeplxApiUrl);
        }
        FreshRSS_Context::$user_conf->save();
    }

    public function translateTitle($entry) {
        // 检查是否启用了翻译
        $feedId = $entry->feed()->id();
        if (isset(FreshRSS_Context::$user_conf->TranslateTitles[$feedId]) && FreshRSS_Context::$user_conf->TranslateTitles[$feedId] == '1') {
            $title = $entry->title();
            $translateController = new TranslateController();
            $translatedTitle = $translateController->translateTitle($title);
            if (!empty($translatedTitle)) {
                $entry->_title($translatedTitle . ' - ' . $title); // 将翻译后的标题放在前，原文标题放在后
            }
        }
        return $entry;
    }

    public function addTranslationOption($feed) {
        $feed->TranslateTitles = '0';
        return $feed;
    }
}