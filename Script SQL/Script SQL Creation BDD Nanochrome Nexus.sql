#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------
CREATE DATABASE nanochrome_nexus;
USE nanochrome_nexus;

#------------------------------------------------------------
# Table: nx_users
#------------------------------------------------------------

CREATE TABLE nx_users(
        id_user        Int  Auto_increment  NOT NULL ,
        name_user      Varchar (50) NOT NULL ,
        firstname_user Varchar (50) NOT NULL ,
        pseudo_user    Varchar (50) NOT NULL ,
        mail_user      Varchar (100) NOT NULL ,
        password_user  Varchar (100) NOT NULL
	,CONSTRAINT nx_users_PK PRIMARY KEY (id_user)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_message
#------------------------------------------------------------

CREATE TABLE nx_message(
        id_message      Int  Auto_increment  NOT NULL ,
        subject_message Longtext NOT NULL ,
        body_message    Longtext NOT NULL ,
        auteur          Varchar (100) NOT NULL ,
        destinataire    Varchar (100) NOT NULL
	,CONSTRAINT nx_message_PK PRIMARY KEY (id_message)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_parties
#------------------------------------------------------------

CREATE TABLE nx_parties(
        id_partie          Int  Auto_increment  NOT NULL ,
        name_partie        Varchar (50) NOT NULL ,
        description_partie Longtext NOT NULL
	,CONSTRAINT nx_parties_PK PRIMARY KEY (id_partie)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_personnages
#------------------------------------------------------------

CREATE TABLE nx_personnages(
        id_personnage          Int  Auto_increment  NOT NULL ,
        name_personnage        Varchar (50) NOT NULL ,
        description_personnage Longtext NOT NULL ,
        id_user                Int ,
        id_partie              Int
	,CONSTRAINT nx_personnages_PK PRIMARY KEY (id_personnage)

	,CONSTRAINT nx_personnages_nx_users_FK FOREIGN KEY (id_user) REFERENCES nx_users(id_user)
	,CONSTRAINT nx_personnages_nx_parties0_FK FOREIGN KEY (id_partie) REFERENCES nx_parties(id_partie)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_message_game
#------------------------------------------------------------

CREATE TABLE nx_message_game(
        id_message_game        Int  Auto_increment  NOT NULL ,
        subject_message_game   Longtext NOT NULL ,
        body_msg_play_received Longtext NOT NULL ,
        auteur_play            Varchar (50) NOT NULL ,
        destinataire_play      Varchar (50) NOT NULL
	,CONSTRAINT nx_message_game_PK PRIMARY KEY (id_message_game)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_friends
#------------------------------------------------------------

CREATE TABLE nx_friends(
        id_friend     Int  Auto_increment  NOT NULL ,
        pseudo_friend Varchar (50) NOT NULL
	,CONSTRAINT nx_friends_PK PRIMARY KEY (id_friend)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_participer
#------------------------------------------------------------

CREATE TABLE nx_participer(
        id_user     Int NOT NULL ,
        id_partie   Int NOT NULL ,
        game_master Bool NOT NULL ,
        note_joueur Longtext NOT NULL
	,CONSTRAINT nx_participer_PK PRIMARY KEY (id_user,id_partie)

	,CONSTRAINT nx_participer_nx_users_FK FOREIGN KEY (id_user) REFERENCES nx_users(id_user)
	,CONSTRAINT nx_participer_nx_parties0_FK FOREIGN KEY (id_partie) REFERENCES nx_parties(id_partie)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_recevoir
#------------------------------------------------------------

CREATE TABLE nx_recevoir(
        id_message_game Int NOT NULL ,
        id_personnage   Int NOT NULL
	,CONSTRAINT nx_recevoir_PK PRIMARY KEY (id_message_game,id_personnage)

	,CONSTRAINT nx_recevoir_nx_message_game_FK FOREIGN KEY (id_message_game) REFERENCES nx_message_game(id_message_game)
	,CONSTRAINT nx_recevoir_nx_personnages0_FK FOREIGN KEY (id_personnage) REFERENCES nx_personnages(id_personnage)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_relier
#------------------------------------------------------------

CREATE TABLE nx_relier(
        id_friend Int NOT NULL ,
        id_user   Int NOT NULL
	,CONSTRAINT nx_relier_PK PRIMARY KEY (id_friend,id_user)

	,CONSTRAINT nx_relier_nx_friends_FK FOREIGN KEY (id_friend) REFERENCES nx_friends(id_friend)
	,CONSTRAINT nx_relier_nx_users0_FK FOREIGN KEY (id_user) REFERENCES nx_users(id_user)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: nx_concerner
#------------------------------------------------------------

CREATE TABLE nx_concerner(
        id_user    Int NOT NULL ,
        id_message Int NOT NULL
	,CONSTRAINT nx_concerner_PK PRIMARY KEY (id_user,id_message)

	,CONSTRAINT nx_concerner_nx_users_FK FOREIGN KEY (id_user) REFERENCES nx_users(id_user)
	,CONSTRAINT nx_concerner_nx_message0_FK FOREIGN KEY (id_message) REFERENCES nx_message(id_message)
)ENGINE=InnoDB;

