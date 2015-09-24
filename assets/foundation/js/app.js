// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs

jQuery(document).ready(function($) {
    if ($("#register-form").length) {

        var username_valid      = false;
        var password_valid      = false;
        var passconf_valid      = false;
        var email_valid         = false;
        var displayname_valid   = false;

        function form_error(error_text) {
            return "<small class='error'>" + error_text + "</small>";
        }

        function validateEmail(email) {
            var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
            return re.test(email);
        }

        function validate_form() {
            if (username_valid &&
                password_valid &&
                passconf_valid &&
                email_valid &&
                displayname_valid) {

                    $("#registerbutton").removeAttr("disabled");
            }
        }

        $("#username").keydown(function() {
            // call for check on username already existing
            var that = $(this);
            var parent = that.parent(".form-input");
            var value = that.val();

            if (value.length < 4) {
                // Username too short!
                if (!parent.children("small.error").length)
                    parent.append(form_error("Username must be at least 4 characters long"));

                return true;
            }

            if (value.length > 32) {
                // Username too long!
                if (!parent.children("small.error").length)
                    parent.append(form_error("Username must be 32 characters or less"));

                return true;
            }

            parent.children("small.error").remove();
            validate_form();
        });

        $("#username").blur(function() {
            var that = $(this);
            var parent = that.parent(".form-input");
            var value = that.val();
            $.ajax({
                url: "/ajax/username_exists/" + value,
                beforeSend: function( xhr ) {
                    xhr.overrideMimeType( "text/plain; charset=utf8" );
                }
            })
            .done(function( data ) {
                var result = parseInt(data);

                if (result == 0) {
                    // Username not found, we're good
                    parent.children("small.error").remove();
                    username_valid = true;
                } else if (result == 1) {
                    if (!parent.children("small.error").length)
                        parent.append(form_error("This username is already in use"));
                } else {
                    if (!parent.children("small.error").length)
                        parent.append(form_error("Username cannot be blank"));
                }
                validate_form();
            });
        });

        $("#pass").keydown(function() {
            // call for check on username already existing
            var that = $(this);
            var parent = that.parent(".form-input");
            var value = that.val();

            if (value.length < 4) {
                // Username too short!
                if (!parent.children("small.error").length)
                    parent.append(form_error("Password must be at least 4 characters long"));

                return true;
            }

            parent.children("small.error").remove();
            password_valid = true;

            var parent = $("#confirm_pass").parent(".form-input");
            if ($("#confirm_pass").val() != that.val()) {
                if (!parent.children("small.error").length)
                    parent.append(form_error("Passwords do not match"));
                return true;
            } else {
                passconf_valid = true;
                parent.children("small.error").remove();
            }
            validate_form();
        });

        $("#confirm_pass").blur(function() {
            var that = $(this);
            var parent = that.parent(".form-input");
            if ($("#pass").val() != that.val()) {
                if (!parent.children("small.error").length)
                    parent.append(form_error("Passwords do not match"));
                return null;
            }

            parent.children("small.error").remove();
            passconf_valid = true;
        });

        $("#email").keydown(function() {
            var that = $(this);
            var parent = that.parent(".form-input");
            var value = that.val();
            if (!validateEmail(value)) {
                if (!parent.children("small.error").length)
                    parent.append(form_error("Your email address does not look valid"));
                return null;

            }

            parent.children("small.error").remove();
            email_valid = true;
            validate_form();

        });

        $("#display_name").keydown(function() {
            // call for check on username already existing
            var that = $(this);
            var parent = that.parent(".form-input");
            var value = that.val();

            if (value.length < 4) {
                // Username too short!
                if (!parent.children("small.error").length)
                    parent.append(form_error("Display must be at least 4 characters long"));

                return true;
            }

            if (value.length > 32) {
                // Username too long!
                if (!parent.children("small.error").length)
                    parent.append(form_error("Display name must be 32 characters or less"));

                return true;
            }
            displayname_valid = true;
            parent.children("small.error").remove();
            validate_form();
        });

        $("#register-form").submit(function() {
            if (!username_valid ||
                !password_valid ||
                !passconf_valid ||
                !email_valid ||
                !displayname_valid) {
                if (!($("#error-shell").children(".alert-box").length)) {
                    $("#error-shell").append("<div data-alert class=\"alert-box alert\">Something went wrong with your submission, please make sure all fields are completed properly.</div>");
                }
                return false;
            }
        });
    }

    // Parser form handles
    if ($("#parser-form").length) {
        $("#file-button").click(function() {
            $("#upload").click();
        });

        if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
            alert('The File APIs are not fully supported in this browser.');
            return;
        }

		$("#upload").change(function() {
    		var file = document.getElementById("upload").files[0];
    		if (file) {
    		    var reader = new FileReader();
    		    reader.readAsText(file, "UTF-8");
    		    reader.onload = function (evt) {
    		    	var raw = evt.target.result;

    		    	$("#file").html(raw);
    		    }
    		    reader.onerror = function (evt) {
    		    	alert("Something went wrong, try again?");
    		    }

    		}
        });
    }

    // Data tables scripts
    $("#pack-table").DataTable({
        "paging": false,
        "autoWidth": false,
        "order": [[ 0, "asc" ]],
        "columns": [
            { "width": "40%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "10%" },
            { "width": "30%" },
        ]
    });

    $("#pack-single-table").DataTable({
        "paging": false,
        "autoWidth": false,
        "order": [[ 0, "asc" ]],
        "columns": [
            { "width": "40%" },
            { "width": "30%" },
            { "width": "5%" },
            { "width": "15%" },
            { "width": "5%" },
            { "width": "5%" },
        ]
    });

    $("#user-scores-table").DataTable({
        "paging": true,
        "pageLength": 50,
        "pagingType": "four_button",
        "autoWidth": false,
        "searching": true,
        "order": [[ 2, "desc" ]]
    });

    $("#chart-scores-table").DataTable({
        "paging": true,
        "pageLength": 50,
        "pagingType": "four_button",
        "autoWidth": false,
        "searching": true,
        "order": [[ 2, "desc" ]]
    });

    $("#rank-table").DataTable({
        "paging": true,
        "pageLength": 50,
        "pagingType": "four_button",
        "autoWidth": false,
        "searching": true,
        "order": [[ 2, "desc" ]]
    });

    $("#auto-file-type").DataTable({
        "paging": true,
        "pageLength": 100,
        "pagingType": "four_button",
        "autoWidth": false,
        "searching": true,
        "order": [[ 0, "asc"], [ 1, "asc"]]
    });

    var song_table = $("#song-table").DataTable({
        "paging": true,
        "pageLength": 50,
        "pagingType": "four_button",
        "autoWidth": false,
        "searching": true,
        "order": [[ 3, "asc" ]],
        "columns": [
            { "width": "25%" },
            { "width": "12%" },
            null,
            null,
            { "width": "20%" },
            null,
            null,
            null
        ]

    });

    $("#chart-name, #artist-name, #pack-name, #rate-val").keyup(function() {
        song_table.draw();
    });

    $("#rate-val, #file-type, #pack-name").change(function() {
        song_table.draw();
    });

    if ($("#chat-box").length) {
        Ps.initialize(document.getElementById('chat-box'));
        $("#chat-box").scrollTop($("#chat-contents").height());
    }

    $('.fdatepicker').fdatepicker();

    $("#upload-ss-btn").click(function() {
        $("#userfile").val("");
        $("#userfile").click();
    });


    // Add events
    $('#userfile').change(function() {
        var file = this.files[0];
        var name = file.name;
        var size = file.size;
        var type = file.type;
        $("html, body").css("cursor", "wait");
        $("#err-filetype").hide();
        $("#err-filesize").hide();
        $("#err-dimensions").hide();

        if ($("#userfile").val().length) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url: '/ajax/upload_screenshot',  //Server script to process data
                type: 'POST',
                xhr: function() {  // Custom XMLHttpRequest
                    var myXhr = $.ajaxSettings.xhr();
                    if(myXhr.upload){ // Check if upload property exists
                    }
                    return myXhr;
                },
                //Ajax events
                beforeSend: function() {

                },
                success: function(data) {
                    console.log(data);
                    $("html, body").css("cursor", "initial");
                    if (data == "<p>The filetype you are attempting to upload is not allowed.</p>") {
                        $("#err-filetype").show();
                    } else if (data == "<p>The uploaded file exceeds the maximum allowed size in your PHP configuration file.</p>") {
                        $("#err-filesize").show();
                    } else if (data == "<p>The image you are attempting to upload doesn't fit into the allowed dimensions.</p>") {
                        $("#err-dimensions").show();
                    } else {
                        $("#image-preview img").attr("src", data);
                        $("#screenshot_url").val(data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $("html, body").css("cursor", "initial");
                },
                // Form data
                data: formData,
                //Options to tell jQuery not to process data or worry about content-type.
                cache: false,
                contentType: false,
                processData: false
            });
        }
    });

    $('.slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.slider-nav'
    });
    $('.slider-nav').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        dots: false,
        arrows: false,
        centerMode: true,
        focusOnSelect: true
    });


    $(document).foundation({
        slider: {
            on_change: function(){
                song_table.draw();
            }
        }
    });

    $(document).confirmWithReveal();
});
// Data Tables extensions
$.fn.dataTableExt.oPagination.four_button = {
    "fnInit": function ( oSettings, nPaging, fnCallbackDraw )
    {
        var nFirst = document.createElement( 'span' );
        var nPrevious = document.createElement( 'span' );
        var nNext = document.createElement( 'span' );
        var nLast = document.createElement( 'span' );

        nFirst.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sFirst ) );
        nPrevious.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sPrevious ) );
        nNext.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sNext ) );
        nLast.appendChild( document.createTextNode( oSettings.oLanguage.oPaginate.sLast ) );

        nFirst.className = "paginate_button button first";
        nPrevious.className = "paginate_button button previous";
        nNext.className="paginate_button button next";
        nLast.className = "paginate_button button last";

        nPaging.appendChild( nFirst );
        nPaging.appendChild( nPrevious );
        nPaging.appendChild( nNext );
        nPaging.appendChild( nLast );

        $(nFirst).click( function () {
            oSettings.oApi._fnPageChange( oSettings, "first" );
            fnCallbackDraw( oSettings );
        } );

        $(nPrevious).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "previous" );
            fnCallbackDraw( oSettings );
        } );

        $(nNext).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "next" );
            fnCallbackDraw( oSettings );
        } );

        $(nLast).click( function() {
            oSettings.oApi._fnPageChange( oSettings, "last" );
            fnCallbackDraw( oSettings );
        } );

        /* Disallow text selection */
        $(nFirst).bind( 'selectstart', function () { return false; } );
        $(nPrevious).bind( 'selectstart', function () { return false; } );
        $(nNext).bind( 'selectstart', function () { return false; } );
        $(nLast).bind( 'selectstart', function () { return false; } );
    },


    "fnUpdate": function ( oSettings, fnCallbackDraw )
    {
        if ( !oSettings.aanFeatures.p )
        {
            return;
        }

        /* Loop over each instance of the pager */
        var an = oSettings.aanFeatures.p;
        for ( var i=0, iLen=an.length ; i<iLen ; i++ )
        {
            var buttons = an[i].getElementsByTagName('span');
            if ( oSettings._iDisplayStart === 0 )
            {
                buttons[0].className = "paginate_disabled_previous button disabled first";
                buttons[1].className = "paginate_disabled_previous button disabled previous";
            }
            else
            {
                buttons[0].className = "paginate_enabled_previous button first";
                buttons[1].className = "paginate_enabled_previous button previous";
            }

            if ( oSettings.fnDisplayEnd() == oSettings.fnRecordsDisplay() )
            {
                buttons[2].className = "paginate_disabled_next button disabled next";
                buttons[3].className = "paginate_disabled_next button disabled last";
            }
            else
            {
                buttons[2].className = "paginate_enabled_next button next";
                buttons[3].className = "paginate_enabled_next button last";
            }
        }
    }
};
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var min = parseInt( $('#min-diff-slider-val').val() );
        var max = parseInt( $('#max-diff-slider-val').val() );
        var diff = parseFloat( data[3] ) || 0; // use data for the diff column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && diff <= max ) ||
             ( min <= diff   && isNaN( max ) ) ||
             ( min <= diff   && diff <= max ) )
        {
            return true;
        }
        return false;
    }
);
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var text = $("#chart-name").val().toLowerCase();
        var compare = data[0].toLowerCase();

        if (compare.indexOf(text) > -1)
            return true;
        return false;
    }
);
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var text = $("#artist-name").val().toLowerCase();
        var compare = data[1].toLowerCase();

        if (compare.indexOf(text) > -1)
            return true;
        return false;
    }
);
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var text = parseFloat($("#rate-val").val());
        var compare = parseFloat(data[2]);

        if (compare == text ||
            $("#disable-rate").is(":checked"))
            return true;
        return false;
    }
);
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var text = $("#pack-name").val().toLowerCase();
        var compare = data[4].toLowerCase();

        if (compare.indexOf(text) > -1)
            return true;
        return false;
    }
);
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        if (!($("#pack-name").length))
            return true;
        var text = $("#file-type").val().toLowerCase();
        var compare = data[6].toLowerCase();

        if (compare.indexOf(text) > -1 ||
            text == "")
            return true;
        return false;
    }
);
