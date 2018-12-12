<?php
$base = __DIR__;
 require_once("$base/model/autor.class.php");
 $autor=new Autor();
 $res=$autor->getAll();
//  $res=$autor->get(6550);
 print_r($res);
 if ($res->correcta) {
    foreach ($res->dades as $row){
        echo $row['id_aut']."-".$row['nom_aut']." ".$row["fk_nacionalitat"]."<br>";
        // echo $res->dades->id_aut."-".$res->dades->nom_aut." ".$res->dades->fk_nacionalitat."<br>";
    }
 } else {
     echo $res->missatge;
 }

 $autor->update(array("id_aut" => 6550, "nom_aut"=>"Campaner, Tomeu","fk_nacionalitat"=>"NORTEAMERICANO"));   //produira un error
 if (!$res->correcta) {
    echo "Error insertant";  // Error per l'usuari
    error_log($res->missatge,3,"$base/log/errors.log");  // Error per noltros
 }   

