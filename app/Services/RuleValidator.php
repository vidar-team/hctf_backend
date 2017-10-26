<?php

namespace App\Services;

use App\Services\Rules\CategoryPassCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Class RuleValidator
 * @package App\Services
 * @author Eridanus Sora <sora@sound.moe>
 */
class RuleValidator
{
    private $teamId;
    private $rules;
    private $rulesHash;

    public function __construct($teamId, $rules)
    {
        $this->teamId = $teamId;
        $this->rules = collect($rules);
        $this->rulesHash = md5($this->rules->toJson());

        // 实例化规则
        $this->rules = $this->rules->map(function ($rule) {
            if ($rule["type"] === "category") {
                $rule["compare"] = new CategoryPassCount($rule["compare"]["targetId"], $rule["compare"]["compareOperator"], $rule["compare"]["compareTo"]);
            } else {
                // TODO
            }
            return $rule;
        });

    }

    /**
     * 检查是否通过验证
     * @param $logs
     * @return bool
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function check($logs)
    {
        $result = true;
        $this->rules->each(function ($rule) use (&$result, $logs) {
            if (array_key_exists("logicOperator", $rule)) {
                if ($rule["logicOperator"] === "and") {
                    $result = $result && $rule["compare"]->check($logs);
                } else if ($rule["logicOperator"] === "or") {
                    $result = $result || $rule["compare"]->check($logs);
                } else {
                    // TODO: Invalid Logic Operator
                }
            } else {
                $result = $rule["compare"]->check($logs);
            }
        });
        // 记录开放时间
        if ($result === true) {
            if (!Cache::has($this->teamId . '-' . $this->rulesHash)) {
                Cache::forever($this->teamId . '-' . $this->rulesHash, Carbon::now()->toIso8601String());
            }
        }
        return $result;
    }

    /**
     * 开放至今的时间 单位秒
     * @return int
     * @author Eridanus Sora <sora@sound.moe>
     */
    public function secondsAfterOpen()
    {
        if (!Cache::has($this->teamId . '-' . $this->rulesHash)) {
            return 0;
        } else {
            return Carbon::parse(Cache::get($this->teamId . '-' . $this->rulesHash))->diffInSeconds(Carbon::now());
        }
    }
}