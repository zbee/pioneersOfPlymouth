create schema pop collate latin1_swedish_ci;

create table users
(
    id binary(16) not null
        primary key,
    username varchar(50) not null,
    firstName varchar(256) null,
    lastName varchar(256) null,
    password varchar(100) not null,
    oldPassword varchar(100) null,
    passwordChanged varchar(20) default '0000000000' not null,
    salt varchar(512) not null,
    email varchar(256) not null,
    oldEmail varchar(512) null,
    emailChanged int(2) default 0 not null,
    phone int default 0 not null,
    dateRegistered int(20) not null,
    activated int(1) default 0 not null,
    title varchar(50) default '' not null,
    twoStep int(1) default 0 not null,
    lastLoggedIn int(20) default 0 not null,
    oldLastLoggedIn int(20) default 0 not null,
    ip varchar(60) default '' not null
);

create table ban
(
    id int(20) auto_increment
        primary key,
    date int(20) null,
    ip varchar(60) null,
    user binary(16) null,
    issuer int default 0 not null,
    reason varchar(512) default 'No reason provided.' not null,
    appealed int(1) default 0 not null,
    constraint ban_users_id_fk
        foreign key (user) references users (id)
            on update cascade on delete cascade
);

create table connections
(
    userID binary(16) not null comment 'The connected user',
    connectionType smallint(1) default 0 not null comment 'The type of connection that is open; 0=site, 1=browser, 2=lobby, 3=game, 4=post-game',
    connectionID binary(16) null,
    lastPing int not null,
    constraint connections_userID_uindex
        unique (userID),
    constraint connections_users_id_fk
        foreign key (userID) references users (id)
            on update cascade on delete cascade
)
    comment 'Table containing users connected to games and lobbies';

alter table connections
    add primary key (userID);

create table gameLobbies
(
    lobbyID binary(16) not null comment 'Binary UUID of the game'
        primary key,
    name varchar(40) not null comment 'User-provided name for the game lobby',
    inviteOnly tinyint default 0 not null comment 'Whether or not the game is invite-only',
    maxPlayers tinyint default 3 not null comment 'Number of players allowed to join; must be greater than 3',
    expansionsIncluded tinyint default 0 not null comment 'Identifier of expansions that are used in this game; raises the maxPlayers possibility',
    mapSetup int(57) unsigned not null,
    date int not null comment 'Unix timestamp game was created',
    owner binary(16) not null comment 'Link to the user UUID that created the game lobby',
    language varchar(2) default 'en' not null comment 'ISO 639-1 code of the language the owner speaks',
    constraint gameLobbies_ak_1
        unique (owner),
    constraint gameLobbies_users_id_fk
        foreign key (owner) references users (id)
            on update cascade on delete cascade
)
    comment 'List of games actively being played or open for joining from the browser';

create index gameLobbies_idx_1
    on gameLobbies (name);

create index gameLobbies_idx_2
    on gameLobbies (inviteOnly);

create index gameLobbies_idx_3
    on gameLobbies (expansionsIncluded);

create index gameLobbies_idx_4
    on gameLobbies (date);

create index gameLobbies_idx_5
    on gameLobbies (owner);

create index gameLobbies_idx_6
    on gameLobbies (language);

create index gameLobbies_idx_7
    on gameLobbies (lobbyID, owner);

create table userblobs
(
    id int(20) auto_increment
        primary key,
    user binary(16) not null,
    code varchar(160) not null,
    action varchar(20) not null,
    date int(20) not null,
    constraint userblobs_users_id_fk
        foreign key (user) references users (id)
            on update cascade on delete cascade
);

create definer = admin@localhost trigger before_insert_uuid_the_user
    before insert
    on users
    for each row
    SET new.id = unhex(replace(uuid(),'-',''));

