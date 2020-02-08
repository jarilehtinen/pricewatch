<?php

namespace PriceWatch;

class Log
{
    private $log;

    /**
     * Read log
     */
    public function readLog()
    {
        if ($this->log) {
            return $this->log;
        }

        $log = trim(file_get_contents('log.txt'));

        if (!$log) {
            return false;
        }

        $log = explode("\n", trim($log));
        
        foreach ($log as $line) {
            $line = explode('##', $line);

            $date = $line[0];
            $url = $line[1];
            $price = $line[2];

            $this->log[$url][] = $price;
        }

        return $this->log;
    }

    /**
     * Log price
     */
    public function logPrice($url, $title, $price)
    {
        $data = date('Y-m-d H:i:s').'##'.$url.'##'.$price."\n";
        file_put_contents('log.txt', $data, FILE_APPEND);
    }
}
