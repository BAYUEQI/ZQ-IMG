## 前言

点一个 Star 再走吧~

一款专为个人需求设计的高效图床解决方案，集成了强大的图片压缩功能与优雅的前台后台管理界面。

项目结构精简高效，提供自定义图片压缩率与尺寸设置，有效降低存储与带宽成本。

支持本地储存，阿里云OSS储存，S3存储。可通过把储存桶挂载到本地的方式解锁更多储存方式。

简洁美观的前端，支持点击、拖拽、粘贴、URL、批量上传。

瀑布流管理后台，便捷查看图片信息，支持图片灯箱、AJAX无加载刷新。

**🎉 新增功能：随机图片API系统** - 基于图床搭建的完整随机图片服务，支持多种获取方式和格式。

## 演示站点

前端：https://img.520jacky.dpdns.org/

后台：https://img.520jacky.dpdns.org/admin/  

演示站点更新较快，可能跟实际效果不太一样

## 🚀 随机图片API

### 概述

基于ZQ-IMG图床程序搭建的完整随机图片API系统，提供多种获取随机图片的方式，支持JSON、重定向、JSONP等多种格式。

### API端点

#### 1. 完整随机图片API (`random.php`)

提供详细的图片信息和多种格式支持。

**基础URL：**
```
https://你的域名/random.php
```

**参数说明：**
- `type`: 获取类型
  - `random` (默认): 随机获取
  - `latest`: 最新上传
  - `oldest`: 最早上传
- `count`: 获取数量 (1-10张，默认1张)
- `format`: 返回格式
  - `json` (默认): JSON格式
  - `redirect`: 直接重定向到图片
  - `jsonp`: JSONP格式
- `callback`: JSONP回调函数名 (仅当format=jsonp时使用)

**使用示例：**

```bash
# 获取1张随机图片的详细信息
https://你的域名/random.php

# 获取3张最新上传的图片
https://你的域名/random.php?type=latest&count=3

# 直接重定向到随机图片
https://你的域名/random.php?format=redirect

# JSONP格式调用
https://你的域名/random.php?format=jsonp&callback=myCallback
```

**返回示例：**
```json
{
  "success": true,
  "code": 200,
  "message": "获取成功",
  "data": {
    "type": "random",
    "count": 1,
    "total_available": 78,
    "images": [
      {
        "id": 98,
        "url": "https://你的域名/i/2025/06/03/410571.jpg",
        "path": "i/2025/06/03/410571.jpg",
        "storage": "local",
        "size": "336 B",
        "size_bytes": 336,
        "upload_time": "2025-06-03 16:06:13",
        "upload_ip": "172.70.223.141"
      }
    ]
  },
  "timestamp": "2025-08-22 18:34:19",
  "api_version": "1.0"
}
```

#### 2. 简单随机图片API (`random-image.php`)

轻量级API，直接获取图片URL或重定向。

**基础URL：**
```
https://你的域名/random-image.php
```

**参数说明：**
- `format`: 返回格式
  - `redirect` (默认): 直接重定向到图片
  - `url`: 返回图片URL
  - `json`: 返回JSON格式
- `count`: 获取数量 (1-5张，默认1张)

**使用示例：**

```bash
# 直接重定向到随机图片
https://你的域名/random-image.php

# 获取随机图片URL
https://你的域名/random-image.php?format=url

# 获取3张随机图片的JSON信息
https://你的域名/random-image.php?format=json&count=3
```

### 实际应用场景

#### 1. 网站背景图片
```html
<img src="https://你的域名/random-image.php" alt="随机背景">
```

#### 2. 博客文章配图
```markdown
![随机配图](https://你的域名/random-image.php)
```

#### 3. 前端开发测试
```javascript
// 获取随机图片URL
fetch('https://你的域名/random-image.php?format=json')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      document.getElementById('randomImage').src = data.images[0];
    }
  });
```

#### 4. 聊天机器人头像
```
https://你的域名/random-image.php
```

### API特性对比

| 功能特性 | `random.php` | `random-image.php` |
|---------|-------------|-------------------|
| **信息详细程度** | 🔴 完整详细信息 | 🟢 仅URL信息 |
| **响应速度** | 🟡 中等 | 🟢 快速 |
| **数据量** | 🔴 较大 | 🟢 较小 |
| **功能丰富度** | 🔴 功能全面 | 🟢 功能简单 |
| **排序方式** | 🔴 支持random/latest/oldest | 🟢 仅random |
| **获取数量** | 🔴 1-10张 | 🟢 1-5张 |
| **返回格式** | 🔴 JSON/重定向/JSONP | 🟢 JSON/重定向/URL |

### 选择建议

- **使用 `random.php` 当**：需要图片的完整信息、多种排序方式、JSONP支持
- **使用 `random-image.php` 当**：只需要图片URL、追求最快响应速度、轻量级应用

## 安装教程

首先下载源码ZIP，将文件上传到网站根目录，访问网址  ，填写相关信息，即可完成安装。

## 运行环境

推荐PHP 8.1 + MySQL >= 5.6

本程序依赖PHP的 Fileinfo 、 Imagick 、 exif拓展，需要自行安装。依赖 pcntl 扩展（宝塔PHP默认已安装）

要求 pcntl_signal 和 pcntl_alarm 函数可用（需主动解除禁用）




## 拓展功能

本程序支持 Upgit 对接在Typora使用，对接方法如下

### 下载upgit

前往下载 [Upgit](https://coobl.lanzouq.com/i5ZZ82ohf8sf)

### 如何配置

修改目录下`config.toml`文件，内容如下

```toml
default_uploader = "PixPro"

[uploaders.PixPro]
request_url = "https://xxx.xxx.xxx/api.php"
token = "这里内容替换为你的Token"
```
### 接入 Typora

转到 Image 选自定义命令作为图像上传器，在命令文本框中输入 Upgit 程序位置，然后就可以使用了
![接入到Typora](https://cdn.dusays.com/2022/05/459-2.jpg)


## 🤝 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目！

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。

---

**🎊 感谢使用 ZQ-IMG 图床程序！**
