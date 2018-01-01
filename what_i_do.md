## hctf线上平台hgame修改版

### 修改 routes/web.php:
增加获取周榜路由
增加ctfPatternCheck中间件

### 增加 database/migrations/2017_12_30_230615_add_some_studentinfo_column_to_team_table
增加hduer字段区分校内校外
增加校内人员信息字段

### 修改 app/Challenge.php
增加一行`'score' => 'float'`，我也不知道当初为什么要加Orz

### 修改 app/Team.php
在`$fillable`中增加校内人员信息字段
增加`scopeOrderByScoreForWeek`分数排序——周榜(hgame专属)

### 修改 app/Services/ScoreService.php
增加`$ctfPattern`变量，区分hctf与hgame的分数模式
增加hgame计分模式

### 修改 app/Http/Controllers/ChallengeController.php
在构造函数中加入私有变量`$ctfPattern`,获取数据库中当前比赛模式
在所有`ScoreService::calculate()`中加入`$this->ctfPattern`, 除了`resetScore`
在`submitFlag`中增加获得基准分的规则, hgame分数更新规则

### 修改 app/Http/Controllers/SystemController.php
增加ctfPattern键
```
POST /API/System/edit

data = {"language":"zh-cn","startTime":"2017-12-30T08:00:00+08:00","endTime":"2018-01-06T08:00:00+08:00","flagPrefix":"hgame{","flagSuffix":"}","ctfPattern":"hgame|hctf"}

GET /API/System/meta

{"status":"success","data":{"startTime":"2017-12-30T08:00:00+08:00","endTime":"2018-01-06T08:00:00+08:00","flagPrefix":"hgame{","flagSuffix":"}","ctfPattern":"hgame"}}
```

修改 app/Http/Controllers/TeamController.php
增加hgame注册
更新`getRanking`中敏感信息遮罩
增加周榜显示`getWeekRanking`,hgame专属
```
POST /API/Team/register

{"language":"zh-cn","teamName":"hammer4","email":"h4@h.com","password":"3ef81cb18bdaac2f67a114146b7f9c8da4bf8ceef8021dfc2da4daa8c1416e52","hduer":"0|1","studentId":"16081112","realName":"啊啊啊","college":"网络空间与安全学院"} //hgame

{"language":"zh-cn","teamName":"hammer4","email":"h4@h.com","password":"3ef81cb18bdaac2f67a114146b7f9c8da4bf8ceef8021dfc2da4daa8c1416e52"} //hctf

POST /API/Team/weekRanking

{"status":"success","data":{"ranking":[{"team_id":3,"team_name":"hammer","hduer":true,"status":"correct","dynamic_total_score":1216},{"team_id":8,"team_name":"hammer2","hduer":true,"status":"correct","dynamic_total_score":1212},{"team_id":null,"team_name":"hammer3","hduer":true,"status":null,"dynamic_total_score":null}],"weeks":1}}
```

