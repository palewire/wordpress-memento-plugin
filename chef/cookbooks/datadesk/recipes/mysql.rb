# Install mysql
package "mysql-server" do
    :upgrade
end

package "libapache2-mod-auth-mysql" do
    :upgrade
end

package "libmysqlclient-dev" do
    :upgrade
end

cookbook_file "/etc/mysql/my.cnf" do
  source "mysql/my.cnf"
  mode 0640
  owner "root"
  group "root"
end

script "Restart MySQL" do
  interpreter "bash"
  user "ubuntu"
  code <<-EOH
    sudo service mysql restart
  EOH
end