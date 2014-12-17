#!/usr/bin/env ruby

require 'mechanize'

USER_AGENTS = ['Windows IE 6', 'Windows IE 7', 'Windows Mozilla', 'Mac Safari', 'Mac FireFox', 'Mac Mozilla', 'Linux Mozilla', 'Linux Firefox', 'Linux Konqueror']

def http_client
	Mechanize.new do |agent|
		agent.user_agent_alias = USER_AGENTS.sample
		agent.max_history = 0
	end
end

profile_link = 'https://www.linkedin.com/pub/lawrence-w-wu/25/107/957'
mech = http_client.get(profile_link)
html= mech.body
puts html


