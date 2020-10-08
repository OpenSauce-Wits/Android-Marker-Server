use testDB;
drop table if exists ams_emulators;
drop table if exists ams_submissions;
CREATE TABLE ams_emulators(
 id     integer primary key auto_increment,
 emulator_id varchar(100),
 state  varchar(100),
 in_use varchar(10)
)engine="innodb";

CREATE TABLE ams_submissions(
    user_id INT NOT NULL AUTO_INCREMENT,
    assignment INT NOT NULL ,
    status VARCHAR(20),
    submission_type VARCHAR(20),
    priority INT,
    PRIMARY KEY (user_id)
);



