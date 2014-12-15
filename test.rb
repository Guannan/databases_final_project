#!/usr/bin/env ruby

require 'mechanize'
# File.readlines('links.txt').each do |line|

# 	if "#{line}".chomp != ""
# 		print "#{line}"
# 	end
# end



USER_AGENTS = ['Windows IE 6', 'Windows IE 7', 'Windows Mozilla', 'Mac Safari', 'Mac FireFox', 'Mac Mozilla', 'Linux Mozilla', 'Linux Firefox', 'Linux Konqueror']

def http_client
  	Mechanize.new do |agent|
    	agent.user_agent_alias = USER_AGENTS.sample
    	agent.max_history = 0
  	end
end


# mech = http_client.get("https://www.linkedin.com/pub/mark-pritt/45/94a/3a8")
mech = http_client.get("https://www.linkedin.com/pub/jianan-zhan/24/421/982")

html= mech.body
first_name = mech.at('.full-name').text.split(' ', 2)[0].strip if mech.at('.full-name')
last_name = mech.at('.full-name').text.split(' ', 2)[1].strip if mech.at('.full-name')
connections = (mech.at('.member-connections').text if mech.at('.member-connections')).gsub(/[^0-9]/, '')
industry = (mech.at('.industry').text.gsub(/\s+/, ' ').strip if mech.at('.industry'))

user_id = 2
age = 0
puts "insert into User values ( #{user_id} , '#{first_name}' , '#{last_name}', #{connections}, #{age}, '#{industry}');";

university_id = 99
degree_id = 12
education = mech.search('.background-education .education').map do |item|
	name   = item.at('h4').text.gsub(/\s+|\n/, ' ').strip if item.at('h4')
	desc   = item.at('h5').text.gsub(/\s+|\n/, ' ').strip if item.at('h5')
	period = item.at('.education-date').text.gsub(/\s+|\n/, ' ').strip if item.at('.education-date')

	puts "insert into Education values ( #{user_id} , #{university_id} , #{degree_id} , '#{desc}', '#{period}', '#{period}');";	
	puts "insert into University values ( #{university_id} , '#{name}');";	
	
end

skill_id = 300
endorsements = 2
skills = (mech.search('.skill-pill .endorse-item-name-text').map { |skill| skill.text.strip if skill.text } rescue nil)
skills.each do |skill|
	skill_id = skill_id + 1
	puts "insert into Has_skill values ( #{user_id} , #{skill_id}, #{endorsements});";	
	puts "insert into Skill values ( #{skill_id} , '#{skill}');";
end

group_id = 400
member_count = 0
groups = mech.search('.groups-name').map do |item|
	group_name = item.text.gsub(/\s+|\n/, ' ').strip
	group_link = "http://www.linkedin.com#{item.at('a')['href']}"

	puts "insert into Member_of values ( #{user_id} , #{group_id});";	
	puts "insert into Groups values ( #{group_id} , '#{group_name}', #{member_count});";	
end

language_id = 200
languages = mech.search('.background-languages #languages ol li').map do |item|
	language_name    = item.at('h4').text rescue nil
	# proficiency = item.at('div.languages-proficiency').text.gsub(/\s+|\n/, ' ').strip rescue nil

	puts "insert into Knows_language values ( #{user_id} , #{language_id});";	
	puts "insert into Languages values ( #{language_id} , '#{language_name}');";
end

# visitor_links = mech.search('.insights-browse-map/ul/li').map do |visitor|
# 	v = {}
# 	v[:link]    = visitor.at('a')['href']
# 	v[:name]    = visitor.at('h4/a').text
# 	v[:title]   = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ').first
# 	v[:company] = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ')[1]
# 	v
# end

# puts visitor_links

