@extends('legal.layout')

@section('title', 'Cookie Policy')

@section('policy')
    <h3>Cookies</h3>
    {{-- se-table right-aligns cells for numbers; this one is all text --}}
    <div class="se-table">
        <table>
            <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-left">What it's for</th>
                    <th class="text-left">How long</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-left whitespace-nowrap"><code>{{ config('session.cookie') }}</code></td>
                    <td class="text-left">Keeps you signed in and remembers your session between pages.</td>
                    <td class="text-left">{{ config('session.lifetime') }} minutes after your last visit</td>
                </tr>
                <tr>
                    <td class="text-left whitespace-nowrap"><code>XSRF-TOKEN</code></td>
                    <td class="text-left">Security: stops other sites submitting forms as you.</td>
                    <td class="text-left">Same as the session</td>
                </tr>
                <tr>
                    <td class="text-left whitespace-nowrap"><code>remember_web_…</code></td>
                    <td class="text-left">Keeps you signed in on this device. Set when you tick "Remember me", and whenever you sign in to
                        Digital Judge with a PIN, so you stay signed in poolside.</td>
                    <td class="text-left">Up to 400 days, or until you sign out</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3>Browser storage</h3>
    <p>Some pages also store small amounts of data on your device, which never leaves it:</p>
    <ul>
        <li>Digital Judge remembers the competition you're in and your role, and which alerts and announcements you've
            read or dismissed.</li>
        <li>Legacy Digital Judge remembers the name you entered.</li>
        <li>Some pages remember display choices, such as how heats are shown, or whether you've seen a tutorial.</li>
        <li>If you turn on notifications, your browser registers a small background script (a service worker) to show
            them.</li>
    </ul>

    <h3>Managing cookies</h3>
    <p>You can delete or block cookies in your browser's settings. If you block them, you won't be able to sign in.
    </p>
@endsection
