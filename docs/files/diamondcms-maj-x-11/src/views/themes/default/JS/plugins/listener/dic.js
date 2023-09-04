class DiamondImgChooser {
    /**
     * 
     * @param {*} options 
     * this.options = {
            en_1: false,
            preselected_img: "",
            imgformat: "square",
            img_1: {},
            en_2: false,
            preselected_link: "",
            en_3 : false
            adapt_launcher: true
            callback : callback
        }
     */

    constructor(options) {
        this.disable_imgs = false;
        this.disable_link = false;
        this.disable_upload = false;
        this.something_correct_specified = false;
        this.options = options;
        this.termined = false;
        this.format = "standard";

        if (typeof this.options.where_is_dic != "undefined" && typeof this.options.where_is_dic == "string"){
            $( "body" ).append('<div id="dic_loader"></div>');
            var DIC = this;
            $( "#dic_loader" ).load( this.options.where_is_dic + "dic.html", function(e){
                DIC.load();
            } );
        }else {
            DIC.load();
        }
        
    }

    load(){
        if (this.options.en_1){
            if (this.options.img_1 !== undefined && (typeof(this.options.img_1) == "object" || typeof(this.options.img_1) == "Object")){
                for(let i in this.options.img_1){
                    var obj = this.options.img_1;
                    $("#dic_option1-imgcontainer").append('<div class="col-sm-3" style=""><div class="dic_img-preview" data-filename="' + obj[i]["filename"] + '" data-path="'+ obj[i]["path"] +'"><img class="img-responsive dic_img-frame" src="' + obj[i]["source_link"] + '" alt=""><p style="word-wrap: break-word;" class="text-center dic_img_name"><strong>' + obj[i]["filename"] + '</strong></p></div></div>');
                }
                if (this.format !== "standard" && (this.format == "square")){
                    $("#dic_option1-squarealert").show();
                }
            }
            if (!this.options.en_2 && !this.options.en_3)
                $("#dic_option1-name").html("");
            $("#dic_option1").show();
        }
        if (this.options.en_2){
            if (!this.options.en_1 && this.options.en_3)
                $("#dic_option2-name").html("Option 1 :");
            else if (!this.options.en_1 && !this.options.en_3)
                $("#dic_option2-name").html("");
            $("#dic_option2").show();
        }
        if (this.options.en_3){
            if (!this.options.en_1 && !this.options.en_2)
                $("#dic_option3-name").html("");
            else if (!this.options.en_1 || !this.options.en_2)
                $("#dic_option3-name").html("Option 2 :");
            $("#dic_option3").show();
        }

        if (this.options.adapt_launcher){
            $("#dic_launcher").val("Choisissez votre image...");
        }

        if (typeof(this.options.imgformat) != "undefined"){
            this.format = this.options.imgformat;
        }
        bsCustomFileInput.init()

        $("#dic_launcher").click(function(e){
            DIC.show_mod();
        });

        $(".dic_img-preview").click(function(e){
            DIC.select_img(this);
        });

        $("#dic_exec").click(function(e){
            DIC.exec(this);
        });

        $("#newimg").on("change", e => {
            DIC.change_uploader(e, this);
        })

        $("#dlt_img").click(function(e){
            DIC.reset_uploader(this);
        });
        
        $("#img_link").on("change", e => {
            DIC.change_img_linker();
        });

        if (typeof(this.options.preselected_link) == "string" || typeof(this.options.preselected_link) == "String"){
            $("#img_link").val(this.options.preselected_link);
            this.change_img_linker();
            this.exec();
        }else if (typeof(this.options.preselected_img) == "string" || typeof(this.options.preselected_img) == "String"){
            this.select_img($("[data-filename = '"+this.options.preselected_img + "']"));
            this.exec();
        }

        this.initialized = true;
    }

    show_mod() {
        if (this.initialized)
            $("#dic_modal").modal('show');
        else
            throw new Error("DiamondImgChooser needs to be first initialized.");
    }

    select_img(item){
        if (this.disable_imgs || !($("#img_link").val() == "" || $("#img_link").val() == " " || $("#img_link").val() == null)) {
                $("#dic_cant1").show();
                $("#dic_exec").attr("disabled", false);
                return false;
        }
        if ($(item).attr("active") == "true") {
            $(item).css("opacity", "");
            $(item).css("border", "");
            $(item).attr("active", "false");
            this.reset();
        }
        else {
            $("#dic_option2-invalid").hide();
            $(".dic_img-preview").css("opacity", "");
            $(".dic_img-preview").css("border", "");
            $(".dic_img-preview").attr("active", "false");
            $(item).css("opacity", "50%");
            $(item).css("border", "1px solid black");
            $(item).attr("active", "true");
            $("#img_choosen").val($(item).attr("data-path"));
            $("#newimg").attr("disabled", true);
            $("#img_link").attr("disabled", true);
            $("#img_link").val("");
            $("#img_link").attr("placeholder", "Vous avez opté pour une autre option.");
            this.disable_link = this.disable_upload = true;
            $("#dic_exec").attr("disabled", false);
            $("#dic_option2").css("opacity", "50%");
            $("#dic_option3").css("opacity", "50%");
        }
    }

    change_uploader(e, th){
        var file = e.target.files[0];
        if (typeof(file) != "undefined" && file.type.substr(0, 5) 
            && (file.type == "image/png" ||file.type == "image/jpg"||file.type == "image/jpeg")){
            var fileReader = new FileReader();
            fileReader.readAsDataURL(file);
            fileReader.onload = function(fileEvent) {  
                var img = new Image();
                img.src = fileEvent.target.result;
                img.onload = function(event){
                    if (DIC.format != "square" || (img.naturalWidth/img.naturalHeight >= 0.9 && img.naturalWidth/img.naturalHeight <= 1.1)){
                        $("#dic_option2-invalid").hide();
                        DIC.do_change_uploader();
                    }else {
                        $("#dic_option2-invalid").show();
                    }
                }
            }
        }else if(typeof(file) != "undefined") {
            $("#dic_option2-invalid").show();
        }else {
            $("#dic_option2-invalid").hide();
            this.do_change_uploader();
        }
    }

    do_change_uploader(){
        $("#dlt_img").attr("disabled", false);
        $(".img-preview").css("opacity", "");
        $(".img-preview").css("border", "");
        $(".img-preview").attr("active", "false");
        $("#img_choosen").val("");
        $("#img_link").attr("disabled", true);
        $("#img_link").attr("placeholder", "Vous avez opté pour une autre option.");
        this.disable_link = this.disable_imgs = true;
        $("#dic_exec").attr("disabled", false);
        $("#dic_option1").css("opacity", "50%");
        $("#dic_option3").css("opacity", "50%");
    }

    change_img_linker(){
        if ($("#img_link").val() == "" || $("#img_link").val() == " " || $("#img_link").val() == null) {
            this.reset()
        }
        else {
            $("#dic_option2-invalid").hide();
            $(".dic_img-preview").css("opacity", "");
            $(".dic_img-preview").css("border", "");
            $(".dic_img-preview").attr("active", "false");
            $("#newimg").attr("disabled", true);
            this.disable_upload = this.disable_imgs = true;
            $("#img_choosen").val("");
            $("#dic_exec").attr("disabled", false);
            $("#dic_option1").css("opacity", "50%");
            $("#dic_option2").css("opacity", "50%");
        }
    }

    reset_uploader(item){
        $("#dic_option2-invalid").hide();
        this.reset();
        $(item).attr("disabled", true);
    }

    reset(){
        this.disable_upload = this.disable_imgs = this.disable_link = false;
        $("#dic_exec").attr("disabled", true);
        $("#dic_cant1").hide();
        $("#dic_option1").css("opacity", "");
        $("#dic_option2").css("opacity", "");
        $("#dic_option3").css("opacity", "");
        $("#img_link").attr("disabled", false);
        $("#img_link").attr("placeholder", "Le lien doit commencer par http:// ou https://");
        $("#dic_newimg-label").html("Choisissez une image sur votre ordinateur");
        $("#newimg").val("");
        $("#newimg").attr("disabled", false);
        $("#img_choosen").val("");
        $("#img_link").val("");

        this.termined = false;
        if (this.options.adapt_launcher)
            $("#dic_launcher").val("Choisissez votre image..." );

        if (typeof(this.options.resetcallback) != "undefined" && this.options.resetcallback != null)
            window[this.options.resetcallback]();
    }

    exec(item){
        if (this.disable_upload + this.disable_imgs + this.disable_link != 2){
            $("#dic_exec").attr("disabled", true);
            return;
        }
        if (this.options.adapt_launcher){
            $("#dic_launcher").val("Image sélectionnée !");
        }
        this.finalData = DiamondSerializeFormData("#dic_form");
        var to_delete = Array();
        for(let [name, value] of this.finalData) {
            if (typeof value == "undefined" || value == "" || value == " " || value == "undefined" || (typeof value == "string" && value.length === 0))
                to_delete.push(name);    
        }
        to_delete.forEach(name => {
            this.finalData.delete(name);
        });
        this.termined = true;
        if (typeof(this.options.callback) != "undefined" && this.options.callback != null)
            window[this.options.callback]();
        
    }

    is_termined(){
        return this.termined;
    }

    get_contentSelected(){
        if (!this.termined)
            return undefined;
        for(let [name, value] of this.finalData) { console.log(name,value); }
        return this.finalData;
    }
}