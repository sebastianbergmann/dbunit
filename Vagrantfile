VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "hashicorp/precise64"
  
  if Vagrant.has_plugin?("vagrant-cachier")
  end

  config.vm.provision "shell", inline: "source /vagrant/build/vagrant.sh"
end
