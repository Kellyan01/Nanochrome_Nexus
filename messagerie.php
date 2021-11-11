<?php 
    /*-----------------------------------------------------
                        CONTROLLER
    -----------------------------------------------------*/
    /*-----------------------------------------------------
                        Session :
    -----------------------------------------------------*/
    //Démarage de la session utilisateur
    session_start();

    /*-----------------------------------------------------
                        Imports :
    -----------------------------------------------------*/        
    //si l'utilisateur est connecté (connexion, ajouter categorie, ajouter tâche, deconnexion)
    if(isset($_SESSION['connected']))
    {
        //import des models
        include('./model/user.php');
        include('./model/message.php');
        //import de la connexion à la bdd
        include('./utils/connect_bdd.php');
        //import de la vue Tableau de Bord Utilisateur
        include('vue/mailbox.php'); 
    }
    //si non connecté, redirection vers l'accueil
    else
    {
        //redirection vers la page Acceuil
        header("Location: index.php");
    }

    /*-----------------------------------------------------
                    Afficher Message Reçu
    -----------------------------------------------------*/ 
    //récupère l'id de l'utilisateur
    $id_user = $_SESSION['idUser'];
    //tableau des messages reçus
    $message_list_received = [];
    try
    {
        //permet d'accepter les accents
        $bdd->exec("set names utf8");

        //vérifie la liste des id des messages reçus
        $reponse = $bdd->prepare('SELECT * FROM reception_box WHERE id_user = :id_user');
        $reponse->execute(array(
            'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
        ));
        //boucle pour parcourir et stocker les données
        $donnees = [];
        foreach ($reponse as $row) {
            //stockage des données
            $donnees[] = $row['id_msg_received'];
        }
        //Vérifie s'il y a des messages
        if(!empty($donnees)){
            //boucle pour parcourir les messages et les stocker
            foreach ($donnees as $id_msg){
                //vérifie la liste des messages
                $reponse = $bdd->prepare('SELECT * FROM message_received WHERE id_msg_received = :id_msg');
                $reponse->execute(array(
                    'id_msg' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_msg),
                ));
                //boucle pour parcourir et stocker les message
                foreach($reponse as $row){
                    //création d'une nouvelle instance de message
                    $new_message = new Message($row['auteur'],"",$row['subject_msg_received'],$row['body_msg_received'],"");
                    //stockage du message
                    $message_list_received[] = $new_message;
                }
            }

            //boucle pour afficher les messages
            //script js déclaration variables message et reception
            echo '<script>let message=""; let reception;';
            //script js récupération de la div #reception dans une variable "reception"
            echo 'reception = document.getElementById("reception");';
            foreach($message_list_received as $row){
                //recupération des valeurs du message
                $auteur = $row->getAuteurMessage();
                $sujet = $row->getSujetMessage();            
                $corps = $row->getCorpsMessage();
                //script js affichage des messages en commençant par le dernier
                echo 'reception.innerHTML = "<div class=\"message\"><div class=\"msgAuteur\"><p>De : '.$auteur.'</p></div><div class=\"msgSujet\"><p>Objet : '.$sujet.'</p></div><div class=\"msgCorps\"><p>Message : '.$corps.'</p></div></div>"+message;';
                echo 'message = reception.innerHTML;';
            }
            //réinitialisation de message
            echo '</script>';
        }
        else
        {
            //script js récupération de la div #reception dans une variable "reception"
            echo '<script>let reception = document.getElementById("reception");';
            //script js remplacement du message
            echo 'reception.innerHTML = "<div class=\"message\"><p>Aucun message reçu</p></div>";';
            echo '</script>';
        }
    }
    catch (Exception $e)
    {
        //affichage d'une exception en cas d’erreur
        die('Erreur : '.$e->getMessage());
    }

     /*-----------------------------------------------------
                    Afficher Message Envoyé
    -----------------------------------------------------*/
    //tableau des messages envoyés
    $message_list_send = []; 
    try
    {
        //permet d'accepter les accents
        $bdd->exec("set names utf8");

        //vérifie la liste des messages envoyé
        $reponse = $bdd->prepare('SELECT * FROM message_send WHERE id_user = :id_user');
        $reponse->execute(array(
            'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
        ));
        //boucle pour parcourir et stocker les données
        $donnees = [];
        foreach ($reponse as $row) {
            //stockage des données
            $donnees[] = $row;
        }
        //Vérifie s'il y a des messages
        if(!empty($donnees)){
            //boucle pour parcourir et stocker les message
            foreach($donnees as $row){
                //création d'une nouvelle instance de message
                $new_message = new Message("",$row['destinataires_list'],$row['subject_msg_send'],$row['body_msg_send'],"");
                //stockage du message
                $message_list_send[] = $new_message;
            }

            //boucle pour afficher les messages
            //script js déclaration variables message et reception
            echo '<script>message="";';
            //script js récupération de la div #reception dans une variable "reception"
            echo 'let envoie = document.getElementById("envoie");';
            foreach($message_list_send as $row){
                //recupération des valeurs du message
                $destinataire = $row->getDestinataireMessage();
                $sujet = $row->getSujetMessage();            
                $corps = $row->getCorpsMessage();
                //script js affichage des messages en commençant par le dernier
                echo 'envoie.innerHTML = "<div class=\"message\"><div class=\"msgDestinataire\"><p>A : '.$destinataire.'</p></div><div class=\"msgSujet\"><p>Objet : '.$sujet.'</p></div><div class=\"msgCorps\"><p>Message : '.$corps.'</p></div></div>"+message;';
                echo 'message = envoie.innerHTML;';
            }
            //réinitialisation de message
            echo '</script>';
        }
        else
        {
            //script js récupération de la div #reception dans une variable "reception"
            echo '<script>let envoie = document.getElementById("envoie");';
            //script js remplacement du message
            echo 'envoie.innerHTML = "<div class=\"message\"><p>Aucun message reçu</p></div>";';
            echo '</script>';
        }
    }
    catch (Exception $e)
    {
        //affichage d'une exception en cas d’erreur
        die('Erreur : '.$e->getMessage());
    }
    

    /*-----------------------------------------------------
                    Fonction Envoyer Message :
    -----------------------------------------------------*/
    //test si les champs sont vides
    if(!isset($_POST['destinataire']) and !isset($_POST['sujet']) and !isset($_POST['corps']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let messageOne = document.querySelector("#message");';
       //script js remplacement du message
       echo 'messageOne.innerHTML = "Veuillez remplir les champs du message";';
       echo '</script>';
    }

    //test si les champs sont complétés
    if(isset($_POST['destinataire']) and isset($_POST['sujet']) and isset($_POST['corps'])
    and !empty($_POST['destinataire']) and !empty($_POST['sujet']) and !empty($_POST['corps']))
    {
        //création des variables du message'
        $auteur = $_SESSION['pseudoUser'];
        $destinataire = strip_tags($_POST['destinataire']);
        $sujet = strip_tags($_POST['sujet']);
        $corps = strip_tags($_POST['corps']);

            try {
                //permet d'accepter les accents
                $bdd->exec("set names utf8");
        
                //vérifie si le destinataire existe
                $req = $bdd->prepare('SELECT * FROM users WHERE pseudo_user = :destinataire');
                $req->execute(array(
                    'destinataire' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $destinataire),
                ));
        
                //boucle pour parcourir et stocker une données
                $donnees = "";
                foreach ($req as $row) {
                    //stockage d'une donnée
                    $donnees = $row['pseudo_user'];
                }
        
                //vérification de l'existence du pseudo dans la bdd
                if (empty($donnees)) {
                    //redirection vers messagerie.php?pseudonoexist
                    header("Location: messagerie.php?pseudonoexist");
                    //fermeture de la connexion
                    $bdd = null;
                    $req = null;
                }
                else {
                    //création d'une nouvelle instance de message
                    $message = new Message("$auteur","$destinataire","$sujet","$corps","");
                    //inscription du message dans la bdd
                    $message->createMessage($bdd);
                    //redirection vers messagerie?messageenvoye.php
                    header("Location: messagerie.php?messageenvoye");
                }
            }
            catch (Exception $e)
            {
                //affichage d'une exception en cas d’erreur
                die('Erreur : '.$e->getMessage());
            }
    }
    else
    {
        //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let messageThree = document.querySelector("#message");';
       //script js remplacement du message
       echo 'messageThree.innerHTML = "Champs non valides";';
       echo '</script>';
    }
    

    /*-----------------------------------------------------
                Gestion des messages d'erreurs :
    -----------------------------------------------------*/
    //test si le compte (login) n'existe pas
    if(isset($_GET['messagerie.php?pseudonoexist']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>let message = document.querySelector("#message");';
         //script js remplacement du message
        echo 'message.innerHTML = "Le pseudo de votre destinataire n\'existe pas !!!";';
        echo '</script>';
    }
    /*-----------------------------------------------------
                Gestion des messages de confirmation :
    -----------------------------------------------------*/
    //test si le compte (login) n'existe pas
    if(isset($_GET['messagerie.php?messageenvoye']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>let messageTwo = document.querySelector("#message");';
        //script js remplacement du message
        echo 'messageTwo.innerHTML = "Votre message a été envoyé";';
        echo '</script>';
    }    
?>