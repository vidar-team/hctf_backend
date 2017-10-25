<?php

return [
    /**
     * 开始比赛的时间
     * Time to start the game
     */
    "startTime" => "2017-04-11T08:00:00+08:00",
    /**
     * 结束比赛的时间
     * Time to end the game
     */
    "endTime" => "2017-11-13T08:00:00+08:00",
    /**
     * Flag 前缀
     * Prefix of flags
     */
    "flagPrefix" => "hctf{",
    /**
     * Flag 后缀
     * Suffix of flags
     */
    "flagSuffix" => "}",
    /**
     * For example, if the prefix is set to "{hctf" and the suffix is set to "}", the flag should be hctf{xxxxxxx}
     * This config is only apply to dynamic flag
     * 本设置仅对动态 Flag 的题目有效
     */
    "salt" => env("APP_KEY")
];