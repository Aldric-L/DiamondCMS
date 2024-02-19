function DiamondThrowError(code){
    $.ajax({
        url : $("head").attr("data-baselink") + "api/tools/get/logNewError/",
        dataType : 'html',
        type : 'POST',
        data: "code=" + code
    });
}


function getValue(target, ignore_dic_undefined=false){
    var name;
    var value;
    if (typeof(target) == "undefined")
        return {name: name, value: value};
        
    if (target.type == "checkbox"){
        name = target.name;
        value = target.checked;
    }else if (target.type == "text" ||target.type == "select" ||target.type == "password" ||target.type == "email" ||target.type == "number" ||target.type == "hidden" || target.localName == "select"){
        name = target.name;
        value = target.value;
    }else if (target.type == "textarea" || target.localName == "textarea"){
        if (typeof tinymce === 'undefined') {
            name = target.name;
            value = target.value;
        }else {
            name = target.name;
            value = tinyMCE.activeEditor.getContent();
        }
    }else if (target.type == "file"){
        if (target.files.length === 0){
            name = target.name;
            value = undefined;
        }else if (target.files.length >= 1){
            if (target.files.length > 1)
                alert("Un seul fichier ne peut être envoyé à la fois. Seul le premier fichier est considéré.");
            name = target.name;
            value = target.files[0];
        }
    }else if (target.type == "button" && target.id == "dic_launcher"){
        console.log(target, DIC.get_contentSelected());
        dic_content = DIC.get_contentSelected();
        if (dic_content != undefined){
            i = 0;
            for(var pair of dic_content) {
                if (ignore_dic_undefined){
                    if (!(typeof(pair[1]) == "undefined" || pair[1] == null || pair[1] == undefined)){
                        name = pair[0];
                        value = pair[1];
                    }
                }else {
                    name[i] = pair[0];
                    value[i] = pair[1];
                }
                i++;
            }
        }   
    }
    return {name: name, value: value};
}