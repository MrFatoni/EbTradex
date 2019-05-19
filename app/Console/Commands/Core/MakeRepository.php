<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('interface') && $this->option('model')) {
            return __DIR__ . '/Stubs/repository.nested.stub';
        } else if ($this->option('interface')) {
            return __DIR__ . '/Stubs/repository.interface.stub';
        } else if ($this->option('model')) {
            return __DIR__ . '/Stubs/repository.model.stub';
        }

        return __DIR__ . '/Stubs/repository.plain.stub';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['interface', 'i', InputOption::VALUE_OPTIONAL, 'Generate a interface for the repository.'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
        ];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    protected function buildClass($name)
    {
//        $repositoryNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('interface')) {
            $replace = $this->buildInterfaceReplacements($replace);
        }

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

//        $replace["use {$repositoryNamespace}\BaseRepository;\n"] = 'BaseRepositoryNamespace;';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @param  array $replace
     * @return array
     */
    protected function buildInterfaceReplacements(array $replace)
    {
        $interface = $this->parseInterface($this->option('interface'));
        if (!interface_exists($interface)) {
            if ($this->confirm("A {$interface} Interface does not exist. Do you want to generate it?", true)) {
                $this->call('make:interface', ['name' => $interface]);
            }
        }

        return array_merge($replace, [
            'DummyInterfaceNamespace' => $interface,
            'DummyInterface' => class_basename($interface),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param $interface
     * @return string
     */
    protected function parseInterface($interface)
    {
        if (preg_match( '([^A-Za-z0-9_/\\\\])', $interface)) {
            throw new InvalidArgumentException('Interface name contains invalid characters.');
        }

        $interface = trim(str_replace('/', '\\', $interface), '\\');

        if (!Str::startsWith($interface, $rootNamespace = $this->laravel->getNamespace())) {
            $repositoryPath =trim(str_replace('/', '\\', $this->getNameInput()), '\\');
            $interface = str_replace($repositoryPath,$interface,$this->qualifyClass($this->getNameInput()));
        }

        return $interface;
    }

    /**
     * Build the model replacement values.
     *
     * @param  array $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyModelNamespace' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }
        return $model;
    }
}
