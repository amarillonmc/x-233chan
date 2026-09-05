# x-233chan
Kusaba X Fork with a translated Chinese frontend, powering a certain project starting with 2333
匿名版系统Kusaba X的fork，增加以下特性：
- 中文前台
- BBCode支持
- 文字颜文字输入
- 彩色饼干ID
- 音频上传（需要服务器支持，基于Flash播放器）


# 安装方法 #
Clone本repo后，用文本编辑器打开``config.php``，根据你的实际情况，修改其中的变量。

修改完毕保存后，将OTHER文件夹中的``install.php``, 对应你``config.php``中``KU_DBTYPE``变量的``install-dbtype.php``以及``kusaba_freshinstall.dbtype.sql``复制粘贴进根目录。

如果你使用虚拟主机，现在应该将全部内容上传到远端服务器上。

执行``/install.php``进行安装。

安装完毕后，删除前述的安装用文件以及``/OTHER``文件夹。

后台在``/manage.php``，默认用户名和密码均为``admin``，请记得修改。

以上

## 前端脚本维护 ##

版面实际加载的是 `lib/javascript/kusaba.js`，修改 `lib/javascript/clean/kusaba.js` 后需要同步更新前者。压缩脚本末尾还有此 Fork 的 ID 着色代码，不能直接用 clean 版本整文件覆盖。

Cookie 读取及管理控件的回归检查（需要 Node.js 18 或更新版本）：

```sh
node --test tests/moderation-cookies.test.js
```

发布管理控件修复时，应更新上述两份脚本，清除 CDN 中 `lib/javascript/kusaba.js` 的缓存（如有），然后在浏览器中强制刷新版面。仅更新 clean 目录不会使线上修复生效。

# 免责声明 #

使用此代码产生的一切后果自负。

此程序的原作者为[Kusuba X制作组](https://github.com/Edaha)。

## 其他 ##

里岛使用的波兰球现国别插件基于[这个repo](https://github.com/exclude/kusaba-int-module)。

文字颜文字列表来自于A岛。

``/assets``文件夹中图像的作者为Woody-Rinn。


----------
以下为原README文件内容

----------
Installation Guide
http://kusabax.cultnet.net/wiki/installation_guide

Other Info (Including Upgrading)
http://kusabax.cultnet.net/wiki/basics
