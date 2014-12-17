#!/usr/bin/env ruby

require 'mechanize'

class RetrieveProfile

	class << self; 
		attr_accessor :user_id, 
					  :university_id,
					  :university_id_mark,
					  :degree_id,
					  :degree_id_mark,
					  :skill_id,
					  :skill_id_mark,
					  :group_id,
					  :group_id_mark,
					  :language_id,
					  :language_id_mark,
					  :employer_id,
					  :employer_id_mark
	end

	@user_id = 0
	@university_id = {}
	@university_id_mark = 100
	@degree_id = {}
	@degree_id_mark = 200
	@skill_id = {}
	@skill_id_mark = 300
	@group_id = {}
	@group_id_mark = 400
	@language_id = {}
	@language_id_mark = 500
	@employer_id = {}
	@employer_id_mark = 600

	# def http_client
	#   	Mechanize.new do |agent|
	#     	agent.user_agent_alias = USER_AGENTS.sample
	#     	agent.max_history = 0
	#   	end
	# end

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
		RetrieveProfile.user_id += 1
	end

	def generate_university_id
		RetrieveProfile.university_id_mark += 1
	end

	def generate_degree_id
		RetrieveProfile.degree_id_mark += 1
	end

	def generate_skill_id
		RetrieveProfile.skill_id_mark += 1
	end

	def generate_group_id
		RetrieveProfile.group_id_mark += 1
	end

	def generate_language_id
		RetrieveProfile.language_id_mark += 1
	end

	def generate_employer_id
		RetrieveProfile.employer_id_mark += 1
	end

	def parse_skills(mech)
		mech.search('.skill-pill .endorse-item-name-text').map { |skill| skill.text.strip if skill.text } rescue nil
	end

	def parse_date(date)
	  	date = "#{date}-01-01" if date =~ /^(19|20)\d{2}$/
	  	Date.parse(date)
	end

	def generate_degree(mech)
		education = mech.search('.background-education .education').map do |item|
			university_name = item.at('h4').text.gsub(/\s+|\n/, ' ').strip if item.at('h4')
			degree_name = item.at('.degree').text.gsub(/\s+|\n/, ' ').strip if item.at('.degree')
			degree_name = degree_name.chomp(',')  # for some reason, all degree names are followed by a comma in the html
			# puts degree_name
			period = item.at('.education-date').text.gsub(/\s+|\n/, ' ').strip if item.at('.education-date')
		end

		delay_me
	end

	def generate_sql(mech)
		first_name = parse_firstname(mech)
		last_name = parse_lastname(mech)
		connections = parse_connections(mech)
		industry = parse_industry(mech)
		skills = parse_skills(mech)

		generate_user_id
		age = 0
		puts "insert into User values ( #{RetrieveProfile.user_id} , '#{first_name}' , '#{last_name}', #{connections}, #{age}, '#{industry}');"

		education = mech.search('.background-education .education').map do |item|
			university_name = item.at('h4').text.gsub(/\s+|\n/, ' ').strip if item.at('h4')
			degree_name = item.at('.degree').text.gsub(/\s+|\n/, ' ').strip if item.at('.degree')
			degree_name = degree_name.chomp(',')  # for some reason, all degree names are followed by a comma in the html			
			degree_name = degree_name.gsub(/\'/, '').strip if item.at('.degree')			
			period = item.at('.education-date').text.gsub(/\s+|\n/, ' ').strip if item.at('.education-date')

			if !RetrieveProfile.university_id.has_key?(university_name)
				RetrieveProfile.university_id[university_name] = RetrieveProfile.university_id_mark
				generate_university_id
			end

			if !RetrieveProfile.degree_id.has_key?(degree_name)
				RetrieveProfile.degree_id[degree_name] = RetrieveProfile.degree_id_mark
				generate_degree_id
			end
			puts "insert into Education values ( #{RetrieveProfile.user_id} , #{RetrieveProfile.university_id[university_name]} , #{RetrieveProfile.degree_id[degree_name]} , '#{degree_name}', '#{period}', '#{period}');"
			puts "insert into University values ( #{RetrieveProfile.university_id[university_name]} , '#{university_name}');"	
		end

		endorsements = 0

		skills.each do |skill|
			
			if !RetrieveProfile.skill_id.has_key?(skill)
				RetrieveProfile.skill_id[skill] = RetrieveProfile.skill_id_mark
				generate_skill_id
			end

			puts "insert into Has_skill values ( #{RetrieveProfile.user_id} , #{RetrieveProfile.skill_id[skill]}, #{endorsements});"
			puts "insert into Skill values ( #{RetrieveProfile.skill_id[skill]} , '#{skill}');"
		end

		member_count = 0
		groups = mech.search('.groups-name').map do |item|
			group_name = item.text.gsub(/\s+|\n/, ' ').strip
			# group_link = "http://www.linkedin.com#{item.at('a')['href']}"

			if !RetrieveProfile.group_id.has_key?(group_name)
				RetrieveProfile.group_id[group_name] = RetrieveProfile.group_id_mark
				generate_group_id
			end

			puts "insert into Member_of values ( #{RetrieveProfile.user_id} , #{RetrieveProfile.group_id[group_name]});"
			puts "insert into Groups values ( #{RetrieveProfile.group_id[group_name]} , '#{group_name}', #{member_count});"
		end

		languages = mech.search('.background-languages #languages ol li').map do |item|
			language_name    = item.at('h4').text rescue nil
			# proficiency = item.at('div.languages-proficiency').text.gsub(/\s+|\n/, ' ').strip rescue nil

			if !RetrieveProfile.language_id.has_key?(language_name)
				RetrieveProfile.language_id[language_name] = RetrieveProfile.language_id_mark
				generate_language_id
			end

			puts "insert into Knows_language values ( #{RetrieveProfile.user_id} , #{RetrieveProfile.language_id[language_name]});"
			puts "insert into Languages values ( #{RetrieveProfile.language_id[language_name]} , '#{language_name}');"
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

			if !RetrieveProfile.employer_id.has_key?(employment[:location])
				RetrieveProfile.employer_id[employment[:location]] = RetrieveProfile.employer_id_mark
				generate_employer_id
			end

			employer_name = employment[:location]
			start_date = employment[:start_date]
			end_date = employment[:end_date]
			puts "insert into Employer values ( #{RetrieveProfile.employer_id[employment[:location]]} , '#{employer_name}');"
			puts "insert into Experience values ( #{RetrieveProfile.user_id} , #{RetrieveProfile.employer_id[employment[:location]]}, '#{start_date}', '#{end_date}');"
		end
	end

	# links_arr.each do |profile_link|
	# 	# puts profile_link
	# 	mech = http_client.get(profile_link)
	# 	# html= mech.body
	# 	generate_sql(mech)

	# 	# visitor_links = mech.search('.insights-browse-map/ul/li').map do |visitor|
	# 	# 	v = {}
	# 	# 	v[:link]    = visitor.at('a')['href']
	# 	# 	v[:name]    = visitor.at('h4/a').text
	# 	# 	v[:title]   = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ').first
	# 	# 	v[:company] = visitor.at('.browse-map-title').text.gsub('...', ' ').split(' at ')[1]
	# 	# 	v
	# 	# end

	# 	delay_me
	# end


	# puts visitor_links
end

links_arr = []
count = 1
File.readlines('onelink.txt').each do |line|
	count += 1
	if "#{line}".chomp != ""
		# puts "#{line}"
		links_arr = links_arr + [line]
	end
end

USER_AGENTS = [ 'Windows IE 6',
				'Windows IE 7', 
				'Windows Mozilla', 
				'Mac Safari', 
				'Mac FireFox', 
				'Mac Mozilla', 
				'Linux Mozilla', 
				'Linux Firefox']

@agent = Mechanize.new
@agent.user_agent_alias = USER_AGENTS.sample
@agent.max_history = 0

profile = RetrieveProfile.new


links_arr.each do |profile_link|
	mech = @agent.get(profile_link)
	profile.generate_sql(mech)
	# profile.generate_degree(mech)


end


