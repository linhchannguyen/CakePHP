$(function () {
    //clear cache for IE
    $.ajaxSetup({
        cache: false,
    });
})

function clearSelectedCourseId() {
    selectedCourseId = 9999;
}

function allUpdate() {
    if (selectedCourseId == 9999) {
        alert(UPDATE_ALL_ALERT);
        return false;
    }

    var target = document.getElementById("CriteoSeachFormCourseid").value;
    document.getElementById('CriteoAllUpdateFormCourseid').value = target;

    var test = new Array();

    $(".editTags").each(function (i) {
        test.push(($(this).val()));

    });

    document.getElementById('CriteoAllUpdateFormData').value = test;
}

function onButtonClick() {
    var target = document.getElementById("CriteoSeachFormCourseid").value;
    document.getElementById('CriteoRegistFormCourseid').value = target;
    document.getElementById('CriteoEditFormCourseid').value = target;
    document.getElementById('CriteoCsvExportFormCourseid').value = target;
    document.getElementById('CriteoImportFormCourseid').value = target;
    document.getElementById('CriteoDownloadFormCourseid').value = target;
}

function deleteCheck() {
    if (window.confirm(DELETE_COMFIRM)) {
        return true;
    } else {
        window.alert(DELETE_CANCELED);
        return false;
    }
}