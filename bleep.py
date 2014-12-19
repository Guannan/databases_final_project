#!/usr/bin/env python

def happy(me):
	print me + " is happy"

def generate_insert_statement(relation,values):
	statement = "insert into " + relation + " values (" + ','.join(values) + ");"
	return statement

# happy("Guannan")

print generate_insert_statement("GOAT", ["mammal", "4 feet", "smelly"])



