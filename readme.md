modTimetable
=
For MODX Revolution 2.3+

Requires PHP 5.5+

Currently in Alpha. Use at own risk.

A user-friendly Custom Manager Page for inputting weekly timetable data with options on how to display it on the web context.

Render it in a HTML grid or straight to divs. Custom chunks available soon.

Example Snippet calls
-------------
[[!modTimetable? &timetables=\`1\` &renderTable=\`1\`]] 

or 

[[!modTimetable? 
    &timetables=\`1,2\` 
    &renderTable=\`1\` 
    &tableHeaderRowTpl=\`myHeaderRowChunk\` 
    &timetableTpl=\`myMainTimetableChunk\`
    &sessionTpl=\`mySessionChunk\`
]]


