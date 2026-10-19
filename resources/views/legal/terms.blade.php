@extends('legal.layout')

@section('title', 'Terms of Service')

@section('summary')
    <ul>
        <li>{{ config('legal.trading_as') }} is a free tool for running and scoring lifesaving competitions.</li>
        <li>Official results are whatever the organiser and referee declare, not whatever the software shows.</li>
        <li>Organisers are responsible for the competitor data they upload, including consent for under-18s.</li>
        <li>You must be 16 or over to have an account.</li>
        <li>We provide the service as it is, and limit our liability as far as UK law allows.</li>
    </ul>
@endsection

@section('policy')
    <h3>1. Who we are</h3>
    <p>{{ config('legal.trading_as') }} ("we", "us") is run by {{ config('legal.operator') }}, a sole trader based in
        the United Kingdom. These terms apply to everyone who uses {{ config('legal.trading_as') }}, including the main
        site, Digital Judge, the live, results and stats sites, and WhatIf. By using the service, or by signing in, you
        agree to them.</p>

    <h3>2. The service</h3>
    <p>{{ config('legal.trading_as') }} lets organisers set up competitions, enter teams and competitors, run heats and
        draws, collect scores from officials, and publish results. It is free to use. We may change, add or remove
        features, and we may stop offering the service, giving reasonable notice where we can.</p>

    <h3>3. Accounts</h3>
    <ul>
        <li>You must be 16 or over to hold an account. Organisers who involve officials aged 16 or 17 are responsible for
            supervising them.</li>
        <li>An organiser may create an account for an official using their name and email address. We'll email that
            person to let them know.</li>
        <li>Keep your sign-in details to yourself and tell us if you think someone else has used your account.</li>
        <li>You're responsible for what's done with your account.</li>
    </ul>

    <h3>4. Results</h3>
    <p>{{ config('legal.trading_as') }} is a tool to help organisers and officials. The official result of a
        competition is whatever the organiser and referee declare under the rules of that competition. Disputes about
        marks, times, placings, disqualifications or penalties go to the organiser and referee, not to us. We do our best
        to keep the software accurate, but we don't guarantee that scores, rankings or calculations are free from
        errors.</p>

    <h3>5. Organisers and competitor data</h3>
    <p>When you upload or enter data about competitors, teams or officials, you're the controller of that data and we
        process it on your behalf to run your competition. You agree that:</p>
    <ul>
        <li>you have a lawful basis under UK data protection law to share the data with us and to publish results;</li>
        <li>you've told competitors that their names, clubs and results will be published on
            {{ config('legal.trading_as') }}, and pointed them to our <a class="link"
                href="{{ route('legal.privacy') }}">Privacy Policy</a>;</li>
        <li>for competitors under 18, you have consent from a parent or guardian, including consent to publish their
            name with their results;</li>
        <li>you only upload the data needed to run the competition;</li>
        <li>you'll handle requests from competitors about their data, and pass on to us any you can't deal with
            yourself.</li>
    </ul>
    <p>In return, we only use that data to run and publish your competition and to provide the service. We keep it
        secure, only let people you've given access see what isn't public, use the processors listed in our Privacy
        Policy, help you respond to data requests, and tell you without undue delay if we become aware of a breach
        affecting it.</p>

    <h3>6. Acceptable use</h3>
    <p>Don't use {{ config('legal.trading_as') }} to break the law, enter false or offensive data, harass anyone, try
        to get into accounts or competitions you don't have access to, disrupt the service, or scrape it at scale. If
        you find a security problem, please tell us at <a class="link"
            href="mailto:{{ config('legal.contact_email') }}">{{ config('legal.contact_email') }}</a> instead of using
        it.</p>

    <h3>7. Suspension and removal</h3>
    <p>We may suspend or remove accounts or competitions that break these terms or put the service or other people at
        risk. Organisers can remove officials from their own competitions at any time.</p>

    <h3>8. Personal devices</h3>
    <p>Officials often judge on their own phones and tablets, poolside. Personal devices are used at your own risk.
        {{ config('legal.trading_as') }}, the event host and affiliated parties accept no responsibility for any loss,
        damage or malfunction of devices.</p>

    <h3>9. Our liability</h3>
    <p>The service is free and provided "as is". As far as the law allows, we're not liable for any loss caused by
        scoring errors, outages, lost data, or decisions made using the service, or for any indirect or consequential
        loss. Nothing in these terms limits liability that can't be limited under UK law, such as for death or personal
        injury caused by negligence, or for fraud.</p>

    <h3>10. Changes to these terms</h3>
    <p>We'll email account holders at least 30 days before any significant change takes effect. Minor changes, such as
        clarifications, take effect when we publish them. The date at the top shows when these terms last changed.</p>

    <h3>11. Law</h3>
    <p>These terms are governed by the law of England and Wales, and the courts of England and Wales have jurisdiction
        over any dispute.</p>
@endsection
