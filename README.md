# baumkataster-wien
A simple query into the Viennese Baumkataster

# Install
```sh
git clone https://github.com/plepe/baumkataster-wien.git
cd baumkataster-wien
git submodule init
git submodule update
git clone git://github.com/twigphp/Twig.git lib/Twig
git clone git://github.com/twigjs/twig.js lib/twig.js
cp conf.php-dist conf.php
$EDITOR conf.php # adapt values to your needs
php import.php
```

