#!/usr/bin/env ruby

require 'mechanize'

links_arr = []
File.readlines('links.txt').each do |line|
	if "#{line}".chomp != ""
		# puts "#{line}"
		links_arr = links_arr + [line]		
	end
end

USER_AGENTS = ['Windows IE 6', 'Windows IE 7', 'Windows Mozilla', 'Mac Safari', 'Mac FireFox', 'Mac Mozilla', 'Linux Mozilla', 'Linux Firefox', 'Linux Konqueror']

@user_id = 0
@university_id = 100
@degree_id = 200
@skill_id = 300
@group_id = 400
@language_id = 500
@employer_id = 600

def http_client
  	Mechanize.new do |agent|
    	agent.user_agent_alias = USER_AGENTS.sample
    	agent.max_history = 0
  	end
end

def delay_me
	sleep(10.0)   # sleep for 10 seconds between queries
end

def parse_firstname(mech)
	mech.at('.full-name').text.split(' ', 2)[0].strip if mech.at('.full-name')
end

def parse_lastname(mech)
	mech.at('.full-name').text.split(' ', 2)[1].strip if mech.at('.full-name')
end

def parse_connections(mech)
	(mech.at('.member-connections').text if mech.at('.member-connections')).gsub(/[^0-9]/, '')
end

def parse_industry(mech)
	mech.at('.industry').text.gsub(/\s+/, ' ').strip if mech.at('.industry')
end

def generate_user_id
	@user_id = @user_id + 1
end

def generate_university_id
	@university_id = @university_id + 1
end

def generate_degree_id
	@degree_id = @degree_id + 1
end

def generate_skill_id
	@skill_id = @skill_id + 1
end

def generate_group_id
	@group_id = @group_id + 1
end

def generate_language_id
	@language_id = @language_id + 1
end

def generate_employer_id
	@employer_id = @employer_id + 1
end

def parse_skills(mech)
	mech.search('.skill-pill .endorse-item-name-text').map { |skill| skill.text.strip if skill.text } rescue nil
end

def parse_date(date)
  	date = "#{date}-01-01" if date =~ /^(19|20)\d{2}$/
  	Date.parse(date)
end

def generate_sql(mech)
	first_name = parse_firstname(mech)
	last_name = parse_lastname(mech)
	connections = parse_connections(mech)
	industry = parse_industry(mech)
	skills = parse_skills(mech)

	generate_user_id
	age = 0
	puts "insert into User values ( #{@user_id} , '#{first_name}' , '#{last_name}', #{connections}, #{age}, '#{industry}');"

	education = mech.search('.background-education .education').map do |item|
		university_name   = item.at('h4').text.gsub(/\s+|\n/, ' ').strip if item.at('h4')
		desc   = item.at('h5').text.gsub(/\s+|\n/, ' ').strip if item.at('h5')
		period = item.at('.education-date').text.gsub(/\s+|\n/, ' ').strip if item.at('.education-date')

		generate_university_id
		generate_degree_id
		puts "insert into Education values ( #{@user_id} , #{@university_id} , #{@degree_id} , '#{desc}', '#{period}', '#{period}');"
		puts "insert into University values ( #{@university_id} , '#{university_name}');"	
	end

	endorsements = 0

	skills.each do |skill|
		generate_skill_id
		puts "insert into Has_skill values ( #{@user_id} , #{@skill_id}, #{endorsements});"
		puts "insert into Skill values ( #{@skill_id} , '#{skill}');"
	end

	member_count = 0
	groups = mech.search('.groups-name').map do |item|
		group_name = item.text.gsub(/\s+|\n/, ' ').strip
		group_link = "http://www.linkedin.com#{item.at('a')['href']}"

		generate_group_id
		puts "insert into Member_of values ( #{@user_id} , #{@group_id});"
		puts "insert into Groups values ( #{@group_id} , '#{group_name}', #{member_count});"
	end

	languages = mech.search('.background-languages #languages ol li').map do |item|
		language_name    = item.at('h4').text rescue nil
		# proficiency = item.at('div.languages-proficiency').text.gsub(/\s+|\n/, ' ').strip rescue nil

		generate_language_id
		puts "insert into Knows_language values ( #{@user_id} , #{@language_id});"
		puts "insert into Languages values ( #{@language_id} , '#{language_name}');"
	end

	employments = []
	if mech.search(".background-experience .current-position").first
		mech.search(".background-experience .current-position").each do |experience|

		employment = {}
		employment[:type] = experience.at('h4').text.gsub(/\s+|\n/, ' ').strip if experience.at('h4')
		employment[:location] = experience.at('h4').next.text.gsub(/\s+|\n/, ' ').strip if experience.at('h4').next
		start_date, end_date = experience.at('.experience-date-locale').text.strip.split(" – ") rescue nil
		employment[:start_date] = parse_date(start_date) rescue nil
		employment[:end_date] = parse_date(end_date) rescue nil

		employments << employment
		end
	end
	if mech.search(".background-experience .past-position").first
		mech.search(".background-experience .past-position").each do |experience|

		employment = {}
		employment[:type] = experience.at('h4').text.gsub(/\s+|\n/, ' ').strip if experience.at('h4')
		employment[:location] = experience.at('h4').next.text.gsub(/\s+|\n/, ' ').strip if experience.at('h4').next		
		start_date, end_date  = experience.at('.experience-date-locale').text.strip.split(" – ") rescue nil
		employment[:start_date] = parse_date(start_date) rescue nil
		employment[:end_date] = parse_date(end_date) rescue nil

		employments << employment
		end
	end

	employments.each do |employment|
		generate_employer_id
		employer_name = employment[:location]
		start_date = employment[:start_date]
		end_date = employment[:end_date]
		puts "insert into Employer values ( #{@employer_id} , '#{employer_name}');"
		puts "insert into Experience values ( #{@user_id} , #{@employer_id}, '#{start_date}', '#{end_date}');"
	end
end

links_arr.each do |profile_link|
	# puts profile_link
	mech = http_client.get(profile_link)
	# html= mech.body
	generate_sql(mech)

	visitor_links = mech.search('.insights-browse-map/ul/li').map do |visitor|
		v = {}
		v[:link]    = visitor.at('a')['href']
		v[:name]    = visitor.at('h4/a').text
		v[:title]   = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ').first
		v[:company] = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ')[1]
		v
	end

	delay_me
end


# puts visitor_links

