# authorization-module
module that will handle all authorization needs


# installation
* bash
```bash
echo '127.0.0.1 authorization-module.local.net' | sudo tee -a /etc/hosts
git clone git@github.com:Neznajki/authorization-module.git
cd authorization-module
./connect-docker.sh
composer install
./bin/console c:cl
./bin/console d:m:m
```
