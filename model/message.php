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
        public function __construct($auteur_message, $destinataire_message, $sujet_message, $corps_message, $id_message)
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
        //méthode Envoyer Message (ajout d'un message en bdd table receive et table send)
        public function createMessage($bdd)
        {   
            //récuparation des valeurs de l'objet
            $auteur_message = $this->getAuteurMessage();
            $destinataire_message = $this->getDestinataireMessage();
            $sujet_message = $this->getSujetMessage(); 
            $corps_message = $this->getCorpsMessage();
            //récupération id de l'auteur
            $id_auteur = $_SESSION['idUser'];      
            try
            {   
                //requête ajout d'un message envoyé (lié à l'auteur)
                $req = $bdd->prepare('INSERT INTO message_send(subject_msg_send, body_msg_send, destinataires_list, id_user) 
                VALUES (:subject_msg_send, :body_msg_send, :destinataires_list, :id_auteur)');
                //éxécution de la requête SQL
                $req->execute(array(
                'subject_msg_send' => $sujet_message,
                'body_msg_send' => $corps_message,
                'destinataires_list' => $destinataire_message,
                'id_auteur' => $id_auteur,
                ));

                //requête ajout d'un message reçu
                $req = $bdd->prepare('INSERT INTO message_received(subject_msg_received, body_msg_received, auteur) 
                VALUES (:subject_msg_received, :body_msg_received, :auteur)');
                //éxécution de la requête SQL
                $req->execute(array(
                'subject_msg_received' => $sujet_message,
                'body_msg_received' => $corps_message,
                'auteur' => $auteur_message,
                ));

                //requête pour avoir l'id du message reçu
                $req = $bdd->prepare('SELECT id_msg_received FROM message_received WHERE subject_msg_received = :subject_msg_received AND body_msg_received = :body_msg_received AND auteur = :auteur');
                //éxécution de la requête SQL
                $req->execute(array(
                    'subject_msg_received' => $sujet_message,
                    'body_msg_received' => $corps_message,
                    'auteur' => $auteur_message,
                ));
                //boucle pour récupérer l'id du message reçu
                $id_receive = "";
                foreach ($req as $row) {
                    //stockage d'une donnée
                    $id_receive = $row['id_msg_received'];
                }
                
                /////////////////////////////////////
                //BOUCLE POUR RECUP ID DESTINATAIRE//
                /////////////////////////////////////
                //requête pour avoir l'id du destinataire
                $req = $bdd->prepare('SELECT id_user FROM users WHERE pseudo_user = :destinataire');
                //éxécution de la requête SQL
                $req->execute(array(
                    'destinataire' => $destinataire_message,
                ));
                //boucle pour récupérer l'id du message reçu
                $id_destinataire = "";
                foreach ($req as $row) {
                    //stockage d'une donnée
                    $id_destinataire = $row['id_user'];
                }

                //requête ajout lien message-destinataire dans reception_box
                $req = $bdd->prepare('INSERT INTO reception_box(id_user, id_msg_received) 
                VALUES (:id_destinataire, :id_msg_received)');
                //éxécution de la requête SQL
                $req->execute(array(
                    'id_destinataire' => $id_destinataire,
                    'id_msg_received' => $id_receive,
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
    }
?>