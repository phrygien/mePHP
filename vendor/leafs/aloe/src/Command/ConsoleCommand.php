<?php

namespace Aloe\Command;

use Psy\Shell;

class ConsoleCommand extends \Aloe\Command
{
    protected static $defaultName = "interact";
    public $description = "Interact with your application";
    public $help = "Interact with your application";

    protected function handle()
    {
        $shell = new Shell();
        $this->writeln($shell->run());
    }
}
