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

cursor.execute("select * from University;")

results = cursor.fetchone()
print str(results[1])   # returns JOHNS HOPKINS

# for sql_statement in sql_statements:
# 	cursor.execute(sql_statement)

myDB.commit()
myDB.close()

