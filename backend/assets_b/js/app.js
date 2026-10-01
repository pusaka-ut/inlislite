/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



//$("#input-dim-1a").fileinput({
//    uploadUrl: "/file-upload-batch/2",
//    allowedFileExtensions: ["jpg", "png", "gif"],
//    minImageWidth: 50,
//    minImageHeight: 50
//});

function showUser(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","ambil/?id="+str,true);
        xmlhttp.send();
    }
}

(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);


alertSwal = function($msg,$type,$timer) {
    swal({
        title: " ",
        text: $msg,
        type: $type,
        timer: $timer,
        cancelButtonText: "Tutup",
        closeOnConfirm: true,
    });
};

cleanResponseError = function($responseText,$varFind){
    var msg = $responseText.replace($varFind, " ")
    return msg;
};

function startLoading()
{
    var url = appBaseUrl;
    $("<div class=\"modal-backdrop\" style=\"opacity:0.5\"><center><img src=\""+url+"/assets_b/images/loading.gif\" class=\"centered\" width=\"150px\" /></center></div>").appendTo(document.body);
}

function endLoading()
{
    $(".modal-backdrop").remove();
}

(function($) {
    $(document).on('click', '.export-xls, .export-csv, .export-pdf, .export-txt, .export-html, .export-json', function() {
        var $el = $(this);
        if ($el.data('export-busy')) {
            return;
        }
        $el.data('export-busy', true);
        setTimeout(function() {
            $el.removeData('export-busy');
        }, 3000);

        var format = 'Dokumen';
        if ($el.hasClass('export-xls')) {
            format = 'Excel';
        } else if ($el.hasClass('export-csv')) {
            format = 'CSV';
        } else if ($el.hasClass('export-pdf')) {
            format = 'PDF';
        } else if ($el.hasClass('export-txt')) {
            format = 'Text';
        } else if ($el.hasClass('export-html')) {
            format = 'HTML';
        } else if ($el.hasClass('export-json')) {
            format = 'JSON';
        }

        var $container = $('#inlisExportToastContainer');
        if (!$container.length) {
            $container = $('<div id="inlisExportToastContainer" class="inlis-export-toast-container"></div>').appendTo('body');
        }

        $container.empty();
        var $toast = $('<div class="inlis-export-toast">' +
            '<div class="inlis-export-toast-icon"><div class="inlis-export-spinner"></div></div>' +
            '<div class="inlis-export-toast-body">' +
            '<div class="inlis-export-toast-title">Menyiapkan Berkas Ekspor</div>' +
            '<div class="inlis-export-toast-text">Memproses data ke format ' + format + '... Unduhan akan dimulai otomatis.</div>' +
            '</div>' +
            '</div>').appendTo($container);

        setTimeout(function() {
            if ($toast && $toast.length) {
                $toast.addClass('toast-success');
                $toast.find('.inlis-export-toast-icon').html('<i class="glyphicon glyphicon-ok" style="font-size:16px;"></i>');
                $toast.find('.inlis-export-toast-title').text('Ekspor Berhasil');
                $toast.find('.inlis-export-toast-text').text('Berkas ' + format + ' telah dialirkan ke browser.');
                setTimeout(function() {
                    $toast.addClass('toast-hiding');
                    setTimeout(function() {
                        $toast.remove();
                    }, 350);
                }, 2200);
            }
        }, 1800);
    });
})(jQuery);
