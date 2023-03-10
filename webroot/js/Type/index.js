$(function() {
    //clear cache for IE
    $.ajaxSetup({
        cache: false,
    });
    var course_id = "";
    var type_id = "";
    //Get course ID with ajax
    get_course_id().done(function(result) {
        course_id = result;
    }).fail(function(result) {
        alert(COURSE_NOT_FOUND);
    });
    var data = "";
    //Get registered form data
    get_form_data(course_id).done(function(result) {
        data = result;
    }).fail(function(result) {

    });

    var type_count_id = 0;
    //When there is no registered data
    if (!data) {
        //The first form does not add a delete button
        addForm(type_count_id, course_id, "", "");
        addRow("", "", 0, "", type_count_id);
        add_style();
    } else {
        $('.form').append()

        for (var i in data) {
            var saved_type_id = data[i].Type.id;
            var saved_type_name = data[i].Type.name;
            var saved_type_title = data[i].Type.title;
            var saved_type_data = [saved_type_name, saved_type_title, saved_type_id];
            //The first form does not add a delete button
            if (i == 0) {
                addForm(i, course_id, "", saved_type_data);
            } else {
                //Add a delete button to the second and subsequent forms
                var form_remove_button = '<input type="button" class="formRemove" id="formRemove' + i + '" value="'+DELETE_TEST_SPECIES+'" />';
                addForm(i, course_id, form_remove_button, saved_type_data);
            }
            for (var j in data[i].Category) {
                var saved_category_id = data[i].Category[j].id;
                var saved_category_name = data[i].Category[j].name;
                var saved_category_type = data[i].Category[j].type_id;
                var saved_category_display_order = data[i].Category[j].display_order;
                var saved_category_data = [saved_category_id, saved_category_name, saved_category_type, saved_category_display_order];
                if (j == 0) {
                    addRow(saved_type_id, "", j, saved_category_data, i);
                } else {
                    var category_remove_button = '<input type="button" class="rowRemove" value="'+DELETE+'" id="rowRemove' + i + '_' + j + '" />';
                    addRow(saved_type_id, category_remove_button, j, saved_category_data, i);
                }
            }
        }
        add_style();
    }
    //Added test species
    $('.formAdd').on('click', function() {

        var last_id = $(".formCount:last").attr('id');
        if (last_id) {
            last_id = last_id.split('form');
            last_id = parseInt(last_id[1]) + 1;
            type_count_id = last_id;
        }

        //Add a delete button to the second and subsequent forms
        var form_remove_button = '<input type="button" class="formRemove" id="formRemove' + type_count_id + '" value="'+DELETE_TEST_SPECIES+'" />';
        addForm(type_count_id, course_id, form_remove_button, "");
        addRow("", "", 0, "", type_count_id);

        add_style();
    });

    $(this).on('click', function(e) {
        //Add test category
        if (e.target.className == "rowAdd") {
            //Get Test Species Number
            var rowAdd_id = e.target.id.split('rowAdd');
            type_count_id = rowAdd_id[1];
            var count_id = $('.' + type_count_id + ':last').attr('id').split('_');
            count_id = parseInt(count_id[1]);
            count_id++;
            //Add a delete button for the second and subsequent test categories
            var category_remove_button = '<input type="button" class="rowRemove" value="'+DELETE+'" id="rowRemove' + type_count_id + '_' + count_id + '" />';
            var type_id = $('.type_count_id_' + type_count_id).val();
            if (!type_id) {
                type_id = "";
            }

            addRow(type_id, category_remove_button, count_id, "", type_count_id);

            add_style();
        }

        //Delete test category
        if (e.target.className == "rowRemove") {

            if (!confirm(ROW_REMOVE)) {
                return false;
            } else {

                var rowRemove_id = e.target.id.split('rowRemove');

                type_id = rowRemove_id[1];
                var delete_category_id = $('#category_id_' + type_id).val();
                if (delete_category_id) {
                    remove_category_data(delete_category_id);
                }

                $('#' + type_id).remove();
            }

        }
        //Delete test species
        if (e.target.className == "formRemove") {

            if (!confirm(FORM_REMOVE)) {
                return false;
            } else {

                //Get test species number to delete
                var class_name = e.target.id.split('formRemove');

                var delete_type_id = $('.type_id_' + class_name[1]).val();

                if (delete_type_id) {
                    remove_type_data(delete_type_id);
                }

                $('#form' + class_name[1]).remove();
            }
        }
    });

    //Validation on form submission
    $('form').submit(function() {
        //If you want to send it again, delete all comments once.
        $("div.valid_type").remove();
        $("div.valid_title").remove();
        $("div.valid_category").remove();
        var return_position = [];

        var error_flag = 0;

        var types_array = [];
        $('.inputType').each(function() {
            var text = $(this).val();
            var position = $(this).position().left;
            //Compare for the same test species name
            if ($.inArray(text, types_array) > -1) {
                $(this).css("border", "1px solid red").after("<div class='valid_type' style='position:absolute; left:" + position + "px;'>"+TEST_SPECIES_WITH_THE_SAME_NAME+"</div>");
                error_flag = 1;
                return_position.push($(this).position().top);
            }
            types_array.push(text);

            if (text.length > 100 || text.length < 1) {
                $(this).css("border", "1px solid red").after("<div class='valid_type' style='position:absolute; left:" + position + "px;'>"+ENTER_WITHIN_100_CHARACTER+"</div>");
                error_flag = 1;
                return_position.push($(this).position().top);
            } else {


                $(this).css("border", "1px solid silver");

            }
        });

        $('.inputTitle').each(function() {
            var text = $(this).val();
            var position = $(this).position().left;
            if (text) {
                if (text.length > 60 || text.length < 1) {
                    $(this).css("border", "1px solid red").after("<div class='valid_title' style='position:absolute; left:" + position + "px;'>"+TITLE_ENTER_WITHIN_60_CHARACTER+"</div>");
                    error_flag = 1;
                    return_position.push($(this).position().top);
                } else {
                    $(this).css("border", "1px solid silver ");
                }
            }
        });


        var type_id;
        var category_arrays = [];
        $('.inputCategory').each(function() {
            var position = $(this).position().left;
            var text = $(this).val();

            var parent_id = $(this).parents('.category').attr('id').split('category')[1];

            if (type_id == parent_id) {

                if ($.inArray(text, category_arrays) > -1) {
                    $(this).css("border", "1px solid red").after("<div class='valid_type' style='position:absolute; left:" + position + "px;'>"+TEST_SEGMENT_WITH_THE_SAME_NAME+"</div>");
                    error_flag = 1;
                    return_position.push($(this).position().top);
                }
                category_arrays.push(text);

            } else {
                category_arrays = [];
                type_id = parent_id;
                category_arrays.push(text);
            }

            if (text.length > 60 || text.length < 1) {
                $(this).css("border", "1px solid red").after("<div class='valid_category' style='position:absolute; left:" + position + "px;'>"+CATEGORY_ENTER_WITHIN_60_CHARACTER+"</div>");
                error_flag = 1;
                return_position.push($(this).position().top);
            } else {
                $(this).css("border", "1px solid silver");
            }
        });

        var display_order_number;
        var category_arrays2 = [];
        $('.inputCategoryDisplayOrder').each(function() {
            var position = $(this).position().left;
            var text = $(this).val();

            var parent_id = $(this).parents('.category').attr('id').split('category')[1];

            if (display_order_number == parent_id) {

                if ($.inArray(text, category_arrays2) > -1) {
                    $(this).css("border", "1px solid red").after("<div class='valid_type' style='position:absolute; left:" + position + "px;'>"+THEY_HAVE_THE_SAME_DISPLAY_NUMBER+"</div>");
                    error_flag = 1;
                    return_position.push($(this).position().top);
                }
                category_arrays2.push(text);

            } else {
                category_arrays2 = [];
                display_order_number = parent_id;
                category_arrays2.push(text);
            }
            if (text.match(/^[0]+$/)) {
                $(this).css("border", "1px solid red").after("<div class='valid_category' style='position:absolute; left:" + position + "px;'>"+CANNOT_BE_ENTERED+"</div>");
                error_flag = 1;
                return_position.push($(this).position().top);

            } else if (text.length > 3 || text.length < 1 || !text.match(/^[0-9]+$/)) {
                $(this).css("border", "1px solid red").after("<div class='valid_category' style='position:absolute; left:" + position + "px;'>"+ENTER_WITHIN_3_ALPHANUMERIC_CHARACTERS+"</div>");
                error_flag = 1;
                return_position.push($(this).position().top);
            } else {
                $(this).css("border", "1px solid silver");
            }
        });

        if (error_flag == 1) {
            alert(PLEASE_CHECK_YOUR_ENTRIES);
            if (return_position) {
                $("html,body").animate({
                    scrollTop: return_position[0] - $(window).height() / 3
                }, {
                    queue: false
                });
            }
            return false;
        }
    });
});


//Added test species
function addForm(type_count_id, course_id, form_remove_button, saved_type_data) {
    var name = "";
    var title = "";
    var type_id = "";
    if (saved_type_data[0]) {
        name = saved_type_data[0];
    }
    if (saved_type_data[1]) {
        title = saved_type_data[1];
    }
    if (saved_type_data[2]) {
        type_id = saved_type_data[2];
    }

    var str = '<div id="form' + type_count_id + '" class="formCount" ><div class="type_form"><div><div class="headline">試験種(ID:' + type_id + ')</div><input type="hidden" name="data[Type][id][]" value="' + type_id + '"  class="type_id_' + type_count_id + '"/><label for="TypeName"><span class="alert">※</span>試験種名</label><input type="hidden" name="data[Type][course_id][]" value="' + course_id + '" /><input class="inputType" name="data[Type][name][]" type="text" value="' + name + '"  />(100文字以内)<label for="TypeTitle">タイトル</label><input name="data[Type][title][]"  type="text"  value="' + title + '" class="inputTitle"/>(60文字以内)<input type="hidden" name="data[Category][type_id][' + type_count_id + ']" value ="' + type_id + '" /><div class="category" id ="category' + type_count_id + '"><input type="button" class="rowAdd" id="rowAdd' + type_count_id + '"  value="試験区分追加" /></div>' + form_remove_button + '</div></div></div>';
    $('.form').append(str);
}

//Add test category
function addRow(type_id, category_remove_button, count_id, saved_category_data, type_count_id) {
    var saved_category_id = "";
    var saved_category_name = "";
    var saved_category_display_order = "";

    if (saved_category_data[0]) {
        saved_category_id = saved_category_data[0];
    }
    if (saved_category_data[1]) {
        saved_category_name = saved_category_data[1];
    }
    if (saved_category_data[3]) {
        saved_category_display_order = saved_category_data[3];
    }
    $("#category" + type_count_id).append('<div class="' + type_count_id + '" id=' + type_count_id + '_' + count_id + '><div class="category_row"><div class="headline">試験区分</div><div class="input text" ><label for="CategoryName"><span class="alert">※</span>区分名</label><input name="data[Category][name][' + type_count_id + '][]"  type="text" value="' + saved_category_name + '" class="inputCategory" />(60文字以内)<span class="input text" ><label for="CategoryDisplayOrder"><span class="alert">※</span>表示順</label><input name="data[Category][display_order][' + type_count_id + '][]"  type="text" value="' + saved_category_display_order + '" class="inputCategoryDisplayOrder" />(半角数字3文字以内)<input type="hidden" name="data[Category][type_id][' + type_count_id + '][]" value ="' + type_id + '" class="type_count_id_' + type_count_id + '"/><input type="hidden" name="data[Category][id][' + type_count_id + '][]" value ="' + saved_category_id + '"  id="category_id_' + type_count_id + '_' + count_id + '" />' + category_remove_button + '</div></div>');

}
//Get course ID with ajax
function get_course_id() {
    return $.ajax({
        type: 'GET',
        url: "/types/get_course_id/",
        async: false,
    });
}
//Get latest test species ID with ajax
function get_type_id() {
    return $.ajax({
        type: 'GET',
        url: "/types/get_type_id/",
        async: false,
    });
}
//Get registered form data with ajax
function get_form_data(course_id) {
    return $.ajax({
        type: 'GET',
        url: "/types/get_form_data/" + course_id,
        async: false,
        dataType: "json",
    });
}
//Delete test species
function remove_type_data(type_id) {
    $.ajax({
        type: "GET",
        url: "/types/remove_type_data/" + type_id,
        async: false,
    });
}
//Delete test category
function remove_category_data(category_id) {
    $.ajax({
        type: "GET",
        url: "/types/remove_category_data/" + category_id,
        async: false,
    });
}

function add_style() {
    $("input[type='text']").each(function() {
        if (!$(this).val()) {
            $(this).css('background-color', '#ededed');
        }
        $(this).focus(function() {
            $(this).css('background-color', 'white');
        }).blur(function() {

            if (!$(this).val()) {
                $(this).css('background-color', '#ededed');
            }
        })
        $(this).keyup(function() {
            if ($(this).val()) {
                $(this).css('background-color', 'white');
            } else {
                $(this).css('background-color', '#ededed');
            }
        })
    })
}