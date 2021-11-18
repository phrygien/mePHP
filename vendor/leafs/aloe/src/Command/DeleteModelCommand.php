<?php

namespace Aloe\Command;

use Aloe\Command;
use Illuminate\Support\Str;

class DeleteModelCommand extends Command
{
    protected static $defaultName = "d:model";
    public $description = "Delete a model";
    public $help = "Delete a model file";

    protected function config()
    {
        $this->setArgument("model", "required", "model name");
    }

    protected function handle()
    {
        $model = Str::studly($this->argument("model"));
        $file = Config::modelsPath("$model.php");

        if (!file_exists($file)) {
            return $this->error("$model doesn't exist!");
        }

        $dirname = dirname($file);

        unlink($file);
        $this->comment("$model deleted successfully");

        $dir = glob("$dirname/*");

        if ($dirname != Config::modelsPath() && count($dir) == 0) {
            if ($this->confirm(asError("> " . explode("/", $model)[0] . " is empty. Delete folder?"))) {
                if (rmdir($dirname)) {
                    $this->comment(explode("/", $model)[0] . " deleted successfully!");
                }
            }
        }
    }
}
