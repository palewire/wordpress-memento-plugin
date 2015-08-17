# Create the apps directory where everything will go
directory "/apps/" do
    owner node[:app_user]
    group node[:app_group]
    mode 0775
end

# Install the virtualenv requirements
script "Add GitHub to known hosts" do
  interpreter "bash"
  user node[:app_user]
  group node[:app_group]
  code <<-EOH
    echo "|1|nFPVjT+tJlghvwL9SqJmckclSkI=|5HR4LAIxnl3I3cl40j5GIy+Qbwk= ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==" >> /home/#{node[:app_user]}/.ssh/known_hosts
    echo "|1|LiSuPv5jaL9TCd9Tgue5BiGAJtE=|KYW9Uqo+gzE+Z3O/0uE8d9kadm0= ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==" >> /home/#{node[:app_user]}/.ssh/known_hosts
  EOH
end

# Pull the git repo
git "/apps/#{node[:app_name]}"  do
  repository node[:app_repo]
  reference "HEAD"
  revision "master"
  user node[:app_user]
  group node[:app_group]
  action :sync
end

# Create the database user
script "Create database user" do
  interpreter "bash"
  user node[:apps_user]
  code <<-EOH
     mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO #{node[:db_user]}@'%' IDENTIFIED BY '#{node[:db_password]}'"
  EOH
  ignore_failure true
end

# Create the database
script "Create database" do
  interpreter "bash"
  user "ubuntu"
  code <<-EOH
    mysql -u #{node[:db_user]} -p#{node[:db_password]} -e "create database #{node[:db_name]}";
  EOH
end