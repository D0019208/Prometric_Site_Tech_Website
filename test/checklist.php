<?php
require_once ("database/database.php");
require_once ("backend/functions.php");

/* Connect to the database */
global $db;
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

//Insert MANDATORY checklistCategories into checklist_checklistCategories table
$categoryIds = ["categoryName"]; 
$insertValues = ["checklistID" => "5", "categoryStatus" => "1"];   
//$response = insertRecords($db, $categoryIds, "checklistCategories", $insertValues, "checklist_checklistCategories");  
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//}

//Insert MANDATORY checklistTasks into checklist_checklistTasks table
$categoryIds = ["taskName"]; 
$insertValues = ["checklistID" => "5", "taskCompleted" => "0"];   
//$response = insertRecords($db, $categoryIds, "checklistTasks", $insertValues, "checklist_checklistTasks"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//}

//Insert MANDATORY checklistTabs into checklist_checklistTabs
$categoryIds = ["tabName"]; 
$insertValues = ["checklistID" => "5", "tabStatus" => "1"];   
//$response = insertRecords($db, $categoryIds, "checklistTabs", $insertValues, "checklist_checklistTabs"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//}

//Insert the MANDATORY checklistActivityType into checklist_checklistActivityType
$categoryIds = ["activityType"]; 
$insertValues = ["checklistID" => "5"];   
//$response = insertRecords($db, $categoryIds, "siteActivityType", $insertValues, "checklist_siteActivityType"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//} 

//Insert OPTIONAL categories into checklist_checklistOptionalCategories table
$categoryIds = ["optionalCategoryName"]; 
$insertValues = ["checklistID" => "5", "optionalCategoryStatus" => "1"];   
//$response = insertRecords($db, $categoryIds, "checklistOptionalCategories", $insertValues, "checklist_checklistOptionalCategories"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//} 

//Insert OPTIONAL tasks into checklist_checklistOptionalTasks table
$categoryIds = ["optionalTaskName"]; 
$insertValues = ["checklistID" => "5", "optionalTaskCompleted" => "0"];   
//$response = insertRecords($db, $categoryIds, "checklistOptionalTasks", $insertValues, "checklist_checklistOptionalTasks"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//}  

//Insert OPTIONAL tabs into checklist_checklistOptionalTabs
$categoryIds = ["optionalTabName"]; 
$insertValues = ["checklistID" => "5", "optionalTabStatus" => "1"];   
//$response = insertRecords($db, $categoryIds, "checklistOptionalTabs", $insertValues, "checklist_checklistOptionalTabs"); 
//
//if($response !== "Success")
//{
//    echo "An error has occured: " . $response;
//}  
?>
<!DOCTYPE html>
<html>
    <head>
        <title>W2UI Demo: grid-21</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
        <script src="js/w2ui/w2ui.min.js" type="text/javascript"></script>
        <link href="css/w2ui/w2ui.min.css" rel="stylesheet" type="text/css"/>
        <style>
            .w2ui-grid .w2ui-grid-header
            { 
                background-image: linear-gradient(#a4d95b, #7ab52a);
            }

            .w2ui-tabs .w2ui-tab.active
            {
                background-color: #a4d95b; 
            } 
        </style>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>

        <div id="layout" style="width: 100%; height: 550px;"></div>

        <script type="text/javascript">
            var style = 'background-color: #F5F6F7; border: 1px solid silver; padding: 3px';









            function addCode(code) {
                var JS = document.createElement('script');
                JS.text = code;
                document.body.appendChild(JS);
            }



            $().w2grid({
                name: 'XenServerSetUp',
                header: 'Xen Server Setup',
                fixedBody: false,
                show: {
                    header: true,
                    toolbar: false,
                    footer: false,
                    lineNumbers: false,
                    toolbarSearch: false,
                    toolbarReload: false,
                    toolbarColumns: false,
                    toolbarAdd: false,
                    toolbarDelete: false,
                    toolbarEdit: false
                },
                columns: [
                    {
                        field: 'taskName',
                        caption: 'Task',
                        size: '50%',
                        sortable: false,
                        resizable: true,
                        editable: {
                            type: 'text'
                        },
                        render: function (record) {
                            return record.taskName;
                        }
                    },
                    {
                        field: 'complete',
                        caption: 'Progress',
                        size: '50%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return  record.complete;
                        }
                    }
                ],
                records:
                        [
                            {recid: 1, taskName: '<div style="font-weight: bold">iDRAC</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" unchecked>'},
                            {recid: 2, taskName: '<div style="font-weight: bold">RAID Configuration</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 3, taskName: '<div style="font-weight: bold">Xen Server Installation</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 4, taskName: '<div style="font-weight: bold">Host Name</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 5, taskName: '<div style="font-weight: bold">XEN Server Patch SP1</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 6, taskName: '<div style="font-weight: bold">Xen Server NTP Configuation</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 7, taskName: '<div style="font-weight: bold">Site VM Bond - Active / Passive</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 8, taskName: '<div style="font-weight: bold">Techsupp into Domain</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 9, taskName: '<div style="font-weight: bold">Techsupp has IE 11 Installed</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {recid: 10, taskName: '<div style="font-weight: bold">License Server Complete</div>', color: 'ffffff', editable: false, complete: '<input type="checkbox" checked>'},
                            {
                                w2ui: {summary: true},
                                recid: 'S-1', taskName: '<span>Xen Server Setup Complete</span>', complete: '<span>In Progress</span>'
                            }
                        ],
                onAdd: function (e) {
                    var f = w2ui.groups.find({
                        recid: 'New group'
                    }, true)[0];
                    if (typeof f === 'undefined') {
                        w2ui.groups.add({
                            recid: 'New group',
                            color: 'ffffff'
                        });
                    } else {
                        w2alert('There is already a group called "New group"!<p>change its name first', '"New group" already in list');
                    }
                },
                onDelete: function (e) {
                    console.log(e);
                },
                onChange: function (e) {
                    e.preventDefault();
                    var v;
                    if (e.column === 0) { // Change a name

                        /*
                         * Check if a group with the same name 
                         * already exists! If so get out...
                         */

                        v = w2ui.groups.find({
                            recid: e.value_new
                        }, true);
                        if (v.length > 0) {
                            w2alert('A group named "' + e.value_new +
                                    '" already exists! <p> Change its name first...');
                        } else {
                            w2ui.groups.records[e.index].recid = e.value_new;

                            /*
                             * If this name is in inds list, change it as well
                             */

                            v = w2ui.inds.find({
                                group: e.value_original
                            }, true);
                            if (typeof v !== 'undefined') {
                                for (i = 0; i < v.length; i++) {
                                    w2ui.inds.records[v[i]].group = e.value_new;
                                }
                            }
                        }
                    } else { // Change a color                       
                        w2ui.groups.records[e.index].color = e.value_new;

                        /*
                         * If this name is in inds list, change its color as well
                         */

                        v = w2ui.inds.find({
                            group: e.recid
                        }, true);
                        if (typeof v !== 'undefined') {
                            for (i = 0; i < v.length; i++) {
                                w2ui.inds.records[v[i]].color = e.value_new;
                            }
                        }
                    }
                    this.refresh();
                }
            });



            $().w2grid({
                name: 'S001Build',
                header: 'S001 Build & Setup',
                fixedBody: false,
                show: {
                    header: true,
                    toolbar: false,
                    footer: false,
                    lineNumbers: false,
                    toolbarSearch: false,
                    toolbarReload: false,
                    toolbarColumns: false,
                    toolbarAdd: false,
                    toolbarDelete: false,
                    toolbarEdit: false
                },
                columns: [
                    {
                        field: 'ITQuotes',
                        caption: 'ITQuotes',
                        size: '15%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return record.ITQuotes;
                        }
                    },
                    {
                        field: 'ITQuotesComplete',
                        caption: 'Progress',
                        size: '5%',
                        sortable: false,
                        resizable: false,
                        editable: false,
                        render: function (record) {
                            return record.ITQuotesComplete;
                        }
                    },
                    {
                        field: 'S001Migration',
                        caption: 'Migration of S001',
                        size: '25%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return record.S001Migration;
                        }
                    },
                    {
                        field: 'S001MigrationComplete',
                        caption: 'Progress',
                        size: '5%',
                        sortable: false,
                        resizable: false,
                        editable: false,
                        render: function (record) {
                            return record.S001MigrationComplete;
                        }
                    },
                    {
                        field: 'XenServerInstallation',
                        caption: 'XenServer Installation',
                        size: '25%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return record.XenServerInstallation;
                        }
                    },
                    {
                        field: 'XenServerInstallationComplete',
                        caption: 'Progress',
                        size: '5%',
                        sortable: false,
                        resizable: false,
                        editable: false,
                        render: function (record) {
                            return record.XenServerInstallationComplete;
                        }
                    },
                    {
                        field: 'S001BuildConfiguration',
                        caption: 'S001 Build Configuration Checks',
                        size: '25%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return record.S001BuildConfiguration;
                        }
                    },
                    {
                        field: 'S001BuildConfigurationComplete',
                        caption: 'Progress',
                        size: '5%',
                        sortable: false,
                        resizable: true,
                        editable: false,
                        render: function (record) {
                            return record.S001BuildConfigurationComplete;
                        }
                    }
                ],
                records:
                        [
                            {recid: 1, ITQuotes: '<div style="font-weight: bold">Insight / ITQuotes Build</div>', ITQuotesComplete: '<input type="checkbox" checked>', S001Migration: '<div style="font-weight: bold">Migration of S001</div>', S001MigrationComplete: '<input type="checkbox" checked>', XenServerInstallation: '<div style="font-weight: bold">S001 NIC Configuration - TCP / IP Checksum Offloads</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold"></div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 2, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Back up NBME Day 1 Files On Previous Server?</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">Time Zone Setup</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 3, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">PRT have confirmed no pending results for site</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">Xen Server Tools Updated</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 4, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Internet Connected to Meraki</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">HP Laserjet Setup</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 5, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Meraki Config Downloaded and Site VPN Up</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">DHCP Reservation Created for Printer</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 6, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Network Switch Installed</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">D Drive Size Increased to 100GB</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 7, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Meraki Connected to Network Switch</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">Increase Memory to 4GB</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 8, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">Server Installed in Rack / Cage</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">vCPU Count Increased to 4 vCPU</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 9, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">PSU 1 connected to Mains</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">Confirm all packages are installed via Managesoft</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 10, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">PSU 2 Connected to UPS</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold">Set File Security Permissions for the D Partition (S001)</div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 11, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">NIC Card installed and UPS Powered on</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold"></div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false},
                            {recid: 12, ITQuotes: '', ITQuotesComplete: '', S001Migration: '', S001MigrationComplete: '', XenServerInstallation: '<div style="font-weight: bold">GB1, iDRAC & Expansion Card Connected to Switch</div>', XenServerInstallationComplete: '<input type="checkbox" checked>', S001BuildConfiguration: '<div style="font-weight: bold"></div>', S001BuildConfigurationComplete: '<input type="checkbox" checked>', editable: false}
                        ],
                onAdd: function (e) {
                    var f = w2ui.groups.find({
                        recid: 'New group'
                    }, true)[0];
                    if (typeof f === 'undefined') {
                        w2ui.groups.add({
                            recid: 'New group',
                            color: 'ffffff'
                        });
                    } else {
                        w2alert('There is already a group called "New group"!<p>change its name first', '"New group" already in list');
                    }
                },
                onDelete: function (e) {
                    console.log(e);
                },
                onChange: function (e) {
                    e.preventDefault();
                    var v;
                    if (e.column === 0) { // Change a name

                        /*
                         * Check if a group with the same name 
                         * already exists! If so get out...
                         */

                        v = w2ui.groups.find({
                            recid: e.value_new
                        }, true);
                        if (v.length > 0) {
                            w2alert('A group named "' + e.value_new +
                                    '" already exists! <p> Change its name first...');
                        } else {
                            w2ui.groups.records[e.index].recid = e.value_new;

                            /*
                             * If this name is in inds list, change it as well
                             */

                            v = w2ui.inds.find({
                                group: e.value_original
                            }, true);
                            if (typeof v !== 'undefined') {
                                for (i = 0; i < v.length; i++) {
                                    w2ui.inds.records[v[i]].group = e.value_new;
                                }
                            }
                        }
                    } else { // Change a color                       
                        w2ui.groups.records[e.index].color = e.value_new;

                        /*
                         * If this name is in inds list, change its color as well
                         */

                        v = w2ui.inds.find({
                            group: e.recid
                        }, true);
                        if (typeof v !== 'undefined') {
                            for (i = 0; i < v.length; i++) {
                                w2ui.inds.records[v[i]].color = e.value_new;
                            }
                        }
                    }
                    this.refresh();
                }
            });



            $().w2grid({
                name: 'Overview',
                header: '8921 Mumbai - Emergency Server Build',
                fixedBody: false,
                show: {
                    header: true,
                    toolbar: false,
                    footer: false,
                    lineNumbers: false,
                    toolbarSearch: false,
                    toolbarReload: false,
                    toolbarColumns: false,
                    toolbarAdd: false,
                    toolbarDelete: false,
                    toolbarEdit: false
                },
                columns: [
                    {
                        field: 'siteDetailsColumn',
                        caption: 'Site Details',
                        size: '5%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<div style="font-weight: bold">' + record.siteDetailsColumn +
                                    '</div>';
                        }
                    },
                    {
                        field: 'siteDetailsColumnValue',
                        caption: '',
                        size: '5%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<div style="font-weight: bold">' + record.siteDetailsColumnValue +
                                    '</div>';
                        }
                    },
                    {
                        field: 'categories',
                        caption: 'Categories',
                        size: '35%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<div style="font-weight: bold">' + record.categories +
                                    '</div>';
                        }
                    },
                    {
                        field: 'progress',
                        caption: 'Progress',
                        size: '15%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<div style="font-weight: bold">' + record.progress +
                                    '</div>';
                        }
                    },
                    {
                        field: 'expected',
                        caption: 'Expected',
                        size: '15%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<div style="font-weight: bold">' + record.expected +
                                    '</div>';
                        }
                    },
                    {
                        field: 'completed',
                        caption: 'Completed',
                        size: '15%',
                        sortable: false,
                        resizable: true,
                        editable: { type: 'text' },
                        render: function (record) {
                            return '<input type="checkbox" name="gender" ' + record.completed + '>';
                        }
                    }
                ],
                records:
                        [
                            {recid: 1, siteDetailsColumn: 'Site Code', siteDetailsColumnValue: '8921', categories: 'Server Base Setup', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 2, siteDetailsColumn: 'Site Name', siteDetailsColumnValue: 'Mumbai', categories: 'Communications Backend Setup', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 3, siteDetailsColumn: 'Site Type', siteDetailsColumnValue: 'Corporate', categories: 'Server Inside Cabinet and Site Communicating', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 4, siteDetailsColumn: 'Activity Type', siteDetailsColumnValue: 'Emergency Server Build', categories: 'Admin & Workstation Ready for Deployment', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 5, siteDetailsColumn: 'Go Live Date', siteDetailsColumnValue: '01-Apr-2019', categories: 'Finalize Server Setup', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 6, siteDetailsColumn: 'Technician', siteDetailsColumnValue: 'Divy Mohan Shorey', categories: 'Cache Proxy Ready (If Applicable)', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 7, siteDetailsColumn: 'FTS Support', siteDetailsColumnValue: 'Charlie Dowd', categories: 'Endpoints & VMs', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 8, siteDetailsColumn: '', siteDetailsColumnValue: '', categories: 'SDVR, Cameras, & LMC Machine', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 9, siteDetailsColumn: '', siteDetailsColumnValue: '', categories: 'Demo Testing', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 10, siteDetailsColumn: '', siteDetailsColumnValue: '', categories: 'Final Checks (Pre Go-Live)', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false},
                            {recid: 11, siteDetailsColumn: '', siteDetailsColumnValue: '', categories: '10 Day Post Live Review of Site', progress: 'Complete', expected: '28-Apr-2019', completed: 'checked', editable: false}
                        ],
                onAdd: function (e) {
                    var f = w2ui.groups.find({
                        recid: 'New group'
                    }, true)[0];
                    if (typeof f === 'undefined') {
                        w2ui.groups.add({
                            recid: 'New group',
                            color: 'ffffff'
                        });
                    } else {
                        w2alert('There is already a group called "New group"!<p>change its name first', '"New group" already in list');
                    }
                },
                onDelete: function (e) {
                    console.log(e);
                },
               
            });












            var array = [{
                    id: 'tab1',
                    caption: 'Overview'
                }, {
                    id: 'tab2',
                    caption: 'Contact'
                }, {
                    id: 'tab3',
                    caption: 'Xen Server Setup'
                }, {
                    id: 'tab4',
                    caption: 'Meraki Setup'
                }, {
                    id: 'tab5',
                    caption: 'S001 Build & Setup'
                }, {
                    id: 'tab6',
                    caption: 'Adm & WKS Base Setup'
                }, {
                    id: 'tab7',
                    caption: 'TCDDC & Xen Desktop'
                }, {
                    id: 'tab8',
                    caption: 'C001 Build'
                }, {
                    id: 'tab9',
                    caption: 'Endpoints & VM Configurations'
                }, {
                    id: 'tab10',
                    caption: 'NVR & LMC'
                }, {
                    id: 'tab11',
                    caption: 'Demos'
                }, {
                    id: 'tab12',
                    caption: 'Final Checks'
                }, {
                    id: 'tab13',
                    caption: '10 Day - Post Live Review'
                }];


            $('#layout').w2layout({
                name: 'Layout',
                padding: 0,
                panels: [{
                        type: 'main',
                        resizable: true,
                        //title: 'Global Site Technology Services Dashboard',
                        style: style,
                        tabs: {
                            name: 'tabs',
                            active: 'tab1',
                            tabs: array,
                            onClick: function (id) {
                                switch (id.target) {
                                    case 'tab1':
                                        w2ui.Layout.content('main', w2ui.Overview);
                                        break;
                                    case 'tab2':
                                        w2ui.Layout.content('main', w2ui.Contact);
                                        break;
                                    case 'tab3':
                                        w2ui.Layout.content('main', w2ui.XenServerSetUp);
                                        break;
                                    case 'tab4':
                                        w2ui.Layout.content('main', w2ui.MerakiSetup);
                                        break;
                                    case 'tab5':
                                        w2ui.Layout.content('main', w2ui.S001Build);
                                        break;
                                    case 'tab6':
                                        w2ui.Layout.content('main', w2ui.AdminWksBaseSetup);
                                        break;
                                    case 'tab7':
                                        w2ui.Layout.content('main', w2ui.TCDDCAndXenDesktop);
                                        break;
                                    case 'tab8':
                                        w2ui.Layout.content('main', w2ui.C001Build);
                                        break;
                                    case 'tab9':
                                        w2ui.Layout.content('main', w2ui.EndpointsAndVMConfigurations);
                                        break;
                                    case 'tab10':
                                        w2ui.Layout.content('main', w2ui.NVRAndLMC);
                                        break;
                                    case 'tab11':
                                        w2ui.Layout.content('main', w2ui.Demos);
                                        break;
                                    case 'tab12':
                                        w2ui.Layout.content('main', w2ui.FinalChecks);
                                        break;
                                    case 'tab13':
                                        w2ui.Layout.content('main', w2ui.PostLiveReview);
                                        break;
                                }
                            }
                        }
                    }]
            });

            w2ui.Layout.content('main', w2ui.Overview);




            //addCode(s);
        </script>

    </body>
</html>