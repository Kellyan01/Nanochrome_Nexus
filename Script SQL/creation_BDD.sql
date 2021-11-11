create database nanochrome;
use nanochrome;
create table users (
	id_user INT(20) auto_increment PRIMARY KEY not null,
    name_user VARCHAR(50) not null,
    firstname_user VARCHAR(50) not null,
    pseudo_user VARCHAR(50)not null,
    mail_user VARCHAR(100) not null,
    password_user VARCHAR(500) not null
);
create table message_send (
	id_msg_send INT(20) auto_increment PRIMARY KEY not null,
    subject_msg_send longtext not null,
    body_msg_send longtext not null,
    destinataires_list longtext not null,
    id_user INT(20) not null,
    foreign key (id_user) references users(id_user)
);
create table message_received (
	id_msg_received INT(20) auto_increment PRIMARY KEY not null,
    subject_msg_received longtext not null,
    body_msg_received longtext not null,
    auteur varchar(200) not null
);
create table reception_box (
    id_user INT(20) not null,
    id_msg_received INT(20) not null,
    primary key (id_user,id_msg_received)
);
alter table reception_box
	add constraint fk_id_user foreign key (id_user) references users(id_user),
    add constraint fk_id_msg_received foreign key (id_msg_received) references message_received(id_msg_received);