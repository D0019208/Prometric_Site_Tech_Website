<!DOCTYPE html>
<?php
require_once ("../database/database.php");
require_once ("../backend/functions.php");

$technician = "Nichita Postolachi";

$activities_complete_query = "SELECT workingSince, technicianFullName FROM technician WHERE technicianFullName = '" . $technician . "'";
$statement = $db->prepare($activities_complete_query);
$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);

$workingSince = new DateTime($result[0]["workingSince"]);
$today = new DateTime('2020-01-21');
//$today = new DateTime(date("Y-m-d"));

$difference = $today->diff($workingSince);

$date = date("Y-m-d");
$time = date("H:i:s");

if ($difference->y >= 1) {
    $anniversary_suffix;

    if ($difference->y == 1) {
        $anniversary_suffix = "year";
    } else {
        $anniversary_suffix = "years";
    }

    $event = $result[0]["technicianFullName"] . " celebrates " . $difference->y . " " . $anniversary_suffix . " at Prometric! Happy anniversary " . explode(' ', $result[0]["technicianFullName"])[0] . " from Nichita!";

    $check_event_query = "SELECT event FROM other_events WHERE event = '" . $event . "'";
    $check_event_statement = $db->prepare($check_event_query);
    $check_event_statement->execute();

    $check_event_result = $check_event_statement->fetchAll(PDO::FETCH_ASSOC);

    if (count($check_event_result) == 0) {
        $query = "INSERT INTO other_events (event, start, technician, link, event_type, image, allDayEvent) VALUES ('" . $event . "', '" . $date . " " . $time . "', '" . $result[0]["technicianFullName"] . "', '#', 'other', 'images/SiteIcons/ModalIcons/ActivityType.png', 0)";
        $statement = $db->prepare($query);
        $statement->execute();
    }
}
?>
<html>
    <head>
        <title>W2UI Demo: grid-17</title>
        <script src="../js/libraries/jquery-3.4.0.min.js" type="text/javascript"></script>
        <script src="../js/libraries/w2ui/w2ui.min.js" type="text/javascript"></script>
        <link href="../css/libraries/w2ui/w2ui.min.css" rel="stylesheet" type="text/css"/>
        <style>
            #grid {
                width: 100%;
                height: 400px;
            }
            .w2ui-grid .w2ui-toolbar-search .w2ui-search-down {
                margin: 3px 0 0 6px;
            }
        </style>
    </head>
    <body>

        <div class="wrapper">
            <div id="grid"></div>
        </div>

        <script type="text/javascript">

            var people = [
                {id: 1, text: 'John Cook'},
                {id: 2, text: 'Steve Jobs'},
                {id: 3, text: 'Peter Sanders'},
                {id: 4, text: 'Mark Newman'},
                {id: 5, text: 'Addy Osmani'},
                {id: 6, text: 'Paul Irish'},
                {id: 7, text: 'Doug Crocford'},
                {id: 8, text: 'Nicolas Cage'}
            ];
            let capitalize = s => s.charAt(0).toUpperCase() + s.slice(1)


            let mkcol = ({field: field, type: type = 'text', editable: editable = {type: type}, caption: caption = capitalize(field),
                    size: size = '100px', sortable: sortable = true, resizable: resizable = true, render: render = type} = {}) =>
            {
                return {field: field, editable: editable, caption: caption, size: size, sortable: sortable, resizable: resizable}
            }


            /*
             var foo = 1;
             console.log({[foo]: 'bar'})
             
             console.log(genColumns({field: 'text', resizable: false}));
             */
            $(function () {
                $('#grid').w2grid({
                    name: 'grid',
                    show: {
                        toolbar: true,
                        footer: true,
                        toolbarSave: true
                    },
                    columns: [
                        mkcol({field: 'recid', caption: 'ID', size: '50px'}),
                        mkcol({field: 'uneditable', editable: null}),
                        mkcol({field: 'text'}),
                        mkcol({field: 'int', render: 'int', editable: {type: 'int', min: 0, max: 32756}}),
                        mkcol({field: 'money', type: 'money'}),
                        mkcol({field: 'percent', render: 'percent:1', editable: {type: 'percent', precision: 1}}),
                        mkcol({field: 'color', editable: {type: 'color'}}),
                        mkcol({field: 'date', type: 'date', style: 'text-align: right'}),
                        mkcol({field: 'time', type: 'time'}),
                        mkcol({field: 'list', size: '50%', editable: {type: 'list', items: people, showAll: true}}),
                        mkcol({field: 'combo', size: '50%', editable: {type: 'combo', items: people, filter: false}}),
                        mkcol({field: 'select', editable: {type: 'select', items: [{id: '', text: ''}].concat(people)},
                            render: function (record, index, col_index) {
                                var html = '';
                                for (var p in people) {
                                    if (people[p].id == this.getCellValue(index, col_index))
                                        html = people[p].text;
                                }
                                return html;
                            }
                        }),
                        mkcol({field: 'check', size: '60px', style: 'text-align: center', editable: {type: 'checkbox', style: 'text-align: center'}})
                    ],
                    toolbar: {
                        items: [
                            {id: 'add', type: 'button', caption: 'Add Record', icon: 'w2ui-icon-plus'}
                        ],
                        onClick: function (event) {
                            if (event.target == 'add') {
                                w2ui.grid.add({recid: w2ui.grid.records.length + 1});
                            }
                        }
                    },
                    records: [
                        {recid: 1, int: 100, money: 100, percent: 55, date: '1/1/2014', combo: 'John Cook', check: true},
                        {recid: 2, int: 200, money: 454.40, percent: 15, date: '1/1/2014', combo: 'John Cook', check: false, list: {id: 2, text: 'Steve Jobs'}},
                        {recid: 3, int: 350, money: 1040, percent: 98, date: '3/14/2014', combo: 'John Cook', check: true},
                        {recid: 4, int: 350, money: 140, percent: 58, date: '1/31/2014', combo: 'John Cook', check: true, list: {id: 4, text: 'Mark Newman'}},
                        {recid: 5, int: 350, money: 500, percent: 78, date: '4/1/2014', check: false},
                        {recid: 6, text: 'some text', int: 350, money: 440, percent: 59, date: '4/4/2014', check: false},
                        {recid: 7, int: 350, money: 790, percent: 39, date: '6/8/2014', check: false},
                        {recid: 8, int: 350, money: 4040, percent: 12, date: '11/3/2014', check: true},
                        {recid: 9, int: 1000, money: 3400, percent: 100, date: '2/1/2014',
                            style: 'background-color: #ffcccc', editable: false}
                    ]
                });
            });
            function showChanged() {
                console.log(w2ui['grid'].getChanges());
                w2alert('Changed records are displayed in the console');
            }

            /*
             $(function () {
             $('#grid1').w2grid({ 
             name: 'grid1', 
             header: 'Activiteiten',
             show: { header: true },
             columnGroups: [
             { caption: '', span: 2 },
             { caption: 'Start', span: 2 },
             { caption: 'End', span: 2 }
             ],
             columns: [                
             { field: 'recid', caption: 'ID', size: '50px', sortable: true, attr: 'align=center' },
             { field: 'lname', caption: 'Title', size: '30%', sortable: true },
             { field: 'sdate', caption: 'Date', size: '20%', sortable: true },
             { field: 'sdate', caption: 'Time', size: '20%' },
             { field: 'sdate', caption: 'Date', size: '120px' }
             ],
             records: [
             { recid: 1, fname: 'John', lname: 'doe', email: 'jdoe@gmail.com', sdate: '4/3/2012' },
             { recid: 2, fname: 'Stuart', lname: 'Motzart', email: 'motzart@hotmail.com', sdate: '4/3/2012' },
             { recid: 3, fname: 'Jin', lname: 'Franson', email: 'jin@yahoo.com', sdate: '4/3/2012' },
             { recid: 4, fname: 'Susan', lname: 'Ottie', email: 'sottie@yahoo.com', sdate: '4/3/2012' },
             { recid: 5, fname: 'Kelly', lname: 'Silver', email: 'kelly@gmail.com', sdate: '4/3/2012' },
             { recid: 6, fname: 'Francis', lname: 'Gatos', email: 'frank@apple.com', sdate: '4/3/2012' }
             ],
             onClick: function (event) {
             w2ui['grid2'].clear();
             var record = this.get(event.recid);
             w2ui['grid2'].add([
             { recid: 0, name: 'ID:', value: record.recid },
             { recid: 1, name: 'First Name:', value: record.fname },
             { recid: 2, name: 'Last Name:', value: record.lname },
             { recid: 3, name: 'Email:', value: record.email },
             { recid: 4, name: 'Date:', value: record.sdate }
             ]);
             }
             });
             
             $('#grid2').w2grid({ 
             header: 'Detailinformatie',
             show: { header: true, columnHeaders: false },
             name: 'grid2', 
             columns: [                
             { field: 'name', caption: 'Name', size: '100px', style: 'background-color: #efefef; border-bottom: 1px solid white; padding-right: 5px;', attr: "align=right" },
             { field: 'value', caption: 'Value', size: '100%' }
             ]
             });    
             });
             */
        </script>

    </body>
</html>