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
                        Vue :
    -----------------------------------------------------*/
    //récupération de la div #message dans message
    echo "<script>";
    echo 'let message = document.querySelector("#message");';
    echo "</script>";
    /*-----------------------------------------------------
                    Afficher Message Reçu
    -----------------------------------------------------*/ 
    //récupère du pseudo de l'utilisateur
    $pseudo_user = $_SESSION['pseudoUser'];
    //création d'une instance de Message
    $message_received = new Message("","$pseudo_user","","");

    //appel de la méthode pour la récupération des messages reçus et stockage de la liste
    $message_list_received = $message_received->messageReceived($bdd);
    
    //vérifie si la liste des messages est vide
    if(empty($message_list_received)){
        //affiche qu'il n'y a aucun message reçu
        //script js récupération de la div #reception dans une variable "reception"
        echo '<script>let reception = document.getElementById("reception");';
        //script js remplacement du message
        echo 'reception.innerHTML = "<div class=\"message\"><p>Aucun message reçu</p></div>";';
        echo '</script>';
    }
    //affiche les message reçus
    else {
        //script js déclaration variables message et reception
        echo '<script>let messageReceived=""; let reception;';
        //script js récupération de la div #reception dans une variable "reception"
        echo 'reception = document.getElementById("reception");';
        //boucle pour afficher les messages
        foreach($message_list_received as $row){
            //recupération des valeurs du message
            $auteur = $row->getAuteurMessage();
            $sujet = $row->getSujetMessage();            
            $corps = $row->getCorpsMessage();
            //script js affichage des messages en commençant par le dernier
            echo 'reception.innerHTML = "<div class=\"message\"><div class=\"msgAuteur\"><p>De : '.$auteur.'</p></div><div class=\"msgSujet\"><p>Objet : '.$sujet.'</p></div><div class=\"msgCorps\"><p>Message : '.$corps.'</p></div></div>"+messageReceived;';
            echo 'messageReceived = reception.innerHTML;';
        }
        //réinitialisation de message
        echo '</script>';
    }

     /*-----------------------------------------------------
                    Afficher Message Envoyé
    -----------------------------------------------------*/
    //création d'une instance de Message
    $message_send = new Message("$pseudo_user","","","");

    //appel de la méthode pour la récupération des messages reçus et stockage de la liste
    $message_list_send = $message_send->messageSend($bdd);
    
    //vérifie si la liste des messages est vide
    if(empty($message_list_send)){
        //affiche qu'il n'y a aucun message reçu
        //script js récupération de la div #reception dans une variable "reception"
        echo '<script>let envoie = document.getElementById("envoie");';
        //script js remplacement du message
        echo 'envoie.innerHTML = "<div class=\"message\"><p>Aucun message envoyé</p></div>";';
        echo '</script>';
    }
    //affiche les message reçus
    else {
        //script js déclaration variables message et reception
        echo '<script>let messageSend=""; let envoie;';
        //script js récupération de la div #reception dans une variable "reception"
        echo 'envoie = document.getElementById("envoie");';
        //boucle pour afficher les messages
        foreach($message_list_send as $row){
            //recupération des valeurs du message
            $destinataire = $row->getDestinataireMessage();
            $sujet = $row->getSujetMessage();            
            $corps = $row->getCorpsMessage();
            //script js affichage des messages en commençant par le dernier
            echo 'envoie.innerHTML = "<div class=\"message\"><div class=\"msgDestinataire\"><p>Pour : '.$destinataire.'</p></div><div class=\"msgSujet\"><p>Objet : '.$sujet.'</p></div><div class=\"msgCorps\"><p>Message : '.$corps.'</p></div></div>"+messageSend;';
            echo 'messageSend = envoie.innerHTML;';
        }
        //réinitialisation de message
        echo '</script>';
    }


    /*-----------------------------------------------------
                    Fonction Envoyer Message :
    -----------------------------------------------------*/
    //test si les champs sont vides
    if(!isset($_POST['destinataire']) and !isset($_POST['sujet']) and !isset($_POST['corps']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let messageMail = document.querySelector("#messageMail");';
       //script js remplacement du message
       echo 'messageMail.innerHTML = "Veuillez remplir les champs du message";';
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

        //création d'une instance de notre message
        $message = new Message($auteur, $destinataire, $sujet, $corps);

        //vérification de l'existence du destinataire
        if($message->showDestinataire($bdd)){
            //inscription du message dans la bdd
            $message->createMessage($bdd);
            //affichage de la bonne exécution de la requête
            //script js remplacement du message
            echo '<script>';
            echo 'message.innerHTML = "Votre message a été envoyé avec succès.";';
            echo "</script>";
        }
        else {
            //message d'erreur
            //script js remplacement du message
            echo "<script>";
            echo 'message.innerHTML = "Le pseudo de votre destinataire existe pas !!!";';
            echo "</script>";
        }
    }

    /*-----------------------------------------------------
                Gestion des messages d'erreurs :
    -----------------------------------------------------*/
    
    /*-----------------------------------------------------
                Gestion des messages de confirmation :
    -----------------------------------------------------*/
    //test si le compte (login) n'existe pas
    /*if(isset($_GET['messagerie.php?messageenvoye']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>let messageTwo = document.querySelector("#message");';
        //script js remplacement du message
        echo 'messageTwo.innerHTML = "Votre message a été envoyé";';
        echo '</script>';
    }*/    
?>