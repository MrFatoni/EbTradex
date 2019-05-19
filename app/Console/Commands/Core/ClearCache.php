<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all {--only=} {--except=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command clear cache from route, view, config and all cache data from application';


    protected $availableCommands;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->availableCommands = config('commonconfig.available_commands');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!is_null($this->option('only'))) {
            $only = explode(',',$this->option('only'));
            $this->availableCommands = array_only($this->availableCommands, $only);
        } elseif (!is_null($this->option('except'))) {
            $except = explode(',',$this->option('except'));
            $this->availableCommands = array_except($this->availableCommands, $except);
        }
        foreach ($this->availableCommands as $key => $command) {
            $this->call($command);
        }
    }
}
