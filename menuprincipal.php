<?php
$db         = new MySql();
$rol        = 4;
$resultter  = "";
if ($rol == 1 && $rol2 == 2) {
    // digitador y analista
    $result = $db->consulta("select * 
			from menup as a
			where tbsistema_id=1 and
				  estado='A' and
				(tbrol_id=0 or
				readonly='1' or
				readwrite='1')
			order by orden");
} elseif ($rol == 1 && $rol2 == 0) {
    // digitador
    $result = $db->consulta("select * 
			from menup as a
			where tbsistema_id=1 and
				  estado='A' and
				(tbrol_id=0 or
				tbrol_id='$rol') and 
				readwrite=1
			order by orden");
} elseif ($rol == 2 && $rol2 == 0) {
    // Analista
    $result = $db->consulta("select * 
			from menup as a
			where tbsistema_id=1 and
				  estado='A' and
				(tbrol_id=0 or
				tbrol_id='$rol') and
				readonly=1
			order by orden");
} elseif ($rol == 3) {
    // Administrador istituci�n
    $result = $db->consulta("select * 
                        from menup as a 
                        where tbsistema_id=1 and
                                    estado='A' and
                                    tbrol_id<='$rol'
                        order by orden");
} elseif ($rol == 4) {
    // Administrador nacional
    $result = $db->consulta("select * 
                        from menup as a 
                        where tbsistema_id=1 and
                                    estado='A'
                        order by orden");
}
?>
<span class="preload1"></span>
<span class="preload2"></span>
<ul id="nav">
    <?php
// desplegar las opciones de primer nivel 1er.
// id_sistema = id que le corresponde como sistema de informaci�n
// 
//$result = $db->consulta("select * from menup as a where id_sistema=1 and estado='A' order by orden");
    while ($row = $db->fetch_array($result)) {
        $id_menu    = $row['id_mp'];
        $menu       = htmlentities($row['menup']);
        $url        = $row['url_mp'];
        ?>
        <li class="top"><a href="<?php echo $url; ?>" id="" class="top_link">
                <span <?php
                if ($url == "") {
                    echo "class='down'";
                }
                ?>> <?php echo $menu; ?></span></a>
                <?php
                //despelgar las opciones de nivel secundario si las ubiere 2do

                if ($rol == 1 && $rol2 == 2) {
                    // digitador y analista
                    $resultsec = $db->consulta("select * 
                                        from menus as a 
                                        where id_mp=$id_menu and 
                                                estado='A' and
                                                (readonly='1' or
                                                readwrite='1')
                                        order by ordens");
                } elseif ($rol == 1 && $rol2 == 0) {
                    // digitador
                    $resultsec = $db->consulta("select * 
                                        from menus as a 
                                        where id_mp=$id_menu and 
                                                estado='A' and
                                                readwrite='1'
                                        order by ordens");
                } elseif ($rol == 2 && $rol2 == 0) {
                    // Analista
                    $resultsec = $db->consulta("select * 
                                        from menus as a 
                                        where id_mp=$id_menu and 
                                                estado='A' and
                                                readonly='1'
                                        order by ordens");
                } elseif ($rol == 3) {
                    // Administrador istituci�n
                    $resultsec = $db->consulta("select * 
                                        from menus as a 
                                        where id_mp=$id_menu and 
                                                estado='A'                                        
                                        order by ordens");
                } elseif ($rol == 4) {
                    // Administrador nacional
                    $resultsec = $db->consulta("select * 
                                        from menus as a 
                                        where id_mp=$id_menu and 
                                                estado='A'                                        
                                        order by ordens");
                }
                if ($db->num_rows($resultsec) > 0) {
                    ?>
                <ul class="sub">
                    <?php
                    while ($resultadoss = $db->fetch_array($resultsec)) {
                        $id_menus = $resultadoss['id_ms'];
                        $menus = htmlentities($resultadoss['menus']);
                        $urls = $resultadoss['urls'];
                        ?>
                        <li><a href="<?php echo $urls; ?>" <?php
                               if ($urls == "") {
                                   echo "class='fly'";
                               }
                               ?>><?php echo $menus; ?></a>
                               <?php
                               // menu de tercer nivel 3er
                               $resultter = $db->consulta("select * from menut as a where id_ms=$id_menus and estado='A'");
                               if ($db->num_rows($resultter) > 0) {
                               ?>
                            <ul>
                                <?php
                                while ($resultadost = $db->fetch_array($resultter)) {
                                    $id_menut = $resultadost['id_mt'];
                                    $menut = htmlentities($resultadost['menut']);
                                    $urlt = $resultadost['url'];
                                    ?>
                                    <li><a href="<?php echo $urlt; ?>" <?php
                                           if ($urlt == "") {
                                               echo "class='fly'";
                                           }
                                           ?>><?php echo $menut; ?></a>
                                            <?
                                            // menu de cuarto nivel 4to
                                            $resultcua = $db->consulta("select * from menuc as a where id_mt=$id_menut and estado='A'");
                                            if ($db->num_rows($resultcua) > 0) {
                                            ?>
                                        <ul>
                                            <?php
                                            while ($resultadosc = $db->fetch_array($resultcua)) {
                                                $id_menuc = $resultadosc['id_mc'];
                                                $menuc = htmlentities($resultadosc['menuc']);
                                                $urlc = $resultadosc['url'];
                                                ?>
                                                <li class=""><a href="<?php echo $urlc; ?>"><?php echo $menuc; ?></a></li>
                                            <?
                                            }
                                            ?>
                                            </ul>
                                        <?php
                                    }
                                    ?>
                                    </li>
                                <?php
                            }
                            ?>
                            </ul>
                        <?php
                    }
                    ?>
                    </li>
                <?php
            }
            ?>
            </ul>
        <?php
    }
    ?>
    </li>
    <?php 
    }
    ?>
</ul>