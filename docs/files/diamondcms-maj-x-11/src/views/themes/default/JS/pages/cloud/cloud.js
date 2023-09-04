$(document).ready(function () {
    bsCustomFileInput.init()
})

function deleter(r, th){
    var id = th.attr("data-id");
    $("#" + id).hide();
}

function renammer_callback(r, th){
    if (r['State'] == 1 && (r['Errors'] == null || typeof(r['Errors']) == "undefined")){  
        //th.parents(".namer").children(".name-link").html(th.parents(".namer").children(".namer_form").children(".name-field").val());
        th.parents(".namer").children(".name-link").html(r['Return']['dispnewname']);
        var link = th.parents(".namer").children(".name-link").attr("data-prefixlink") + r['Return']['newname'] + "/" + r['Return']['ext'];
        th.parents(".namer").children(".name-link").attr("href", link);
        var id = th.parents(".namer").parents(".file-item").attr("data-id");  
        th.parents(".namer").parents(".file-item").attr("data-name", r['Return']['finalnewname']);   
        $("#hidden-filename-field-" + id).val(r['Return']['finalnewname'].replaceAll(r['Return']['prefix'] + "_", ""));
        $("#dispname-modal-" + id).html(r['Return']['dispnewname']);       
        $("#truename-modal-" + id).html(r['Return']['finalnewname']);      
        $("#link-modal-" + id).html(link); 
        $("#link-modal-" + id).attr("href", link); 
        th.parents(".namer").children(".simpleSendRenameAttr").attr("data-processing", false);
        console.log(r['Return'])
    }
}

function rename(th){
    var item = th.parents(".file-item");
    var naming = item.children(".namer")
    naming.children(".name-link").hide();
    naming.children(".namer_form").children(".name-field").show();

    function launch_rename(e){
        e.preventDefault();
        e.stopPropagation();
        e.data['name-link'].show();
        e.data['name-field'].hide();
        if (e.data['name-field'].val() != e.data['name-link'].html() && e.data['simpleSendRenameAttr'].attr("data-processing") != true){
            e.data['simpleSendRenameAttr'].attr("data-processing", true);
            processSimpleSend(e, e.data['simpleSendRenameAttr']);
        }
    }

    var event_options = {
        'name-link' : naming.children(".name-link"),
        'name-field' : naming.children(".namer_form").children(".name-field"),
        'id' : item.attr("data-id"),
        'simpleSendRenameAttr' : naming.children(".simpleSendRenameAttr"),
    }
    naming.children(".namer_form").children(".name-field").off("blur");
    naming.children(".namer_form").children(".name-field").off("keypress");
    naming.children(".namer_form").children(".name-field").on("blur", event_options, launch_rename);
    naming.children(".namer_form").children(".name-field").on('keypress', event_options, function(e) {
        if(e.which == 13) {
            $(this).blur();
        }
    });
}

$(".renamer").click(function(e){
    rename($(this));
});

$(".access_level-field").on("change", function (e) {
    processSimpleSend(e, $(this).parents(".editer").children(".simpleSendEditAttr"));
});

$(".hidden-access-field").on("change", function (e) {
    processSimpleSend(e, $(this).parents(".editer").children(".simpleSendEditAttr"));
});

$("#btnCreateFolder").on("click", function (e) {
    $(this).attr("disabled", true);
    $("#newFolderitem").show();
    $("#newfolder_name").on('keypress', function(e) {
        if(e.which == 13) {
            $(this).blur();
        }
    });
    $("#newfolder_name").on("blur", function (e){
        e.preventDefault();
        e.stopPropagation();
        processSimpleSend(e, $("#simpleSendNewFolder"));
    });
});


const items = document.querySelectorAll('.item-file');
items.forEach(item => {
    item.addEventListener('dragstart', dragStart);
});



function dragStart(e) {
    if (!$(e.target).hasClass("file-item"))
        target = $(e.target).parents(".file-item")
    else
        target = $(e.target)
    e.dataTransfer.setData("text", target.attr("data-name"));
    setTimeout(() => {
        target.addClass("hide")
    }, 0);
}


const boxes = document.querySelectorAll('.item-folder');

boxes.forEach(box => {
    box.addEventListener('dragenter', dragEnter)
    box.addEventListener('dragover', dragOver);
    box.addEventListener('dragleave', dragLeave);
    box.addEventListener('drop', drop);
});


function dragEnter(e) {
    e.preventDefault();
    if (!$(e.target).hasClass("file-item"))
        target = $(e.target).parents(".file-item")
    else
        target = $(e.target)
    target.addClass("drag-over")
}

function dragOver(e) {
    e.preventDefault();
    if (!$(e.target).hasClass("file-item"))
        target = $(e.target).parents(".file-item")
    else
        target = $(e.target)
    target.addClass("drag-over")
}

function dragLeave(e) {
    if (!$(e.target).hasClass("file-item"))
        target = $(e.target).parents(".file-item")
    else
        target = $(e.target)
    target.removeClass("drag-over")
}

function drop(e) {
    if (!$(e.target).hasClass("file-item"))
        target = $(e.target).parents(".file-item")
    else
        target = $(e.target)
    target.removeClass("drag-over")
    $("#mooveFileForm").children("[name='pathfrom']").val($("#originalPathfrom").val() /*+ e.dataTransfer.getData('text/plain')*/)
    $("#mooveFileForm").children("[name='itemname']").val(e.dataTransfer.getData('text/plain'))
    if (target.attr("data-name") == ".."){
        $("#mooveFileForm").children("[name='pathto']").val($("#parentPath").val() /*+ e.dataTransfer.getData('text/plain')*/)
    }else {
        $("#mooveFileForm").children("[name='pathto']").val($("#originalPathfrom").val() + target.attr("data-name") + "/" /*+ e.dataTransfer.getData('text/plain')*/)
    }
    
    processSimpleSend(e, $("#simpleSendMooveAttr"));
}