<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Console\ConsoleOptionParser;

class TesterCommand extends Command
{

    protected $root_path = TESTS . DS . 'TestCase';

    protected $path;

    protected $dirs = [['Test Directories']];

    protected $files = [['Test Files']];

    protected $tests = [['Tests']];

    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser
            ->addArguments([
            'dir' => ['help' => 'A directory of tests to run or examine'],
            'file' => ['help' => 'A test file to run or examine'],
            'method' => ['help' => 'A single test to run']
        ])
            ->addOption('in', [
            'help' => 'Examine the content of a directory or file',
            'short' => 'i',
            'boolean' => true
        ]);

        return $parser;
    }

    public function __construct()
    {
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->helper('Table')->output($this->getSuites());
    }

    public function setSuites()
    {
        foreach ($this->Folder->subdirectories() as $dir) {
            $result = (str_replace(TESTS, '', $dir));
            array_push($this->suites, [$result]);
        }
    }

    public function getSuites()
    {
        return $this->suites;
    }

    public function getPath()
    {
        return $this->path ?? $this->root_path;
    }
}
