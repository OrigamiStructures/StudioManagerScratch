<?php
namespace App\Command;

use Cake\Collection\Collection;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Console\ConsoleOptionParser;
use function Couchbase\passthruDecoder;

class TesterCommand extends Command
{
    protected $IO;

    /**
     * @var string default path to tests
     */
    protected $root_path = TESTS . DS . 'TestCase' . DS;

    /**
     * @var string full path to user requested directory
     */
    protected $requestPath;

    /**
     * @var string user's requested directory
     */
    protected $requestDir;

    /**
     * @var string user's requested test file
     */
    protected $requestFile;

    /**
     * @var string user's requested test method
     */
    protected $requestMethod;

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
    protected $dirs = ['Test Directories'];

    /**
     * @var array accessible files at this level
     */
    protected $files = ['Test Files'];

    /**
     * @var array public test methods in the chosen file
     */
    protected $tests = ['Tests'];

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
        $this->IO = $io;
        $this->populateArgs($args);

        /*
         * If a is not mentioned and the user wants to inspect
         */
        if (is_null($this->requestMethod) && $this->inspect) {
            $this->doInspection();
            $this->renderList($this->dirs);
            $this->renderList($this->files);
            $this->renderList($this->tests);
            $this->IO->out("\n");
        /*
         * If a method is mentioned, we run it no matter what user wants on inspection
         */
        } elseif (!is_null($this->requestMethod)) {
            $this->inspect = false;
            $this->commands[] = $this->getCommand($this->requestDir, $this->requestFile, $this->requestMethod);
            $this->IO->out('Got to run one test method');
        /*
         * If a file is mentioned, run its tests
          */
        } elseif (!is_null($this->requestFile)) {
            $this->commands[] = $this->getCommand($this->requestDir, $this->requestFile);
        /*
         * Run the files in a directory
         */
        } else {
            $this->readDirectory();
            $fileList = $this->files;
            array_shift($fileList);
            foreach ($fileList as $file) {
                $this->commands[] = $this->getCommand('', $file);
            }
        }
        /*
         * Process any commands that were compiled
         */
        if (!$this->inspect) {
            $this->renderTests();
        }
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
        if (!is_null($this->requestFile)) {
            $methods = new Collection(get_class_methods($this->fileClassName()));
            $tests = $methods->filter(function($methodName, $index) {
                return substr($methodName, 0, 4) === 'test';
            });
            foreach ($tests->toArray() as $test) {
                array_push($this->tests, "$this->requestDir $this->requestFile $test");
            }
        }
    }

    public function fileClassName()
    {
        return str_replace('/', '\\','/App/Test/TestCase/' . $this->requestDir . $this->requestFile);
    }
    /**
     * Set the directory and file lists for display
     *
     * Looking at the current path, assemble the list of
     * accessible sub-directories and test files
     */
    public function readDirectory($path = null)
    {
        $path = $path ?? $this->requestDir;
        $Folder = new Folder($this->getRequestPath());
        $content = $Folder->read();
        foreach ($content[0] as $dir) {
            $nextDir = $path . $dir . DS;
            array_push($this->dirs, $nextDir);
//            if ($this->recursive) {
//                var_dump($nextDir);
//                $this->readDirectory($nextDir);
//            }
        }
        foreach ($content[1] as $file) {
            array_push(
                $this->files,
               $path . ' ' . str_replace('.php', '', $file)
            );
        }
    }

    /**
     * Returns the current full path
     * The requested path or the default root pat
     *
     * @return string
     */
    public function getRequestPath()
    {
        return $this->requestPath ?? $this->root_path;
    }

    /**
     * Set the full path to the requested directory
     * path always end with DS character
     */
    protected function setRequestPath()
    {
        $this->requestPath = $this->root_path . $this->requestDir;
    }

    public function populateArgs(Arguments $args)
    {
        $d = trim($args->getArgument('dir'), DS);
        $this->requestDir =  !empty($d) ? $d . DS : null;
        $f = str_replace('.php', '', $args->getArgument('file'));
        $this->requestFile = !empty($f) ? $f : null;
        $this->requestMethod = $args->getArgument('method');
        $this->inspect = $args->getOption('inspect');
        $this->recursive = $args->getOption('recursive');
        $this->setRequestPath();
    }

    //<editor-fold desc="RENDERING">

    public function renderList($list)
    {
        $this->IO->info("\n##" . array_shift($list) . '##');
        $this->IO->out(implode("\n", $list));
    }

    public function renderTest($result)
    {
        if (stristr($result, 'Failure') || stristr($result, 'Error')) {
            $this->IO->error($result);
        } else {
            $this->IO->success($result);
        }
    }
    public function renderTests()
    {
        foreach ($this->commands as $command) {
            $this->IO->quiet($command);
            $result = exec($command);
            $this->renderTest($result);
            $this->IO->verbose(shell_exec($command));
        }
    }

    //</editor-fold>


}
