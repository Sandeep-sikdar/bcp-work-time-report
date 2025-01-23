$(document).ready(function() {
    console.log('started');
    // Initialize date pickers
    $("#dateStart, #dateFinish").datepicker();

    // Handle quick period selection
    $("input[name='quickPeriod']").change(function() {
        setQuickPeriod($(this).val());
    });

    // Handle form submission
    $("#saveTemplate").click(function(event) {
        event.preventDefault(); // Prevent form submission
        console.log('Save Template button pressed');
        saveTemplate();
    });

    $("#generateReport").click(function(event) {
        event.preventDefault(); // Prevent form submission
        console.log('Generate Report button clicked');
        generateReport();
    });

    // Load templates on page load
    loadTemplates();
});

function setQuickPeriod(period) {
    let startDate, endDate;
    const today = new Date();

    if (period === "lastWeek") {
        startDate = new Date(today.setDate(today.getDate() - 7));
        endDate = new Date();
    } else if (period === "lastMonth") {
        startDate = new Date(today.setMonth(today.getMonth() - 1));
        endDate = new Date();
    } else if (period === "lastQuarter") {
        startDate = new Date(today.setMonth(today.getMonth() - 3));
        endDate = new Date();
    }

    $("#dateStart").datepicker("setDate", startDate);
    $("#dateFinish").datepicker("setDate", endDate);
}

function saveTemplate() {
    const formData = {
        responsible: $("#responsible").val(),
        creator: $("#creator").val(),
        project: $("#project").val(),
        tags: $("#tags").val(),
        dateStart: $("#dateStart").val(),
        dateFinish: $("#dateFinish").val(),
        quickPeriod: $("input[name='quickPeriod']:checked").val()
    };

    $.post('savetemplate.php', formData, function(response) {
        console.log('Template saved:', response);
        loadTemplates(); // Reload templates after saving
    });
}

function loadTemplates() {
    $.get('loadtemplates.php', function(response) {
        displayTemplates(response);
    });
}

function displayTemplates(templates) {
    const templateContainer = $("#savedTemplates");
    templateContainer.empty();

    if (templates.length === 0) {
        templateContainer.append('<p>No templates saved.</p>');
        return;
    }

    const table = $('<table></table>').addClass('template-table');
    const header = $('<tr></tr>')
        .append('<th>Responsible</th>')
        .append('<th>Creator</th>')
        .append('<th>Project</th>')
        .append('<th>Tags</th>')
        .append('<th>Date Start</th>')
        .append('<th>Date Finish</th>')
        .append('<th>Quick Period</th>')
        .append('<th>Actions</th>');
    table.append(header);

    templates.forEach(template => {
        const row = $('<tr></tr>')
            .append(`<td>${template.responsible}</td>`)
            .append(`<td>${template.creator}</td>`)
            .append(`<td>${template.project}</td>`)
            .append(`<td>${template.tags}</td>`)
            .append(`<td>${template.dateStart}</td>`)
            .append(`<td>${template.dateFinish}</td>`)
            .append(`<td>${template.quickPeriod}</td>`)
            .append(`<td><button onclick="fillForm(${template.id})">Load</button></td>`);
        table.append(row);
    });

    templateContainer.append(table);
}

function fillForm(templateId) {
    $.get('loadtemplate.php', { id: templateId }, function(template) {
        $("#responsible").val(template.responsible);
        $("#creator").val(template.creator);
        $("#project").val(template.project);
        $("#tags").val(template.tags);
        $("#dateStart").val(template.dateStart);
        $("#dateFinish").val(template.dateFinish);
        $(`input[name='quickPeriod'][value='${template.quickPeriod}']`).prop('checked', true);
    });
}

function generateReport() {
    // Implement report generation logic here
}

function displayReport(report) {
    // Implement report display logic here
}