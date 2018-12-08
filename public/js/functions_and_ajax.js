/*
 * Funcion para inicialiar elemento xmlhttprequest
 */
function define_xmlhttp(){
    //comprobar si se esta usando Internet Explorer
    try {
        // Si la versiï¿½n de JS es superior a 5
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e){
        //utilizamos el tradicional Objeto ActiveX
        try {
            // Si estamos usando Internet Explorer
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e)
        {
            // en caso contrario otro navegador no IE
            xmlhttp = false
        }
    }
    // Si no estamos usando IE,
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined'){
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

/*
 * funcion para correr una rutina ajax multiple uso
 */
function runajax(serverPage, objID){
    xmlhttp = define_xmlhttp();
    // ejecutando ajax
    var obj = document.getElementById(objID);
    xmlhttp.open("POST", serverPage);
    xmlhttp.onreadystatechange = function(){
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
            obj.innerHTML = xmlhttp.responseText;
        }
        else{
            obj.innerHTML = "Cargando...";
        }
    }
    xmlhttp.send(null);
}

/*
 * Ejecuta las ventanas emergentes deacuerdo a la opción
 * seleccionada en el menu principal
 */
function run_form_modal(idForm){
    if (idForm=='copy'){
        var obj = document.getElementById('copy')
        obj.onclick();
    }
    if (idForm=='import'){
        var obj = document.getElementById('import')
        obj.onclick();
    }    
}

/*
 * funcion para comprobar el numero de campos de cada archivo
 */
function evaluar_num_campos(){
    obj0 = document.getElementById('archivo')
    obj1 = document.getElementById('num_campos_servidor')
    obj2 = document.getElementById('num_campos_archivo')
    if (obj0.value==""){
        alert('No hay ningún archivo seleccionado');
        return false
    }
    if (obj1.value != obj2.value){
        alert('El numero de campos no son iguales, no se logró importar los datos');
        return false
    }
    else{
        runajax('import_cnx.php?file='+document.getElementById('archivo').value,'div_message')
    }
    return true
}


function redirect(id){
    document.getElementById('id_reporte').value = id;
    document.vrb_pivote.submit();
}

function exportar(){
    obj = document.getElementById('dato_exportado')
    obj2 = document.getElementById('tblresultado')
    document.vrb_exportar.submit();
}


function evaluar_archivo_a_copiar(){
    obj0 = document.getElementById('archivo').value
    var ext = obj0.substring(obj0.lastIndexOf('.')).toLowerCase();
    if (ext!='.txt'){
        alert('El archivo debe ser tipo CSV y extensión .txt')
        return false
    }
    else{
        return true
    }
}

