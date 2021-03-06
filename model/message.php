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
        //m??thode pour v??rifier si un destinataire existe
        public function showDestinataire($bdd)
        {
            //r??cup??ration du destinataire dans l'objet
            $destinataire = $this->getDestinataireMessage();
            try
            {  
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //requ??te pour stocker le contenu de toute la table le contenu est stock?? dans le tableau $req_destinataire
                $req_destinataire = $bdd->prepare('SELECT * FROM nx_users WHERE pseudo_user = :pseudo_user');
                $req_destinataire->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $destinataire),
                ));
                //boucle pour parcourir et stocker une donn??es
                $donnees = "";
                foreach ($req_destinataire as $row) {
                    //stockage d'une donn??e
                    $donnees = $row['pseudo_user'];
                }
                //fermeture de la connexion
                $bdd = null;
                $req_destinataire = null;
                //v??rification de l'existence du pseudo dans la bdd
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
            //affichage d'une exception en cas d???erreur
            die('Erreur : '.$e->getMessage());
            }        
        }

        //m??thode Envoyer Message (ajout d'un message en bdd)
        public function createMessage($bdd)
        {   
            //r??cuparation des valeurs de l'objet
            $auteur_message = $this->getAuteurMessage();
            $destinataire_message = $this->getDestinataireMessage();
            $sujet_message = $this->getSujetMessage(); 
            $corps_message = $this->getCorpsMessage();   
            try
            {   
                //requ??te ajout d'un message
                $req = $bdd->prepare('INSERT INTO nx_message(subject_message, body_message, auteur, destinataire) 
                VALUES (:subject_message, :body_message, :auteur, :destinataire)');
                //??x??cution de la requ??te SQL
                $req->execute(array(
                'subject_message' => $sujet_message,
                'body_message' => $corps_message,
                'auteur' => $auteur_message,
                'destinataire' => $destinataire_message
                ));

                //requ??te ajout association concerner
                //requ??te pour avoir l'id du message
                $req = $bdd->prepare('SELECT id_message FROM nx_message WHERE subject_message = :subject_message AND body_message = :body_message AND auteur = :auteur AND destinataire = :destinataire');
                //??x??cution de la requ??te SQL
                $req->execute(array(
                    'subject_message' => $sujet_message,
                    'body_message' => $corps_message,
                    'auteur' => $auteur_message,
                    'destinataire' => $destinataire_message
                ));
                //boucle pour r??cup??rer l'id du message re??u
                $id_message = "";
                foreach ($req as $row) {
                    //stockage d'une donn??e
                    $id_message = $row['id_message'];
                }
                //association auteur-message
                //r??cup??ration id de l'auteur
                $id_user = $_SESSION['idUser'];
                //requ??te pour lier l'auteur et le message
                $req = $bdd->prepare('INSERT INTO nx_concerner(id_user, id_message) 
                VALUES (:id_user, :id_message)');
                //??x??cution de la requ??te SQL
                $req->execute(array(
                    'id_user' => $id_user,
                    'id_message' => $id_message
                ));
                
                //association destinataire-message
                //requ??te pour avoir l'id du destinataire
                $req = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :destinataire');
                //??x??cution de la requ??te SQL
                $req->execute(array(
                    'destinataire' => $destinataire_message,
                ));
                //boucle pour r??cup??rer l'id du destinataire
                $id_destinataire = "";
                foreach ($req as $row) {
                    //stockage d'une donn??e
                    $id_destinataire = $row['id_user'];
                }

                //requ??te ajout lier le destinataire et le message
                $req = $bdd->prepare('INSERT INTO nx_concerner(id_user, id_message) 
                VALUES (:id_user, :id_message)');
                //??x??cution de la requ??te SQL
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
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }        
        }

        //m??thode pour Afficher les Messages re??us : retourne une liste d'objet message
        public function messageReceived($bdd)
        {
            //r??cup??re du pseudo de l'utilisateur
            $pseudo_user = $this->getDestinataireMessage();
            //tableau des messages re??us
            $message_list_received = [];
            try
            {
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //r??cup??re l'id de l'utilisateur
                $reponse = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :pseudo_user');
                $reponse->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                ));
                //boucle pour parcourir et stocker les donn??es
                $donnees = [];
                foreach ($reponse as $row) {
                    //stockage des donn??es
                    $donnees[] = $row['id_user'];
                }
                //stockage de l'id
                $id_user = $donnees[0];

                //v??rifie la liste des id des messages li??s ?? l'utilisateur
                $reponse = $bdd->prepare('SELECT id_message FROM nx_concerner WHERE id_user = :id_user');
                $reponse->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                ));
                //boucle pour parcourir et stocker les donn??es
                $id_message = [];
                foreach ($reponse as $row) {
                    //stockage des donn??es
                    $id_message[] = $row['id_message'];
                }

                //V??rifie s'il y a des messages li??s
                if(!empty($id_message)){
                    //boucle pour parcourir les messages re??u et les stocker
                    foreach ($id_message as $row){
                        //v??rifie la liste des messages
                        $reponse = $bdd->prepare('SELECT * FROM nx_message WHERE id_message = :id_message AND destinataire = :pseudo_user');
                        $reponse->execute(array(
                            'id_message' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row),
                            'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user)
                        ));
                        //boucle pour parcourir et stocker les message
                        foreach($reponse as $row){
                            //cr??ation d'une nouvelle instance de message
                            $new_message = new Message($row['auteur'],"",$row['subject_message'],$row['body_message']);
                            //stockage du message
                            $message_list_received[] = $new_message;
                        }
                    }
                }
                //fermeture de la connexion
                $bdd = null;
                $reponse = null;

                //retourne la liste des messages re??us
                return $message_list_received;
            }
            catch (Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }
        }

        //m??thode pour Afficher les Messages envoy??s
        public function messageSend($bdd)
        {
            //r??cup??re du pseudo de l'utilisateur
            $pseudo_user = $this->getAuteurMessage();
            //tableau des messages envoy??s
            $message_list_send = [];
            try
            {
                //permet d'accepter les accents
                $bdd->exec("set names utf8");

                //r??cup??re l'id de l'utilisateur
                $reponse = $bdd->prepare('SELECT id_user FROM nx_users WHERE pseudo_user = :pseudo_user');
                $reponse->execute(array(
                    'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user),
                ));
                //boucle pour parcourir et stocker les donn??es
                $donnees = [];
                foreach ($reponse as $row) {
                    //stockage des donn??es
                    $donnees[] = $row['id_user'];
                }
                //stockage de l'id
                $id_user = $donnees[0];

                //v??rifie la liste des id des messages li??s ?? l'utilisateur
                $reponse = $bdd->prepare('SELECT id_message FROM nx_concerner WHERE id_user = :id_user');
                $reponse->execute(array(
                    'id_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $id_user),
                ));
                //boucle pour parcourir et stocker les donn??es
                $id_message = [];
                foreach ($reponse as $row) {
                    //stockage des donn??es
                    $id_message[] = $row['id_message'];
                }

                //V??rifie s'il y a des messages li??s
                if(!empty($id_message)){
                    //boucle pour parcourir les messages re??u et les stocker
                    foreach ($id_message as $row){
                        //v??rifie la liste des messages
                        $reponse = $bdd->prepare('SELECT * FROM nx_message WHERE id_message = :id_message AND auteur = :pseudo_user');
                        $reponse->execute(array(
                            'id_message' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row),
                            'pseudo_user' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $pseudo_user)
                        ));
                        //boucle pour parcourir et stocker les message
                        foreach($reponse as $row){
                            //cr??ation d'une nouvelle instance de message
                            $new_message = new Message("",$row['destinataire'],$row['subject_message'],$row['body_message']);
                            //stockage du message
                            $message_list_send[] = $new_message;
                        }
                    }
                }
                //fermeture de la connexion
                $bdd = null;
                $reponse = null;
                //retourne la liste des messages re??us
                return $message_list_send;
            }
            catch (Exception $e)
            {
                //affichage d'une exception en cas d???erreur
                die('Erreur : '.$e->getMessage());
            }     
        }
    }
?>