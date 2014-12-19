drop table if exists User;
create table User (
  UserID integer,
  Fname varchar(45),
  Lname varchar(45),
  NumConnection integer,
  Age integer,
  Industry varchar(45)
);

drop table if exists Education;
create table Education (
  UserID integer,
  UniversityID integer,
  DegreeID integer,
  Field varchar(45),
  StartDate varchar(45),
  EndDate varchar(45)
);

drop table if exists Degree;
create table Degree (
  DegreeID integer,
  DegreeName varchar(45)
);

drop table if exists University;
create table University (
  UniversityID integer,
  UniversityName varchar(45)
);

drop table if exists Experience;
create table Experience (
  UserID integer,
  EmployerID integer,
  StartDate varchar(45),
  EndDate varchar(45)
);

drop table if exists Employer;
create table Employer (
  EmployerID integer,
  EmployerName varchar(45)
);

drop table if exists Has_skill;
create table Has_skill (
  UserID integer,
  SkillID integer,
  Endorsements integer
);

drop table if exists Skill;
create table Skill (
  SkillID integer,
  SkillName varchar(45)
);

drop table if exists Knows_language;
create table Knows_language (
  UserID integer,
  LanguageID integer
);

drop table if exists Languages;
create table Languages (
  LanguageID integer,
  LanguageName varchar(45)
) ;

drop table if exists Member_of;
create table Member_of (
  UserID integer,
  GroupID integer
) ;

drop table if exists Groups;
create table Groups (
  GroupID integer,
  GroupName varchar(45),
  NumMembers integer
);

