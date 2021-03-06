---------------------------------------
modTimetable
---------------------------------------
Version: 1.0.1-pl
Author: Murray Wood <murray@digitalpenguin.hk>
---------------------------------------

For MODX Revolution 2.6.5+

Requires PHP 7.1+



A user-friendly Custom Manager Page for inputting weekly timetable data with options on how to display it on the web context.

Render it in a HTML grid or straight to divs.

Example Snippet calls
-------------

To show timetables with IDs 1 and 2 in a table:

[[!modTimetable? &timetables=`1,2` &renderTable=`1`]]

or to include custom chunks:
[[!modTimetable?
    &timetables=`1,2`
    &renderTable=`1`
    &tableHeaderRowTpl=`myHeaderRowChunk`
    &timetableTpl=`myMainTimetableChunk`
    &sessionTpl=`mySessionChunk`
]]

To display sessions for a single day from multiple timetables:
(This depends on what you set the day name as in the CMP.)
[[!modTimetable?
    &timetables=`1,2`
    &day=`Tuesday`
]]

To display a single day from multiple timetables relative to the current day:
(This will display the next "active" day that contains sessions.)
[[!modTimetable?
    &timetables=`1,2`
    &day=`auto`
]]

A snippet that returns the next day name that has active sessions:
[[!mtNextDayName]]

