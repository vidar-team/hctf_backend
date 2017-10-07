<?php

namespace App\Services\Rules;

class CategoryPassCount implements BaseRule
{
    private $categoryId;
    private $compareOperator;
    private $compareTo;

    public function __construct($categoryId, $compareOperator, $compareTo)
    {
        $this->categoryId = $categoryId;
        $this->compareOperator = $compareOperator;
        $this->compareTo = $compareTo;
    }

    public function check($logs)
    {
        $passCount = collect($logs)->filter(function ($log) {
            return $log->category_id === $this->categoryId;
        })->count();
        if ($this->compareOperator === "gt") {
            return $passCount > $this->compareTo;
        } else if ($this->compareOperator === "lt") {
            return $passCount < $this->compareTo;
        } else {
            return $passCount === $this->compareTo;
        }
    }
}