@extends('layouts.admin')

@section('title')
    Command console
@endsection

@section('content')
    <div x-data="commandConsole({{ Js::from($commands) }}, {{ Js::from(request('command')) }})" class="max-w-5xl">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-500">Admin tools</p>
                <h2 class="mb-1">Command console</h2>
                <p class="text-gray-600">Run application commands with their declared arguments.</p>
            </div>
            <span class="text-xs uppercase tracking-widest text-gray-500">{{ count($commands) }} commands</span>
        </div>

        @if (!$authenticated)
            <form method="POST" action="{{ route('admin.commands.authenticate') }}"
                class="max-w-md border-t-4 border-se bg-white p-6 shadow-sm">
                @csrf
                <h3 class="mb-2">Unlock command console</h3>
                <p class="text-sm text-gray-600 mb-5">Enter the console password configured on the server.</p>
                <label class="block text-sm font-semibold mb-1" for="password">Password</label>
                <input class="w-full mb-3" id="password" name="password" type="password" required autofocus>
                @error('password')
                    <p class="text-red-600 text-sm mb-3">{{ $message }}</p>
                @enderror
                <button class="btn" type="submit">Unlock</button>
            </form>
        @else
            @if (session('command_result'))
                <pre
                    class="mb-6 whitespace-pre-wrap border-l-4 {{ session('command_result.type') === 'success' ? 'border-green-500' : 'border-red-500' }} bg-white p-4 text-sm">{{ session('command_result.message') ?: 'Command completed without output.' }}</pre>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
                <div>
                    <input class="w-full mb-3" type="search" placeholder="Search commands..." x-model="filter">
                    <div class="space-y-1">
                        <template x-for="command in filteredCommands" :key="command.name">
                            <button type="button"
                                class="block w-full text-left border-b border-gray-200 px-3 py-3 hover:bg-gray-100"
                                :class="selected?.name === command.name ? 'bg-gray-100 border-l-4 border-se' : ''"
                                @click="select(command)">
                                <strong class="block" x-text="command.name"></strong>
                                <span class="block text-sm text-gray-500" x-text="command.description || 'No description'">
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.commands.execute') }}" x-show="selected" x-cloak
                    @submit="running = true">
                    @csrf
                    <input type="hidden" name="command" :value="selected?.name">
                    <h3 class="mb-1" x-text="selected?.name"></h3>
                    <p class="text-sm text-gray-600 mb-5" x-text="selected?.description"></p>
                    <template x-for="argument in selected?.arguments || []" :key="argument.name">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-1" :for="'argument-' + argument.name"
                                x-text="argument.name + (argument.required ? ' *' : '')"></label>
                            <template x-if="argument.model">
                                <div class="relative">
                                    <input class="w-full" type="search" :id="'argument-' + argument.name"
                                        :placeholder="'Search ' + argument.model.split('\\\\').pop() + '... '"
                                        x-model="argument.search" @input.debounce.300ms="searchModel(argument)">
                                    <input type="hidden" :name="'arguments[' + argument.name + ']'"
                                        :value="argument.value">
                                    <div class="absolute z-10 w-full bg-white border border-gray-300"
                                        x-show="argument.results?.length" @click.outside="argument.results = []">
                                        <template x-for="result in argument.results" :key="result.id">
                                            <button type="button"
                                                class="block w-full text-left px-3 py-2 hover:bg-gray-100"
                                                @click="argument.value = result.id; argument.search = result.label; argument.results = []"
                                                x-text="result.label"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!argument.model">
                                <input class="w-full" :id="'argument-' + argument.name"
                                    :name="'arguments[' + argument.name + ']'" :required="argument.required"
                                    type="text">
                            </template>
                            <small class="text-gray-500" x-text="argument.description"></small>
                        </div>
                    </template>
                    <template x-for="option in selected?.options || []" :key="option.name">
                        <div class="mb-4">
                            <template x-if="option.boolean">
                                <label class="flex items-center gap-2 text-sm font-semibold">
                                    <input type="checkbox" :name="'options[' + option.name + ']'" value="1">
                                    <span x-text="'--' + option.name"></span>
                                </label>
                            </template>
                            <template x-if="!option.boolean">
                                <label class="block text-sm font-semibold">
                                    <span class="block mb-1" x-text="'--' + option.name"></span>
                                    <input class="w-full" type="text" :name="'options[' + option.name + ']'"
                                        :required="option.required">
                                </label>
                            </template>
                        </div>
                    </template>
                    <button class="btn" type="submit" :disabled="running"><span
                            x-text="running ? 'Running...' : 'Run command'"></span></button>
                </form>
            </div>
        @endif
    </div>

    @if ($authenticated)
        <script>
            function commandConsole(commands, initialCommand) {
                return {
                    commands,
                    filter: '',
                    selected: commands.find(command => command.name === initialCommand) || commands[0],
                    running: false,
                    get filteredCommands() {
                        return this.commands.filter(command => `${command.name} ${command.description}`.toLowerCase()
                            .includes(this.filter.toLowerCase()));
                    },
                    select(command) {
                        this.selected = JSON.parse(JSON.stringify(command));
                    },
                    async searchModel(argument) {
                        if (!argument.search || argument.search.length < 2) {
                            argument.results = [];
                            return;
                        }
                        const response = await fetch(
                            `{{ route('admin.commands.search-model') }}?argument=${encodeURIComponent(argument.name)}&q=${encodeURIComponent(argument.search)}`
                            );
                        argument.results = await response.json();
                    }
                };
            }
        </script>
    @endif
@endsection
