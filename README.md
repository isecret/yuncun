
![项目主页](https://files.wangmao.me/img/WX20190422-111741.png)

## 关于网易云 · 乐评

项目灵感来源于网易云音乐的与农夫山泉合作的乐瓶营销「乐瓶」——这 30 条乐评，是从网易云音乐后台点赞数最高的 8000 条乐评中，经过人工筛选产生的，它们文字简练，富有故事性，即使脱离歌曲本身也可以被理解。

在使用网易云音乐的时候，常常在评论区看到与之共鸣的评论。有时候很想将其记录下来，同朋友分享。时间久了，那种感动依然不可褪去。

你能在这倾听别人的故事，亦或许是你的故事。

## 快速接入

[项目主页](https://comments.hk/) | [API 文档](https://github.com/isecret/yuncun/blob/master/DOC.md)

## 我们能提供什么

项目后台定期拉取热门歌曲排行榜列表并获取其中的热门评论，通过接口随机分发一条热门评论，你可以查看 [API 文档](https://github.com/isecret/yuncun/blob/master/DOC.md) 快速接入。

当然，你也可以通过提交歌曲或歌单 ID 来完善这个项目，将你的感动带给更多的人。

## 安装

1. 克隆项目，部署到服务器，环境需要 `PHP > 7.1.3`，Nginx 配置参考。
```nginx
# 前端 comments.hk 域名指向 /proejct/html 目录
server {
  listen 80;
  listen 443 ssl http2;
  ...
  server_name comments.hk;
  index index.html index.htm index.php;
  root /data/wwwroot/comments.hk/html;
  ...
  location ~ [^/]\.php(/|$) {
    #fastcgi_pass remote_php_ip:9000;
    fastcgi_pass unix:/dev/shm/php-cgi.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
  }
}

# 后端 api.comments.hk 域名指向 /project/public 目录
server {
  listen 80;
  listen 443 ssl http2;
  ...
  server_name api.comments.hk;
  index index.html index.htm index.php;
  root /data/wwwroot/comments.hk/public;
  ...
  location ~ [^/]\.php(/|$) {
    #fastcgi_pass remote_php_ip:9000;
    fastcgi_pass unix:/dev/shm/php-cgi.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
  }
}
```

2. 复制项目配置文件，配置项目、数据库地址，并赋予 `storage` 写入权限。

```bash
cd /project_path
cp ./html/js/env.js.example ./html/js/env.js
vim ./html/js/env.js
...
window.api_domain = 'https://api.comments.hk'; # 项目前端请求后端的 URL 
...
cp .env.example .env
vim .env
...
APP_DEBUG=true  #是否开启 DEBUG 模式，上线后请设置为 false
APP_URL=http://localhost  #项目URL

DB_CONNECTION=mysql
DB_HOST=127.0.0.1  #数据库地址
DB_PORT=3306  #数据库端口
DB_DATABASE=laravel  #数据库名
DB_USERNAME=root  #数据库用户
DB_PASSWORD=  #数据库密码
...
chmod -R 777 storage  #赋予写入权限
```

3. 安装项目依赖包，生成项目随机 key，并完成数据库迁移。

```bash
composer install #安装项目依赖
php artisan key:generate  #生成项目随机 key
php artisan migrate  #数据库迁移
```

4. 同步热门榜评论
```bash
php artisan comments:toplist
```

## 感谢

- [今日诗词](https://www.jinrishici.com/)
- [Hitokoto - 一言](https://hitokoto.cn/)

## 数据来源

项目歌曲数据、图像和评论数据来源于网易云音乐，网易云音乐对其拥有内容、商标所有权。
