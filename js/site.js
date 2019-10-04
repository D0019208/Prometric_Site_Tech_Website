/* global Checklist, w2ui, Swal, Intl */

$(document).ready(function () {
    var style = 'background-color: #F5F6F7; border: 1px solid silver; padding: 3px';
    var key = decodeURI(getURLValue('checklistID'));
    var tabsArray = [];
    //Default tab
    var grid = "Overview";
    var allowedToEdit;
    var userBlockedFromEdit = false;

    var category_contact;
    var tab_contact;

    var checklist = createChecklist();

    Checklist.prototype.s001Formater = function ()
    {
        var itQuotes = $("[col=1]");
        var s001Migration = $("[col=4]");
        var cabinetInstall = $("[col=7]");
        var s001Build = $("[col=10]");

        filterArray(itQuotes, '<div style="max-height: 24px;text-align: center;" title="">');
        filterArray(s001Migration, '<div style="max-height: 24px;text-align: center;" title="">');
        filterArray(cabinetInstall, '<div style="max-height: 24px;text-align: center;" title="">');
        filterArray(s001Build, '<div style="max-height: 24px;text-align: center;" title="">');

        if (typeof $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_1')[0] !== 'undefined')
        {
            var itQuotesRecordCount = itQuotes.length;
            var itQuotesCheckCount = 0;
            for (var i = 0; i < itQuotesRecordCount; i++)
            {
                if (i < w2ui["S001Build"].records.length && w2ui["S001Build"].records.length !== 0)
                {
                    if (w2ui["S001Build"].records[i].ITQuotesComplete === "")
                    {
                        itQuotes[i].innerHTML = "";
                    } else if (typeof w2ui["S001Build"].records[i].ITQuotesComplete === "string")
                    {
                        if (w2ui["S001Build"].records[i].ITQuotesComplete.includes("unchecked"))
                        {
                            if (typeof w2ui["S001Build"].records[i].w2ui === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[i].w2ui !== "undefined")
                            {
                                if (typeof w2ui["S001Build"].records[i].w2ui.changes === "undefined")
                                {
                                    var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
                                    $(selector)[0].checked = false;
                                }

//                            if (typeof w2ui["S001Build"].records[i].w2ui.changes.ITQuotesComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                            }
                        }
                    } else
                    {
                        if (w2ui["S001Build"].records[i].ITQuotesComplete === false)
                        {
                            if (typeof w2ui["S001Build"].records[i].w2ui === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[i].w2ui !== "undefined")
                            {
                                if (typeof w2ui["S001Build"].records[i].w2ui.changes === "undefined")
                                {
                                    var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
                                    $(selector)[0].checked = false;
                                }

//                            if (typeof w2ui["S001Build"].records[i].w2ui.changes.ITQuotesComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + i + "_1 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                            }
                        }
                    }


                    if (typeof w2ui["S001Build"].records[i].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["S001Build"].records[i].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["S001Build"].records[i].w2ui.changes.ITQuotesComplete !== 'undefined')
                            {
                                if (new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[i].w2ui.changes.ITQuotesComplete))
                                {
                                    itQuotesCheckCount++;
                                }
                            } else if (typeof w2ui["S001Build"].records[i].w2ui.changes.ITQuotesComplete === 'undefined')
                            {
                                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete))
                                {
                                    itQuotesCheckCount++;
                                }
                            }
                        } else
                        {
                            if (typeof w2ui["S001Build"].records[i].w2ui.changes === 'undefined')
                            {
                                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete))
                                {
                                    itQuotesCheckCount++;
                                }
                            }
                        }

                    } else if (typeof w2ui["S001Build"].records[i].w2ui === 'undefined')
                    {

                        if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[i].ITQuotesComplete))
                        {
                            itQuotesCheckCount++;
                        }
                    }

                    if (i === itQuotesRecordCount - 1)
                    {
                        //Update progress
                        //console.log("checked = " + itQuotesCheckCount)
                        //console.log("total = " + itQuotesRecordCount)
                        if (itQuotesCheckCount === itQuotesRecordCount)
                        {
                            w2ui["S001Build"].summary[0].ITQuotesComplete = 'Complete';
                        } else if (itQuotesCheckCount < itQuotesRecordCount && itQuotesCheckCount >= 1)
                        {
                            w2ui["S001Build"].summary[0].ITQuotesComplete = 'In Progress';
                        } else if (itQuotesCheckCount === 0)
                        {
                            w2ui["S001Build"].summary[0].ITQuotesComplete = 'Not Started';
                        }

                        $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_1')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["ITQuotesComplete"] + '</div>';
                    }
                } else
                {

                    $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_1')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["ITQuotesComplete"] + '</div>';
                }

                //CONTINUE HERE
            }
        }

        if (typeof $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_4')[0] !== 'undefined')
        {
            var s001MigrationRecordCount = s001Migration.length;
            var s001MigrationCheckCount = 0;
            for (var j = 0; j < s001MigrationRecordCount; j++)
            {
                if (j < w2ui["S001Build"].records.length && w2ui["S001Build"].records.length !== 0)
                {
                    if (w2ui["S001Build"].records[j].S001MigrationComplete === "")
                    {
                        s001Migration[j].innerHTML = "";
                    } else if (typeof w2ui["S001Build"].records[j].S001MigrationComplete === "string")
                    {
                        if (w2ui["S001Build"].records[j].S001MigrationComplete.includes("unchecked"))
                        {
                            if (typeof w2ui["S001Build"].records[j].w2ui === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[j].w2ui !== "undefined")
                            {
                                if (typeof w2ui["S001Build"].records[j].w2ui.changes === "undefined")
                                {
                                    var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
                                    $(selector)[0].checked = false;
                                }

//                            if (typeof w2ui["S001Build"].records[j].w2ui.changes.S001MigrationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                            }
                        }
                    } else
                    {
                        if (w2ui["S001Build"].records[j].S001MigrationComplete === false)
                        {
                            if (typeof w2ui["S001Build"].records[j].w2ui === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[j].w2ui !== "undefined")
                            {
                                if (typeof w2ui["S001Build"].records[j].w2ui.changes === "undefined")
                                {
                                    var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
                                    $(selector)[0].checked = false;
                                }

//                            if (typeof w2ui["S001Build"].records[j].w2ui.changes.S001MigrationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + j + "_4 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                            }
                        }
                    }


                    if (typeof w2ui["S001Build"].records[j].w2ui !== 'undefined')
                    {
                        if (typeof w2ui["S001Build"].records[j].w2ui.changes !== 'undefined')
                        {
                            if (typeof w2ui["S001Build"].records[j].w2ui.changes.S001MigrationComplete !== 'undefined')
                            {
                                if (new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[j].w2ui.changes.S001MigrationComplete))
                                {
                                    s001MigrationCheckCount++;
                                }
                            } else if (typeof w2ui["S001Build"].records[j].w2ui.changes.S001MigrationComplete === 'undefined')
                            {
                                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete))
                                {
                                    s001MigrationCheckCount++;
                                }
                            }
                        } else
                        {
                            if (typeof w2ui["S001Build"].records[j].w2ui.changes === 'undefined')
                            {
                                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete))
                                {
                                    s001MigrationCheckCount++;
                                }
                            }
                        }

                    } else if (typeof w2ui["S001Build"].records[j].w2ui === 'undefined')
                    {

                        if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[j].S001MigrationComplete))
                        {
                            s001MigrationCheckCount++;
                        }
                    }

                    if (j === s001MigrationRecordCount - 1)
                    {
                        //Update progress
                        //console.log("checked = " + s001MigrationCheckCount)
                        //console.log("total = " + s001MigrationRecordCount)
                        if (s001MigrationCheckCount === s001MigrationRecordCount)
                        {
                            w2ui["S001Build"].summary[0].S001MigrationComplete = 'Complete';
                        } else if (s001MigrationCheckCount < s001MigrationRecordCount && s001MigrationCheckCount >= 1)
                        {
                            w2ui["S001Build"].summary[0].S001MigrationComplete = 'In Progress';
                        } else if (s001MigrationCheckCount === 0)
                        {
                            w2ui["S001Build"].summary[0].S001MigrationComplete = 'Not Started';
                        }

                        $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_4')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["S001MigrationComplete"] + '</div>';
                    }
                } else
                {
                    $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_4')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["S001MigrationComplete"] + '</div>';
                }
            }
        }


        //console.log(cabinetInstall);
        var cabinetInstallRecordCount = cabinetInstall.length;
        var cabinetInstallCheckCount = 0;
        for (var k = 0; k < cabinetInstallRecordCount; k++)
        {
            if (k < w2ui["S001Build"].records.length)
            {
                if (w2ui["S001Build"].records[k].XenServerInstallationComplete === "")
                {
                    cabinetInstall[k].innerHTML = "";
                } else if (typeof w2ui["S001Build"].records[k].XenServerInstallationComplete === "string")
                {
                    if (w2ui["S001Build"].records[k].XenServerInstallationComplete.includes("unchecked"))
                    {
                        if (typeof w2ui["S001Build"].records[k].w2ui === "undefined")
                        {
                            var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                            $(selector)[0].checked = false;
                        } else if (typeof w2ui["S001Build"].records[k].w2ui !== "undefined")
                        {
                            if (typeof w2ui["S001Build"].records[k].w2ui.changes === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete === 'undefined')
                            {
                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            }

//                            if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                        }
                    }
                } else
                {
                    if (w2ui["S001Build"].records[k].XenServerInstallationComplete === false)
                    {
                        if (typeof w2ui["S001Build"].records[k].w2ui === "undefined")
                        {
                            var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                            $(selector)[0].checked = false;
                        } else if (typeof w2ui["S001Build"].records[k].w2ui !== "undefined")
                        {
                            if (typeof w2ui["S001Build"].records[k].w2ui.changes === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete === 'undefined')
                            {
                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            }
//                            if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + k + "_7 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                        }
                    }
                }

                //CONTINUE HERE
                //Check the progress
                if (typeof w2ui["S001Build"].records[k].w2ui !== 'undefined')
                {
                    if (typeof w2ui["S001Build"].records[k].w2ui.changes !== 'undefined')
                    {
                        if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete !== 'undefined')
                        {
                            if (new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete))
                            {
                                cabinetInstallCheckCount++;
                            }
                        } else if (typeof w2ui["S001Build"].records[k].w2ui.changes.XenServerInstallationComplete === 'undefined')
                        {
                            if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete))
                            {
                                cabinetInstallCheckCount++;
                            }
                        }
                    } else
                    {
                        if (typeof w2ui["S001Build"].records[k].w2ui.changes === 'undefined')
                        {
                            if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete))
                            {
                                cabinetInstallCheckCount++;
                            }
                        }
                    }

                } else if (typeof w2ui["S001Build"].records[k].w2ui === 'undefined')
                {

                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[k].XenServerInstallationComplete))
                    {
                        cabinetInstallCheckCount++;
                    }
                }
            }

            if (k === cabinetInstallRecordCount - 1)
            {
                //Update progress
                //console.log("checked = " + cabinetInstallCheckCount)
                //console.log("total = " + cabinetInstallRecordCount)
                if (cabinetInstallCheckCount === cabinetInstallRecordCount)
                {
                    w2ui["S001Build"].summary[0].XenServerInstallationComplete = 'Complete';
                } else if (cabinetInstallCheckCount < cabinetInstallRecordCount && cabinetInstallCheckCount >= 1)
                {
                    w2ui["S001Build"].summary[0].XenServerInstallationComplete = 'In Progress';
                } else if (cabinetInstallCheckCount === 0)
                {
                    w2ui["S001Build"].summary[0].XenServerInstallationComplete = 'Not Started';
                }

                $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_7')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["XenServerInstallationComplete"] + '</div>';
            }
        }

        var s001BuildRecordCount = s001Build.length;
        var s001BuildCheckCount = 0;
        for (var l = 0; l < s001BuildRecordCount; l++)
        {
            if (l < w2ui["S001Build"].records.length)
            {
                if (w2ui["S001Build"].records[l].S001BuildConfigurationComplete === "")
                {
                    s001Build[l].innerHTML = "";
                } else if (typeof w2ui["S001Build"].records[l].S001BuildConfigurationComplete === "string")
                {
                    if (w2ui["S001Build"].records[l].S001BuildConfigurationComplete.includes("unchecked"))
                    {
                        if (typeof w2ui["S001Build"].records[l].w2ui === "undefined")
                        {
                            var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                            $(selector)[0].checked = false;
                        } else if (typeof w2ui["S001Build"].records[l].w2ui !== "undefined")
                        {
                            if (typeof w2ui["S001Build"].records[l].w2ui.changes === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete === 'undefined')
                            {
                                var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            }

//                            if (typeof w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                        }
                    }
                } else
                {
                    if (w2ui["S001Build"].records[l].S001BuildConfigurationComplete === false)
                    {
                        if (typeof w2ui["S001Build"].records[l].w2ui === "undefined")
                        {
                            var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                            $(selector)[0].checked = false;
                        } else if (typeof w2ui["S001Build"].records[l].w2ui !== "undefined")
                        {
                            if (typeof w2ui["S001Build"].records[l].w2ui.changes === "undefined")
                            {
                                var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            } else if (typeof w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete === 'undefined')
                            {
                                var selector = "#grid_S001Build_data_" + l + "_10 > div > input[type=checkbox]";
                                $(selector)[0].checked = false;
                            }
//                            if (typeof w2ui["S001Build"].records[l].w2ui.changes.XenServerInstallationComplete === 'undefined')
//                            {
//                                var selector = "#grid_S001Build_data_" + k + "_10 > div > input[type=checkbox]";
//                                $(selector)[0].checked = false;
//                            }
                        }
                    }
                }

                //CONTINUE HERE
                //Check the progress
                if (typeof w2ui["S001Build"].records[l].w2ui !== 'undefined')
                {
                    if (typeof w2ui["S001Build"].records[l].w2ui.changes !== 'undefined')
                    {
                        if (typeof w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete !== 'undefined')
                        {
                            if (new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete))
                            {
                                s001BuildCheckCount++;
                            }
                        } else if (typeof w2ui["S001Build"].records[l].w2ui.changes.S001BuildConfigurationComplete === 'undefined')
                        {
                            if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete))
                            {
                                s001BuildCheckCount++;
                            }
                        }
                    } else
                    {
                        if (typeof w2ui["S001Build"].records[l].w2ui.changes === 'undefined')
                        {
                            if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete))
                            {
                                s001BuildCheckCount++;
                            }
                        }
                    }

                } else if (typeof w2ui["S001Build"].records[l].w2ui === 'undefined')
                {
                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete) || new RegExp("\\b" + "true" + "\\b").test(w2ui["S001Build"].records[l].S001BuildConfigurationComplete))
                    {
                        s001BuildCheckCount++;
                    }
                }

            }




            //console.log("length " + recordCount);
            //console.log("checked " + checkCount);



            if (l === s001BuildRecordCount - 1)
            {
                //console.log("checked = " + s001BuildCheckCount)
                //console.log("total = " + s001BuildRecordCount)
                //Update progress
                if (s001BuildCheckCount === s001BuildRecordCount)
                {
                    w2ui["S001Build"].summary[0].S001BuildConfigurationComplete = 'Complete';
                } else if (s001BuildCheckCount < s001BuildRecordCount && s001BuildCheckCount >= 1)
                {
                    w2ui["S001Build"].summary[0].S001BuildConfigurationComplete = 'In Progress';
                } else if (s001BuildCheckCount === 0)
                {
                    w2ui["S001Build"].summary[0].S001BuildConfigurationComplete = 'Not Started';
                }

                $("#grid_S001Build_rec_S-1").find('#grid_S001Build_data_0_10')[0].innerHTML = '<div style="text-align: center;">' + w2ui["S001Build"].summary[0]["S001BuildConfigurationComplete"] + '</div>';
            }
        }
    };

    Checklist.prototype.checklistSwitch = function (event)
    {
        switch (event.target) {
            case 'Overview Tab':
                w2ui.Layout.content('main', w2ui.Overview);
                grid = "Overview";
                break;
            case 'test':
                if (typeof w2ui.testshane === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Shane";
                    let local_tab_identifier = "test";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("testshane", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "test", tab, "tests", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.testshane);

                if (detectMobile())
                {
                    w2ui.testshane.on('select', function (event) {
                        //console.log(event)
                        event.onComplete = function () {
                            checklist.changeRecord("testshane");
                        };
                    });
                } else
                {
                    w2ui.testshane.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function (event) {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("testshane");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("testshane");

                grid = "testshane";
                break;
            case 'Contact Tab':
                w2ui.Layout.content('main', w2ui.Contact);

                w2ui.Contact.on('change', function (event) {
                    event.onComplete = function () {

                        checklist.changeRecordContact();

                    };


                });



                w2ui.Contact.on('editField', function (event) {
                    var value = event.originalEvent.path[0].innerText;

                    for (var i = 0; i < w2ui.Contact.records.length; i++)
                    {
                        if (value === w2ui.Contact.records[i].contactName)
                        {
                            if (w2ui.Contact.records[i].contactType !== "TCA")
                            {
                                event.preventDefault();
                            }
                        }
                    }
                });


                w2ui.Contact.on('restore', function (event) {
                    event.onComplete = function () {
                        checklist.changeRecordContact();
                    };


                });


                checklist.checkIfAllowedEdit("Contact");

                grid = "Contact";
                break;
            case 'Comms Backend Tab':
                if (typeof w2ui.CommsBackend === "undefined")
                {
                    //TO DO - Added categories to function, might be able to get categories from database and check in loop if thye match and pass that in instead
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Backend";
                    let local_tab_identifier = "Comms Backend Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("CommsBackend", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Comms Backed", tab, "Setup Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.CommsBackend);

                if (detectMobile())
                {
                    w2ui.CommsBackend.on('select', function (event) {
                        //console.log(event)
                        event.onComplete = function () {
                            checklist.changeRecord("CommsBackend");
                        };
                    });
                } else
                {
                    w2ui.CommsBackend.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function (event) {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("CommsBackend");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("CommsBackend");

                grid = "CommsBackend";
                break;
            case 'Xen Server Setup Tab':
                if (typeof w2ui.XenServerSetup === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Server - Base";
                    let local_tab_identifier = "Xen Server Setup Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("XenServerSetup", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Xen Server Setup", tab, "Xen Server Setup Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.XenServerSetup);

                if (detectMobile())
                {
                    w2ui.XenServerSetup.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("XenServerSetup");
                        };
                    });
                } else
                {
                    w2ui.XenServerSetup.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("XenServerSetup");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                grid = "XenServerSetup";
                checklist.checkIfAllowedEdit("XenServerSetup");
                break;
            case 'S001 Build & Setup Tab':
                w2ui.Layout.content('main', w2ui.S001Build);

                grid = "S001Build";
                checklist.checkIfAllowedEdit("S001Build");
                break;
            case 'Adm & WKS Base Setup Tab':
                if (typeof w2ui.AdmWksSetup === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Workstations - Backend";
                    let local_tab_identifier = "Adm & WKS Base Setup Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("AdmWksSetup", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Admin / Workstation Base Setup", tab, "Admin / Workstation Base Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.AdmWksSetup);

                if (detectMobile())
                {
                    w2ui.AdmWksSetup.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("AdmWksSetup");
                        };
                    });
                } else
                {
                    w2ui.AdmWksSetup.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("AdmWksSetup");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("AdmWksSetup");
                grid = "AdmWksSetup";

                break;
            case 'TCDDC & Xen Desktop Tab':
                if (typeof w2ui.tcddcXenDesktop === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Workstations - Backend";
                    let local_tab_identifier = "TCDDC & Xen Desktop Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("tcddcXenDesktop", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "TCDDC / Xen Desktop", tab, "Xen Server Setup Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.tcddcXenDesktop);

                if (detectMobile())
                {
                    w2ui.tcddcXenDesktop.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("tcddcXenDesktop");
                        };
                    });
                } else
                {
                    w2ui.tcddcXenDesktop.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("tcddcXenDesktop");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("tcddcXenDesktop");
                grid = "tcddcXenDesktop";
                break;
            case 'C001 Build Tab':
                if (typeof w2ui.c001Build === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Cache";
                    let local_tab_identifier = "C001 Build Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("c001Build", "jsonGenerator/optionalGenericGridFormat.php", "backend/save/saveOptionalTasks.php", "C001 Build", tab, "C001 Build Complete", "checklist_checklistOptionalTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.c001Build);

                if (detectMobile())
                {
                    w2ui.c001Build.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("c001Build");
                        };
                    });
                } else
                {
                    w2ui.c001Build.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("c001Build");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("c001Build");
                grid = "c001Build";
                break;
            case 'Email Machine Tab':
                if (typeof w2ui.emailMachine === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Email";
                    let local_tab_identifier = "Email Machine Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("emailMachine", "jsonGenerator/optionalGenericGridFormat.php", "backend/save/saveOptionalTasks.php", "Email Machine", tab, "Email Machine Complete", "checklist_checklistOptionalTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.emailMachine);

                if (detectMobile())
                {
                    w2ui.emailMachine.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("emailMachine");
                        };
                    });
                } else
                {
                    w2ui.emailMachine.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("emailMachine");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("emailMachine");
                grid = "emailMachine";
                break; 
                
            case 'NVR & LMC Tab':
                if (typeof w2ui.NVRAndLMC === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "DVR";
                    let local_tab_identifier = "NVR & LMC Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("NVRAndLMC", "jsonGenerator/optionalGenericGridFormat.php", "backend/save/saveOptionalTasks.php", "NVR & LMC Setup", tab, "NVR & LMC Complete", "checklist_checklistOptionalTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.NVRAndLMC);

                if (detectMobile())
                {
                    w2ui.NVRAndLMC.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("NVRAndLMC");
                        };
                    });
                } else
                {
                    w2ui.NVRAndLMC.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("NVRAndLMC");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("NVRAndLMC");
                grid = "NVRAndLMC";

                break;
            case 'Endpoints & VM Configurations Tab':
                if (typeof w2ui.EndpointsAndVMConfigurations === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Workstations - Ready";
                    let local_tab_identifier = "Endpoints & VM Configurations Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("EndpointsAndVMConfigurations", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Endpoints & VM's", tab, "Endpoints & VMs Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.EndpointsAndVMConfigurations);

                if (detectMobile())
                {
                    w2ui.EndpointsAndVMConfigurations.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("EndpointsAndVMConfigurations");
                        };
                    });
                } else
                {
                    w2ui.EndpointsAndVMConfigurations.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("EndpointsAndVMConfigurations");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("EndpointsAndVMConfigurations");
                grid = "EndpointsAndVMConfigurations";
                break;
            case 'Demos Tab':
                if (typeof w2ui.Demos === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Demo";
                    let local_tab_identifier = "Demos Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("Demos", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Demo Checks", tab, "Demos", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.Demos);

                if (detectMobile())
                {
                    w2ui.Demos.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("Demos");
                        };
                    });
                } else
                {
                    w2ui.Demos.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("Demos");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("Demos");
                grid = "Demos";
                break;
            case 'Final Checks Tab':
                if (typeof w2ui.FinalChecks === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Final Checks";
                    let local_tab_identifier = "Final Checks Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("FinalChecks", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "Final Checks", tab, "Final Checks Complete", "checklist_checklistTasks", [category]);
                }

                w2ui.Layout.content('main', w2ui.FinalChecks);

                if (detectMobile())
                {
                    w2ui.FinalChecks.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("FinalChecks");
                        };
                    });
                } else
                {
                    w2ui.FinalChecks.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("FinalChecks");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("FinalChecks");
                grid = "FinalChecks";
                break;
            case '10 Day - Post Live Review Tab':
                if (typeof w2ui.PostLiveReview === "undefined")
                {
                    let category_identifires = checklist.getCategoryIdentifiers();
                    let tab_identifiers = checklist.getTabIdentifiers();

                    let local_category_identifier = "Post Live Review";
                    let local_tab_identifier = "10 Day - Post Live Review Tab";
                    let found = false;

                    let category;
                    let tab;

                    for (let i = 0; i < category_identifires.length; i++)
                    {
                        if (category_identifires[i].identifier === local_category_identifier)
                        {
                            category = category_identifires[i].categoryName;

                            for (let j = 0; j < tab_identifiers.length; j++)
                            {
                                if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                                {
                                    tab = tab_identifiers[j].tabName;
                                    found = true;

                                    break;
                                }
                            }
                        }

                        if (found)
                        {
                            break;
                        }
                    }

                    checklist.createGenericGrid("PostLiveReview", "jsonGenerator/genericGridFormat.php", "backend/save/saveGenericTasks.php", "10 Day Post Live Review", tab, "Completed", "checklist_checklistTasks", [category]);
                }


                w2ui.Layout.content('main', w2ui.PostLiveReview);

                if (detectMobile())
                {
                    w2ui.PostLiveReview.on('select', function (event) {
                        event.onComplete = function () {
                            checklist.changeRecord("PostLiveReview");
                        };
                    });
                } else
                {
                    w2ui.PostLiveReview.on('click', function (event) {
                        if (event.originalEvent.path[0].localName !== "input")
                        {
                            event.preventDefault();
                        } else
                        {
                            event.onComplete = function () {
                                if (event.originalEvent.path[0].localName === "input" && allowedToEdit && userBlockedFromEdit === false)
                                {
                                    checklist.changeRecord("PostLiveReview");
                                } else
                                {
                                    event.preventDefault();
                                }
                            };
                        }
                    });
                }

                checklist.checkIfAllowedEdit("PostLiveReview");
                grid = "PostLiveReview";
                break;

        }
    };

    Checklist.prototype.checkIfToSave = function (passedGrid, event)
    {
        var size = w2ui[passedGrid].records.length;
        var askSave = false;

        for (var i = 0; i < size; i++)
        {
            if (typeof w2ui[passedGrid].records[i].w2ui !== "undefined")
            {
                if (typeof w2ui[passedGrid].records[i].w2ui.changes !== "undefined")
                {
                    askSave = true;
                }
            }
        }

        if (askSave)
        {
            Swal.fire({
                type: 'warning',
                title: 'Save Changes?',
                text: "If you change tabs now you will lose you're changes. Save Changes?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save Changes'
            }).then(function (result) {

                if (result.value) {
                    w2ui[passedGrid].save();
                    checklist.checklistSwitch(event);
                } else
                {
                    checklist.checklistSwitch(event);
                }
            });

        } else
        {
            checklist.checklistSwitch(event);
        }
    };

    Checklist.prototype.createGenericGrid = function (name, link, link2, header, tab, summary, table, category)
    {
        $().w2grid({
            name: name,
            url: {
                get: link,
                save: link2
            },
            header: header,
            fixedBody: false,
            multiSelect: false,
            show: {
                header: true,
                toolbar: true,
                toolbarSave: true,
                footer: false,
                lineNumbers: false,
                toolbarSearch: false,
                toolbarReload: false,
                toolbarColumns: false,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarEdit: false
            },
            multiSearch: true,
            searches: [
                {field: 'taskName', caption: 'Task Name', type: 'text'}
            ],
            columns: [
                {
                    field: 'taskName',
                    caption: 'Task',
                    size: '50%',
                    sortable: false,
                    resizable: true
                },
                {
                    field: 'complete',
                    caption: 'Progress',
                    size: '50%',
                    sortable: false,
                    resizable: true,
                    editable: false
                }
            ],
            postData: {
                key: key,
                tab: tab,
                summary: summary,
                table: table,
                category: category,
                technician: sessionStorage.getItem("userName"),
                overnightSupport: checklist.getOvernightSupport(),
                email: sessionStorage.getItem("email"),
                siteCode: checklist.getSiteCode(),
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            },
            onError: function (event) {
                console.log(event);
            }
        });
    };

    Checklist.prototype.changeRecord = function (gridName)
    {
        var id = w2ui[gridName].getSelection(true)[0];
        var recId = w2ui[gridName].getSelection()[0];
        var recordCount = w2ui[gridName].records.length;
        var checkCount = 0;

        if (typeof recId !== 'undefined')
        {
            sessionStorage.setItem("recID", recId);
        } else if (sessionStorage.getItem("recID") !== null) {
            recId = sessionStorage.getItem("recID");
        }

        if (typeof id !== 'undefined')
        {
            sessionStorage.setItem("id", id);
        } else if (sessionStorage.getItem("id") !== null)
        {
            id = sessionStorage.getItem("id");
        }

        if (typeof w2ui[gridName].records[id].w2ui !== 'undefined')
        {
            if (typeof w2ui[gridName].records[id].w2ui.changes !== 'undefined')
            {
                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                {
                    delete w2ui[gridName].records[id].w2ui;
                    w2ui[gridName].set(recId, {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'});
                    w2ui[gridName].refresh();
                } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                {
                    delete w2ui[gridName].records[id].w2ui;
                    w2ui[gridName].set(recId, {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'});
                    w2ui[gridName].refresh();
                }
            } else
            {
                if (typeof w2ui[gridName].records[id].w2ui.changes !== 'undefined')
                {
                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                    {
                        delete w2ui[gridName].records[id].w2ui;
                        w2ui[gridName].set(recId, {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'});
                        w2ui[gridName].refresh();
                    } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                    {
                        delete w2ui[gridName].records[id].w2ui;
                        w2ui[gridName].set(recId, {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'});
                        w2ui[gridName].refresh();
                    }
                } else if (typeof w2ui[gridName].records[id].w2ui.changes === 'undefined')
                {
                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].complete))
                    {

                        w2ui[gridName].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                        w2ui[gridName].refresh();
                    } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].complete))
                    {
                        w2ui[gridName].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui[gridName].refresh();


                    }
                }
            }

        } else if (typeof w2ui[gridName].records[id].w2ui === 'undefined')
        {
            if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].complete))
            {
                w2ui[gridName].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                w2ui[gridName].refresh();
            } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].complete))
            {
                w2ui[gridName].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                w2ui[gridName].refresh();
            }
        }






        if (typeof w2ui[gridName].records[id].w2ui === 'undefined')
        {
            if (w2ui[gridName].records[id]["taskName"] === "Server Prebuilt by 3rd Party" && new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].complete))
            {
                //alert("undefined 1")
                for (var i = 0; i < recordCount; i++)
                {
                    var tempRecId = w2ui[gridName].records[i]["recid"];
                    w2ui[gridName].set(tempRecId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                }

                w2ui[gridName].refresh();
            } else if (w2ui[gridName].records[id]["taskName"] === "Server Prebuilt by 3rd Party" && new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].complete))
            {
                //alert("undefined 2")
                for (var i = 0; i < recordCount; i++)
                {
                    delete w2ui[gridName].records[i].w2ui;
                    var tempRecId = w2ui[gridName].records[i]["recid"];
                    w2ui[gridName].set(tempRecId, {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'});
                }

                w2ui[gridName].refresh();
            }
        } else
        {
            if (typeof w2ui[gridName].records[id].w2ui.changes.complete !== 'undefined')
            {
                if (w2ui[gridName].records[id]["taskName"] === "Server Prebuilt by 3rd Party" && new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                {
                    //alert("NOT undefined 1")
                    for (var i = 0; i < recordCount; i++)
                    {
                        var tempRecId = w2ui[gridName].records[i]["recid"];
                        w2ui[gridName].set(tempRecId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                    }

                    w2ui[gridName].refresh();
                } else if (w2ui[gridName].records[id]["taskName"] === "Server Prebuilt by 3rd Party" && new RegExp("\\b" + "unchecked" + "\\b").test(w2ui[gridName].records[id].w2ui.changes.complete))
                {
                    //alert("NOT undefined 2")
                    for (var i = 0; i < recordCount; i++)
                    {
                        var tempRecId = w2ui[gridName].records[i]["recid"];
                        w2ui[gridName].set(tempRecId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                    }

                    w2ui[gridName].refresh();
                }
            }
        }



        //console.log(w2ui[gridName]);



        //Check the progress
        for (var i = 0; i < recordCount; i++)
        {
            if (typeof w2ui[gridName].records[i].w2ui !== 'undefined')
            {
                if (typeof w2ui[gridName].records[i].w2ui.changes !== 'undefined')
                {
                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[i].w2ui.changes.complete))
                    {
                        checkCount++;
                    }

                } else
                {
                    if (typeof w2ui[gridName].records[i].w2ui.changes === 'undefined')
                    {
                        if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[i].complete) && w2ui[gridName].records[i].taskName !== "Server Prebuilt by 3rd Party")
                        {
                            checkCount++;
                        }

                        if (w2ui[gridName].records[i].taskName === "Server Prebuilt by 3rd Party")
                        {
                            recordCount = w2ui[gridName].records.length - 1;
                        }
                    }
                }

            } else if (typeof w2ui[gridName].records[i].w2ui === 'undefined')
            {

                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui[gridName].records[i].complete) && w2ui[gridName].records[i].taskName !== "Server Prebuilt by 3rd Party")
                {
                    checkCount++;
                }

                if (w2ui[gridName].records[i].taskName === "Server Prebuilt by 3rd Party")
                {
                    recordCount = w2ui[gridName].records.length - 1;
                }
            }
        }

        //console.log("length " + recordCount);
        //console.log("checked " + checkCount);

        //Update progress
        if (checkCount === recordCount)
        {
            w2ui[gridName].summary[0].complete = 'Complete';
            w2ui[gridName].refresh();
        } else if (checkCount < recordCount && checkCount >= 1)
        {
            w2ui[gridName].summary[0].complete = 'In Progress';
            w2ui[gridName].refresh();
        } else if (checkCount === 0)
        {
            w2ui[gridName].summary[0].complete = 'Not Started';
            w2ui[gridName].refresh();
        }
    };

    Checklist.prototype.changeRecordContact = function ()
    {
        //console.log(w2ui["Contact"]);
        var id = w2ui["Contact"].getSelection(true)[0];
        var recId = w2ui["Contact"].getSelection()[0];
        var recordCount = w2ui["Contact"].records.length;
        var checkCount = 0;

        if (w2ui["Contact"].records[id])
            if (typeof recId !== 'undefined')
            {
                sessionStorage.setItem("recID", recId);
            } else if (sessionStorage.getItem("recID") !== null) {
                recId = sessionStorage.getItem("recID");
            }

        if (typeof id !== 'undefined')
        {
            sessionStorage.setItem("id", id);
        } else if (sessionStorage.getItem("id") !== null)
        {
            id = sessionStorage.getItem("id");
        }



        if (typeof w2ui["Contact"].records[id].w2ui !== 'undefined')
        {
            if (typeof w2ui["Contact"].records[id].w2ui.changes !== 'undefined')
            {
                //alert("Changes is defined");
                //alert(w2ui["Contact"].records[id].contactName)

                if (typeof w2ui["Contact"].records[id].w2ui.changes.contactName !== 'undefined' && typeof w2ui["Contact"].records[id].w2ui.changes.contactNumber !== 'undefined')
                {
                    //alert("Both values are changed");
                    //both values are defined
                    if (w2ui["Contact"].records[id].w2ui.changes.contactName !== '' && w2ui["Contact"].records[id].w2ui.changes.contactNumber !== '')
                    {
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui["Contact"].refresh();
                    } else
                    {
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                        w2ui["Contact"].refresh();
                    }
                } else if (w2ui["Contact"].records[id].contactName !== null && typeof w2ui["Contact"].records[id].w2ui.changes.contactNumber !== 'undefined') {
                    //alert("one change value is defined and so is the const 1");

                    if (typeof w2ui["Contact"].records[id].w2ui.changes.contactNumber === 'string' && new RegExp("\\b" + "unchecked" + "\\b").test(w2ui["Contact"].records[id].w2ui.changes.complete))
                    {
                        //alert('2'); 
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui["Contact"].refresh();
                    } else if (typeof w2ui["Contact"].records[id].w2ui.changes.contactNumber === 'string' && new RegExp("\\b" + "checked" + "\\b").test(w2ui["Contact"].records[id].complete))
                    {
                        //alert('3');
                        if (w2ui["Contact"].records[id].w2ui.changes.contactNumber.length === 0)
                        {
                            w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                            w2ui["Contact"].refresh();
                        }
                    } else if (w2ui["Contact"].records[id].w2ui.changes.contactNumber !== '' && w2ui["Contact"].records[id].contactName.length > 0)
                    {
                        //alert("4");
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui["Contact"].refresh();
                    } else if (w2ui["Contact"].records[id].w2ui.changes.contactNumber !== '' && w2ui["Contact"].records[id].contactName.length === 0)
                    {
                        //alert("5"); 
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                        w2ui["Contact"].refresh();
                    }

                } else if (typeof w2ui["Contact"].records[id].w2ui.changes.contactName !== 'undefined' && w2ui["Contact"].records[id].contactNumber !== '')
                {
                    //alert("one change value is defined and so is the const 2");

                    if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["Contact"].records[id].complete) && w2ui["Contact"].records[id].w2ui.changes.contactName === '' || w2ui["Contact"].records[id].contactName === '')
                    {
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                        w2ui["Contact"].refresh();
                    } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui["Contact"].records[id].complete))
                    {
                        //alert("dd") 
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui["Contact"].refresh();
                    } else if (new RegExp("\\b" + "unchecked" + "\\b").test(w2ui["Contact"].records[id].w2ui.changes.complete) && w2ui["Contact"].records[id].w2ui.changes.contactName !== '' && w2ui["Contact"].records[id].contactName !== '')
                    {
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                        w2ui["Contact"].refresh();
                    }
                } else
                {
                    if (w2ui["Contact"].records[id].w2ui.changes.contactName !== '' && w2ui["Contact"].records[id].w2ui.changes.contactNumber === '')
                    {
                        //alert("sdsdfsdf") 
                        w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" unchecked>'}}});
                        w2ui["Contact"].refresh();
                    }
                }
            } else
            {
                //alert("df");
                //false alarm
                if (w2ui["Contact"].records[id].contactName !== '' && w2ui["Contact"].records[id].contactNumber !== '')
                {
                    w2ui["Contact"].set(recId, {w2ui: {changes: {complete: '<input class="complete" type="checkbox" style="cursor: not-allowed; transform: scale(1.2); margin-left: 1px;" onclick="return false;" readonly="readonly" checked>'}}});
                    w2ui["Contact"].refresh();
                }
            }
        }

        //Check the progress
        for (var i = 0; i < recordCount; i++)
        {
            if (typeof w2ui["Contact"].records[i].w2ui !== 'undefined')
            {
                if (typeof w2ui["Contact"].records[i].w2ui.changes !== 'undefined')
                {
                    if (typeof w2ui["Contact"].records[i].w2ui.changes.complete !== 'undefined')
                    {
                        if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["Contact"].records[i].w2ui.changes.complete))
                        {
                            checkCount++;
                        }
                    } else
                    {
                        if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["Contact"].records[i].complete))
                        {
                            checkCount++;
                        }
                    }
                }
            } else
            {
                if (new RegExp("\\b" + "checked" + "\\b").test(w2ui["Contact"].records[i].complete))
                {
                    checkCount++;
                }
            }
        }

        //Update progress
        if (checkCount === recordCount)
        {
            w2ui["Contact"].summary[0].complete = 'Complete';
            w2ui["Contact"].refresh();
        } else if (checkCount < recordCount && checkCount >= 1)
        {
            w2ui["Contact"].summary[0].complete = 'In Progress';
            w2ui["Contact"].refresh();
        } else if (checkCount === 0)
        {
            w2ui["Contact"].summary[0].complete = 'Not Started';
            w2ui["Contact"].refresh();
        }
    };

    Checklist.prototype.checkIfAllowedEdit = function (gridName)
    {
        if (sessionStorage.getItem("userName") !== null)
        {
            allowedToEdit = true;
        } else
        {
            allowedToEdit = false;
            event.preventDefault();
        }

        //console.log(data);

        if (sessionStorage.getItem("userName") !== null)
        {
            allowedToEdit = true;
        } else
        {
            allowedToEdit = false;
        }


        if (detectMobile())
        {
            if (gridName === "Overview")
            {
                w2ui[gridName].on('change', function (event)
                {
                    if (allowedToEdit === false && sessionStorage.getItem("userName") === null)
                    {
                        event.preventDefault();

                        Swal.fire({
                            type: 'error',
                            title: 'Not allowed to edit',
                            text: 'Please login to edit the checklist.',
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function (result) {
                            if (result.value) {
                                $('#modalLogin').modal('toggle');
                            }
                        });

                        allowedToEdit = false;

                    } else if (allowedToEdit === false && sessionStorage.getItem("userName") !== null)
                    {
                        allowedToEdit = true;
                    }
                });
            } else
            {
                w2ui[gridName].on('select', function (event)
                {
                    if (allowedToEdit === false && sessionStorage.getItem("userName") === null)
                    {
                        event.preventDefault();

                        Swal.fire({
                            type: 'error',
                            title: 'Not allowed to edit',
                            text: 'Please log in to edit the checklist.',
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function (result) {
                            if (result.value) {
                                $('#modalLogin').modal('toggle');
                            }
                        });

                        allowedToEdit = false;

                    } else if (allowedToEdit === false && sessionStorage.getItem("userName") !== null)
                    {
                        allowedToEdit = true;
                    }
                });
            }
        } else
        {
            if (gridName === "Overview")
            {
                w2ui[gridName].on('change', function (event)
                {
                    if (allowedToEdit === false && sessionStorage.getItem("userName") === null)
                    {
                        event.preventDefault();

                        Swal.fire({
                            type: 'error',
                            title: 'Not allowed to edit',
                            text: 'Please log in to edit the checklist.',
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function (result) {
                            if (result.value) {
                                $('#modalLogin').modal('toggle');
                            }
                        });

                        allowedToEdit = false;
                    } else if (allowedToEdit === false && sessionStorage.getItem("userName") !== null)
                    {
                        allowedToEdit = true;
                    }
                });
            } else
            {
                w2ui[gridName].on('click', function (event)
                {
                    if (allowedToEdit === false && sessionStorage.getItem("userName") === null)
                    {
                        event.preventDefault();

                        Swal.fire({
                            type: 'error',
                            title: 'Not allowed to edit',
                            text: 'Please log in to edit the checklist.',
//                footer: '<a href>Why do I have this issue?</a>',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Login'
                        }).then(function (result) {
                            if (result.value) {
                                $('#modalLogin').modal('toggle');
                            }
                        });

                        allowedToEdit = false;
                    } else if (allowedToEdit === false && sessionStorage.getItem("userName") !== null)
                    {
                        allowedToEdit = true;
                    }
                });
            }
        }
    };

    Checklist.prototype.initializeChecklist = function ()
    {
        $().w2grid({
            name: 'Overview',
            url: {
                get: 'jsonGenerator/overview.php',
                save: 'backend/save/saveCategoryDates.php'
            },
            header: checklist.getChecklistHeader(),
            fixedBody: false,
            show: {
                header: true,
                toolbar: true,
                toolbarSave: true,
                footer: false,
                lineNumbers: false,
                toolbarSearch: false,
                toolbarReload: false,
                toolbarColumns: false,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarEdit: false
            },
            multiSelect: false,
            columns: [
                {
                    field: 'siteDetailsColumn',
                    caption: 'Site Details',
                    size: '135px',
                    sortable: false,
                    resizable: true
//                editable: {type: 'text'}
                },
                {
                    field: 'siteDetailsColumnValue',
                    caption: '',
                    size: '130px',
                    sortable: false,
                    resizable: true
                },
                {
                    field: 'categories',
                    caption: 'Categories',
                    size: '270px',
                    sortable: false,
                    resizable: true
                },
                {
                    field: 'progress',
                    caption: 'Progress',
                    size: '71px',
                    sortable: false,
                    resizable: true,
                    render: function (record) {
                        var html = '';
                        //console.log(record.progress);

                        if (record.progress === 'Complete')
                        {
                            html = '<div class="completed">' + record.progress + '</div>';
                        } else if (record.progress === 'In Progress')
                        {
                            html = '<div class="inProgress">' + record.progress + '</div>';
                        } else
                        {
                            html = '<div>' + record.progress + '</div>';
                        }

                        return html;
                    }
                },
                {
                    field: 'expected',
                    caption: 'Expected',
                    size: '135px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'}
                },
                {
                    field: 'completedOn',
                    caption: 'Completed',
                    size: '168px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'datetime', format: 'yyyy-mm-dd|hh:mm|h24'}
                }
//                ,{
//                    field: 'completed',
//                    caption: 'Completed',
//                    size: '76px',
//                    sortable: false,
//                    resizable: true
//                }
            ],
            postData: {
                key: key
            },
            onLoad: function (event)
            {
                event.onComplete = function () {
                    checklist.checkIfAllowedEdit("Overview");

                    $(".completed").parent().css('background-color', '#a4d95b');
                    $(".inProgress").parent().css('background-color', '#80bfff');

                    var lastScrollLeft = 0;
                    $('#grid_Overview_records').scroll(function () {

                        var documentScrollLeft = $('#grid_Overview_records').scrollLeft();
                        if (lastScrollLeft !== documentScrollLeft) {
                            $(".completed").parent().css('background-color', '#a4d95b');
                            $(".inProgress").parent().css('background-color', '#80bfff');
                        }
                    });
                };
            },
            onSave: function (event) {
                event.onComplete = function () {
                    $(".completed").parent().css('background-color', '#a4d95b');
                    $(".inProgress").parent().css('background-color', '#80bfff');
                };
            },
            onError: function (event) {
                console.log(event);
            }
        });

        $().w2grid({
            name: 'Contact',
            url: {
                get: "jsonGenerator/contactFormat.php",
                save: 'backend/save/saveContactTasks.php'
            },
            header: 'Contact Details',
            fixedBody: false,
            show: {
                header: true,
                toolbar: true,
                toolbarSave: true,
                footer: false,
                lineNumbers: false,
                toolbarSearch: false,
                toolbarReload: false,
                toolbarColumns: false,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarEdit: false
            },
            multiSelect: false,
            columns: [
                {
                    field: 'contactType',
                    caption: 'Type',
                    size: '50%',
                    sortable: false,
                    resizable: true
                },
                {
                    field: 'contactName',
                    caption: 'Name',
                    size: '50%',
                    sortable: false,
                    resizable: true,
                    editable: {
                        type: 'text'
                    }
                },
                {
                    field: 'contactNumber',
                    caption: 'Phone Number',
                    size: '50%',
                    sortable: false,
                    resizable: true,
                    editable: {
                        type: 'text'
                    }
                },
                {
                    field: 'complete',
                    caption: 'Progress',
                    size: '50%',
                    sortable: false,
                    resizable: true
                }
            ],
            postData: {
                key: key,
                summary: "Contact Complete",
                technician: sessionStorage.getItem("userName"),
                overnightSupport: checklist.getOvernightSupport(),
                email: sessionStorage.getItem("email"),
                siteCode: checklist.getSiteCode(),
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            },
            onSave: function (event) {
                w2ui.Contact.postData['category'] = category_contact;
            },
            onLoad: function (event) {
                let category_identifires = checklist.getCategoryIdentifiers();
                let tab_identifiers = checklist.getTabIdentifiers();

                let local_category_identifier = "Contact";
                let local_tab_identifier = "Contact Tab";
                let found = false;

                for (let i = 0; i < category_identifires.length; i++)
                {
                    if (category_identifires[i].identifier === local_category_identifier)
                    {
                        category_contact = category_identifires[i].categoryName;
                        for (let j = 0; j < tab_identifiers.length; j++)
                        {
                            if (tab_identifiers[j].tab_identifier === local_tab_identifier)
                            {
                                tab_contact = tab_identifiers[j].tabName;
                                found = true;

                                break;
                            }
                        }
                    }

                    if (found)
                    {
                        break;
                    }
                }
            },
            onError: function (event) {
                console.log(event);
            }
        });

        $().w2grid({
            name: 'S001Build',
            url: {
                get: "jsonGenerator/s001GridFormat.php",
                save: 'backend/save/saveS001Tasks.php'
            },
            header: 'XenServer / S001 Build',
            fixedBody: false,
            show: {
                header: true,
                toolbar: true,
                footer: false,
                lineNumbers: false,
                toolbarSearch: false,
                toolbarReload: false,
                toolbarColumns: false,
                toolbarAdd: false,
                toolbarDelete: false,
                toolbarSave: true,
                toolbarEdit: false
            },
            columns: [
                {
                    field: 'ITQuotes',
                    caption: 'Pre Build',
                    size: '145px',
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
                    size: '72px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'checkbox'}
                },
                {
                    field: 'break',
                    resizable: false,
                    size: '28px',
                    render: function (record) {
                        return record.break1;
                    }
                },
                {
                    field: 'S001Migration',
                    caption: 'Pre Build',
                    size: '145px',
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
                    size: '72px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'checkbox'}
                },
                {
                    field: 'break',
                    resizable: false,
                    size: '28px',
                    render: function (record) {
                        return record.break1;
                    }
                },
                {
                    field: 'XenServerInstallation',
                    caption: 'Cabinet Install',
                    size: '248px',
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
                    size: '72px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'checkbox'}
                },
                {
                    field: 'break',
                    resizable: false,
                    size: '28px',
                    render: function (record) {
                        return record.break1;
                    }
                },
                {
                    field: 'S001BuildConfiguration',
                    caption: 'S001 Build Configuration Checks',
                    size: '313px',
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
                    size: '72px',
                    sortable: false,
                    resizable: true,
                    editable: {type: 'checkbox'}
                }
            ], postData: {
                key: key,
                tab: "S001 Build & Setup",
                category: ["Server (Fully Installed in Cabinet)", "Sites Router is Communicating"],
                technician: sessionStorage.getItem("userName"),
                overnightSupport: checklist.getOvernightSupport(),
                email: sessionStorage.getItem("email"),
                siteCode: checklist.getSiteCode(),
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
            },
            onClick: function (event) {
                checklist.checkIfAllowedEdit("S001Build");
                //console.log(w2ui["S001Build"]) 
                if (!allowedToEdit)
                {
                    checklist.s001Formater();

                    event.preventDefault();
                    event.stopPropagation();
                } else
                {
                    checklist.s001Formater();
                }
            },
            onSelect: function (event) {
                checklist.checkIfAllowedEdit("S001Build");

                if (!allowedToEdit)
                {
                    checklist.s001Formater();

                    event.preventDefault();
                    event.stopPropagation();
                } else
                {
                    checklist.s001Formater();
                }
            },
            onChange: function (event) {
                checklist.checkIfAllowedEdit("S001Build");
                if (!allowedToEdit)
                {
                    checklist.s001Formater();

                    event.preventDefault();
                    event.stopPropagation();
                } else
                {
                    checklist.s001Formater();
                }
            },
            onSave: function (event) {

                event.onComplete = function () {
                    checklist.s001Formater();
                    //console.log(w2ui["S001Build"]);
                    //console.log("ttttt")
                    var lastScrollLeft = 0;
                    $('#grid_S001Build_records').scroll(function (event) {

                        var documentScrollLeft = $('#grid_S001Build_records').scrollLeft();
                        if (lastScrollLeft !== documentScrollLeft) {
                            checklist.s001Formater();
                        }
                    });


                };
            },
            onLoad: function (event) {
                event.onComplete = function () {
                    checklist.s001Formater();
                    var lastScrollLeft = 0;
                    $('#grid_S001Build_records').scroll(function (event) {

                        var documentScrollLeft = $('#grid_S001Build_records').scrollLeft();
                        if (lastScrollLeft !== documentScrollLeft) {
                            checklist.s001Formater();
                        }
                    });

                    checklist.s001Formater();
                };
            },
            onError: function (event) {
                console.log(event);
            }
        });

        $('#layout').w2layout({
            name: 'Layout',
            padding: 0,
            panels: [
                {
                    type: 'main',
                    resizable: true,
                    //title: 'Global Site Technology Services Dashboard',
                    style: style
                },
                {
                    type: 'top',
                    style: style,
                    size: '3.5%',
                    tabs: {
                        name: 'tabs',
                        active: 'Overview Tab',
                        tabs: tabsArray,
                        onClick: function (event) {
                            event.onComplete = function (event) {

                                checklist.checkIfToSave(grid, event);
                                //console.log(grid);
                            };
                        }
                    }
                }]
        });

        w2ui.Layout.content('main', w2ui.Overview);
    };

    Checklist.prototype.getTabsAndStart = function ()
    {
        $.ajax({
            type: 'POST',
            url: "jsonGenerator/tab.php",
            dataType: 'text',
            data: {checklistID: key},
            success: function (data) {
                //console.log(data);
                var json = JSON.parse(data);

                for (var i = 0; i < json.length; i++)
                {
                    tabsArray.push(json[i]);
                }


                if (typeof tabsArray === 'undefined' || tabsArray === null)
                {
                    Swal.fire({
                        type: 'error',
                        title: 'Checklist initialization failed.',
                        text: 'An error has occured when initializing your checklist, please wait a bit and refresh the page to try again.',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Refresh Page'
                    }).then(function (result) {
                        if (result.value) {
                            location.reload();
                        }
                    });
                } else
                {
                    //console.log(tabsArray); 
                    checklist.initializeChecklist();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    };

    function createChecklist()
    {
        $.ajax({
            type: 'POST',
            url: "backend/getRequiredSiteData.php",
            dataType: 'text',
            data: {accessCode: "checklistData", checklistID: key},
            success: function (data) {
                var localChecklist;
                //console.log(data);
                var json = JSON.parse(data);
                var json_length = json.length;

                let category_identifiers = [];
                let tab_identifiers = [];

                //console.log(json);

                localChecklist = new Checklist(key, json[0].siteCode, json[0].siteName, json[0].siteCountry, json[0].siteType, json[0].activityType, json[0].expectedGoLiveDate, json[0].createdOn, json[0].technician, json[0].checklistHeader, json[0].complete, json[0].status, json[0].region, json[0].overNightSupport);



                for (let i = 1; i < json_length; i++)
                {
                    let keys = Object.keys(json[i]);

                    if (keys[0] === "categoryName" && keys[1] === "identifier")
                    {
                        category_identifiers.push({categoryName: json[i].categoryName, identifier: json[i].identifier});
                    } else if (keys[0] === "tabName" && keys[1] === "tab_identifier")
                    {
                        tab_identifiers.push({tabName: json[i].tabName, tab_identifier: json[i].tab_identifier});
                    }
                }

                localChecklist.setCategoryIdentifiers(category_identifiers);
                localChecklist.setTabIdentifiers(tab_identifiers);

                if (typeof localChecklist === 'undefined' || localChecklist === null)
                {
                    Swal.fire({
                        type: 'error',
                        title: 'Checklist creation failed.',
                        text: 'An error has occured when creating your checklist, please wait a bit and refresh the page to try again.',
                        showCancelButton: true, //imageUrl: "https://images3.imgbox.com/4f/e6/wOhuryw6_o.jpg",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Refresh Page'
                    }).then(function (result) {
                        if (result.value) {
                            location.reload();
                        }
                    });
                } else
                {
                    //console.log(checklist);
                    $("#siteCallout").text("Site Checklist - " + localChecklist.getSiteCode());

                    checklist = localChecklist;
                    checklist.getTabsAndStart();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                onAjaxError(jqXHR, textStatus, errorThrown);
            }
        });
    }
});