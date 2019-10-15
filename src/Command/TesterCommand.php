<?php
namespace App\Command;

use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Console\ConsoleOptionParser;

class TesterCommand extends Command
{

    /**
     * @var string default path to tests
     */
    protected $root_path = TESTS . DS . 'TestCase' . DS;

    /**
     * @var string full path to user requested directory
     */
    protected $path;

    /**
     * @var string user's requested directory
     */
    protected $dir;

    /**
     * @var string user's requested test file
     */
    protected $file;

    /**
     * @var string user's requested test method
     */
    protected $method;

    /**
     * @var boolean user's inspection option choice
     */
    protected $inspect;

    /**
     * @var boolean user's recursive option choice
     */
    protected $recursive;

    /**
     * @var array accessible directories at this level
     */
    protected $dirs = [['Test Directories']];

    /**
     * @var array accessible files at this level
     */
    protected $files = [['Test Files']];

    /**
     * @var array public test methods in the chosen file
     */
    protected $tests = [['Tests']];

    protected $commands = [];

    /**
     * @param ConsoleOptionParser $parser
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser
        ->addArguments([
            'dir' => ['help' => 'A directory of tests to run or examine'],
            'file' => ['help' => 'A test file to run or examine'],
            'method' => ['help' => 'A single test to run']
        ])
        ->addOption('inspect', [
            'help' => 'Examine the content of a directory or file',
            'short' => 'i',
            'boolean' => true
        ])
        ->addOption('recursive', [
            'help' => 'Run tests in subdirectories too',
            'short' => 'r',
            'boolean' => true
            ])
        ->setDescription("Run a test suite, test file, or a single test.");

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->populateArgs($args);

        /*
         * If a is not mentioned and the user wants to inspect
         */
        if (is_null($this->method) && $this->inspect) {
            $this->doInspection();
            $io->helper('Table')->output($this->dirs);
            $io->helper('Table')->output($this->files);
            $io->helper('Table')->output($this->tests);
        /*
         * If a method is mentioned, we run it no matter what user wants on inspection
         */
        } elseif (!is_null($this->method)) {
            $this->inspect = false;
            $this->commands[] = $this->getCommand($this->dir, $this->file, $this->method);
            $io->out('Got to run one test method');
        /*
         * If a file is mentioned, run its tests
          */
        } elseif (!is_null($this->file)) {
            $this->commands[] = $this->getCommand($this->dir, $this->file);
        } else {
            $this->readDirectory();
            $fileList = $this->files;
            array_shift($fileList);
            foreach ($fileList as $file) {
                $this->commands[] = $this->getCommand('', $file[0]);
            }
            var_dump($this->commands);
            $io->out('Got to run a whole suite');
        }
        if (!$this->inspect) {
            foreach ($this->commands as $command) {
                $io->out($command);
                $io->out(exec($command));
            }
        }
//        $io->out('thinking');
//        $io->out(passthru('vendor/bin/phpunit test tests/TestCase/Model/Table/ArtStacksTableTest.php'));
    }

    public function getCommand($dir, $file = null, $test = null) {
        $testFile = str_replace(' ', '', 'tests/TestCase/' . $dir . $file . '.php');

        if (!is_null($test)) {
            $filter = " --filter $test";
        } else {
            $filter = '';
        }
        return "vendor/bin/phpunit test $testFile $filter";
    }


    public function doInspection()
    {
        $this->readDirectory();
        if (!is_null($this->file)) {
            $methods = new Collection(get_class_methods($this->fileClassName()));
            $tests = $methods->filter(function($methodName, $index) {
                return substr($methodName, 0, 4) === 'test';
            });
            foreach ($tests->toArray() as $test) {
                array_push($this->tests, ["$this->dir $this->file $test"]);
            }
        }
    }

    public function fileClassName()
    {
        return str_replace('/', '\\','/App/Test/TestCase/' . $this->dir . $this->file);

    }
    /**
     * Set the directory and file lists for display
     *
     * Looking at the current path, assemble the list of
     * accessible sub-directories and test files
     */
    public function readDirectory()
    {
        $Folder = new Folder($this->getPath());
        $content = $Folder->read();
        foreach ($content[0] as $dir) {
            array_push($this->dirs, [$this->dir . $dir . DS]);
        }
        foreach ($content[1] as $file) {
            array_push(
                $this->files,
               [$this->dir . ' ' . str_replace('.php', '', $file)]
            );
        }
    }

    /**
     * Returns the current full path
     *
     * The requested path or the default root pat
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path ?? $this->root_path;
    }

    /**
     * Set the full path to the requested directory
     *
     * path always end with DS character
     */
    protected function setPath()
    {
        $this->path = $this->root_path . $this->dir;
    }

    public function populateArgs(Arguments $args)
    {
        $d = trim($args->getArgument('dir'), DS);
        $this->dir =  !empty($d) ? $d . DS : null;
        $f = str_replace('.php', '', $args->getArgument('file'));
        $this->file = !empty($f) ? $f : null;
        $this->method = $args->getArgument('method');
        $this->inspect = $args->getOption('inspect');
        $this->recursive = $args->getOption('recursive');
        $this->setPath();
    }
}
