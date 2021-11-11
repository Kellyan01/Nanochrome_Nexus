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
    //import du model
    include('./model/user.php');
    //import de la connexion à la bdd
    include('./utils/connect_bdd.php');
    //import de la vue connexion/inscription
    include('vue/accueil.php'); 
    
    /*-----------------------------------------------------
                            Tests Connexion :
    -----------------------------------------------------*/    
    //déclaration js variable message
    echo '<script>let message;</script>';
    //test si les champs sont vides
    if(!isset($_POST['mail_user_connexion']) AND !isset($_POST['password_user_connexion']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Veuillez remplir les champs du formulaire";';
       echo '</script>';
    }
    //test si les champs sont complétés
    if(isset($_POST['mail_user_connexion']) AND isset($_POST['password_user_connexion']))
    {
        //création des variables de connexion
        $mail_connexion = strip_tags($_POST['mail_user_connexion']);
        $password_connexion = strip_tags($_POST['password_user_connexion']);
        //Nouvelle instance de User
        $user = new User("", "", "", "$mail_connexion", "$password_connexion");

        //test si le compte existe (login)
        if($user->showUserMail($bdd))
        {   
            //test si le login et le mot de passe correspondent
            if($user->userConnnected($bdd))
            {
                //génération des super globales 
                $user->generateSuperGlobale($bdd);                
                //test login et mot de passe correct
                if($_SESSION['connected'])
                {
                    //redirection vers board.php
                    header("Location: nexus.php");
                }
            }
            //test mot de passe incorrect
            else
            {
                //redirection vers index.php?passworderror
                header("Location: index.php?loginerror");
            }                  
        }
        //test le compte n'existe pas
        else
        {
            //redirection vers index.php?cptnoexist
            header("Location: index.php?cptnoexist");
        }
    }

    /*-----------------------------------------------------
                            Tests Inscription :
    -----------------------------------------------------*/   
    //test si les champs sont vides
    if(!isset($_POST['nom_user']) and !isset($_POST['prenom_user']) and !isset($_POST['pseudo_user']) and !isset($_POST['mail_user']) and !isset($_POST['password_user']) and !isset($_POST['repeat_password_user']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Veuillez remplir les champs du formulaire";';
       echo '</script>';
    }

    //test si les champs sont complétés
    if(isset($_POST['nom_user']) and isset($_POST['prenom_user']) and isset($_POST['pseudo_user']) and isset($_POST['mail_user']) and isset($_POST['password_user']) and isset($_POST['repeat_password_user'])
    and !empty($_POST['nom_user']) and !empty($_POST['prenom_user']) and !empty($_POST['pseudo_user']) and !empty($_POST['mail_user']) and !empty($_POST['password_user']) and !empty($_POST['repeat_password_user']))
    {
        //création des variables d'inscription'
        $nom_user = strip_tags($_POST['nom_user']);
        $prenom_user = strip_tags($_POST['prenom_user']);
        $pseudo_user = strip_tags($_POST['pseudo_user']);
        $mail_user = strip_tags($_POST['mail_user']);
        $password_user = strip_tags($_POST['password_user']);
        $repeat_password_user = strip_tags($_POST['repeat_password_user']);
        $password_user_hash = password_hash($password_user, PASSWORD_DEFAULT);

        //vérification de la correspondance des mots de passe
        if($password_user == $repeat_password_user){
                //création d'une instance de user
                $user = new User($nom_user, $prenom_user, $pseudo_user, $mail_user, $password_user_hash);
        
                //vérification de l'existence du mail dans la bdd
                if ($user->showUserMail($bdd)) {
                    //redirection vers index.php?mailexist
                    header("Location: index.php?mailexist");
                }
                else {
                    //vérifie si le pseudo existe déjà        
                    if ($user->showUserPseudo($bdd)) {
                        //message d'erreur
                        //script js récupération du paragraphe #message dans une variable "message"
                        echo '<script>message = document.querySelector("#message");';
                        //script js remplacement du message
                        echo 'message.innerHTML = Inscription annulée : ce pseudo existe déjà.";';
                        echo '</script>';
                    }
                    else {
                        $user->createUser($bdd);
        
                        //affichage de la bonne exécution de la requête
                        //script js récupération du paragraphe #message dans une variable "message"
                        echo '<script>message = document.querySelector("#message");';
                        //script js remplacement du message
                        echo 'message.innerHTML = "Inscription de '.$prenom_user.' '.$nom_user.' réalisée avec succès.";';
                        echo '</script>';  
                    }
                }
        }
        else {
            //redirection vers index.php?passworderror
            header("Location: index.php?passworderror");
        }
    }
    /*else{
        //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Champs non valides";';
       echo '</script>';
    
    }*/
    

    /*-----------------------------------------------------
                Gestion des messages d'erreurs :
    -----------------------------------------------------*/
    //test si le compte (login) n'existe pas
    if(isset($_GET['cptnoexist']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>message = document.querySelector("#message");';
         //script js remplacement du message
        echo 'message.innerHTML = "Le compte n\'existe pas !!!";';
        echo '</script>';
    }
    //test si le mot de passe est incorrect
    if(isset($_GET['passworderror']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>message = document.querySelector("#message");';
         //script js remplacement du message
        echo 'message.innerHTML = "Le mot de passe est incorrect !!!";';
        echo '</script>';
    }
    //test si le mot de passe est incorrect
    if(isset($_GET['loginerror']))
    {
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>message = document.querySelector("#message");';
         //script js remplacement du message
        echo 'message.innerHTML = "Le mail et/ou le mot de passe sont incorrects !!!";';
        echo '</script>';
    }
    //test deconnexion
    if(isset($_GET['deconnected']))
    {   
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>message = document.querySelector("#message");';
        //script js remplacement du message
        echo 'message.innerHTML = "Déconnecté !!!";';
        echo '</script>';
    }
    //test existence adresse mai lexistante à la création de compte
    if(isset($_GET['mailexist'])){
        //script js récupération du paragraphe #message dans une variable "message"
        echo '<script>message = document.querySelector("#message");';
        //script js remplacement du message
        echo 'message.innerHTML = Inscription annulée : cette adresse mail existe déjà.";';
        echo '</script>';
    }
?>