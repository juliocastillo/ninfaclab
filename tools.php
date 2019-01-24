<?php
/*
* Library Functions for sistems health
* j.castillo
*/
//convierte la fecha de sql a fecha en espa�o usando /
function datetosp($date){
    if ($date!='0000-00-00'){
        return date("d/m/Y",strtotime($date));
    }
};

//convertie fecha en espa�o usando / a fecha sql
function datetosql($date){
    if ($date!='0000-00-00'){    
        list($d,$m,$a)=explode("/",$date);
        return $a."-".$m."-".$d;
    }
};

function solonumero($numero){
    if (ereg('^[0-9]+$', $numero)) {
        return true; // numero valido
    }
    else{
        return false; // numero no valido
    }
}

//convierte mes en letras
function mes_en_letras($mes){
    if ($mes==1)
        return 'Enero';
    if ($mes==2)
        return 'Febrero';
    if ($mes==3)
        return 'Marzo';
    if ($mes==4)
        return 'Abril';
    if ($mes==5)
        return 'Mayo';
    if ($mes==6)
        return 'Junio';
    if ($mes==7)
        return 'Julio';
    if ($mes==8)
        return 'Agosto';
    if ($mes==9)
        return 'Septiembre';
    if ($mes==10)
        return 'Octubre';
    if ($mes==11)
        return 'Noviembre';
    if ($mes==12)
        return 'Diciembre';
    
};

/*! 
  @function num2letras () 
  @abstract Dado un n?mero lo devuelve escrito. 
  @param $num number - N?mero a convertir. 
  @param $fem bool - Forma femenina (true) o no (false). 
  @param $dec bool - Con decimales (true) o no (false). 
  @result string - Devuelve el n?mero escrito en letra. 

*/ 
function num2letras($num, $fem = false, $dec = true) { 
   $matuni[2]  = "dos"; 
   $matuni[3]  = "tres"; 
   $matuni[4]  = "cuatro"; 
   $matuni[5]  = "cinco"; 
   $matuni[6]  = "seis"; 
   $matuni[7]  = "siete"; 
   $matuni[8]  = "ocho"; 
   $matuni[9]  = "nueve"; 
   $matuni[10] = "diez"; 
   $matuni[11] = "once"; 
   $matuni[12] = "doce"; 
   $matuni[13] = "trece"; 
   $matuni[14] = "catorce"; 
   $matuni[15] = "quince"; 
   $matuni[16] = "dieciseis"; 
   $matuni[17] = "diecisiete"; 
   $matuni[18] = "dieciocho"; 
   $matuni[19] = "diecinueve"; 
   $matuni[20] = "veinte"; 
   $matunisub[2] = "dos"; 
   $matunisub[3] = "tres"; 
   $matunisub[4] = "cuatro"; 
   $matunisub[5] = "quin"; 
   $matunisub[6] = "seis"; 
   $matunisub[7] = "sete"; 
   $matunisub[8] = "ocho"; 
   $matunisub[9] = "nove"; 

   $matdec[2] = "veint"; 
   $matdec[3] = "treinta"; 
   $matdec[4] = "cuarenta"; 
   $matdec[5] = "cincuenta"; 
   $matdec[6] = "sesenta"; 
   $matdec[7] = "setenta"; 
   $matdec[8] = "ochenta"; 
   $matdec[9] = "noventa"; 
   $matsub[3]  = 'mill'; 
   $matsub[5]  = 'bill'; 
   $matsub[7]  = 'mill'; 
   $matsub[9]  = 'trill'; 
   $matsub[11] = 'mill'; 
   $matsub[13] = 'bill'; 
   $matsub[15] = 'mill'; 
   $matmil[4]  = 'millones'; 
   $matmil[6]  = 'billones'; 
   $matmil[7]  = 'de billones'; 
   $matmil[8]  = 'millones de billones'; 
   $matmil[10] = 'trillones'; 
   $matmil[11] = 'de trillones'; 
   $matmil[12] = 'millones de trillones'; 
   $matmil[13] = 'de trillones'; 
   $matmil[14] = 'billones de trillones'; 
   $matmil[15] = 'de billones de trillones'; 
   $matmil[16] = 'millones de billones de trillones'; 
   
   //Zi hack
   $float=explode('.',$num);
   $num=$float[0];

   $num = trim((string)@$num); 
   if ($num[0] == '-') { 
      $neg = 'menos '; 
      $num = substr($num, 1); 
   }else 
      $neg = ''; 
   while ($num[0] == '0') $num = substr($num, 1); 
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
   $zeros = true; 
   $punt = false; 
   $ent = ''; 
   $fra = ''; 
   for ($c = 0; $c < strlen($num); $c++) { 
      $n = $num[$c]; 
      if (! (strpos(".,'''", $n) === false)) { 
         if ($punt) break; 
         else{ 
            $punt = true; 
            continue; 
         } 

      }elseif (! (strpos('0123456789', $n) === false)) { 
         if ($punt) { 
            if ($n != '0') $zeros = false; 
            $fra .= $n; 
         }else 

            $ent .= $n; 
      }else 

         break; 

   } 
   $ent = '     ' . $ent; 
   if ($dec and $fra and ! $zeros) { 
      $fin = ' coma'; 
      for ($n = 0; $n < strlen($fra); $n++) { 
         if (($s = $fra[$n]) == '0') 
            $fin .= ' cero'; 
         elseif ($s == '1') 
            $fin .= $fem ? ' una' : ' un'; 
         else 
            $fin .= ' ' . $matuni[$s]; 
      } 
   }else 
      $fin = ''; 
   if ((int)$ent === 0) return 'Cero ' . $fin; 
   $tex = ''; 
   $sub = 0; 
   $mils = 0; 
   $neutro = false; 
   while ( ($num = substr($ent, -3)) != '   ') { 
      $ent = substr($ent, 0, -3); 
      if (++$sub < 3 and $fem) { 
         $matuni[1] = 'una'; 
         $subcent = 'as'; 
      }else{ 
         $matuni[1] = $neutro ? 'un' : 'uno'; 
         $subcent = 'os'; 
      } 
      $t = ''; 
      $n2 = substr($num, 1); 
      if ($n2 == '00') { 
      }elseif ($n2 < 21) 
         $t = ' ' . $matuni[(int)$n2]; 
      elseif ($n2 < 30) { 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      }else{ 
         $n3 = $num[2]; 
         if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
         $n2 = $num[1]; 
         $t = ' ' . $matdec[$n2] . $t; 
      } 
      $n = $num[0]; 
      if ($n == 1) { 
         $t = ' ciento' . $t; 
      }elseif ($n == 5){ 
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
      }elseif ($n != 0){ 
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
      } 
      if ($sub == 1) { 
      }elseif (! isset($matsub[$sub])) { 
         if ($num == 1) { 
            $t = ' mil'; 
         }elseif ($num > 1){ 
            $t .= ' mil'; 
         } 
      }elseif ($num == 1) { 
         $t .= ' ' . $matsub[$sub] . '?n'; 
      }elseif ($num > 1){ 
         $t .= ' ' . $matsub[$sub] . 'ones'; 
      }   
      if ($num == '000') $mils ++; 
      elseif ($mils != 0) { 
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
         $mils = 0; 
      } 
      $neutro = true; 
      $tex = $t . $tex; 
   } 
   $tex = $neg . substr($tex, 1) . $fin; 
   //Zi hack --> return ucfirst($tex);
   $end_num=ucfirst($tex).' '.$float[1].'/100 dolares';
   return $end_num; 
} 


function validarFecha($fecha){
    $sep = "[/]";
    $reg = ereg("(0[1-9]|[12][0-9]|3[01])$sep(0[1-9]|1[012])$sep(19|20)[0-9]{2}",$fecha);
        return $reg; // fecha valida
}


function solotexto($string){
    if (ereg('^[a-zA-Z �����A������]+$', $string)) {
        return true; // es cadena validad
    }
    else{
        return false; // es cadena no valida
    }
}


// esta funcion se ejecutara al principio de todo PHP que vaya a usar formato de fecha
function datevalidsp(){
    ?>
    <script type="text/javascript" language="javascript">
    var sendReq;
    var accion=0;
    var patron = new Array(2,2,4)
    var patron2 = new Array(1,3,3,3,3)
    
    function mascara(d,sep,pat,nums){
    if(d.valant != d.value)
    {
            val = d.value
            largo = val.length
            val = val.split(sep)
            val2 = ''
            for(r=0;r<val.length;r++){
                            val2 += val[r]
            }
            if(nums){
                for(z=0;z<val2.length;z++){
                    if(isNaN(val2.charAt(z))){
                        letra = new RegExp(val2.charAt(z),"g")
                        val2 = val2.replace(letra,"")
                    }
                }
            }
            val = ''
            val3 = new Array()
            for(s=0; s<pat.length; s++)
            {
                val3[s] = val2.substring(0,pat[s])
                val2 = val2.substr(pat[s])
            }
            for(q=0;q<val3.length; q++)
            {
            if(q ==0){
                            val = val3[q]
            }
            else{
                if(val3[q] != ""){
                val += sep + val3[q]
                }
            }
            }
            d.value = val
            d.valant = val
            }
        }


    //------------------------------------------------------verificar el formato de la fecha-------------------------------------------------------
    function formatofecha(id, fecha){
        /*var formatofecha = new RegExp("[0-9][0-9]\-[0-9][0-9]\-[0-9][0-9][0-9][0-9]");*/
        var regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;

        if(fecha!="")
        {

            if(!regex.test(fecha))
            {
                alert("Ingrese una fecha con formato dd/mm/aaaa");
                document.getElementById(id).value="";
                return false;
            }
            /*var regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
            if(!regex.test(fecha)){
                alert("Ingrese una fecha con formato dd/mm/aaaa");
                $('fechanacimiento').value='';
                $('edad').innerHTML='';
                return;
            }*/
            /*var d = fecha.replace(regex, '$2/$1/$3');
            alert(d);*/

            var d = new Date(fecha.replace(regex, '$2/$1/$3'));
            /*alert(d);*/
            if(!((parseInt(RegExp.$2, 10) == (1+d.getMonth()) ) && (parseInt(RegExp.$1, 10) == d.getDate()) && (parseInt(RegExp.$3, 10) == d.getFullYear()))){
                alert('La fecha '+fecha+' no existe. Por favor digite una fecha v�lida.');
                document.getElementById(id).value="";
                return;
            }
        }
    }


    function date_system_valid(id){
        var fecha_actual = new Date()

        var dia = fecha_actual.getDate()
        var mes = fecha_actual.getMonth() + 1
        var anio = fecha_actual.getFullYear()

        if (mes < 10)
            mes = '0' + mes

        if (dia < 10)
            dia = '0' + dia

        var hoy=dia + "/" + mes + "/" + anio;

        inputdate = document.getElementById(id)
        if (inputdate.value == 't' || inputdate.value == 'T'){
            inputdate.value = hoy
            return true
        }


        //alert(hoy);
        if(document.getElementById(id).value != "")
        {
            var vari=document.getElementById(id).value;
            var naci = vari.split('/');
            var fechan=naci[0]+"/"+naci[1]+"/"+naci[2];
            //alert (fechan);

        f1=hoy.split('/');
        f2=fechan.split('/');
//        date1=new Date(f1[2],f1[1]/1,f1[0]);
//        date2=new Date(f2[2],f2[1]/1,f2[0]);

            //var date1  = new Date(hoy);
            //var date2  = new Date(fn);
        date1=f1[2]+f1[1]+f1[0];
        date2=f2[2]+f2[1]+f2[0];    

            //
            if (date1 >= date2)
            {
                return true;

            }
            else
            {
                alert ("La fecha no debe ser mayor que la fecha de hoy");
                document.getElementById(id).value="";
                document.getElementById(id).focus();
                return false;
                //document.getElementById('cal-button-2').focus();
                //document.getElementById('cal-button-2').click();
            }
        }
    }

    function date_greater_than_or_equal_to(id,date_greater, date_less, message){
        /*
            * capturando fecha mayor
            */

        f1=date_greater.split('/');
        date1=f1[2]+f1[1]+f1[0];
        
        //date1=new Date(f1[2],f1[1]/1,f1[0]);

        /*
        * capturando fecha menor
        */

       
        f2=date_less.split('/');
        date2=f2[2]+f2[1]+f2[0];  
        //date2=new Date(f2[2],f2[1]/1,f2[0]);
        /*
        * comparando fechas
        */
        if (date1 >= date2){
            return true;
        } else {
            if (f1=="" || f2 == ""){
                return false
            } else {
                alert (message);
                document.getElementById(id).value="";
                document.getElementById(id).focus();
                return false;
            }
        }
    }

        function upper(obj){
            obj.value = obj.value.toUpperCase()
        }
        function nomayor125(obj){
            if (isNaN(obj.value)){
                alert("La edad debe ser un n�mero");
                obj.value="";
            }
            var edad = parseInt(obj.value)
            if (edad > 125 || edad<1){
                alert("Edad aceptada 1-125 a�os")
                obj.value=""
            }
        }
        
        function evaluaedad(){
            edadp = document.getElementById('edadp').value;
            edad = document.getElementById('edad')
            if (edadp==2 && edad.value>11){
                edad.value = '';
            }
            if (edadp==3 && edad.value>30){
                edad.value = '';
            }
        }

    </script>   
    <?php
}
