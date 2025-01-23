<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Work Time Report</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            /* max-width: 600px; */
            margin: 0;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: inline-block;
            margin: 8px 0px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        #dialog {
            display: none;
        }

        #departmentList,
        #userList {
            list-style-type: none;
            padding: 0;
        }

        #departmentList li,
        #userList li {
            padding: 5px;
            cursor: pointer;
        }

        #departmentList li:hover,
        #userList li:hover {
            background-color: #f0f0f0;
        }

        .hidden {
            display: none;
        }

        .department-container {
            /* margin: 20px 0; */
            font-family: Arial, sans-serif;
        }

        .parent-department {
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0px;
        }

        .parent-label {
            font-weight: bold;
            color: #000000;
            cursor: pointer;
            border: 1px solid white;
            width: 96.7%;
            /* display: flex; */
            /* align-items: center; */
            padding: 5px;
            border-radius: 3px;
            transition: background-color 0.3s, color 0.3s;
        }

        .parent-label:hover {
            background-color: #f0f8ff;
        }

        .child-departments {
            display: none;
            margin-left: 20px;
            padding-left: 15px;
            border-left: 2px dashed #ccc;
        }

        .child-sub-departments {
            margin-left: 10px;
            display: block;
        }

        .child-checkbox {
            margin: 5px 0;
        }

        input[type="checkbox"] {
            margin-right: 8px;
        }

        .toggle-icon {
            margin-left: auto;
            font-size: 14px;
            padding: 10px;
            cursor: pointer;
        }

        .toggle-icon:hover {
            color: #555;
        }

        /* Custom Scrollbars */
        #employee-list::-webkit-scrollbar,
        #creator-list::-webkit-scrollbar,
        #project-list::-webkit-scrollbar {
            width: 6px;
            /* Thin scrollbar */
        }

        #employee-list::-webkit-scrollbar-track,
        #creator-list::-webkit-scrollbar-track,
        #project-list::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            /* Light track background */
            border-radius: 5px;
        }

        #employee-list::-webkit-scrollbar-thumb,
        #creator-list::-webkit-scrollbar-thumb,
        #project-list::-webkit-scrollbar-thumb {
            background-color: #888;
            /* Thumb color */
            border-radius: 5px;
            transition: background-color 0.3s ease;
            /* Smooth transition */
        }

        #employee-list::-webkit-scrollbar-thumb:hover,
        #creator-list::-webkit-scrollbar-thumb:hover,
        #project-list::-webkit-scrollbar-thumb:hover {
            background-color: #555;
            /* Darker thumb color on hover */
        }

        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }

        /* Section Headers */
        h3 {
            margin-bottom: 10px;
        }

        /* Search Input */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: -1px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Scrollable List */
        #employee-list,
        #creator-list,
        #project-list {
            max-height: 100px;
            /* Adjust height as needed */
            overflow-y: auto;
            /* border: 1px solid #ddd; */
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        /* Label styling for checkboxes */
        #employee-list label,
        #creator-list label,
        #project-list label,
        .child-checkbox {
            border-radius: 5px;
            border: 1px solid #fff;
            margin: 2px;
            padding: 0px 5px 0px 5px;
            cursor: pointer;
        }

        /* Hover Effect for Labels */
        #employee-list label:hover,
        #creator-list label:hover,
        #project-list label:hover,
        .parent-label:hover {
            background-color: #dae7ff;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        /* Checkbox Styling */
        input[type="checkbox"] {
            margin-right: 8px;
        }

        /* Toggle Icon for Departments */
        .toggle-icon {
            margin-left: auto;
            font-size: 14px;
            cursor: pointer;
        }

        .toggle-icon:hover {
            color: #555;
        }

        /* Child Departments */
        .child-departments {
            display: none;
            margin-left: 20px;
            padding-left: 15px;
            border-left: 2px dashed #ccc;
        }

        .child-sub-departments {
            margin-left: 10px;
            display: block;
        }

        /* Table Styles */
        .template-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .template-table thead {
            background-color: #007BFF;
            color: white;
        }

        .template-table th,
        .template-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        .template-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .template-table tr:hover {
            background-color: #ddd;
            transition: background-color 0.3s ease;
        }

        /* Button Styles */
        .btn-load,
        .btn-delete {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-load {
            background-color: #007BFF;
            color: white;
        }

        .btn-load:hover {
            background-color: #0066cc;
            transform: scale(1.05);
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #e53935;
            transform: scale(1.05);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-message {
            color: #fff;
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
        }

        #generateReport,
        #weeklyReportGenerate,
        #dailyReportGenerate {
            margin-top: 20px;
        }

        #employee-content>label>img,
        #creator-content>label>img,
        #project-content>label>img,
        #employee-selected>label>img,
        #creator-selected>label>img,
        #project-selected>label>img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin: 0px 0px -8px 0px;
            border: 1.9px solid #555555;
        }

        #employee-selected>label,
        #creator-selected>label,
        #project-selected>label {
            background-color: #dae7ff;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .buttons {
            position: sticky;
            bottom: 84px;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .buttons button {
            padding: 5.5px 7px;
            margin: -9px 7px 0px 0px;
        }

        .toggle {
            width: 75px;
            height: 30px;
            background: #ddd;
            /* position: absolute; */
            top: 50%;
            left: 50%;
            margin: 25px 0px;
            border-radius: 8px;
            box-shadow: inset -3px -2px 6px 0px rgb(0 0 0 / 23%);
            /* transform: translate(-50%, -50%); */
            transition: .01s ease;
        }

        .toggle .in-toggle input {
            position: relative;
            width: 100%;
            height: 100%;
            cursor: pointer;
            opacity: 0;
            margin: 0;
            z-index: 1;
        }

        .clicked {
            background: #007BFF;
            box-shadow: inset -3px -3px 5px 0px rgb(0 59 255);
        }

        .in-toggle {
            width: 44%;
            height: 87%;
            border-radius: 8px;
            background: #fff;
            position: relative;
            top: -142%;
            transition: .01s ease;
        }

        .tg-left {
            left: 2%;
        }

        .tg-right {
            left: 52%;
        }

        .fields {
            margin: -15px 0px -15px 0px;
        }

        #templates {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        #templatesHead h3 {
            color: #333;
            border-bottom: 2px solid #dddddd;
            padding-bottom: 0px;
            margin-bottom: 0px;
        }

        #templates div {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #templates button {
            margin-left: 10px;
        }

        .template-title {
            flex-grow: 1;
            font-weight: bold;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    require_once(__DIR__ . '/crestcurrent.php');

    // Save auth data if available
    if (!empty($_REQUEST['AUTH_ID']) && !empty($_REQUEST['DOMAIN']) && !empty($_REQUEST['REFRESH_ID']) && !empty($_REQUEST['APP_SID'])) {
        $_SESSION['auth_data'] = [
            'AUTH_ID' => $_REQUEST['AUTH_ID'],
            'DOMAIN' => $_REQUEST['DOMAIN'],
            'REFRESH_ID' => $_REQUEST['REFRESH_ID'],
            'APP_SID' => $_REQUEST['APP_SID'],
            'CLIENT_ENDPOINT' => 'https://' . $_REQUEST['DOMAIN'] . '/rest/'
        ];
        CRestCurrent::saveAuthData($_REQUEST);
    }

    // Use session data if available
    if (!empty($_SESSION['auth_data'])) {
        $authData = $_SESSION['auth_data'];
    } else {
        $authData = [];
    }

    // getting the settings data
    $settings = json_decode(file_get_contents(__DIR__ . '/settings.json'), true);
    ?>
    <h1 style="margin:0;">Work Time Report</h1>
    <!-- <?php echo htmlspecialchars($settings['access_token']); ?> -->

    <form id="myForm" method="POST">
        <div class="commonFields">
            <div class="fields">
                <label for="responsible">Users: <span style="color: black;font-size: 15px;font-family: none;">(includes all members of task who time tracked)</span></label>
                <input type="text" id="search-employees" placeholder="Search responsible..." onkeyup="filterEmployees()">
                <div id="employee-list" style="user-select: none;">
                    <div id="employee-selected"></div>
                    <div id="employee-content" class="hidden"></div>
                    <div class="buttons" style="float: right;">
                        <button type="button" id="selectAllEmployee">Select All</button>
                        <button type="button" id="clearAllEmployee">Clear</button>
                        <button type="button" id="toggleEmployee">Invert</button>
                    </div>
                </div>
            </div>

            <div class="fields">
                <label for="creator">Creator:</label>
                <input type="text" id="search-creators" placeholder="Search creator..." onkeyup="filterCreators()">
                <div id="creator-list" style="user-select: none;">
                    <div id="creator-selected"></div>
                    <div id="creator-content" class="hidden"></div>
                    <div class="buttons" style="float: right;">
                        <button type="button" id="selectAllCreator">Select All</button>
                        <button type="button" id="clearAllCreator">Clear</button>
                        <button type="button" id="toggleCreator">Invert</button>
                    </div>
                </div>
            </div>

            <div class="fields">
                <label for="project">Project:</label>
                <input type="text" id="search-projects" placeholder="Search project..." onkeyup="filterProjects()">
                <div id="project-list" style="user-select: none;">
                    <div id="project-selected"></div>
                    <div id="project-content" class="hidden"></div>
                    <div class="buttons" style="float: right;">
                        <button type="button" id="selectAllProject">Select All</button>
                        <button type="button" id="clearAllProject">Clear</button>
                        <button type="button" id="toggleProject">Invert</button>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap:10px;">
                <div style="width: 50%;">
                    <div id="singleDate" class="hidden">
                        <label for="dateStart">Date Start:</label>
                        <input type="text" id="dateStart" name="dateStart" value="<?php echo htmlspecialchars($_GET['dateStart'] ?? ''); ?>"><br>
                    </div>
                    <div id="bothDate" class="hidden">
                        <label for="dateFinish">Date Finish:</label>
                        <input type="text" id="dateFinish" name="dateFinish" value="<?php echo htmlspecialchars($_GET['dateFinish'] ?? ''); ?>"><br>
                    </div>
                    <Label for="dates">Date:</Label>&nbsp;&nbsp;&nbsp;
                    <input type="radio" id="Today" name="quickDate" value="Today" checked>
                    <label for="Today">Today</label>&nbsp;&nbsp;&nbsp;
                    <input type="radio" id="LastWeek" name="quickDate" value="Last Week">
                    <label for="Last Week">Last Week</label>&nbsp;&nbsp;&nbsp;
                    <input type="radio" id="LastMonth" name="quickDate" value="Last Month">
                    <label for="Last Month">Last Month</label><br>
                    <input type="text" id="dates" name="datefilter" value="" />
                </div>
                <div style="width: 50%;">
                    <label for="tags">Tags:</label>
                    <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($_GET['tags'] ?? ''); ?>"><br>
                </div>
            </div>
        </div>

        <button title="Detailed report of users" type="button" id="generateReport">Detail Report</button>
        <button title="" type="button" id="masterSheet">Master Report</button>
        <button type="button" id="saveTemplate">Save Template</button>
    </form>

    <div id="templatesHead" class="hidden">
        <h3>Saved Templates</h3>
    </div>
    <div id="templates">
        <!-- template here -->
    </div>

    <div id="reportSpace" class="hidden">
        <div class="container" style="position: sticky; top: 1%;z-index:999;">
            <div class="toggle">
                <p style="font-size: 16px;/* margin-top: 50px; */padding-top: 3px;">&nbsp;&nbsp;<span style="color:white;">On</span>&nbsp;&nbsp;&nbsp;Off</p>
                <div class="in-toggle tg-left">
                    <input type="checkbox" id="switch" />
                </div>
            </div>
            <p style="margin-top: -52px;margin-left: 82px;">Task Grouping</p>
        </div>

        <div id="detailedResult">
            <h3>Detailed Report</h3>
            <!-- Report will be displayed here -->
        </div>

        <div class="hidden" id="groupedResult">
            <h3>Grouped Report</h3>
            <!-- Report will be displayed here -->
        </div>
    </div>

    <div class="hidden" id="masterReport">
        <!-- master sheet will be displayed here -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        let users = {};
        let projects = {};
        let currentReport;
        let employeeWise;
        let projectWise;

        $(document).ready(function() {
            $('input[name="datefilter"]').daterangepicker({
                autoUpdateInput: false,
                drops: 'up',
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                document.getElementById('dateStart').value = picker.startDate.format('MM/DD/YYYY');
                document.getElementById('dateFinish').value = picker.endDate.format('MM/DD/YYYY');
            });

            $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                document.getElementById('dateStart').value = '';
                document.getElementById('dateFinish').value = '';
            });

            fetchAllUsers();
            fetchAllProjects();
            // fetchAllDepartments();
            // Handle quick period selection
            $("input[name='quickPeriod']").change(function() {
                setQuickPeriod($(this).val());
            });
            // Handle form submission
            $("#saveTemplate").click(function(event) {
                event.preventDefault(); // Prevent form submission
                // console.log('Save Template button pressed');
                saveTemplate();
            });

            $("#generateReport").click(function(event) {
                event.preventDefault(); // Prevent form submission
                var dateStart = document.getElementById("dateStart").value;
                var dateFinish = document.getElementById("dateFinish").value;

                if (dateStart === "" || dateFinish === "") {
                    alert("Please select both start and finish dates");
                    return;
                }

                generateReport('generateReport', '#detailedResult', '#groupedResult', '#projectGroupedResult');
            });

            $("#masterSheet").click(function(event) {
                event.preventDefault(); // Prevent form submission
                var dateStart = document.getElementById("dateStart").value;
                var dateFinish = document.getElementById("dateFinish").value;

                if (dateStart === "" || dateFinish === "") {
                    alert("Please select both start and finish dates");
                    return;
                }

                generateReport('masterReport', '#detailedResult', '#groupedResult', '#projectGroupedResult');
            });
            displayTemplates();

            function formatDateToYYYYMMDD(date) {
                // Check if the input is already a Date object
                if (!(date instanceof Date)) {
                    date = new Date(date);
                }

                // Check if the date is valid
                if (isNaN(date.getTime())) {
                    throw new Error("Invalid date");
                }

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }
        });

        var toggle_btn = $(".toggle .in-toggle input");
        var i_tg = $(".in-toggle");
        var toggle = $(".toggle");

        toggle_btn.on("click", clicked);

        function clicked() {
            if (toggle_btn.is(":checked")) {
                i_tg.addClass("tg-right");

                toggle.addClass("clicked");
            } else {
                i_tg.removeClass("tg-right");
                toggle.removeClass('clicked');
            }
        }

        function saveTemplate() {

            let templateName = prompt("Enter template name:");
            const data = JSON.parse(localStorage.getItem("templates")) || [];
            let isExist = false

            data.map(template => {
                if (templateName === template.title) {
                    isExist = true;
                }
            });

            if (isExist) {
                alert("please set a different name");
            }


            if (templateName && !isExist) {
                // Gather selected responsible employees
                const responsible = Array.from(
                    document.querySelectorAll('#employee-list input[type="checkbox"]:checked')
                ).map(checkbox => checkbox.value);

                // Gather selected creators
                const creator = Array.from(
                    document.querySelectorAll('#creator-list input[type="checkbox"]:checked')
                ).map(checkbox => checkbox.value);

                // Gather selected projects
                const project = Array.from(
                    document.querySelectorAll('#project-list input[type="checkbox"]:checked')
                ).map(checkbox => checkbox.value);

                // Process tags: Split by comma and trim extra whitespace
                const tagsInput = $("#tags").val();
                const tagsArray = tagsInput ?
                    tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag) // Remove empty tags
                    :
                    []; // Default to an empty array if input is empty

                // Gather dates
                const dateStart = $("#dateStart").val();
                const dateFinish = $("#dateFinish").val();

                // Build formData object
                const formData = {
                    title: templateName,
                    responsible: responsible, // Send as an array
                    creator: creator, // Send as an array
                    project: project, // Send as an array
                    tags: tagsArray, // Ensure empty array if no valid tags
                    startDate: dateStart,
                    endDate: dateFinish,
                };

                const isAvailable = localStorage.getItem("templates");

                // Initialize `templates` in localStorage if it doesn't exist
                if (!isAvailable) {
                    localStorage.setItem("templates", JSON.stringify([])); // Store an empty array as a JSON string
                }

                // Retrieve the templates and parse them into an array
                const templates = JSON.parse(localStorage.getItem("templates"));

                // Add the new formData object to the templates array
                templates.push(formData);

                // Store the updated templates array back into localStorage as a JSON string
                localStorage.setItem("templates", JSON.stringify(templates));

                // Retrieve and parse the stored template to check correctness
                const template = JSON.parse(localStorage.getItem("templates"));
                // console.log(template);
            }
            displayTemplates();
        }

        function displayTemplates() {
            const templateContainer = document.getElementById("templates");

            // Clear existing content to avoid duplication
            templateContainer.innerHTML = "";

            const data = JSON.parse(localStorage.getItem("templates")) || [];
            // console.log(data);

            if (data.length > 0) {
                document.getElementById('templatesHead').style.display = 'block';
            } else {
                document.getElementById('templatesHead').style.display = 'none';
            }

            // Map over the templates and generate HTML content
            const list = data.map((item, index) => {
                return `<div>                            
                    <span class="template-title">${item.title}</span>
                    <button type="button" onclick="loadTemplate(${index});" id="load-${index}">Load</button>
                    <button type="button" onclick="deleteTemplate(${index});" id="delete-${index}">Delete</button>
                </div>`;
            }).join("");

            // Append the generated HTML to the container
            templateContainer.innerHTML += list;
        }

        function loadTemplate(index) {
            // Retrieve the template array from local storage
            const templates = JSON.parse(localStorage.getItem('templates'));

            // Check if the template array exists and the index is valid
            if (templates && index >= 0 && index < templates.length) {
                const template = templates[index];

                // Set the form fields with the template data
                $("#templateName").val(template.title);
                $("#dateStart").val(template.startDate);
                $("#dateFinish").val(template.endDate);
                $("#dates").val(`${template.startDate} - ${template.endDate}`);

                clearAll("employee-list", "employee-content");
                clearAll("creator-list", "creator-content");
                clearAll("project-list", "project-content");

                // Set the responsible employees
                document.querySelectorAll('#employee-list input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = template.responsible.includes(checkbox.value);
                    toggleCheckbox(checkbox, "employee-content", "employee-selected");
                });

                // Set the creators
                document.querySelectorAll('#creator-list input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = template.creator.includes(checkbox.value);
                    toggleCheckbox(checkbox, "creator-content", "creator-selected");
                });

                // Set the projects
                document.querySelectorAll('#project-list input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = template.project.includes(checkbox.value);
                    toggleCheckbox(checkbox, "project-content", "project-selected");
                });

                // Set the tags
                $("#tags").val(template.tags.join(', '));
            } else {
                console.error('Invalid index or template not found');
            }
        }

        function deleteTemplate(index) {
            // Retrieve the template array from local storage
            let template = JSON.parse(localStorage.getItem('templates'));

            // Check if the template array exists and the index is valid
            if (template && index >= 0 && index < template.length) {
                // Remove the item at the specified index
                template.splice(index, 1);

                // Save the updated array back to local storage
                localStorage.setItem('templates', JSON.stringify(template));
                displayTemplates();
            } else {
                console.error('Invalid index or template not found');
            }
        }

        document.getElementById("switch").addEventListener("change", () => {
            const status = this.checked
            this.checked = !status

            if (this.checked) {
                document.getElementById("detailedResult").style.display = "none";
                document.getElementById("groupedResult").style.display = "block";
            } else {
                document.getElementById("groupedResult").style.display = "none";
                document.getElementById("detailedResult").style.display = "block";
            }
        })

        // Quick Dates selection
        document.addEventListener('DOMContentLoaded', () => {
            const quickDateRadios = document.getElementsByName('quickDate');
            const datesInput = document.getElementById('dates');
            const dateStartInput = document.getElementById('dateStart');
            const dateFinishInput = document.getElementById('dateFinish');

            const today = new Date();
            const defaultStartDate = formatDate(today);
            const defaultEndDate = formatDate(today);
            datesInput.value = `${defaultStartDate} - ${defaultEndDate}`;
            dateStartInput.value = `${defaultStartDate}`;
            dateFinishInput.value = `${defaultEndDate}`;

            quickDateRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    let startDate = '';
                    let endDate = '';
                    const today = new Date();

                    switch (radio.value) {
                        case 'Today':
                            startDate = formatDate(today);
                            endDate = formatDate(today);
                            break;
                        case 'Last Week':
                            const lastWeekStart = new Date(today);
                            lastWeekStart.setDate(today.getDate() - today.getDay() - 6); // Start of last week (Monday)
                            const lastWeekEnd = new Date(lastWeekStart);
                            lastWeekEnd.setDate(lastWeekStart.getDate() + 6); // End of last week (Sunday)
                            startDate = formatDate(lastWeekStart);
                            endDate = formatDate(lastWeekEnd);
                            break;
                        case 'Last Month':
                            const lastMonth = new Date(today);
                            lastMonth.setMonth(today.getMonth() - 1);
                            const lastMonthStart = new Date(lastMonth.getFullYear(), lastMonth.getMonth(), 1); // First day of last month
                            const lastMonthEnd = new Date(lastMonth.getFullYear(), lastMonth.getMonth() + 1, 0); // Last day of last month
                            startDate = formatDate(lastMonthStart);
                            endDate = formatDate(lastMonthEnd);
                            break;
                    }

                    datesInput.value = `${startDate} - ${endDate}`;
                    dateStartInput.value = `${startDate}`;
                    dateFinishInput.value = `${endDate}`;
                });
            });

            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${month}/${day}/${year}`;
            }
        });

        // The visibility of the selector divs
        document.addEventListener("DOMContentLoaded", () => {
            const inputsAndLists = [{
                    input: "search-employees",
                    list: "employee-content"
                },
                {
                    input: "search-creators",
                    list: "creator-content"
                },
                {
                    input: "search-projects",
                    list: "project-content"
                }
            ];

            let currentVisibleList = null; // Keep track of the currently visible list

            inputsAndLists.forEach(({
                input,
                list
            }) => {
                const inputElement = document.getElementById(input);
                const listElement = document.getElementById(list);

                // Show the corresponding list when the input is focused
                inputElement.addEventListener("focus", () => {
                    if (currentVisibleList && currentVisibleList !== listElement) {
                        currentVisibleList.style.display = "none"; // Hide the previous list
                    }
                    listElement.style.display = "block";
                    currentVisibleList = listElement; // Update the currently visible list
                });
            });

            // Click outside to hide all lists
            document.addEventListener("click", (event) => {
                if (!event.target.closest("input") && !event.target.closest("div")) {
                    if (currentVisibleList) {
                        currentVisibleList.style.display = "none";
                        currentVisibleList = null;
                    }
                }
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("selectAllEmployee").addEventListener("click", () => selectAll("employee-list", "employee-selected"));
            document.getElementById("clearAllEmployee").addEventListener("click", () => clearAll("employee-list", "employee-content"));
            document.getElementById("toggleEmployee").addEventListener("click", () => toggleAll("employee"));
        });

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("selectAllCreator").addEventListener("click", () => selectAll("creator-list", "creator-selected"));
            document.getElementById("clearAllCreator").addEventListener("click", () => clearAll("creator-list", "creator-content"));
            document.getElementById("toggleCreator").addEventListener("click", () => toggleAll("creator"));
        });

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("selectAllProject").addEventListener("click", () => selectAll("project-list", "project-selected"));
            document.getElementById("clearAllProject").addEventListener("click", () => clearAll("project-list", "project-content"));
            document.getElementById("toggleProject").addEventListener("click", () => toggleAll("project"));
        });

        // Function to select all checkboxes in a given container
        function selectAll(containerId, destId) {
            const checkboxes = document.querySelectorAll(`#${containerId} input[type='checkbox']`);
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
                toggleCheckbox(checkbox, containerId, destId);
            });
        }
        // Function to clear all checkboxes in a given container
        function clearAll(containerId, destId) {
            const checkboxes = document.querySelectorAll(`#${containerId} input[type='checkbox']`);
            const destination = document.getElementById(destId);
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
                const label = checkbox.parentElement;
                destination.appendChild(label);
            });
        }
        // Function to toggle checkboxes in a given container
        function toggleAll(containerId) {
            const checkboxes = document.querySelectorAll(`#${containerId}-list input[type='checkbox']`);
            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    const destination = document.getElementById(`${containerId}-content`);
                    const label = checkbox.parentElement;
                    destination.appendChild(label);
                } else {
                    checkbox.checked = true;
                    const destination = document.getElementById(`${containerId}-selected`);
                    const label = checkbox.parentElement;
                    destination.appendChild(label);
                }

            });
        }

        function generateReport(reportType, detailedContainerId, groupedContainerId, projectGroupedContainerId) {
            // Gather selected responsible employees
            const responsible = Array.from(
                document.querySelectorAll('#employee-list input[type="checkbox"]:checked')
            ).map(checkbox => checkbox.value);

            // Gather selected creators
            const creator = Array.from(
                document.querySelectorAll('#creator-list input[type="checkbox"]:checked')
            ).map(checkbox => checkbox.value);

            // Gather selected projects
            const project = Array.from(
                document.querySelectorAll('#project-list input[type="checkbox"]:checked')
            ).map(checkbox => checkbox.value);

            // Process tags: Split by comma and trim extra whitespace
            const tagsInput = $("#tags").val();
            const tagsArray = tagsInput ?
                tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag) // Remove empty tags
                :
                []; // Default to an empty array if input is empty

            // Gather dates
            const dateStart = $("#dateStart").val();
            const dateFinish = $("#dateFinish").val();

            // Build formData object
            const formData = {
                "access_token": "<?php echo htmlspecialchars($settings['access_token']); ?>",
                responsible: responsible, // Send as an array
                creator: creator, // Send as an array
                project: project, // Send as an array
                tags: tagsArray, // Ensure empty array if no valid tags
                startDate: dateStart,
                endDate: dateFinish,
            };

            // console.log("Form Data of Search:", formData);

            // Define the API URL
            const url = 'https://bcp-work-time-report-backend-gsavdwauaqbwckgr.southeastasia-01.azurewebsites.net/report/';

            // Show loading overlay
            $("body").append("<div class='loading-overlay'><div class='loading-message'>Searching...</div></div>");

            if (reportType == "generateReport") {
                // Send data to the backend using POST
                $.post(url, formData, function(response) {
                    // Remove loading overlay and display the report
                    $(".loading-overlay").remove();
                    document.getElementById('reportSpace').style.display = 'block';
                    document.getElementById('masterReport').style.display = 'none';

                    //---------------------                
                    // console.log(response);
                    // console.log(response.finalDailyData);
                    // console.log(response.finalWeeklyData);
                    displayReport(response.finalDailyData, detailedContainerId);
                    displayReport(response.finalWeeklyData, groupedContainerId);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    // Remove loading overlay on error
                    $(".loading-overlay").remove();
                    $(resultContainerId).html("<div class='error'>Error loading report. Please try again.</div>");
                });
            } else if (reportType == "masterReport") {
                $.post(url, formData, function(response) {
                    $(".loading-overlay").remove();
                    document.getElementById('masterReport').style.display = 'block';
                    document.getElementById('reportSpace').style.display = 'none';
                    displayReportMaster(response.finalWeeklyData, masterSheet);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    // Remove loading overlay on error
                    $(".loading-overlay").remove();
                    $(resultContainerId).html("<div class='error'>Error loading report. Please try again.</div>");
                });
            }

        }

        function fetchAllProjects() {
            const url = 'https://bcp-work-time-report-backend-gsavdwauaqbwckgr.southeastasia-01.azurewebsites.net/task/projects/';

            // Show loading overlay
            document.body.insertAdjacentHTML(
                'beforeend',
                "<div class='loading-overlay'><div class='loading-message'>Loading data...</div></div>"
            );

            // Fetch data using AJAX
            $.ajax({
                url: url,
                type: 'POST', // Use POST for sending data in the body
                contentType: 'application/json', // Set content type to JSON
                data: JSON.stringify({
                    "access_token": "<?php echo htmlspecialchars($settings['access_token']); ?>"
                }),
                success: function(response) {
                    $(".loading-overlay").remove(); // Remove loading overlay
                    const Container = document.getElementById('project-content');
                    renderProject(response, Container);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    $(".loading-overlay").remove();
                    const container = document.getElementById('project-content');
                    container.innerHTML = "<p>Error loading employees.</p>";
                }
            });
        }

        function renderProject(response, container) {
            // container.innerHTML = ''; // Clear any existing content

            if (response && response.length > 0) {
                // Iterate through the projects
                response.forEach(project => {
                    if (project) {
                        const label = document.createElement('label');
                        const checkbox = document.createElement('input');
                        const img = document.createElement('img');

                        checkbox.type = 'checkbox';
                        checkbox.name = 'responsible_employees[]';
                        checkbox.value = project.ID; // Use the unique project ID
                        checkbox.style.display = "none";
                        // Set up the image
                        img.src = project.IMAGE || 'R2.png'; // Use a default image if PERSONAL_PHOTO is not provided

                        // Append elements to the label
                        label.appendChild(checkbox);
                        label.appendChild(img);
                        label.appendChild(document.createTextNode(` ${project.NAME}`));
                        label.appendChild(document.createElement('br'));

                        container.appendChild(label);
                        checkbox.addEventListener('change', function() {
                            toggleCheckbox(this, 'project-content', 'project-selected');
                        });
                    }
                });
            } else {
                // Show a message if no employees are found
                container.innerHTML = '<p>No active employees found.</p>';
            }
        }

        function fetchAllUsers() {
            const url = 'https://bcp-work-time-report-backend-gsavdwauaqbwckgr.southeastasia-01.azurewebsites.net/user/active/get';

            // Show loading overlay
            document.body.insertAdjacentHTML(
                'beforeend',
                "<div class='loading-overlay'><div class='loading-message'>Loading data...</div></div>"
            );

            // Fetch data using AJAX
            $.ajax({
                url: url,
                type: 'POST', // Use POST for sending data in the body
                contentType: 'application/json', // Set content type to JSON
                data: JSON.stringify({
                    "access_token": "<?php echo htmlspecialchars($settings['access_token']); ?>"
                }),
                success: function(response) {
                    $(".loading-overlay").remove(); // Remove loading overlay
                    const resContainer = document.getElementById('employee-content');
                    const creContainer = document.getElementById('creator-content');
                    renderResponsible(response, resContainer);
                    renderResponsible(response, creContainer);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    $(".loading-overlay").remove();
                    const container = document.getElementById('employee-content');
                    container.innerHTML = "<p>Error loading employees.</p>";
                }
            });
        }

        function renderResponsible(response, container) {
            // container.innerHTML = ''; // Clear any existing content

            if (response && response.totalActiveUsers && response.totalActiveUsers.length > 0) {
                // Iterate through the employees
                response.totalActiveUsers.forEach(employee => {
                    // Only include active employees
                    if (employee.ACTIVE) {
                        const label = document.createElement('label');
                        const checkbox = document.createElement('input');
                        const img = document.createElement('img');

                        checkbox.type = 'checkbox';
                        checkbox.name = 'responsible_employees[]';
                        checkbox.value = employee.ID; // Use the unique employee ID
                        checkbox.style.display = 'none';

                        // Set up the image
                        img.src = employee.PERSONAL_PHOTO || 'R.png'; // Use a default image if PERSONAL_PHOTO is not provided

                        // Create employee name
                        const fullName = `${employee.NAME} ${employee.LAST_NAME}`;

                        // Append elements to the label
                        label.appendChild(checkbox);
                        label.appendChild(img);
                        label.appendChild(document.createTextNode(` ${fullName}`));
                        label.appendChild(document.createElement('br'));

                        // Append the label to the container
                        container.appendChild(label);
                        if (container.id == "employee-content") {
                            checkbox.addEventListener('change', function() {
                                toggleCheckbox(this, 'employee-content', 'employee-selected');
                            });
                        } else {
                            checkbox.addEventListener('change', function() {
                                toggleCheckbox(this, 'creator-content', 'creator-selected');
                            });
                        }
                    }
                });
            } else {
                // Show a message if no employees are found
                container.innerHTML = '<p>No active employees found.</p>';
            }
        }

        // Function to toggle checkboxes between containers
        function toggleCheckbox(checkbox, originalListId, selectedListId) {
            const originalList = document.getElementById(originalListId);
            const selectedList = document.getElementById(selectedListId);

            if (checkbox.checked) {
                // Move to the selected list
                const label = checkbox.parentElement;
                selectedList.appendChild(label);
            } else {
                // Move back to the original list
                const label = checkbox.parentElement;
                originalList.appendChild(label);
            }
        }

        // Function to filter employees based on search
        function filterEmployees() {
            const searchValue = document.getElementById('search-employees').value.toLowerCase();
            const employees = document.querySelectorAll('#employee-list label');

            employees.forEach(employee => {
                const text = employee.textContent.toLowerCase();
                employee.style.display = text.includes(searchValue) ? '' : 'none';
            });
        }
        // Function to filter creators based on search
        function filterCreators() {
            const searchValue = document.getElementById('search-creators').value.toLowerCase();
            const employees = document.querySelectorAll('#creator-list label');

            employees.forEach(employee => {
                const text = employee.textContent.toLowerCase();
                employee.style.display = text.includes(searchValue) ? '' : 'none';
            });
        }
        // Function to filter projects based on search
        function filterProjects() {
            const searchValue = document.getElementById('search-projects').value.toLowerCase();
            const employees = document.querySelectorAll('#project-list label');

            employees.forEach(employee => {
                const text = employee.textContent.toLowerCase();
                employee.style.display = text.includes(searchValue) ? '' : 'none';
            });
        }

        const statusLabels = {
            1: "New",
            2: "Pending",
            3: "In Progress",
            4: "Supposedly Completed",
            5: "Completed",
            6: "Deferred",
            7: "Declined",
            // Add more status labels as needed
        };

        // Function to convert seconds to time format
        function convertSecondsToTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:${String(secs).padStart(2, "0")}`;
        }

        // Function to trim date to YYYY-MM-DD format
        function trimDate(date) {
            return date.slice(0, 10);
        }

        // Function to calculate total duration of tasks for a user
        function calculateTotalDuration(tasks) {
            return tasks.reduce((total, task) => total + parseInt(task.duration), 0);
        }

        // Function to extract and display tags
        function displayTags(tags) {
            if (!tags || Object.keys(tags).length === 0) return "No Tags";

            return Object.values(tags).map(tag => tag.title).join(", ");
        }

        // Function to display report
        function displayReport(report, resultContainerId) {
            let output = "";
            let employee = "";
            let project = "";
            let grouped = "";
            output = `<h1 style='font-size: 24px; margin-bottom: 16px;position: sticky;top:0%;'>Work Report from ${document.getElementById("dateStart").value} to ${document.getElementById("dateFinish").value}</h1>`;

            currentReport = report;
            if (report.length > 0) {
                report.forEach(user => {
                    output += `<div>         
                <h2 style='font-weight: bold; font-size: 20px; margin-bottom: 16px;'>${user.name}</h2>   
                <table class="TableToExport" style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">   
                    <thead>
                        <tr style="background-color: #007BFF; color: white;">
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Created Date</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Group</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Task Title</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Creator</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Status</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Tags</th>
                            <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Duration</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    user.tasks.forEach(task => {
                        output += `<tr style="background-color: #f2f2f2;">
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.createdDate ? trimDate(task.createdDate) : ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.group && task.group.name ? task.group.name : ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.title || ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.creator && task.creator.name ? task.creator.name : ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.status !== undefined ? (statusLabels[task.status] || "Unknown") : ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.tags ? displayTags(task.tags) : ''}</td>
                    <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${task.duration !== undefined ? convertSecondsToTime(parseInt(task.duration)) : ''}</td>
                </tr>`;
                    });

                    output += `<tr>
                <td colSpan='6' style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Total Time Taken</td>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(calculateTotalDuration(user.tasks))}</td>
                </tr>
                </tbody>
                </table>
                </div>`;
                });

                // Calculate grand total duration
                const grandTotalDuration = report.reduce((grandTotal, user) => {
                    return grandTotal + calculateTotalDuration(user.tasks);
                }, 0);

                output += `<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: ${convertSecondsToTime(parseInt(grandTotalDuration))}</h2>`;
                //---------------------------------------------------------------------------------------------------------------------------------------------------------------------

                // Unique usernames and total time taken
                const usernameTotals = {};
                report.forEach(user => {
                    const totalDuration = calculateTotalDuration(user.tasks);
                    usernameTotals[user.name] = (usernameTotals[user.name] || 0) + parseInt(totalDuration);
                });

                employee += `<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Total Time Taken By Employees</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background-color: #007BFF; color: white;">
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Username</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Time</th>
                    </tr>
                </thead>
                <tbody>`;

                let grandTotalUserTime = 0;
                for (const [username, totalTime] of Object.entries(usernameTotals)) {
                    employee += `<tr style="background-color: #f2f2f2;">
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${username}</td>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${convertSecondsToTime(totalTime)}</td>
                </tr>`;
                    grandTotalUserTime += totalTime; // Sum for grand total
                }

                employee += `<tr>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Grand Total Time</td>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(grandTotalUserTime)}</td>
                </tr>`;

                employee += `</tbody>
                </table>`;
                //---------------------------------------------------------------------------------------------------------------------------------------------------------------------

                // List of all projects and total time
                const projectTotals = {};
                report.forEach(user => {
                    user.tasks.forEach(task => {
                        const projectName = task.group && task.group.name ? task.group.name : "Unknown Project";
                        const duration = task.duration !== undefined ? task.duration : 0;
                        projectTotals[projectName] = (projectTotals[projectName] || 0) + duration;
                    });
                });

                // Sort project names alphabetically
                const sortedProjects = Object.keys(projectTotals).sort();

                project += `<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Total Time Used In Projects</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background-color: #007BFF; color: white;">
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Project</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Time</th>
                    </tr>
                </thead>
                <tbody>`;

                let grandTotalProjectTime = 0;
                sortedProjects.forEach(projectName => {
                    const totalTime = projectTotals[projectName];
                    project += `<tr style="background-color: #f2f2f2;">
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${projectName}</td>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${convertSecondsToTime(parseInt(totalTime))}</td>
                </tr>`;
                    grandTotalProjectTime += parseInt(totalTime); // Sum for grand total
                });

                project += `<tr>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Grand Total Time</td>
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(parseInt(grandTotalProjectTime))}</td>
                </tr>`;

                project += `</tbody>
                </table>`;
                //---------------------------------------------------------------------------------------------------------------------------------------------------------------------

                let grandTotalDurationGrouped = 0;
                grouped += `<h2 style='font-weight: bold; font-size: 20px; margin-top: 32px;'>Grouped Project Report</h2>`;

                report.forEach(user => {
                    grouped += `<h2 style='font-weight: bold; font-size: 20px; margin-bottom: 16px;'>${user.name}</h2>   
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">   
                <thead>
                    <tr style="background-color: #007BFF; color: white;">
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Group</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Task Title(s)</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Creator(s)</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Status</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Tags</th>
                        <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Duration</th>
                    </tr>
                </thead>
                <tbody>`;

                    // Grouping tasks by group name
                    const groupedTasks = user.tasks.reduce((acc, task) => {
                        const groupName = task.group?.name || "Unknown Group";
                        if (!acc[groupName]) {
                            acc[groupName] = {
                                taskTitles: [],
                                creators: new Set(),
                                statuses: new Set(),
                                tags: {}, // Store tags as an object to work with displayTags
                                totalDuration: 0
                            };
                        }
                        acc[groupName].taskTitles.push(task.title || "");
                        if (task.creator?.name) acc[groupName].creators.add(task.creator.name);
                        if (task.status !== undefined) acc[groupName].statuses.add(statusLabels[task.status] || "Unknown");
                        // Merge tags into an object to maintain structure for displayTags
                        if (task.tags) {
                            Object.entries(task.tags).forEach(([key, value]) => {
                                acc[groupName].tags[key] = value;
                            });
                        }

                        acc[groupName].totalDuration += parseInt(task.duration || 0);
                        return acc;
                    }, {});

                    let userTotalDuration = 0;

                    // Constructing rows for grouped tasks
                    Object.entries(groupedTasks).forEach(([groupName, groupData]) => {
                        userTotalDuration += groupData.totalDuration;

                        grouped += `<tr style="background-color: #f2f2f2;">
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${groupName}</td>
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${groupData.taskTitles.join("<br>")}</td>
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${Array.from(groupData.creators).join(", ")}</td>
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${Array.from(groupData.statuses).join(", ")}</td>
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${displayTags(groupData.tags)}</td>
                                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${convertSecondsToTime(groupData.totalDuration)}</td>
                            </tr>`;
                    });



                    // Add total row for the user
                    grouped += `<tr>
                            <td colspan='5' style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Total Time Taken</td>
                            <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(userTotalDuration)}</td>
                        </tr>`;

                    grandTotalDurationGrouped += userTotalDuration;

                    grouped += `</tbody>
                            </table>`;
                });

                // Grand Total row at the end
                grouped += `<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: ${convertSecondsToTime(grandTotalDurationGrouped)}</h2>`;

                currentReport = output;
                employeeWise = employee;
                projectWise = project;
                projectGroupedWise = grouped;
                output += `<button onclick="downloadReport(currentReport, employeeWise, projectWise, projectGroupedWise)" style="margin-top: 20px; padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">Download as XLS</button>`;
            } else {
                output += "<div>No data available</div>";
            }

            if (resultContainerId == "#detailedResult") {
                document.getElementById("detailedResult").innerHTML = output;
            } else {
                document.getElementById("groupedResult").innerHTML = output;
            }
        }

        function displayReportMaster(report, resultContainerId) {
            let output = `<h1 style='font-size: 24px; margin-bottom: 16px; position: sticky; top: 0%;'>
            Master Sheet for ${document.getElementById("dateStart").value} to ${document.getElementById("dateFinish").value}</h1>`;
            currentReport = report;

            if (report.length > 0) {
                // Step 1: Collect all unique group names across all users, including "Non-Grouped Tasks"
                const uniqueGroups = new Set(["Non-Grouped Tasks"]);
                report.forEach(user => {
                    user.tasks.forEach(task => {
                        if (task.group && task.group.name) {
                            uniqueGroups.add(task.group.name);
                        }
                    });
                });
                const groupArray = Array.from(uniqueGroups); // Convert Set to Array for indexing

                // Initialize totals for each group
                const groupTotals = groupArray.reduce((totals, group) => {
                    totals[group] = 0;
                    return totals;
                }, {});

                let grandTotalDuration = 0; // Initialize grand total duration

                // Step 2: Start building the table
                output += `<table class="TableToExport" style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 18px; text-align: left; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px; overflow: hidden;">
                <thead>
                <tr style="background-color: #007BFF; color: white;">
                    <th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>User Name</th>`;

                // Add dynamic group columns
                groupArray.forEach(group => {
                    output += `<th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${group}</th>`;
                });

                // Add Total column
                output += `<th style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>Total Duration</th>
                </tr>
                </thead>
                <tbody>`;

                // Step 3: Populate rows for each user
                report.forEach(user => {
                    const groupDurations = {};

                    // Calculate total duration for each group for this user
                    user.tasks.forEach(task => {
                        const groupName = task.group && task.group.name ? task.group.name : 'Non-Grouped Tasks';
                        groupDurations[groupName] = (groupDurations[groupName] || 0) + (task.duration || 0);

                        // Add to group total
                        if (groupName in groupTotals) {
                            groupTotals[groupName] += task.duration || 0;
                        }
                    });

                    // Compute total duration for the user
                    const totalDuration = Object.values(groupDurations).reduce((a, b) => a + b, 0);
                    grandTotalDuration += totalDuration; // Add to grand total

                    // Build the row
                    output += `<tr style="background-color: #f2f2f2;">
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${user.name}</td>`;

                    // Fill group duration columns
                    groupArray.forEach(group => {
                        const duration = groupDurations[group] || 0;
                        output += `<td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${convertSecondsToTime(duration)}</td>`;
                    });

                    // Add total duration column
                    output += `<td style='padding: 12px 15px; border-bottom: 1px solid #ddd;'>${convertSecondsToTime(totalDuration)}</td>
                </tr>`;
                });

                // Step 4: Add "Total" row for workgroup times
                output += `<tr style="background-color: #007BFF; color: white;">
                <td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>Total</td>`;

                groupArray.forEach(group => {
                    const total = groupTotals[group] || 0;
                    output += `<td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(total)}</td>`;
                });

                // Add grand total to the last cell
                output += `<td style='padding: 12px 15px; border-bottom: 1px solid #ddd; font-weight: bold;'>${convertSecondsToTime(grandTotalDuration)}</td>
                </tr>`;

                output += `</tbody></table>`;

                // Optional: Display Grand Total separately if needed
                output += `<h2 style='font-weight: bold; font-size: 24px; margin-top: 32px;'>Grand Total Time Taken: ${convertSecondsToTime(grandTotalDuration)}</h2>`;

                currentReport = output;
                output += `<button onclick="downloadReportMaster(currentReport)" style="margin-top: 20px; padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">Download as XLS</button>`;
            } else {
                output += "<p>No data available for the selected date range.</p>";
            }

            // Inject output into the container
            document.getElementById("masterReport").innerHTML = output;
        }

        function displayTags(tags) {
            // If tags is not an array (like an object), handle it
            if (!Array.isArray(tags)) {
                let key = Object.keys(tags);
                return key.map(tag => tags[tag].title).reduce((acc, curr) => acc ? `${acc}, ${curr}` : curr, "");
            }

            // If tags is already an array
            return tags.reduce((acc, curr) => acc ? `${acc}, ${curr}` : curr, "");
        }

        function downloadReportMaster(masterHtml) {
            const formData = new FormData();
            formData.append('masterHtml', masterHtml);


            fetch('download.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (response.ok) {
                        return response.blob(); // Get the file as a blob
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;

                    // Get the current date in yymmdd format
                    const date = new Date();
                    const yymmdd = date.toISOString().slice(2, 10).replace(/-/g, '');

                    a.download = `${yymmdd}_Master Sheet.xlsx`; // Specify the new naming convention
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url); // Clean up
                })
                .catch(error => console.error('Error downloading the report:', error));
        }

        function downloadReport(detailHtml, userTotalsHtml, projectTotalsHtml, projectGroupedHtml) {
            const formData = new FormData();
            formData.append('detailHtml', detailHtml);
            formData.append('userTotalsHtml', userTotalsHtml);
            formData.append('projectTotalsHtml', projectTotalsHtml);
            formData.append('projectGroupedHtml', projectGroupedHtml);

            fetch('download.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (response.ok) {
                        return response.blob(); // Get the file as a blob
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;

                    // Get the current date in yymmdd format
                    const date = new Date();
                    const yymmdd = date.toISOString().slice(2, 10).replace(/-/g, '');

                    a.download = `${yymmdd}_Team Timetracking.xlsx`; // Specify the new naming convention
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url); // Clean up
                })
                .catch(error => console.error('Error downloading the report:', error));
        }

        // Helper functions for time conversion
        function convertTimeToSeconds(timeString) {
            const parts = timeString.split(':');
            const hours = parseInt(parts[0], 10) || 0;
            const minutes = parseInt(parts[1], 10) || 0;
            const seconds = parseInt(parts[2], 10) || 0;
            return (hours * 3600) + (minutes * 60) + seconds;
        }

        function convertSecondsToTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    </script>

</body>

</html>