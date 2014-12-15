drop table if exists User;
create table User (
  UserID integer,
  Fname varchar(15),
  Lname varchar(15),
  NumConnection integer,
  Age integer,
  Industry varchar(15)
);

drop table if exists Education;
create table Education (
  UserID integer,
  UniversityID integer,
  DegreeID integer,
  Field varchar(15),
  StartDate varchar(15),
  EndDate varchar(15)
);

drop table if exists Degree;
create table Degree (
  DegreeID integer,
  DegreeName varchar(15)
);

drop table if exists University;
create table University (
  UniversityID integer,
  UniversityName varchar(15)
);

drop table if exists Experience;
create table Experience (
  UserID integer,
  EmployerID integer,
  StartDate varchar(15),
  EndDate varchar(15)
);

drop table if exists Employer;
create table Employer (
  EmployerID integer,
  EmployerName varchar(15)
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
  SkillName varchar(15)
);

drop table if exists Knows_language;
create table Knows_language (
  UserID integer,
  LanguageID integer
);

drop table if exists Languages;
create table Languages (
  LanguageID integer,
  LanguageName varchar(15)
) ;

drop table if exists Member_of;
create table Member_of (
  UserID integer,
  GroupID integer
) ;

drop table if exists Groups;
create table Groups (
  GroupID integer,
  GroupName varchar(15),
  NumMembers integer
);


insert into User values ( 2 , 'Jianan' , 'Zhan', 500, 0, 'Higher Education');
insert into Education values ( 2 , 99 , 12 , 'Doctor of Philosophy (PhD), Biomedical/Medical Engineering', '2012 – 2018 (expected)', '2012 – 2018 (expected)');
insert into University values ( 99 , 'Johns Hopkins School of Medicine');
insert into Education values ( 2 , 99 , 12 , '', '2010 – 2012', '2010 – 2012');
insert into University values ( 99 , 'The Johns Hopkins University');
insert into Education values ( 2 , 99 , 12 , '', '2006 – 2010', '2006 – 2010');
insert into University values ( 99 , '浙江大学');
insert into Has_skill values ( 2 , 301, 2);
insert into Skill values ( 301 , 'Biomedical Engineering');
insert into Has_skill values ( 2 , 302, 2);
insert into Skill values ( 302 , 'Computational Biology');
insert into Has_skill values ( 2 , 303, 2);
insert into Skill values ( 303 , 'Biostatistics');
insert into Has_skill values ( 2 , 304, 2);
insert into Skill values ( 304 , 'Mathematical Modeling');
insert into Has_skill values ( 2 , 305, 2);
insert into Skill values ( 305 , 'Machine Learning');
insert into Has_skill values ( 2 , 306, 2);
insert into Skill values ( 306 , 'Algorithms');
insert into Has_skill values ( 2 , 307, 2);
insert into Skill values ( 307 , 'Python');
insert into Has_skill values ( 2 , 308, 2);
insert into Skill values ( 308 , 'R');
insert into Has_skill values ( 2 , 309, 2);
insert into Skill values ( 309 , 'Bioinformatics');
insert into Has_skill values ( 2 , 310, 2);
insert into Skill values ( 310 , 'Matlab');
insert into Has_skill values ( 2 , 311, 2);
insert into Skill values ( 311 , 'PCR');
insert into Has_skill values ( 2 , 312, 2);
insert into Skill values ( 312 , 'Biomaterials');
insert into Has_skill values ( 2 , 313, 2);
insert into Skill values ( 313 , 'Materials Science');
insert into Has_skill values ( 2 , 314, 2);
insert into Skill values ( 314 , 'Stem Cells');
insert into Has_skill values ( 2 , 315, 2);
insert into Skill values ( 315 , 'Cell Culture');
insert into Has_skill values ( 2 , 316, 2);
insert into Skill values ( 316 , 'Molecular Biology');
insert into Has_skill values ( 2 , 317, 2);
insert into Skill values ( 317 , 'Cell Biology');
insert into Member_of values ( 2 , 400);
insert into Groups values ( 400 , '华人事业互助会', 0);
insert into Member_of values ( 2 , 400);
insert into Groups values ( 400 , 'Johns Hopkins Career Builders', 0);
insert into Knows_language values ( 2 , 200);
insert into Languages values ( 200 , 'English');
insert into Knows_language values ( 2 , 200);
insert into Languages values ( 200 , 'Mandarin');
