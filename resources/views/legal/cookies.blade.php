@extends('legal.layout')

@section('title', 'Cookie Policy')

@section('policy')
    <h3>Cookies</h3>
    <div class="overflow-x-auto">
        <table class="se-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>What it's for</th>
                    <th>How long</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>{{ config('session.cookie') }}</code></td>
                    <td>Keeps you signed in and remembers your session between pages.</td>
                    <td>{{ config('session.lifetime') }} minutes after your last visit</td>
                </tr>
                <tr>
                    <td><code>XSRF-TOKEN</code></td>
                    <td>Security: stops other sites submitting forms as you.</td>
                    <td>Same as the session</td>
                </tr>
                <tr>
                    <td><code>remember_web_…</code></td>
                    <td>Keeps you signed in on this device. Set when you tick "Remember me", and whenever you sign in to
                        Digital Judge with a PIN, so you stay signed in poolside.</td>
                    <td>Up to 400 days, or until you sign out</td>
                </tr>
                <tr>
                    <td><code>__cf_bm</code></td>
                    <td>May be set by Cloudflare, which protects the site, to tell people from bots.</td>
                    <td>30 minutes</td>
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
