@extends('legal.layout')

@section('title', 'Privacy Policy')

@section('policy')
    <h3>Who's responsible for your data</h3>
    <p>{{ config('legal.trading_as') }} is run by {{ config('legal.operator') }}, a sole trader in the United Kingdom.
    </p>
    <ul>
        <li><strong>Accounts, sign-in records and logs:</strong> we're the controller.</li>
        <li><strong>Competitor and team data</strong> uploaded by an organiser: the organiser is the controller, and we
            process it on their behalf under our <a class="link" href="{{ route('legal.terms') }}">Terms of
                Service</a>. Questions about why you were entered, or about your results, are best sent to the
            organiser first. You can always contact us too.</li>
    </ul>

    <h3>What we hold and why</h3>
    <ul>
        <li><strong>Your account:</strong> name, email address, a hashed password, an optional profile photo, and which
            organisations and competitions you can access, so we can provide the service (contract). Your profile photo
            is shown to other officials and anyone with its link can see it.</li>
        <li><strong>Accounts made for you:</strong> an organiser can create an official's account with just a name and
            email address. We email you when this happens.</li>
        <li><strong>Signing in:</strong> one-time PINs (stored hashed, valid for 2 minutes), sessions, and a record of
            each device you've signed in on (IP address, browser, device type and when it was last used), so you can
            see and sign out your devices and we can keep accounts secure (legitimate interests).</li>
        <li><strong>On the day:</strong> which competitions you officiate and your role, whether you're online,
            notifications you've subscribed to, and announcements you send (contract).</li>
        <li><strong>Scores and history:</strong> marks, times, placings, judge notes, disqualifications, penalties and
            appeals, including the names and positions of the officials who reported and seconded them, and a record
            of who entered or changed what and when. We keep this to run the competition and to settle disputes after
            results are published (legitimate interests).</li>
        <li><strong>Competitors and teams:</strong> names, clubs, teams, leagues or age categories, and any other fields
            the organiser imports, under the organiser's lawful basis.</li>
        <li><strong>Legacy Digital Judge:</strong> the name you type when you join, kept in your session and on your
            device.</li>
        <li><strong>WhatIf:</strong> an email address, a hashed password and copies of competitions you edit.</li>
        <li><strong>Server logs:</strong> errors and technical events, which can include IP addresses (legitimate
            interests).</li>
    </ul>
    <p>We don't collect dates of birth, addresses or phone numbers. If that changes, we'll update this policy before we
        start.</p>

    <h3>What's published</h3>
    <p>Once an organiser releases results, anyone can see them on the main, results, live and stats sites. That
        includes competitors' full names, clubs and teams, heats and draws, scores and placings, disqualification and
        penalty details with the names of the officials who reported and seconded them, and judges' notes with the
        name of the official who wrote them. Before release, these aren't public.</p>

    <h3>Children</h3>
    <p>Competitors are often under 18. Organisers must have consent from a parent or guardian before entering them,
        including consent to publish their name with their results. A parent, guardian or the competitor can ask the
        organiser, or us, to remove or anonymise their name, and for under-18s we will. Accounts are only for people
        aged 16 or over.</p>

    <h3>Who we share it with</h3>
    <p>We don't sell personal data or use it for advertising. We use these services to run
        {{ config('legal.trading_as') }}:</p>
    <ul>
        <li><strong>Our server host:</strong> data is stored on a server in the United Kingdom.</li>
        <li><strong>Cloudflare</strong> (United States) sits in front of the site to protect it and speed it up, so
            your requests, including your IP address, pass through it. Transfers are covered by the UK–US data bridge
            and Cloudflare's data processing terms.</li>
        <li><strong>Zoho Mail</strong> (European Union) sends our emails. The EU is covered by UK adequacy
            regulations.</li>
        <li><strong>Browser push services</strong> (run by Google, Mozilla or Apple, depending on your browser) deliver
            notifications if you turn them on.</li>
        <li><strong>bulsca.co.uk</strong> serves some club logos, so it receives your IP address when a page shows
            one.</li>
    </ul>
    <p>Fonts and scripts are served from our own server. We may also share data where the law requires it.</p>

    <h3>How long we keep it</h3>
    <ul>
        <li>Accounts: until you ask us to delete them.</li>
        <li>Device sign-in records: 12 months after they were last used, or a month after you sign the device out.</li>
        <li>Server logs: 14 days.</li>
        <li>Backups: the last 10 copies of the database, replaced as new ones are made.</li>
        <li>WhatIf competitions: 14 days after you last change them. WhatIf accounts with no competitions left are then
            deleted.</li>
        <li>Results and their history: kept as the sporting record. If you ask, we'll remove or anonymise an adult's
            name where there's good reason, and an under-18's name whenever asked.</li>
    </ul>

    <h3>Your rights</h3>
    <p>Under UK data protection law you can ask to see the data we hold about you, correct it, delete it, restrict or
        object to how we use it, or get a copy to take elsewhere. To delete your account, or for any of these, email
        <a class="link" href="mailto:{{ config('legal.contact_email') }}">{{ config('legal.contact_email') }}</a>. We'll
        reply within a month.
    </p>
    <p>If you're unhappy with how we've handled your data, you can complain to the Information Commissioner's Office
        at <a class="link" href="https://ico.org.uk/make-a-complaint/" target="_blank"
            rel="noopener">ico.org.uk</a>. We'd appreciate the chance to put it right first.</p>

    <h3>Cookies</h3>
    <p>We only use cookies and browser storage that the site needs to work. See the <a class="link"
            href="{{ route('legal.cookies') }}">Cookie Policy</a>.</p>

    <h3>Changes</h3>
    <p>We'll update this policy when what we collect or how we use it changes, and email account holders about
        significant changes.</p>
@endsection
