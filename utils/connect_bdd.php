<?php 
          //connexion à la bdd           
          $bdd = new PDO('mysql:host=localhost;dbname=nanochrome_nexus', 'root','root', 
          array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));  
?>