@extends('digitaljudge.mpa-layout')
@section('title')
    Judge
@endsection
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />

';
@endphp
@section('content')
    <div class="flex flex-col space-y-3" x-data="{
        me: '{{ $meId }}',
    
        liveListen() {
            this.echo.listen('.judge.status-update', (e) => {
                // event data has JudgeId and status
                const user = this.users.find(u => u.id === e.judgeId);
                if (user) {
                    user.status = e.status;
                    user.state = 'active';
    
                    // set new timeout to set idle after 5s
                    if (user.timeout) {
                        clearTimeout(user.timeout);
                    }
    
    
                    user.timeout = setTimeout(() => {
                        user.state = 'idle';
                    }, 5000);
    
    
                } else {
                    console.warn('Received status update for unknown user:', e.judgeId);
                }
    
            });
    
    
    
        },
    
        stateToColor(state) {
            switch (state) {
                case 'active':
                    return 'bg-green-500';
                case 'idle':
                    return 'bg-orange-400';
                default:
                    return 'bg-gray-400';
            }
        },
    
    }" x-init="liveListen">


        <div class="flex justify-between">
            <div class=" flex space-x-2 items-center">
                <p>Active</p>
                <div class="size-3  bg-green-500 rounded-full animate-pulse"></div>
            </div>
            <div class=" flex space-x-2 items-center">
                <p>Idle</p>
                <div class="size-3  bg-orange-400 rounded-full animate-pulse"></div>
            </div>
            <div class=" flex space-x-2 items-center">
                <p>Unknown</p>
                <div class="size-3  bg-gray-400 rounded-full animate-pulse"></div>
            </div>
        </div>

        <template x-for="user in users.filter(u => u.id != me)">
            i
            <div class="se-card se-card-hover se-card-body">
                <div class="flex items-center justify-between h-full">
                    <div class="text-left">
                        <h2 x-text="user.name"></h2>
                        <p x-text="user.status ?? 'Unknown'"></p>
                    </div>


                    <div :class="stateToColor(user.state)" class="size-6 rounded-full animate-pulse"></div>







                </div>
            </div>

            <div>
                <p x-text="user.name"></p>
                <p x-text="user.status ? user.status : 'Unknown'"></p>

            </div>
        </template>

        <template x-if="users.filter(u => u.id != me).length ==0 ">
            <p>No officials active.</p>
        </template>






    </div>

    <div class="text-center mt-6">
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
    </div>
@endsection
