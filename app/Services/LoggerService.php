<?php

namespace App\Services;

class LoggerService
{
    public function create($level = "INFO", $message = "")
    {
        try {
            $newLog = new \App\SystemLog([
                "level" => $level,
                "message" => $message
            ]);
            $newLog->save();
        } catch (\Exception $e) {

        }
    }
    public function debug($message = ""){
        $this->create("DEBUG", $message);
    }
    public function info($message = ""){
        $this->create("INFO", $message);
    }
    public function notice($message = ""){
        $this->create("NOTICE", $message);
    }
    public function error($message = ""){
        $this->create("ERROR", $message);
    }
    public function critical($message = ""){
        $this->create("CRITICAL", $message);
    }
    public function alert($message = ""){
        $this->create("ALERT", $message);
    }
    public function emergency($message = ""){
        $this->create("EMERGENCY", $message);
    }

}