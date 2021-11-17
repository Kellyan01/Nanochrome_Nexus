<?php
    class Message
    {   
        /*-----------------------------------------------------
                            Attributs :
        -----------------------------------------------------*/  
        private $id_message;
        private $auteur_message;
        private $destinataire_message;
        private $sujet_message;
        private $corps_message;
        /*-----------------------------------------------------
                            Constucteur :
        -----------------------------------------------------*/        
        public function __construct($auteur_message, $destinataire_message, $sujet_message, $corps_message)
        {   $this->auteur_message = $auteur_message;
            $this->destinataire_message = $destinataire_message;
            $this->sujet_message = $sujet_message;
            $this->corps_message = $corps_message;
        }
        /*-----------------------------------------------------
                        Getter and Setter :
        -----------------------------------------------------*/
        //id_message Getter and Setter
        public function getIdMessage()
        {
            return $this->id_message;
        }
        public function setIdMessage($newIdMessage)
        {
            $this->id_message = $newIdMessage;
        }
        //auteur_message Getter and Setter
        public function getAuteurMessage()
        {
            return $this->auteur_message;
        }
        public function setAuteurMessage($newAuteurMessage)
        {
            $this->auteur_message = $newAuteurMessage;
        }
        //destinataire_message Getter and Setter
        public function getDestinataireMessage()
        {
            return $this->destinataire_message;
        }
        public function setDestinataireMessage($newDestinataireMessage)
        {
            $this->destinataire_message = $newDestinataireMessage;
        }
        //sujet_message Getter and Setter
        public function getSujetMessage()
        {
            return $this->sujet_message;
        }
        public function setSujetMessage($newSujetMessage)
        {
            $this->sujet_message = $newSujetMessage;
        }
        //corps_message Getter and Setter
        public function getCorpsMessage()
        {
            return $this->corps_message;
        }
        public function setCorpsMessage($newCorpsMessage)
        {
            $this->corps_message = $newCorpsMessage;
        }
        /*-----------------------------------------------------
                            Fonctions :
        -----------------------------------------------------*/
        //méthode pour vérifier si un destinataire existe
        public function showDestinataire($bdd)
        {
            //récupération du destinataire dans l'objet
            $destinataire = $this->getDestinataireMessage();
            try
            {  
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //requête pour stocker le contenu de toute la table le contenu est stocké dans le tableau $req_destinataire
                $req_destinataire = $bdd->prepare('SELECT * FROM nx_users WHERE pseudo_user = :pseudo_user');
                $req_destinataire->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $destinataire),
                ));
                //boucle pour parcourir et stocker une données
                $donnees = "";
                foreach ($req_destinataire as $row) {
                    //stockage d'une donnée
                    $donnees = $row['pseudo_user'];
                }
                //fermeture de la connexion
                $bdd = null;
                $req_destinataire = null;
                //vérification de l'existence du pseudo dans la bdd
                if ($destinataire == $donnees) {
                    //retourne true si il existe
                    return true;
                }
                else {
                    return false;
                }
            }
            catch(Exception $e)
            {
            //affichage d'une exception en cas d’erreur
            die('Erreur : '.$e->getMessage());
            }        
        }

        //méthode Envoyer Message (ajout d'un message en bdd)
        public function createMessage($bdd)
        {   
            //récuparation des valeurs de l'objet
            $auteur_message = $this->getAuteurMessage();
            $destinataire_message = $this->getDestinataireMessage();
            $sujet_message = $this->getSujetMessage(); 
            $corps_message = $this->getCorpsMessage();   
            try
            {   
                //requête ajout d'un message
                $req = $bdd->prepare('INSERT INTO nx_message(subject_message, body_message, auteur, destinataire) 
                VALUES (:subject_message, :body_message, :auteur, :destinataire)');
                //éxécution de la requête SQL
                $req->execute(array(
                'subject_message' => $sujet_message,
                'body_message' => $corps_message,
                'auteur' => $auteur_message,
                'destinataire' => $destinataire_message
                ));

                //requête ajout association concerner
                //requête pour avoir l'id du message
                $req = $bdd->prepare('SELECT id_message FROM nx_message WHERE subject_message = :subject_message AND body_message = :body_message AND auteur = :auteur AND destinataire = :destinataire');
                //éxécution de la requête SQL
                $req->execute(array(
                    'subject_message' => $sujet_message,
                    'body_message' => $corps_message,
                    'auteur' => $auteur_message,
                    'destinataire' => $destinataire_message
                ));
                //boucle pour récupérer l'id du message reçu
                $id_message = "";
                foreach ($req as $row) {
                    //stockage d'une donnée
                    $id_message = $row['id_message'];
                }
                //association auteur-message
                //récupération id de l'auteur
                $id_user = $_SESSION['idUser'];
                //requête pour lier l'auteur et le message
                $req = $bdd->prepare('INSERT INTO nx_concerner(id_user, id_message) 
                VALUES (:id_user, :id_message)');
                //éxécution de la requête SQL
                $req->execute(array(
                    'id_user' => $id_user,
                    'id_message' => $id_message
                ));
                
                //association destinataire-message
                //requête pour avoir l'id du destinataire
                $req = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :destinataire');
                //éxécution de la requête SQL
                $req->execute(array(
                    'destinataire' => $destinataire_message,
                ));
                //boucle pour récupérer l'id du destinataire
                $id_destinataire = "";
                foreach ($req as $row) {
                    //stockage d'une donnée
                    $id_destinataire = $row['id_user'];
                }

                //requête ajout lier le destinataire et le message
                $req = $bdd->prepare('INSERT INTO nx_concerner(id_user, id_message) 
                VALUES (:id_user, :id_message)');
                //éxécution de la requête SQL
                $req->execute(array(
                    'id_user' => $id_destinataire,
                    'id_message' => $id_message
                ));

                //fermeture de la connexion
                $bdd = null;
                $req = null;
            }
            catch(Exception $e)
            {
                //affichage d'une exception en cas d’erreur
                die('Erreur : '.$e->getMessage());
            }        
        }

        //méthode pour Afficher les Messages reçus : retourne une liste d'objet message
        public function messageReceived($bdd)
        {
            //récupère du pseudo de l'utilisateur
            $pseudo_user = $this->getDestinataireMessage();
            //tableau des messages reçus
            $message_list_received = [];
            try
            {
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //récupère l'id de l'utilisateur
                $reponse = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :pseudo_user');
                $reponse->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                ));
                //boucle pour parcourir et stocker les données
                $donnees = [];
                foreach ($reponse as $row) {
                    //stockage des données
                    $donnees[] = $row['id_user'];
                }
                //stockage de l'id
                $id_user = $donnees[0];

                //vérifie la liste des id des messages liés à l'utilisateur
                $reponse = $bdd->prepare('SELECT id_message FROM nx_concerner WHERE id_user = :id_user');
                $reponse->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                ));
                //boucle pour parcourir et stocker les données
                $id_message = [];
                foreach ($reponse as $row) {
                    //stockage des données
                    $id_message[] = $row['id_message'];
                }

                //Vérifie s'il y a des messages liés
                if(!empty($id_message)){
                    //boucle pour parcourir les messages reçu et les stocker
                    foreach ($id_message as $row){
                        //vérifie la liste des messages
                        $reponse = $bdd->prepare('SELECT * FROM nx_message WHERE id_message = :id_message AND destinataire = :pseudo_user');
                        $reponse->execute(array(
                            'id_message' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row),
                            'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user)
                        ));
                        //boucle pour parcourir et stocker les message
                        foreach($reponse as $row){
                            //création d'une nouvelle instance de message
                            $new_message = new Message($row['auteur'],"",$row['subject_message'],$row['body_message']);
                            //stockage du message
                            $message_list_received[] = $new_message;
                        }
                    }
                }

                //retourne la liste des messages reçus
                return $message_list_received;
            }
            catch (Exception $e)
            {
                //affichage d'une exception en cas d’erreur
                die('Erreur : '.$e->getMessage());
            }
        }

        //méthode pour Afficher les Messages envoyés
        public function messageSend($bdd)
        {
            //récupère du pseudo de l'utilisateur
            $pseudo_user = $this->getAuteurMessage();
            //tableau des messages envoyés
            $message_list_send = [];
            try
            {
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //récupère l'id de l'utilisateur
                $reponse = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :pseudo_user');
                $reponse->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                ));
                //boucle pour parcourir et stocker les données
                $donnees = [];
                foreach ($reponse as $row) {
                    //stockage des données
                    $donnees[] = $row['id_user'];
                }
                //stockage de l'id
                $id_user = $donnees[0];

                //vérifie la liste des id des messages liés à l'utilisateur
                $reponse = $bdd->prepare('SELECT id_message FROM nx_concerner WHERE id_user = :id_user');
                $reponse->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                ));
                //boucle pour parcourir et stocker les données
                $id_message = [];
                foreach ($reponse as $row) {
                    //stockage des données
                    $id_message[] = $row['id_message'];
                }

                //Vérifie s'il y a des messages liés
                if(!empty($id_message)){
                    //boucle pour parcourir les messages reçu et les stocker
                    foreach ($id_message as $row){
                        //vérifie la liste des messages
                        $reponse = $bdd->prepare('SELECT * FROM nx_message WHERE id_message = :id_message AND auteur = :pseudo_user');
                        $reponse->execute(array(
                            'id_message' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row),
                            'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user)
                        ));
                        //boucle pour parcourir et stocker les message
                        foreach($reponse as $row){
                            //création d'une nouvelle instance de message
                            $new_message = new Message("",$row['destinataire'],$row['subject_message'],$row['body_message']);
                            //stockage du message
                            $message_list_send[] = $new_message;
                        }
                    }
                }

                //retourne la liste des messages reçus
                return $message_list_send;
            }
            catch (Exception $e)
            {
                //affichage d'une exception en cas d’erreur
                die('Erreur : '.$e->getMessage());
            }     
        }
    }
?>