# Install php
package "php5" do
    :upgrade
end

# Install mysql connections
package "php5-mysql" do
    :upgrade
end

script "restart-apache" do
  interpreter "bash"
  user "root"
  code <<-EOH
    apachectl restart
  EOH
end