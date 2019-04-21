## 网易云 · 乐评 API 文档

### JSON 格式

请求地址：`https://api.comments.hk/`

请求方式：`GET`

请求参数： 暂无

返回类型：JSON

返回参数：

|    参数名    | 含义 |
| ---------- | --- |
| song_id | 歌曲 ID |
| title | 歌曲名称 |
| images | 歌曲封面图片，已处理为 https 链接 |
| author | 歌曲作者 |
| album | 歌曲所属专辑 |
| description | 歌曲描述 |
| pub_date | 歌曲发行时间 |
| comment_id | 评论 ID |
| comment_user_id | 评论所属用户 ID |
| comment_nickname | 评论所属用户名称 |
| comment_avatar_url | 评论所属用户头像链接，已处理为 https 链接 |
| comment_content | 评论正文 |
| comment_pub_date | 评论发表日期 |

### 示例：
```js
<script>
  var xhr = new XMLHttpRequest();
  xhr.open('get', 'https://api.comments.hk/');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      var data = JSON.parse(xhr.responseText);
      var hotComments = document.getElementById('hotComments');
      hotComments.innerText = data.comment_content;
    }
  }
  xhr.send();
</script>
```

### 更新日志
- 2019/4/21 更新 JSON 文档第一版
