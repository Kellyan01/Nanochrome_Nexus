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
        include('vue/board.php'); 
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
    //récupère le profil utilisateur
    $nom = $_SESSION['nameUser'];
    $prenom = $_SESSION['firstNameUser'];
    $pseudo = $_SESSION['pseudoUser'];
    $mail = $_SESSION['mailUser'];
    $password = $_SESSION['passwordUser'];
    //affichage du profil utilisateur
    echo "<script>";
    echo "let nom = document.getElementById('nom');";
    echo "let prenom = document.getElementById('prenom');";
    echo "let pseudo = document.getElementById('pseudo');";
    echo "let mail = document.getElementById('mail');";
    echo "nom.innerHTML = '<span>Nom :</span> $nom';";
    echo "prenom.innerHTML = '<span>Prénom :</span> $prenom';";
    echo "pseudo.innerHTML = '<span>Pseudo :</span> $pseudo';";
    echo "mail.innerHTML = '<span>Mail :</span> $mail';";
    echo "</script>";

    /*-----------------------------------------------------
                        Modifier Profil :
    -----------------------------------------------------*/ 
    //préremplit le formulaire de mofication avec le profil utilisateur actuel
    echo "<script>";
    echo "let modif_nom = document.getElementById('modif_nom');";
    echo "let modif_prenom = document.getElementById('modif_prenom');";
    echo "let modif_pseudo = document.getElementById('modif_pseudo');";
    echo "let modif_mail = document.getElementById('modif_mail');";
    echo "modif_nom.setAttribute('value', '$nom');";
    echo "modif_prenom.setAttribute('value', '$prenom');";
    echo "modif_pseudo.setAttribute('value', '$pseudo');";
    echo "modif_mail.setAttribute('value', '$mail');";
    echo "</script>";

    //test si les champs sont vides
    if(!isset($_POST['modif_nom']) AND !isset($_POST['modif_prenom']) AND !isset($_POST['modif_pseudo']) AND !isset($_POST['modif_mail']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Veuillez remplir les champs du formulaire";';
       echo '</script>';
    }
    //test si les champs sont complétés
    if(isset($_POST['modif_nom']) AND isset($_POST['modif_prenom']) AND isset($_POST['modif_pseudo']) AND isset($_POST['modif_mail'])
    and !empty($_POST['modif_nom']) AND !empty($_POST['modif_prenom']) AND !empty($_POST['modif_pseudo']) AND !empty($_POST['modif_mail']))
    {
        //création des variables de modification
        $modif_nom = strip_tags($_POST['modif_nom']);
        $modif_prenom = strip_tags($_POST['modif_prenom']);
        $modif_pseudo = strip_tags($_POST['modif_pseudo']);
        $modif_mail = strip_tags($_POST['modif_mail']);
        //Nouvelle instance de User
        $user = new User("$modif_nom", "$modif_prenom", "$modif_pseudo", "$modif_mail", "");

        //test si le pseudo existe déjà et n'est pas celui de l'utilisateur
        if($user->showUserPseudo($bdd) && $modif_pseudo != $pseudo)
        {   
            //redirection vers nexus.php?pseudoexist
            header("Location: nexus.php?pseudoexist");
        }
        //test si le mail existe déjà et n'est pas celui de l'utilisateur
        else if($user->showUser($bdd) && $modif_mail != $mail)
        {
            //redirection vers nexus.php?mailexist
            header("Location: nexus.php?mailexist");
        }
        else
        {
            //exécution de la modification
            $user->updateUser($bdd);
            //modification de la superglobal $_SESSION
            $_SESSION['nameUser'] = $modif_nom;
            $_SESSION['firstNameUser'] =  $modif_prenom;
            $_SESSION['pseudoUser'] =  $modif_pseudo;
            $_SESSION['mailUser'] = $modif_mail;
            //message de validation
            //script js
            echo '<script>';
            //script js remplacement du message
            echo 'message.innerHTML = "Votre profil a été modifié avec succès !!!";';
            echo '</script>';
        }
    }

    /*-----------------------------------------------------
                Modification de Mot de Passe :
    -----------------------------------------------------*/
    //test si les champs sont vides
    if(!isset($_POST['old_password']) AND !isset($_POST['new_password']) AND !isset($_POST['repeat_password']))
    {   
       //script js récupération du paragraphe #message dans une variable "message"
       echo '<script>let message = document.querySelector("#message");';
       //script js remplacement du message
       echo 'message.innerHTML = "Veuillez remplir les champs du formulaire";';
       echo '</script>';
    }
    //test si les champs sont complétés
    if(isset($_POST['old_password']) AND isset($_POST['new_password']) AND isset($_POST['repeat_password'])
    and !empty($_POST['old_password']) AND !empty($_POST['new_password']) AND !empty($_POST['repeat_password']))
    {
        //création des variables de modification
        $old_password = strip_tags($_POST['old_password']);
        $new_password = strip_tags($_POST['new_password']);
        $repeat_password = strip_tags($_POST['repeat_password']);

        //test si l'ancien password correspond à celui du compte
        if(password_verify($old_password, $password)){
            //test si le nouveau password correspond au repeat password
            if($new_password == $repeat_password){
                //Nouvelle instance de User
                $user = new User("", "", "", "", "$new_password");

                //hashage du mot de passe
                $user->cryptPassword();

                //exécution de la modification
                $user->updatePasswordUser($bdd);
                //modification de la superglobal $_SESSION
                $_SESSION['passwordUser'] = $user->getPasswordUser();
                //message de validation
                //script js
                echo '<script>';
                //script js remplacement du message
                echo 'message.innerHTML = "Votre mot de passe a été modifié avec succès !!!";';
                echo '</script>';
            }
            else
        {
            //redirection vers nexus.php?passwordnotsimilar
            header("Location: nexus.php?passwordnotsimilar");
        }
        }
        else
        {
            //redirection vers nexus.php?passworderror
            header("Location: nexus.php?passworderror");
        }
    }

    /*-----------------------------------------------------
                Gestion des messages d'erreurs :
    -----------------------------------------------------*/
    //test si le compte (login) n'existe pas
    if(isset($_GET['mailexist']))
    {   
        //script js
        echo '<script>';
         //script js remplacement du message
        echo 'message.innerHTML = "Cette adresse mail existe déjà !!!";';
        echo '</script>';
    }
    //test si le peuso existe déjà
    if(isset($_GET['pseudoexist']))
    {   
        //script js
        echo '<script>';
         //script js remplacement du message
        echo 'message.innerHTML = "Ce pseudo existe déjà !!!";';
        echo '</script>';
    }
    //test si le mot de passe est incorrect
    if(isset($_GET['passworderror']))
    {   
        //script js
        echo '<script>';
         //script js remplacement du message
        echo 'message.innerHTML = "Mot de passe invalide !!!";';
        echo '</script>';
    }
    //test si le nouveau mot de passe correspond à sa répétition
    if(isset($_GET['passwordnotsimilar']))
    {   
        //script js
        echo '<script>';
         //script js remplacement du message
        echo 'message.innerHTML = "Le nouveau mot de passe ne correspond pas à sa répétition !!!";';
        echo '</script>';
    }
?>