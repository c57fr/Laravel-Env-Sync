<?php
/**
 * Laravel-Env-Sync
 *
 * @author Julien Tant - Craftyx <julien@craftyx.fr>
 */

namespace Jtant\LaravelEnvSync\Console;

use Illuminate\Console\Command;
use Jtant\LaravelEnvSync\Reader\ReaderInterface;
use Jtant\LaravelEnvSync\SyncService;

class DiffCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:diff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the difference between env files';

    /**
     * @var ReaderInterface
     */
    private $reader;


    /**
     * Create a new command instance.
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        parent::__construct();
        $this->reader = $reader;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $env = base_path('.env');
        $example = base_path('.env.example');

        $envValues = $this->reader->read($env);
        $exampleValues = $this->reader->read($example);

        $keys = array_unique(array_merge(array_keys($envValues), array_keys($exampleValues)));
        sort($keys);

        $header = ["Key", basename($env), basename($example)];
        $lines = [];
        foreach ($keys as $key) {
            $envVal = isset($envValues[$key]) ? $envValues[$key] : '<error>NOT FOUND</error>';
            $exampleVal = isset($exampleValues[$key]) ? $exampleValues[$key] : '<error>NOT FOUND</error>';
            $lines[] = [$key, $envVal, $exampleVal];
        }

        $this->table($header, $lines);
    }
}
