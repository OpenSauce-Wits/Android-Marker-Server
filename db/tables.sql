use testDB;
drop table if exists ams_emulators;
CREATE TABLE ams_emulators(
 id     integer primary key auto_increment,
 emulator_id varchar(100),
 state  varchar(100),
 in_use varchar(10)
)engine="innodb";