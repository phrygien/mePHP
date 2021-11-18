<?php

namespace Aloe\Command;

use Aloe\Command;
use Illuminate\Support\Str;

class GenerateConsoleCommand extends Command
{
    protected static $defaultName = "g:command";
    public $description = "Create a new console command";
    public $help = "Create a custom aloe cli command";

    protected function config()
    {
        $this->setArgument("consoleCommand", "required", 'command name');
    }

    protected function handle()
    {
        list($commandName, $className) = $this->mapNames($this->argument("consoleCommand"));

        $file = Config::commandsPath("$className.php");

        if (file_exists($file)) {
            return $this->error("$className already exists!");
        }

        if (file_exists(Config::commandsPath(".init"))) {
            unlink(Config::commandsPath(".init"));
        }

        touch($file);

        $fileContent = \file_get_contents(__DIR__ . '/stubs/console.stub');
        $fileContent = str_replace(['ClassName', 'CommandName'], [$className, $commandName], $fileContent);
        \file_put_contents($file, $fileContent);

        $this->comment("$className generated successfully");

        $aloe = Config::rootpath("leaf");
        $aloeContents = file_get_contents($aloe);
        $aloeContents = str_replace(
            "\$console->register(",
            "\$console->register(\App\Console\\$className::class);
\$console->register(",
            $aloeContents
        );
        \file_put_contents($aloe, $aloeContents);

        $this->comment("$className registered successfully");
    }

    public function mapNames($command)
    {
        $className = $command;

        if (strpos($command, ":")) {
            $commandItems = explode(":", $command);
            $items = [];

            foreach ($commandItems as $item) {
                $items[] = Str::studly($item);
            }

            $className = implode("", $items);
        }

        if (!strpos($className, "Command")) {
            $className .= "Command";
        } else {
            $command = str_replace("Command", "", $command);
        }

        return [Str::lower($command), Str::studly($className)];
    }
}
