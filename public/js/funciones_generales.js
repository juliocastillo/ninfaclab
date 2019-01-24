function define_xmlhttp() {
    //comprobar si se esta usando Internet Explorer
    try {
        // Si la versi�n de JS es superior a 5
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        //utilizamos el tradicional Objeto ActiveX
        try {
            // Si estamos usando Internet Explorer
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e)
        {
            // en caso contrario otro navegador no IE
            xmlhttp = false
        }
    }
    // Si no estamos usando IE,
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

function runajax(serverPage, objID) {
    xmlhttp = define_xmlhttp();
    // ejecutando ajax
    var obj = document.getElementById(objID);
    xmlhttp.open("POST", serverPage);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            obj.innerHTML = xmlhttp.responseText;
        } else {
            obj.innerHTML = "<img src='../html/images/loading.gif'>";
        }
    }
    xmlhttp.send(null);
}
//validacion en la vista de captura de tabuladores
function obliga() {
    if (document.getElementById('nombre_tab').value == "") {
        alert('Nombre del tabulador es obligatorio');
        document.getElementById('nombre_tab').focus()
        return false
    }
    return error
}

//validacion en la vista de captura de tabuladores
function obligatorio(objID) {
    if (objID.value == "") {
        alert('Campo es obligatorio');
        document.getElementById(objID.name).focus();
        return false
    }
}

function validarPregunta() {
    if (document.getElementById('p').value == "") {
        alert('Campo pregunta es obligatorio');
        document.getElementById('p').focus();
        return false
    }
    if (document.getElementById('c').value == "") {
        alert('Campo es obligatorio');
        document.getElementById('c').focus();
        return false
    }
    if (document.getElementById('l').value == "") {
        alert('Campo largo es obligatorio');
        document.getElementById('l').focus();
        return false
    }

}


function autobusqueda(thevalue, e) {
    xmlhttp = define_xmlhttp();
    var theextrachar = e.which;
    if (theextrachar == undefined) {
        theextrachar = e.keyCode;
    }

    // ubicacion que se carga la pagina
    var objID1 = "divpregunta";

    //tener en cuenta el caracter backspace
    if (theextrachar == 8) {
        if (thevalue.length == 1) {
            var serverPage = "./models/pregunta_bus.php";
        } else {
            var serverPage = "./models/pregunta_bus.php" + "?q=" +
                    thevalue.substr(0, (thevalue.length - 1));
        }
    } else {
        var serverPage = "./models/pregunta_bus.php" + "?q=" +
                String.fromCharCode(theextrachar);
    }
    var obj = document.getElementById(objID1);
    xmlhttp.open("GET", serverPage);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            obj.innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.send(null);
}


// PARA FORMULARIO DE CONFIGURACION DE TABULADORES
// extraer el ID del radio button que se ha seleccionado.
function groupID() {
    var lista = document.getElementsByName('groupsel');
    for (i = 0; i < lista.length; i++) {
        if (lista[i].checked) {
            return lista[i].value;
        }
    }
}

//validacion en la vista de captura de Grupo
function validarGrupo() {
    if (document.getElementById('nombre_grup').value == "") {
        alert('Nombre del grupo es obligatorio');
        document.getElementById('nombre_grup').focus()
        return false
    }

    return error
}

function inicializaDataTable(cbo, pageLength, lengthChange, searching) {
    $(document).ready(function () {
        $('#' + cbo).DataTable({
            "pageLength": pageLength, //ajusta el tamaño de la pagina en n registros
            "lengthChange": lengthChange,
            "searching": searching,
            "ordering": false,
            "columnDefs": [
                {"orderable": false, "targets": 0}
            ],
            language: {
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ entries",
                "loadingRecords": "Loading...",
                "processing": "Processing...",
                "search": "Buscar en tabla:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
}

function inicializaDataTableEditor(cbo, pageLength, lengthChange, searching) {
    $(document).ready(function () {
        $('#' + cbo).DataTable({
            "pageLength": pageLength, //ajusta el tamaño de la pagina en n registros
            "lengthChange": lengthChange,
            "searching": searching,
            language: {
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ entries",
                "loadingRecords": "Loading...",
                "processing": "Processing...",
                "search": "Buscar en tabla:",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
}