<?php
namespace App\Services;

use App\Services\Rules\CategoryPassCount;

/**
 * Class RuleValidator
 * @package App\Services
 * @author Eridanus Sora <sora@sound.moe>
 */
class RuleValidator {
    private $teamId;
    private $rules;

    public function __construct($teamId, $rules){
        $this->teamId = $teamId;
        $this->rules = collect($rules);

        // 实例化规则
        $this->rules = $this->rules->map(function($rule){
            if ($rule["type"] === "category"){
                $rule["compare"] = new CategoryPassCount($rule["compare"]["targetId"], $rule["compare"]["compareOperator"], $rule["compare"]["compareTo"]);
            }
            else{
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
    public function check($logs){
        $result = true;
        $this->rules->each(function($rule) use (&$result, $logs) {
            if (array_key_exists("logicOperator", $rule)){
                if ($rule["logicOperator"] === "and"){
                    $result = $result && $rule["compare"]->check($logs);
                }
                else if ($rule["logicOperator"] === "or"){
                    $result = $result || $rule["compare"]->check($logs);
                }
                else{
                    // TODO: Invalid Logic Operator
                }
            }
            else{
                $result = $rule["compare"]->check($logs);
            }
        });
        return $result;
    }
}