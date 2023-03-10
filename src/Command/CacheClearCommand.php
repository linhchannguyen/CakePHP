<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Cache\Cache;

class CacheClearCommand extends AppCommand {

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $config_list = Cache::configured();
        foreach ($config_list as $value) {
            Cache::clear($value);
        }
  }
}
