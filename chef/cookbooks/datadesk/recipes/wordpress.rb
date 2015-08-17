# Install mysql
package "wget" do
    :upgrade
end

script "Install Wordpress" do
  interpreter "bash"
  user "root"
  code <<-EOH
    cd /var/www/html &&
    wget http://wordpress.org/latest.tar.gz &&
    tar -xzvf latest.tar.gz;
  EOH
end

template "/var/www/html/wordpress/wp-config.php" do
  source "wordpress/wp-config.php.erb"
  mode 644
  owner "root"
  group "root"
  variables({
     :db_name => node[:db_name],
     :db_user => node[:db_user],
     :db_password => node[:db_password]
  })
end

script "Permissions" do
  interpreter "bash"
  user "root"
  code <<-EOH
    chown nobody /var/www/html/wordpress/wp-config.php &&
    chgrp nogroup /var/www/html/wordpress/wp-config.php;
  EOH
end

template "/etc/apache2/sites-enabled/#{node[:app_name]}" do
  source "wordpress/vhost.erb"
  mode 0640
  owner "root"
  group "root"
  variables({
     :apache_port => node[:apache_port],
     :app_name => node[:app_name]
  })
end

script "Symbolic link to plugin code" do
  interpreter "bash"
  user "root"
  code <<-EOH
    ln -s /apps/#{node[:app_name]}/memento/ /var/www/html/wordpress/wp-content/plugins/memento
  EOH
end

script "restart-apache" do
  interpreter "bash"
  user "root"
  code <<-EOH
    apachectl restart
  EOH
end