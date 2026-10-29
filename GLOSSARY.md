# BULSCA Scorer

Scoring and results for BULSCA lifesaving competitions, including the Digital Judge app that officials use poolside on the day.

## Language

### Competitions

**Event**:
A SERC or speed event in a competition.
_Avoid_: Activity (that's a recorded action)

**Entity**:
A Team or Competitor that takes part in an Event and gets results.
_Avoid_: Participant, entrant

**Objective**:
A part of a SERC with its own marking points, e.g. a casualty, which an Official marks Entities against.
_Avoid_: Judge (for the slot), casualty, position

**League**:
A grouping of Entities within a competition, e.g. A League or B League. Pools run heats for their assigned Leagues, and SERC tanks are set up by League.
_Avoid_: Bracket

**League Objectives**:
A SERC setting where each League is assigned exactly one Objective, and every Entity is marked against its League's Objective instead of one the Official picks.
_Avoid_: Restricted judges, restricted SERC

**Pool**:
A separate body of water at a competition with its own lane count, where speed heats for its assigned Leagues run with their own heat numbering.
_Avoid_: Tank (that's for SERCs), venue

**Heat**:
A group of Entities racing one speed event together in one Pool. Heat numbers restart in each Pool.
_Avoid_: Race, round

**Tank**:
One of the separate areas where a SERC runs at the same time as others.
_Avoid_: Pool (that's for speed events), station

**Draw**:
An Entity's place in a Tank's running order. A competition has one set of Draws, shared by all its SERCs.
_Avoid_: Heat (that's for speed events), slot

### Officials

**Official**:
A person added to a competition to judge, time or referee it.
_Avoid_: Judge (when meaning any official), user

**Referee**:
An Official with authority to review DQs and penalties and to make Announcements. A competition can have several.
_Avoid_: Head ref, head judge

**Digital Judge**:
The app Officials sign in to with their own account to judge, time and referee on the day.
_Avoid_: v2, new judge app

**Legacy Digital Judge**:
The older Digital Judge that Officials join with only a PIN and a name. It still works, but it gets no new features.
_Avoid_: v1, classic, old judge app

### Organising

**Organiser**:
A person who sets up and runs a competition: its Events, Entities, Officials and results. An Organiser can have limited permissions, e.g. only results, and is still an Organiser.
_Avoid_: Admin, owner, host, helper

**Organisation**:
A club, university or body that runs competitions. Its Organisers can manage all of its competitions.
_Avoid_: Club, team

**Management**:
The signed-in area where Organisers edit their competitions and organisations.
_Avoid_: Admin, dashboard, backend

**Admin**:
A site-wide administrator who runs the site itself (seasons, users, speed records), as opposed to an Organiser of one competition.
_Avoid_: Organiser, superuser

**Personal Competition**:
A competition that belongs to no organisation. Anyone signed in can create one, and becomes its first Organiser.
_Avoid_: Owned competition, private competition

**Invite**:
An email asking someone to become an Organiser of a competition or organisation. Accepting it creates their account if they don't have one.
_Avoid_: Sign-up, registration

### On the day

**Announcement**:
A message from a Referee to every Official on a competition, e.g. "Heat 4 delayed 10 min".
_Avoid_: Broadcast, notice, message

**Withdraw**:
A Referee taking an Announcement back, so it's gone for everyone. Announcements are withdrawn, never edited.
_Avoid_: Delete, retract

**Alert**:
A personal notice to one Official about a DQ or penalty they submitted or need to review.
_Avoid_: Notification

**Pack**:
A printable PDF for one role on the day, e.g. the Chief Timekeeper Pack or a Marshalling Pack.
_Avoid_: Printable, sheet (when meaning the whole document)

**Rough mark sheet**:
A blank table for a judge to rough-mark every Entity against one of a SERC's Objectives, before the marks are entered. Printed per SERC; not a Pack, as it serves a judge rather than a role.
_Avoid_: Raw marking sheet

### Results

**Mark**:
The score an Official gives one Entity on one of an Objective's marking points.
_Avoid_: Result, score

**Time**:
A team's recorded time in a speed event.
_Avoid_: Result

**Place**:
A team's position in a heat's order of finish (OOF).
_Avoid_: OOF (when meaning the value)

**Results sheet**:
An overall ranking for the whole competition or one League, built from chosen Events, each with a weight, and a Formula.
_Avoid_: Schema, result schema (fine in code, not in the UI)

**Master sheet**:
An overall ranking made by combining several Results sheets, e.g. one overall from each League's sheet.
_Avoid_: Master schema, master results

**Formula**:
How a Results sheet turns each Event's results into points: built-in, an Organisation's saved Formula, or custom.
_Avoid_: Result schema, result adjustment

**Activity**:
Any recorded action someone took on a competition, e.g. generating heats, swapping lanes, entering a Mark or submitting a DQ.
_Avoid_: Event (that's a SERC or speed event), audit, log entry

**History**:
The Activity about Marks, Times and Places: who entered or changed one, and when. Used to settle disputes after results go out.
_Avoid_: Audit trail, timeline, log
