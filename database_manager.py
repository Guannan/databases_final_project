#!/usr/bin/env python

import MySQLdb
import dbconfig
import sys
import os


# TODO need to check for variable types
def generate_insert_statement(relation,values):
	statement = "insert into " + relation + " values (" + ','.join(values) + ");"
	return statement


myDB = MySQLdb.connect(host = dbconfig.host, user = dbconfig.user, passwd = dbconfig.password, db = dbconfig.dbname)
cursor = myDB.cursor()

# sql_statements = []
"""
cursor.execute("select * from University;")
results = cursor.fetchone()
print str(results[1])   # returns JOHNS HOPKINS
"""

field = "Financial Services" # This could be changed to whatever field you need
getSkills_cmd = "select S.SkillName, COUNT(U.UserID)  from User as U, Has_skill as Hs, Skill as S where U.Industry = \""+field+"\" AND U.UserID = Hs.UserID AND Hs.SkillID = S.SkillID GROUP BY S.SkillName Order by COUNT(U.UserID) DESC"
#getSkills_cmd = "select U.UserID from User as U where U.Industry = \""+field+"\""
#getSkills_cmd = "select User.UserID, User.Lname from User where User.industry = \"Computer Software\""
#getSkills_cmd = "select Lname from User where User.UserID = 54"
cursor.execute(getSkills_cmd)
results = cursor.fetchall()
#print results
pop_skills = []
for i in xrange(min(15,len(results))):
	pop_skills.append(results[i][0])
dbprint pop_skills

i=0
old_list = []
for skill in pop_skills:
	skillFilter_cmd = "select User.UserID from User, Has_skill, Skill where User.UserID = Has_skill.UserID AND Has_skill.SkillID = Skill.SkillID AND Skill.SkillName = \""+skill+"\""
	cursor.execute(skillFilter_cmd)
	results = cursor.fetchall()
	if i==0:
		for ID in results:
			old_list.append(ID[0])
	else:
		new_list = []
		for ID in results:
			new_list.append(ID[0])
		old_list = list(set(old_list).intersection(new_list))
		if len(old_list)<=10:
			break
	i+=1

for ID in old_list:
	findUser_cmd = "select User.Fname, User.Lname from User where User.UserID="+str(ID)
	cursor.execute(findUser_cmd)
	results = cursor.fetchall()
	print results[0][0], results[0][1]
# for sql_statement in sql_statements:
# 	cursor.execute(sql_statement)

myDB.commit()
myDB.close()

