<?php
    class User
    {   
        /*-----------------------------------------------------
                            Attributs :
        -----------------------------------------------------*/  
        private $id_user;
        private $name_user;
        private $firstname_user;
        private $pseudo_user;
        private $mail_user;
        private $password_user;
        /*-----------------------------------------------------
                            Constucteur :
        -----------------------------------------------------*/        
        public function __construct($name_user, $firstname_user, $pseudo_user, $mail_user, $password_user)
        {   $this->name_user = $name_user;
            $this->firstname_user = $firstname_user;
            $this->pseudo_user = $pseudo_user;
            $this->mail_user = $mail_user;
            $this->password_user = $password_user;
        }
        /*-----------------------------------------------------
                        Getter and Setter :
        -----------------------------------------------------*/
        //id_user Getter and Setter
        public function getIdUser()
        {
            return $this->id_user;
        }
        public function setIdUser($newIdUser)
        {
            $this->id_user = $newIdUser;
        }
        //name_user Getter and Setter
        public function getNameUser()
        {
            return $this->name_user;
        }
        public function setNameUser($newNameUser)
        {
            $this->name_user = $newNameUser;
        }
        //firstname_user Getter and Setter
        public function getFirstNameUser()
        {
            return $this->firstname_user;
        }
        public function setFirstNameUser($newFirstNameUser)
        {
            $this->firstname_user = $newFirstNameUser;
        }
        //pseudo_user Getter and Setter
        public function getPseudoUser()
        {
            return $this->pseudo_user;
        }
        public function setPseudoUser($newPseudoUser)
        {
            $this->pseudo_user = $newPseudoUser;
        }
        //login_user Getter and Setter
        public function getMailUser()
        {
            return $this->mail_user;
        }
        public function setMailUser($newMailUser)
        {
            $this->mail_user = $newMailUser;
        }
        //mdp_user Getter and Setter
        public function getPasswordUser()
        {
            return $this->password_user;
        }
        public function setPasswordUser($newPasswordUser)
        {
            $this->password_user = $newPasswordUser;
        }
        /*-----------------------------------------------------
                            Fonctions :
        -----------------------------------------------------*/
        //methode chiffrage du mot de passe :
        public function cryptPassword(){
            $passwordToCrypt = $this->getPasswordUser();
            $passwordToCrypt = password_hash($passwordToCrypt, PASSWORD_DEFAULT);
            $this->setPasswordUser($passwordToCrypt);
        }     
        //m??thode ajout d'un utilisateur en bdd
        public function createUser($bdd)
        {   
            //r??cuparation des valeurs de l'objet
            $name_user = $this->getNameUser();
            $firstname_user = $this->getFirstNameUser();
            $pseudo_user = $this->getPseudoUser();
            $mail_user = $this->getMailUser();
            $password_user = $this->getPasswordUser();        
            try
            {   
                //permet d'accepter les accents
                $bdd->exec("set names utf8");
                
                //requ??te ajout d'un utilisateur
                $req = $bdd->prepare('INSERT INTO nx_users(name_user, firstname_user, pseudo_user, mail_user, password_user) 
                VALUES (:name_user, :firstname_user, :pseudo_user, :mail_user, :password_user)');
                //??x??cution de la requ??te SQL
                $req->execute(array(
                'name_user' => $name_user,
                'firstname_user' => $firstname_user,
                'pseudo_user' => $pseudo_user,
                'mail_user' => $mail_user,
                'password_user' => $password_user,
                ));

                //fermeture de la connexion
                $bdd = null;
                $req = null;
            }
            catch(Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                //die('Erreur : '.$e->getMessage());
                die('Erreur : CreateUser'.$e->getMessage());
            }        
        }
        //m??thode pour v??rifier si un utilisateur existe dans la bdd via le mail
        public function showUserMail($bdd)
        {
             //r??cuparation des valeurs de l'objet       
             $mail_user = $this->getMailUser();        
             try
             {  
                 //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //requ??te pour stocker le contenu de toute la table le contenu est stock?? dans le tableau $req_mail
                $req_mail = $bdd->prepare('SELECT * FROM nx_users WHERE mail_user = :mail_user');
                $req_mail->execute(array(
                    'mail_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $mail_user),
                ));
                //boucle pour parcourir et stocker une donn??es
                $donnees = "";
                foreach ($req_mail as $row) {
                    //stockage d'une donn??e
                    $donnees = $row['mail_user'];
                }
                //fermeture de la connexion
                $bdd = null;
                $req_mail = null;
                //v??rification de l'existence du mail dans la bdd
                if ($mail_user == $donnees) {
                    //retourne true si il existe
                    return true;
                }
                else {
                    return false;
                }
             }
             catch(Exception $e)
             {
             //affichage d'une exception en cas d???erreur
             die('Erreur : '.$e->getMessage());
             }        
        }
        //m??thode pour v??rifier si un utilisateur existe dans la bdd via le pseudo
        public function showUserPseudo($bdd)
        {
             //r??cuparation des valeurs de l'objet       
             $pseudo_user = $this->getPseudoUser();        
             try
             {           
                 //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //requ??te pour stocker le contenu de toute la table le contenu est stock?? dans le tableau $req_mail
                $req_pseudo = $bdd->prepare('SELECT * FROM nx_users WHERE pseudo_user = :pseudo_user');
                $req_pseudo->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                ));
                //boucle pour parcourir et stocker une donn??es
                $donnees = "";
                foreach ($req_pseudo as $row) {
                    //stockage d'une donn??e
                    $donnees = $row['pseudo_user'];
                }
                //v??rification de l'existence du pseudo dans la bdd
                if ($pseudo_user == $donnees) {
                    //fermeture de la connexion
                    $bdd = null;
                    $req_pseudo = null;
                    //retourne true si il existe
                    return true;
                }
                else {
                    //fermeture de la connexion
                    $bdd = null;
                    $req_pseudo = null;
                    return false;
                }
             }
             catch(Exception $e)
             {
             //affichage d'une exception en cas d???erreur
             die('Erreur : '.$e->getMessage());
             }        
        }
        //m??thode qui g??n??re les super globales avec les valeurs d'attributs d'un utilisateur en bdd
        public function generateSuperGlobale($bdd)
        {
            //r??cuparation des valeurs de l'objet       
            $mail_user = $this->getMailUser();       
            try
            {                   
               //requ??te pour stocker le contenu de toute la table le contenu est stock?? dans le tableau $reponse
               $reponse = $bdd->prepare('SELECT * FROM nx_users WHERE mail_user = :mail_user LIMIT 1');
               $reponse->execute(array(
                    'mail_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $mail_user),
               ));
               foreach($reponse as $donnees){
                    $id =  $donnees['id_user'];
                    $name =  $donnees['name_user'];
                    $fisrtName =  $donnees['firstname_user'];
                    $pseudo =  $donnees['pseudo_user'];
                    $mail =  $donnees['mail_user'];
                    $password =  $donnees['password_user'];
                    //cr??ation des super globales Session                
                    $_SESSION['idUser'] =  $id;
                    $_SESSION['nameUser'] = $name;
                    $_SESSION['firstNameUser'] =  $fisrtName;
                    $_SESSION['pseudoUser'] =  $pseudo;
                    $_SESSION['mailUser'] = $mail;
                    $_SESSION['passwordUser'] = $password;
                    $_SESSION['connected'] = true;
               }
               
               //fermeture de la connexion
               $bdd = null;
               $reponse = null;
            }
            catch(Exception $e)
            {
            //affichage d'une exception en cas d???erreur
            die('Erreur : '.$e->getMessage());
            }   
        }
         
        //m??thode pour tester la connexion d'un utilisateur
        public function userConnnected($bdd)
        {
             //r??cuparation des valeurs de l'objet       
             $mail_user = $this->getMailUser();        
             $password_user = $this->getPasswordUser();
             try
             {                   
                //requ??te pour stocker le contenu de toute la table le contenu est stock?? dans le tableau $reponse
                $reponse = $bdd->prepare('SELECT * FROM nx_users WHERE mail_user = :mail_user LIMIT 1');
                $reponse->execute(array(
                    'mail_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $mail_user),
                ));
                //boucle pour parcourir et stocker une donn??es
                $mail = "";
                $pass = "";
                foreach ($reponse as $row) {
                    //stockage d'une donn??e
                    $mail = $row['mail_user'];
                    $pass = $row['password_user'];
                }

                //return $donnees['password_user'];
                if($mail_user == $mail AND password_verify($password_user, $pass))
                {
                    //fermeture de la connexion
                    $bdd = null;
                    $reponse = null;

                    //retourne true si il existe (login en mdp)
                    return true;
                }
                else
                {
                    //fermeture de la connexion
                    $bdd = null;
                    $reponse = null;

                    return false;
                }       
             }
             catch(Exception $e)
             {
             //affichage d'une exception en cas d???erreur
             die('Erreur : '.$e->getMessage());
             }        
        }
         //m??thode g??n??ration d'un token de connexion
         public function createToken($bdd)
         {  
            //r??cuparation des valeurs de l'objet
            $mail_user = $this->getMailUser();
            $password_user = $this->getPasswordUser();
            //chaine token en clair
            $token = "$mail_user$password_user";
            //hashage du token
            $token = password_hash($token, PASSWORD_DEFAULT);
            //retourne le token 
            return $token;    
         }
         //m??thode mise ?? jour des informations d'un utilisateur, sauf le mot de passe
         public function updateUser($bdd)
         {
            //r??cup??ration de l'id de l'utilisateur
            $id_user = $_SESSION['idUser'];
            //r??cuparation des nouvelles valeurs de l'objet       
            $name_user = $this->getNameUser();
            $firstname_user = $this->getFirstNameUser();
            $pseudo_user = $this->getPseudoUser();
            $mail_user = $this->getMailUser();
            try 
            {
                //requ??te modification utilisateur
                $req = $bdd->prepare('UPDATE nx_users
                    SET name_user = :name_user, firstname_user = :firstname_user, pseudo_user = :pseudo_user, mail_user = :mail_user
                    WHERE id_user = :id_user'
                );
                $req->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                    'name_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $name_user),
                    'firstname_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $firstname_user),
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                    'mail_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $mail_user),
               ));
               //fermeture de la connexion
               $bdd = null;
               $req = null;
            }
            catch(Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }        
         }
         //m??thode mise ?? jour du mot de passe d'un utilisateur
         public function updatePasswordUser($bdd)
         {
            //r??cup??ration de l'id de l'utilisateur
            $id_user = $_SESSION['idUser'];
            //r??cuparation des nouvelles valeurs de l'objet       
            $password_user = $this->getPasswordUser();
            try 
            {
                //requ??te modification password
                $req = $bdd->prepare('UPDATE nx_users
                    SET password_user = :password_user
                    WHERE id_user = :id_user'
                );
                $req->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                    'password_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $password_user),
               ));
               //fermeture de la connexion
               $bdd = null;
               $req = null;
            }
            catch(Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }       
         }
         //m??thode pour supprimer un compte utilisateur
         public function deleteUser($bdd)
         {
            //r??cup??ration de l'id de l'utilisateur
            $id_user = $_SESSION['idUser'];
            try 
            {
                //requ??te modification password
                $req = $bdd->prepare('DELETE FROM nx_users WHERE id_user = :id_user');
                $req->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
               ));
               //fermeture de la connexion
               $bdd = null;
               $req = null;
            }
            catch(Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }     
         }
    }
?>