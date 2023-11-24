@extends('digitaljudge.mpa-layout')
@section('title')
    Help
@endsection
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />';
@endphp
@section('content')
    <p>For the most upto date help and guidance, read the <a class="link"
            href="https://www.bulsca.co.uk/resources/view/848b3997-b542-4fc4-bfcb-d7a02888c950">DigitalJudge Guide</a></p>
    <br>

    <p>
        Contents:
    <ul class="list-disc ml-6">
        <li><a class="link" href="#advice">Advice</a></li>
        <li><a class="link" href="#mpd">My phone died?</a></li>
        <li><a class="link" href="#imambias">I made a mistake but I've already submitted?</a></li>
        <li><a class="link" href="#msws">My submission wont submit?</a></li>
        <li><a class="link" href="#idoofbatdsf">I'm doing Order of Finish but a team didn't start/finish?</a></li>
        <li><a class="link" href="#sthaphdist">SERC Team has a penalty, how do I score them?</a></li>
        <li><a class="link" href="#idlump">I don't like using my phone!</a></li>
    </ul>
    </p>


    <h5 id="advice" class="mt-4">Advice</h5>
    <p>When Judging any event, you want to make sure you're paying as much attention to the event as possible. Your
        style of
        judging when using a phone is down to you. However, you may find it easiest to write <strong>rough</strong>
        scores
        on <strong>paper</strong> and then copy them over once each SERC/Heat has finished.



    </p>
    <h5 id="mpd" class="mt-4">My phone died?</h5>
    <p>Go and talk with the Judge closest to you. Get them to add all the objectives you were judging to their
        seelction.
        Then after each SERC get them to enter your results aswell.
        <br>
        If you cannot enter the previous Teams results due to it forcing the sharing judge onto the next team, go and
        speak
        with the SERC Setter and get them to select your objectives and update the makr to what it should be.
    </p>

    <h5 id="imambias" class="mt-4">I made a mistake but I've already submitted?</h5>
    <p>Go and talk to the SERC Setter/Head Ref. They can ammend any mistakes for you.</p>

    <h5 id="msws" class="mt-4">My submission wont submit?</h5>
    <p>Make sure you've entered data for all teams and checked any confirmation boxes.
        <br>
        If you have and it still wont work, refresh the page and try again. If it still wont work, log out and back in.
        Otherwise go and talk to the SERC Setter/Head Ref. They can input scores for you or return you to paper judging
        in
        the mean time.
    </p>

    <h5 id="idoofbatdsf" class="mt-4">I'm doing Order of Finish but a team didn't start/finish?</h5>
    <p>Only click teams that have finished. Any teams that don't finish should be left unordered. This includes Rope Throw
        teams that don't get all their ropes in.
    </p>

    <h5 id="sthaphdist" class="mt-4">SERC Team has a penalty, how do I score them?</h5>
    <p>In most cases you should 0 out the affected marking points. Or the entire objective depending on the outcome. You
        must make sure the marks reflect the penatly. If you're unsure ask your SERC Setter/Head Ref!</p>

    <h5 id="idlump" class="mt-4">I don't like using my phone!</h5>
    <p>I'm sorry that you're not enjoying the experience. Go and talk to the SERC Setter and you may be able to arrange
        to
        return to paper judging for your objectives. However this may not be possible. If you aren't enjoying the
        experience
        the Data Manage would appreciate your feedback!</p>
@endsection
