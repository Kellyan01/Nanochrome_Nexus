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
        //import du model
        include('./model/user.php');
        //import de la connexion à la bdd
        include('./utils/connect_bdd.php');
        //import de la vue Tableau de Bord Utilisateur
        include('./vue/supression.php'); 
    }
    //si non connecté, redirection vers l'accueil
    else
    {
        //redirection vers la page Acceuil
        header("Location: index.php");
    }

    
    /*-----------------------------------------------------
                Suppression de Compte :
    -----------------------------------------------------*/
    //test si les champs sont vides
    if(!isset($_POST['passwordDelete']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Veuillez compléter votre mot de passe";';
       echo '</script>';
    }
    //test si les champs sont complétés
    if(isset($_POST['passwordDelete']) AND !empty($_POST['passwordDelete']))
    {
        //récupère le password utilisateur et password entré
        $password_confirm = strip_tags($_POST['passwordDelete']);
        $password_user = $_SESSION['passwordUser'];
        //test si le password entré correspond au password de l'utilisateur
        if(password_verify($password_confirm, $password_user)){
            //Nouvelle instance de User
            $user = new User("", "", "", "", "");
            //appel à la suppression
            $user->deleteUser($bdd);
            //destruction de la session
            session_destroy();
            //redirection vers l'index.php
            header("Location: index.php");

        }
        else{
            //redirection vers delete.php?passworderror
            header("Location: delete.php?passworderror");
        }
    }
    else{
         //redirection vers delete.php?champsincomplet
         echo '<script>let message = document.querySelector("#message");';
        //script js remplacement du message
        echo 'message.innerHTML = "Veuillez compléter votre mot de passe";';
        echo '</script>';
    }

    /*-----------------------------------------------------
                Gestion des messages d'erreurs :
    -----------------------------------------------------*/
    //test si le mot de passe est incorrect
    if(isset($_GET['passworderror']))
    {   
        //script js
        echo '<script>';
         //script js remplacement du message
        echo 'message.innerHTML = "Mot de passe invalide !!!";';
        echo '</script>';
    }
?>