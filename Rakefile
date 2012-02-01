begin
	require 'tasks/standalone_migrations'

	if(ENV['TABLE_PREFIX'])
		ActiveRecord::Base.table_name_prefix = ENV['TABLE_PREFIX']
	end
	
rescue LoadError => e
	puts "gem install standalone_migrations to get db:migrate:* tasks! (Error: #{e})"
end