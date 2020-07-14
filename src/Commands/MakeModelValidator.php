<?php

namespace ModelValidator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModelValidator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:validator {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create A New Eloquent Model Validator';

    /**
     * Class to create.
     *
     * @var array|string
     */
    protected $class;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * MakeModelValidator constructor.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeClassName()->compileStub();
        $this->info(class_basename($this->getClassName()).' Created Successfully!');
    }

    public function compileStub()
    {
        if ($this->files->exists($path = $this->getPath())) {
            $this->error("\n\n\t".$path.' Already Exists!'."\n");
            die;
        }
        $this->makeDirectory($path);

        $stubPath = config('modelvalidator.generator.stub', __DIR__.'/../stubs/modelvalidator.stub');

        if (! $this->files->exists($stubPath) || ! is_readable($stubPath)) {
            $this->error(sprintf('File "%s" does not exist or is unreadable.', $stubPath));
            die;
        }

        $tmp = $this->applyValuesToStub($this->files->get($stubPath));
        $this->files->put($path, $tmp);
    }

    public function applyValuesToStub($stub)
    {
        $className = $this->getClassBasename($this->getClassName());
        $modelName = $this->argument('name');
        $modelNameCamel = Str::camel($modelName);

        $search = ['{{class}}', '{{namespace}}', '{{model}}', '{{model_camel}}'];
        $replace = [$className, str_replace('\\'.$className, '', $this->getClassName()), $modelName, $modelNameCamel];

        return str_replace($search, $replace, $stub);
    }

    private function getClassBasename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    public function getPath()
    {
        return $this->laravel->path.DIRECTORY_SEPARATOR.$this->getFileName();
    }

    public function getFileName()
    {
        return str_replace([$this->getAppNamespace(), '\\'], ['', DIRECTORY_SEPARATOR], $this->getClassName().'.php');
    }

    public function getAppNamespace()
    {
        return $this->laravel->getNamespace();
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Create Validator Class Name.
     *
     * @return $this
     */
    public function makeClassName()
    {
        $parts = array_map([Str::class, 'studly'], explode('\\', $this->argument('name')));
        $className = array_pop($parts);
        $ns = count($parts) > 0 ? implode('\\', $parts).'\\' : '';

        $fqClass = config('modelvalidator.namespace', 'App\\ModelValidators\\').$ns.$className;

        if (substr($fqClass, -6, 6) !== 'Validator') {
            $fqClass .= 'Validator';
        }

        if (class_exists($fqClass)) {
            $this->error("\n\n\t$fqClass Already Exists!\n");
            die;
        }

        $this->setClassName($fqClass);

        return $this;
    }

    public function setClassName($name)
    {
        $this->class = $name;

        return $this;
    }

    public function getClassName()
    {
        return $this->class;
    }
}
