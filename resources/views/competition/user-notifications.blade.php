@extends('layout')

@section('title')
    Notifications | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.teams', $comp) }}">Notifications</a>
    </div>
@endsection

@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4" x-data="{
            permissionStatus: Notification.permission,
            get allowed() {
                return this.permissionStatus == 'granted'
            },
        
            watchForChange() {
                setInterval(() => {
                    this.permissionStatus = Notification.permission
                }, 500)
            },
        
            async initPushNotifs() {
                if (!navigator.serviceWorker.ready) {
                    return
                }
        
        
        
        
                const status = await Notification.requestPermission()
        
                if (status == 'granted') {
                    this.subscribeUser()
                }
            },
        
            subscribeUser() {
                navigator.serviceWorker.ready
                    .then((registration) => {
                        const subscribeOptions = {
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array(
                                'BPoYue6lS_kql4tTqsJKYEaQxjF6tO8AM6lTHKfQWBPnmSh4TJ03fTAHAJaPs0AvomJ6avT-6M8hJl8mhg6tbm8'
                            ),
                        };
        
                        return registration.pushManager.subscribe(subscribeOptions);
                    })
                    .then((pushSubscription) => {
                        this.storePushSubscription(pushSubscription);
                    });
            },
        
            storePushSubscription(pushSubscription) {
                const token = document
                    .querySelector('meta[name=csrf-token]')
                    .getAttribute('content');
        
                fetch('{{ route('comps.notifications.push-store', $comp) }}', {
                        method: 'POST',
                        body: JSON.stringify(pushSubscription),
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': token,
                        },
                    })
                    .then((res) => {
                        return res.json();
                    })
                    .then((res) => {
        
                    })
                    .catch((err) => {
                        alert(`Failed to store notification subscription. (You won't recieve any notifications)`)
                    });
            },
        
            urlBase64ToUint8Array(base64String) {
                var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
                var base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/');
        
                var rawData = window.atob(base64);
                var outputArray = new Uint8Array(rawData.length);
        
                for (var i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            },
        
            unsubscribe() {
        
        
        
                if (Notification.permission == 'granted') {
                    // Get the service worker registration
                    navigator.serviceWorker.getRegistration().then(function(reg) {
                        if (reg) {
                            reg.pushManager.getSubscription().then(function(subscription) {
                                if (subscription) {
                                    // Unsubscribe from push notifications
                                    subscription.unsubscribe().then(function() {
                                        console.log('removed subscription')
                                    }).catch(function(error) {
        
                                    });
                                } else {
        
                                }
                            });
                        } else {
        
                        }
                    });
                } else {
        
                }
            },
        
        
        
        
        
        
        
        
        }" x-init="watchForChange()">
            <div class="flex justify-between">
                <h2 class="mb-0">Notifications</h2>

            </div>
            <div x-show="allowed" x-cloak>
                <p>You have notifications <span class=" text-green-600 font-bold">enabled</span> on <span
                        class=" underline font-bold">this
                        device.</span></p>
                <br>

                <button class="btn" @click="subscribeUser()">Send test notification</button>

                {{-- <button class="btn btn-danger" @click="unsubscribe">Disable
                    Notifications</button> --}}


            </div>

            <div x-show="!allowed" x-cloak>

                <div x-show="permissionStatus == 'denied'">
                    <p>You have previously <strong>denied</strong> notifications. To enabled them please allow them for
                        this website in your browsers settings.</p>
                </div>
                <div x-show="permissionStatus == 'default'">
                    <button class="btn btn-success" @click="initPushNotifs">Enable Notification</button>
                </div>


            </div>


        </div>


    </div>
@endsection
