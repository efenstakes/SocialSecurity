
-- 
drop database if exists sority;

create database if not exists sority;

use sority;


create table users (
    id int auto_increment,
    name varchar(34) not null,
    password varchar(200) not null,
    phone varchar(15),
    user_type enum('REGULAR', 'ADMIN'),
    primary key(id)
);


create table security_hubs (
    id int auto_increment,
    name varchar(34) not null,
    city varchar(20),
    lat varchar(20),
    lng varchar(20),
    hub_type enum ('HOSPITAL', 'POLICE_STATION'),
    primary key(id)
);


create table alerts(
    id int auto_increment,
    from_user int not null,
    from_lat varchar(200),
    from_lng varchar(200),
    alert_text longtext,
    foreign key(from_user) references users(id),
    primary key(id)
);



create table alert_buddies (
  id int,
  me_id int,
  buddy_id varchar(15),
  foreign key(me_id) references users(id),
  primary key(id)
);
