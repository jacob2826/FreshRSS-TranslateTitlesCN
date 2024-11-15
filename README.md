# TranslateTitlesCN

`TranslateTitlesCN` 是一个为 [FreshRSS](https://github.com/FreshRSS/FreshRSS) 开发的插件，它能够将指定订阅源中的文章标题翻译成中文。用户可以选择使用 DeeplX、谷歌翻译或 LibreTranslate 服务来完成翻译。

> **声明**: 本项目在开发过程中大量使用了 [ChatGPT](https://chat.openai.com/)，特此对 [OpenAI](https://openai.com) 表示感谢。

## 参考图

<img src="https://github.com/jacob2826/FreshRSS-TranslateTitlesCN/blob/main/screenshot-20241115-214506.png" width="300px"/>

<img src="https://github.com/jacob2826/FreshRSS-TranslateTitlesCN/blob/main/screenshot-20231219-142310.png" width="300px"/>

## 安装方法

1. 下载 `TranslateTitlesCN` 插件。
2. 将`TranslateTitlesCN`文件夹放置在您的 FreshRSS 实例的 `./extensions` 目录下。
3. 登录到您的 FreshRSS 实例。
4. 进入管理面板，然后导航到“扩展”部分。
5. 在插件列表中找到 `TranslateTitlesCN`，点击“启用”。

## 使用方法

安装并启用插件后，进入插件的配置页面进行相关设置。在这里，您可以：

- **选择翻译服务**：您可以选择 DeeplX、谷歌翻译或 LibreTranslate 作为翻译服务提供者。
  - **DeeplX**：使用 DeeplX 服务进行翻译时，
    - 您可以选择部署 [DeeplX](https://github.com/OwO-Network/DeepLX/) 项目，并在插件配置中提供 DeeplX API 地址。默认地址为 `http://localhost:1188/translate`。
    - 或您可以使用其他人已部署好的 DeeplX 服务的 API 地址，如 `https://api.deeplx.fun/translate`。
  - **谷歌翻译**：选择谷歌翻译服务不需要额外配置。
  - **LibreTranslate**：使用 LibreTranslate 服务时，
    - 您可以自行部署 [LibreTranslate](https://github.com/LibreTranslate/LibreTranslate) 服务，这是一个开源的离线翻译服务，可以有效避免网络不佳导致无法翻译的情况。
    - 您也可以选择使用别人部署好的公共实例。
    - 需要在插件配置中设置 LibreTranslate 服务器地址，例如 `https://libretranslate.com/`。
    - 如果您的 LibreTranslate 服务器需要 API Key，请在配置中填写。
    - 公共实例列表可参考：[LibreTranslate 公共实例](https://github.com/LibreTranslate/LibreTranslate#mirrors)
- **为每个订阅源单独启用或禁用翻译功能**：您可以控制哪些订阅源的标题需要被翻译。

## 注意事项

- 使用 DeeplX 服务时，请确保 DeeplX 项目已正确部署，且 API 地址设置正确。
- 为防止频繁请求 DeeplX 导致 IP 被封禁，请谨慎使用。
- 本插件仅适用于 FreshRSS，确保您的 FreshRSS 版本与插件兼容。

## 贡献

如果您对 `TranslateTitlesCN` 有任何改进建议或想要贡献代码，请通过 GitHub 仓库提交 Pull Request 或 Issue。

您也可以 [赞助开发者(爱发电)](https://afdian.net/a/jacob2826) ，您的名字将会出现在赞助者列表中。

## 许可

该项目根据 [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html) 开源。
