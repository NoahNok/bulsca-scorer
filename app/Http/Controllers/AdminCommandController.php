<?php

namespace App\Http\Controllers;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\BufferedOutput;

class AdminCommandController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.commands', [
            'authenticated' => $request->session()->get('admin_commands_authenticated', false),
            'commands' => $this->commands(),
        ]);
    }

    public function authenticate(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);
        $configuredPassword = (string) config('app.artisan_gui_password');

        if ($configuredPassword === '' || !hash_equals($configuredPassword, $request->string('password')->toString())) {
            return back()->withErrors(['password' => 'The command console password is incorrect.']);
        }

        $request->session()->put('admin_commands_authenticated', true);

        return redirect()->route('admin.commands');
    }

    public function searchModel(Request $request)
    {
        abort_unless($request->session()->get('admin_commands_authenticated', false), 403);

        $modelClass = $this->modelClassFor($request->string('argument')->toString());
        abort_unless($modelClass !== null, 404);

        $query = $modelClass::query();
        $term = trim($request->string('q')->toString());
        $model = new $modelClass;
        $table = $model->getTable();

        if ($term !== '') {
            $query->where(function ($query) use ($term, $table, $model) {
                foreach (['name', 'title', 'email'] as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $query->orWhere($column, 'like', "%{$term}%");
                    }
                }
                $query->orWhere($model->getKeyName(), $term);
            });
        }

        return $query->limit(30)->get()->map(fn($model) => [
            'id' => $model->getKey(),
            'label' => $this->modelLabel($model),
        ])->values();
    }

    public function execute(Request $request)
    {
        abort_unless($request->session()->get('admin_commands_authenticated', false), 403);

        $commandName = $request->string('command')->toString();
        $command = collect($this->commands())->firstWhere('name', $commandName);
        abort_unless($command, 404);

        $arguments = [];
        foreach ($command['arguments'] as $argument) {
            $value = $request->input("arguments.{$argument['name']}");
            if ($argument['array']) {
                $value = is_array($value) ? $value : ($value === null ? [] : [$value]);
            }
            if ($value !== null && $value !== '') {
                $arguments[$argument['name']] = $value;
            }
        }

        foreach ($command['options'] as $option) {
            $value = $request->input("options.{$option['name']}");
            if ($option['boolean']) {
                if ($request->boolean("options.{$option['name']}")) {
                    $arguments["--{$option['name']}"] = true;
                }
            } elseif ($value !== null && $value !== '') {
                $arguments["--{$option['name']}"] = $value;
            }
        }

        $output = new BufferedOutput();
        try {
            $exitCode = Artisan::call($commandName, $arguments, $output);
            $result = ['type' => $exitCode === 0 ? 'success' : 'error', 'message' => trim($output->fetch())];
        } catch (\Throwable $exception) {
            report($exception);
            $result = ['type' => 'error', 'message' => $exception->getMessage()];
        }

        return redirect()->route('admin.commands', ['command' => $commandName])->with('command_result', $result);
    }

    private function commands(): array
    {
        return collect(Artisan::all())
            ->filter(fn($command) => $command instanceof Command && Str::startsWith(get_class($command), 'App\\Console\\Commands\\'))
            ->map(function (Command $command) {
                $definition = $command->getDefinition();

                return [
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'arguments' => collect($definition->getArguments())->map(fn($argument) => [
                        'name' => $argument->getName(),
                        'description' => $argument->getDescription(),
                        'required' => $argument->isRequired(),
                        'array' => $argument->isArray(),
                        'model' => $this->modelClassFor($argument->getName(), $command),
                    ])->values()->all(),
                    'options' => collect($definition->getOptions())->map(fn($option) => [
                        'name' => $option->getName(),
                        'description' => $option->getDescription(),
                        'required' => $option->isValueRequired(),
                        'boolean' => !$option->acceptValue(),
                    ])->values()->all(),
                ];
            })->sortBy('name')->values()->all();
    }

    private function modelClassFor(string $argumentName, ?Command $command = null): ?string
    {
        $name = Str::singular(Str::beforeLast($argumentName, '_id'));
        $candidate = 'App\\Models\\' . Str::studly($name);

        if (class_exists($candidate) && is_subclass_of($candidate, \Illuminate\Database\Eloquent\Model::class)) {
            return $candidate;
        }

        if ($command) {
            $reflection = new \ReflectionMethod($command, 'handle');
            foreach ($reflection->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($parameter->getName() === $argumentName && $type instanceof \ReflectionNamedType && class_exists($type->getName()) && is_subclass_of($type->getName(), \Illuminate\Database\Eloquent\Model::class)) {
                    return $type->getName();
                }
            }
        }

        return null;
    }

    private function modelLabel(object $model): string
    {
        foreach (['name', 'title', 'email'] as $attribute) {
            if (!empty($model->{$attribute})) {
                return "{$model->{$attribute}} (#{$model->getKey()})";
            }
        }

        return "#{$model->getKey()}";
    }
}
