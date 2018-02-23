# HCTF-Backend

HCTF 2017 平台后端。

本仓库是平台的后端，需要和[前端](https://github.com/Last-Order/hctf_frontend)配合使用，关于前端的部署请参见前端仓库。在使用时，请使用`Nginx`或`Apache`等服务器将根目录指向前端(`/dist`目录)，`/API`目录指向后端(`/public`目录)。

一份示例的`Apache Vhost`（生产环境）配置如下：
```
<VirtualHost *:80>
      ServerName hctf.local

      DocumentRoot /var/www/hctf/hctf-frontend/dist
      Alias /API /var/www/hctf/hctf-backend/public
</VirtualHost>
```

开发环境下请使用反向代理将目录指向对应开发服务器。

## 平台简介

HCTF 2017 采用自主开发的全新比赛平台，主要特色包括分层机制和反作弊机制。

### 分层机制
#### 结构

本平台题目主要结构共有四层：

分类(Category)->层级(Level)->题目(Challenge)->Flag

逐级向下，每一道题目都需要分配到一个指定的层级中，每一个层级也需要自己所属的分类。因此，在创建新的题目时，首先应该新建分类，而后新建层级，最后把题目分配进去。

#### 开层规则

平台的每一个层级(Level)和每一道题目(Challenge)都可以设定独立的开放时间，只有两者都满足时题目才会进入开放规则判定。

每一个层级可以设定自己的开放规则。开放规则目前仅支持根据已完成的题目数量进行设定，但在设计时已经留出扩展余地，添加新类型的规则应该不会太过困难。

![](http://ww1.sinaimg.cn/large/e985a6f7ly1foq6gjvxn6g211d0k57jl.gif)

如图所示，不同的规则可以通过逻辑运算符连接，目前不支持括号。

### 反作弊

#### 题目最小完成时间

每一道题目可以设定最小的完成时间，从题目满足开放条件后第一次显示开始计算，小于设定时间的将会被自动封禁。

#### 动态 Flag

在注册时，平台会给每一个队伍分配一个`Token`作为队伍的标识，该标识不应被泄露。在题目创建时，可以选择开启动态 Flag。

动态 Flag 意味着每个队伍的 Flag 将会根据用户的答题 Token 和题目答案计算。公式如下： 

`userFlag = "hctf{" + SHA256(userToken + flag) + "}"`

其中，`userFlag`指该用户的专属 Flag ，提交其他 Flag 将视为错误答案。`userToken`指该用户的答题`Token`，`flag`指在题目创建时填入的 Flag ，其作用相当于盐。

公式中的前缀后缀(`hctf{}`)可以在系统设置中修改。

动态 Flag 多用于 Pwn 题，在连上服务器后先要求用户输入`Token`，然后再进入其他流程，最终给出答案时根据题目本身的 Flag 和用户`Token`计算最终 Flag。

#### 多 Flag

在动态计算 Flag 不便时，还可以使用多 Flag。

多 Flag 意味着需要为每一个队伍设定一个 Flag，具体输入格式请查看题创建页面。

多 Flag 机制多用于 Bin 题，这意味着需要事先编译好多于参赛队伍数量的二级制文件，并把每个队伍指向不同的文件下载地址。

在题目 URL 设置中可以使用`{teamId}`作为占位符，在展示给用户时该占位符将会被一段基于用户 ID 的哈希替代。

为了防止下载目录被遍历，该`{teamId}`的计算公式如下

`teamId = SHA256(id + salt)`

其中`id`是队伍在数据库中的自增主键，`salt`为盐，**请务必更改**`.env`文件中的`APP_SALT`值。

提交其他队伍的 Flag 将会被自动封禁。

#### 开放规则

如前文所述，每一个层级有自己开放规则。以下几种情况均会被自动封禁。

1. 提交未到开放时间的题目的正确 Flag。
2. 提交未满足开放规则的题目的正确 Flag。

## 多语言

平台前台部分提供了中文和英文两种语言，后台管理部分仅有中文。

前台部分可以通过修改`resources/lang`下的`json`文件进行扩展。

添加新的语言需要前端配合，请移步前端仓库。

## 动态分数

在 HCTF 2017 中我们采用了动态分数的设定，如不需要，可以修改`app\Services\ScoreService.php`。

## 后端部署指南

拉取代码、安装依赖

```
git clone https://github.com/Last-Order/hctf_backend
cd ./hctf_backend
composer install # 安装依赖
```

依照`.env.example`创建 `.env`。记得添加一个`APP_SALT`。

生成 Laravel App Key

`php artisan key:generate`

执行数据库迁移

`php artisan migrate`

初始化数据库

编辑`database/seeds`下的两个文件，设定初始信息。

```
php artisan db:seed --class=ConfigTableSeeder
php artisan db:seed --class=TeamTableSeeder
```

Enjoy it.