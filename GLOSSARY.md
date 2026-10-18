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

**League Objectives**:
A SERC setting where each League is assigned exactly one Objective, and every Entity is marked against its League's Objective instead of one the Official picks.
_Avoid_: Restricted judges, restricted SERC

### Officials

**Official**:
A person added to a competition to judge, time or referee it.
_Avoid_: Judge (when meaning any official), user

**Referee**:
An Official with authority to review DQs and penalties and to make Announcements. A competition can have several.
_Avoid_: Head ref, head judge

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

**Activity**:
Any recorded action someone took on a competition, e.g. generating heats, swapping lanes, entering a Mark or submitting a DQ.
_Avoid_: Event (that's a SERC or speed event), audit, log entry

**History**:
The Activity about Marks, Times and Places: who entered or changed one, and when. Used to settle disputes after results go out.
_Avoid_: Audit trail, timeline, log
