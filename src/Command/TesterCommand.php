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
    private $io;

    private $args;

    /**
     * @var string default path to tests
     */
    protected $root_path = TESTS . 'TestCase' . DS;

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

    protected $errors = ['Tests with failures and errors'];

    protected $warnings = ['Tests that did not run due to errors'];

    /**
     * @param ConsoleOptionParser $parser
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $desc = <<<DESC

----------------------------------------------
Run a test suite, test file, or a single test.
----------------------------------------------
DESC;
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
        ->setDescription($desc);

        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->io = $io;
        $this->args = $args;
        $this->populateArgs($args);

        /*
         * If a is not mentioned and the user wants to inspect
         */
        if (is_null($this->requestMethod) && $this->inspect) {
            $this->doInspection();
            $this->renderList($this->dirs);
            $this->renderList($this->files);
            $this->renderList($this->tests);
            $this->io->out("\n");
        /*
         * If a method is mentioned, we run it no matter what user wants on inspection
         */
        } elseif (!is_null($this->requestMethod)) {
            $this->inspect = false;
            $this->commands[] = $this->getCommand($this->getPathArg(), $this->requestFile, $this->requestMethod);
            $this->io->out('Got to run one test method');
        /*
         * If a file is mentioned, run its tests
          */
        } elseif (!is_null($this->requestFile)) {
            $this->commands[] = $this->getCommand($this->getPathArg(), $this->requestFile);
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
                array_push($this->tests, "{$this->getPathArg()} $this->requestFile $test");
            }
        }
    }

    public function fileClassName()
    {
        return str_replace('/', '\\','/App/Test/TestCase/' . $this->getPathArg() . $this->requestFile);
    }
    /**
     * Set the directory and file lists for display
     *
     * Looking at the current path, assemble the list of
     * accessible sub-directories and test files
     */
    public function readDirectory($path = null)
    {
        $path = $path ?? $this->getPathArg();
        $Folder = new Folder($this->getFullPath($path));
        $content = $Folder->read();
        foreach ($content[0] as $dir) {
            $nextDir = $path . $dir . DS;
            array_push($this->dirs, $nextDir);
            if ($this->recursive) {
                $this->readDirectory($nextDir);
            }
        }
        foreach ($content[1] as $file) {
            if (substr($file, -8, 4) === 'Test') {
                array_push(
                    $this->files,
                    $path . ' ' . str_replace('.php', '', $file)
                );
            }
        }
    }

    public function populateArgs(Arguments $args)
    {
        $f = str_replace('.php', '', $args->getArgument('file'));
        $this->requestFile = !empty($f) ? $f : null;
        $this->requestMethod = $args->getArgument('method');
        $this->inspect = $args->getOption('inspect');
        $this->recursive = $args->getOption('recursive');
    }

    //<editor-fold desc="PATH GENERATION">

    /**
     * get a path argument for tester (arg 1)
     *
     * These relative paths are always manipulated to have a trailing DS.
     *
     * @param string|null $path
     * @return string|null
     */
    protected function getPathArg($path = null) {
        $path = $path ?? $this->args->getArgument('dir');
        $trimmedPath = trim($path, DS);
        return !empty($trimmedPath) ? $trimmedPath . DS : null;
    }

    /**
     * Returns the current full path
     * The requested path or the default root pat
     *
     * @return null|string a path argument
     */
    public function getFullPath($path = null)
    {
        return $this->root_path . $this->getPathArg($path);
    }

    //</editor-fold>

    //<editor-fold desc="RENDERING">

    public function renderList($list)
    {
        $this->io->info("\n##" . array_shift($list) . '##');
        $this->io->out(implode("\n bin/cake tester ", $list));
    }

    public function renderTest($result, $command)
    {
        if (stristr($result, 'Failure') || stristr($result, 'Error')) {
            $this->io->error($result);
            $this->errors[] = $command;
        } elseif (stristr($result, 'Class \'test\' could not be found in')) {
            $this->io->warning($result);
            $this->warnings[] = $command;
        } else {
            $this->io->success($result);
            return true;
        }
    }
    public function renderTests()
    {
        $errors = ['Tests with errors and failures'];
        $this->io->out("\n");
        foreach ($this->commands as $command) {
            $this->io->quiet($command);
            $result = exec($command);
            $this->renderTest($result, $command);
            $this->io->verbose(shell_exec($command));
        }
        $this->renderList($this->errors);
        $this->renderList($this->warnings);
        $this->io->out("\n");
    }

    //</editor-fold>


}
