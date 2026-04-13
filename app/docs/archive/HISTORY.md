 1  git status
    2  history
    3  logout
    4  php artisan package:discover
    5  ll
    6  nano README.md
    7  git status
    8  git add .
    9  git commit -m 'adjustments'
   10  git push
   11  ll
   12  cd /etc/nginx
   13  ll
   14  ll sites-available/
   15  ll sites-enabled/
   16  sudo nano /etc/nginx/sites-available/houston1
   17  sudo nginx -t && sudo systemctl reload nginx
   18  ls ~/smbgen/public/
   19  cat ~/smbgen/public/index.php
   20  ll
   21  ls sites-available/
   22  ll
   23  cd sites-available/
   24  ll
   25  rm smbgen
   26  sudo rm smbgen
   27  cd ..
   28  cd sites-enabled/
   29  ll
   30  sudo rm smbgen
   31  ll
   32  history
   33  sudo nginx -t && sudo systemctl reload nginx
   34  ll
   35  ll ../sites-available/
   36  cat ../sites-available/houston1
   37  sudo nano ../sites-available/houston1
   38  git status
   39  sudo systemctl reload nginx
   40  sudo nano houston1
   41  ll
   42  cd ../sites-available/
   43  ll
   44  sudo nano houston1
   45  sudo nginx -t
   46  sudo systemctl reload nginx
   47  cat /etc/nginx/sites-available/houston1 | grep root
   48  sudo chown -R www-data:www-data /home/alex/smbgen
   49  sudo find /home/alex/smbgen -type d -exec chmod 755 {} \;
   50  sudo find /home/alex/smbgen -type f -exec chmod 644 {} \;
   51  sudo nginx -t
   52  sudo systemctl reload nginx
   53  cd ~/smbgen/
   54  ll
   55  php artisan config:clear
   56  phgp artison route:clear
   57  php artisan route:clear
   58  php artisan serve
   59  cat /etc/nginx/sites-available/houston1 | grep root
   60  ls -l /home/alex/smbgen/public/index.php
   61  php artisan route:list
   62  tail -n 50 storage/logs/laravel.log
   63  curl -I http://localhost
   64  sudo systemctl reload nginx
   65  sudo systemctl restart php8.3-fpm
   66  ls resources/views/welcome.blade.php
   67  php
   68  php artisan make:view welcome
   69  ll
   70  ls public/
   71  cd ..
   72  ll
   73  cd /etc/nginx
   74  ll
   75  cd conf.d/
   76  ll
   77  cd ..
   78  ls sites-available/
   79  ls sites-enabled/
   80  ls -al sites-enabled/
   81  ls -al sites-available/
   82  cat sites-available/houston1
   83  ls ~/smbgen/
   84  ls -ltra ~/smbgen/
   85  ll
   86  cd ~/smbgen/
   87  ll
   88  cd routes/
   89  ll
   90  nano web.php
   91  cd ..
   92  ls resources/views/welcome.blade.php
   93  cat resources/views/welcome.blade.php
   94  ll
   95  nano .env
   96  sudo nano .env
   97  php artisan config:cache
   98  sudo php artisan config:cache
   99  sudo systemctl restart php8.3-fpm
  100  sudo systemctl reload nginx
  101  tail -n 40 /var/log/nginx/error.log
  102  sudo tail -n 40 /var/log/nginx/error.log
  103  sudo chown -R www-data:www-data /home/alex/smbgen
  104  sudo find /home/alex/smbgen -type d -exec chmod 755 {} \;
  105  sudo find /home/alex/smbgen -type f -exec chmod 644 {} \;
  106  sudo systemctl reload nginx
  107  php
  108  cd ..
  109  sudo rm -rf ~/smbgen/
  110  composer create-project laravel/laravel smbgen
  111  sudo chown -R www-data:www-data ~/smbgen
  112  sudo find ~/smbgen -type d -exec chmod 755 {} \;
  113  sudo find ~/smbgen -type f -exec chmod 644 {} \;
  114  sudo chmod -R 775 ~/smbgen/storage ~/smbgen/bootstrap/cache
  115  cd ~/smbgen
  116  cp .env.example .env
  117  sudo cp .env.example .env
  118  php artisan key:generate
  119  nano .env
  120  sudo php artisan key:generate
  121  sudo nano .env
  122  php artisan config:clear
  123  php artisan route:clear
  124  php artisan view:clear
  125  php artisan optimize
  126  sudo php artisan optimize
  127  sudo systemctl restart php8.3-fpm
  128  sudo systemctl reload nginx
  129  grep root /etc/nginx/sites-available/houston1
  130  ls -l /home/alex/smbgen/public/index.php
  131  sudo php artisan route:list
  132  php artisan route:list
  133  tail -n 40 /var/log/nginx/error.log
  134  sudo tail -n 40 /var/log/nginx/error.log
  135  sudo chown -R www-data:www-data /home/alex/smbgen
  136  sudo find /home/alex/smbgen -type d -exec chmod 755 {} \;
  137  sudo find /home/alex/smbgen -type f -exec chmod 644 {} \;
  138  sudo chmod -R 775 /home/alex/smbgen/storage /home/alex/smbgen/bootstrap/cache
  139  sudo systemctl restart php8.3-fpm
  140  sudo systemctl reload nginx
  141  ls -l /home/alex/smbgen/public/index.php
  142  ps aux | grep nginx
  143  ls -l /home/alex/smbgen/public/index.php
  144  history
  145  tail -n 40 /var/log/nginx/error.log
  146  sudo tail -n 40 /var/log/nginx/error.log
  147  sudo -u www-data cat /home/alex/smbgen/public/index.php
  148  sudo chown -R www-data:www-data /home/alex/smbgen
  149  sudo find /home/alex/smbgen -type d -exec chmod 755 {} \;
  150  sudo find /home/alex/smbgen -type f -exec chmod 644 {} \;
  151  sudo chmod -R 775 /home/alex/smbgen/storage /home/alex/smbgen/bootstrap/cache
  152  sudo systemctl restart php8.3-fpm
  153  sudo systemctl reload nginx
  154  sudo -u www-data cat /home/alex/smbgen/public/index.php
  155  sudo chmod 711 /home/alex
  156  sudo -u www-data cat /home/alex/smbgen/public/index.php
  157  sudo apt install php8.3-sqlite3
  158  sudo systemctl restart php8.3-fpm
  159  php -m | grep sqlite
  160  sudo php artisan migrate
  161  cat .env
  162  nano resources/views/welcome.blade.php
  163  sudo nano resources/views/welcome.blade.php
  164  git init
  165  sudo git init
  166  sudo git add .
  167  git config --global --add safe.directory /home/alex/smbgen
  168  git commit -m "Initial Laravel commit"
  169  sudo git commit -m "Initial Laravel commit"
  170  sudo git config --global --add safe.directory /home/alex/smbgen
  171  sudo git commit -m "Initial Laravel commit"
  172  git remote add origin git@github.com:alexramsey92/smbgen.git
  173  sudo git remote add origin git@github.com:alexramsey92/smbgen.git
  174  git push -u origin main
  175  sudo git push
  176  sudo git push --set-upstream origin master
  177  git remote add origin git@github.com:alexramsey92/smbgen.git
  178  code .
  179  git remote remove origin
  180  sudo git remote remove origin
  181  sudo git remote add origin git@github.com:alexramsey92/smbgen.git
  182  sudo git push
  183  sudo git push --set-upstream origin master
  184  git branch -M main
  185  sudo git branch -M main
  186  sudo git remote add origin git@github.com:alexramsey92/smbgen.git
  187  sudo git push -u origin main
  188  git status
  189  git add .
  190  sudo git add .
  191  git status
  192  git commit -m 'first commit'
  193  sudo git commit -m 'first'
  194  history
  195  sudo git push -u origin main
  196  ls .ssh
  197  ssh-keygen -t ed25519 -C "houston1.oldlinecyber.com"
  198  cat ~/.ssh/id_ed25519.pub
  199  ssh -T git@github.com
  200  git push -u origin main
  201  sudo git push -u origin main
  202  sudo ssh-keygen -t ed25519 -C "deploy@smbgen" -f ~/.ssh/smbgen_deploy_key
  203  sudo cat ~/.ssh/smbgen_deploy_key.pub
  204  nano ~/.ssh/config
  205  cd ..
  206  nano ~/deploy-smbgen.sh
  207  sudo chmod +x ~/deploy-smbgen.sh
  208  ~/deploy-smbgen.sh
  209  sudo ~/deploy-smbgen.sh
  210  nano ~/deploy-smbgen.sh
  211  ~/deploy-smbgen.sh
  212  nano ~/deploy-smbgen.sh
  213  ~/deploy-smbgen.sh
  214  nano ~/deploy-smbgen.sh
  215  ~/deploy-smbgen.sh
  216  sudo chown -R alex:alex ~/smbgen
  217  ~/deploy-smbgen.sh
  218  sudo chown -R www-data:www-data /home/alex/smbgen/storage /home/alex/smbgen/bootstrap/cache
  219  sudo chmod -R 775 /home/alex/smbgen/storage /home/alex/smbgen/bootstrap/cache
  220  sudo systemctl restart php8.3-fpm
  221  sudo systemctl reload nginx
  222  sudo chown www-data:www-data /home/alex/smbgen/database/database.sqlite
  223  sudo chmod 664 /home/alex/smbgen/database/database.sqlite
  224  sudo chmod 711 /home/alex
  225  sudo systemctl restart php8.3-fpm
  226  sudo systemctl reload nginx
  227  ls -l /home/alex/smbgen/database/database.sqlite
  228  ls -ld /home/alex/smbgen/database
  229  sudo chmod 755 /home/alex/smbgen/database
  230  sudo chmod 755 /home/alex/smbgen
  231  sudo chmod 711 /home/alex
  232  sudo systemctl restart php8.3-fpm
  233  sudo -u www-data touch /home/alex/smbgen/database/__test.sqlite
  234  sudo chown -R www-data:www-data /home/alex/smbgen/database
  235  sudo chmod -R 775 /home/alex/smbgen/database
  236  sudo chmod 711 /home/alex
  237  sudo -u www-data touch /home/alex/smbgen/database/__test.sqlite
  238  history
  239  git status
  240  cd smbgen/
  241  git stauts
  242  git status
  243  git add .
  244  git commit -m 'sqlite'
  245  git config --global user.name "Alexander Ramsey"
  246  git config --global user.email alexramsey92@gmail.com
  247  git commit --amend --reset-author
  248  git status
  249  git push
  250  git push --set-upstream origin main
  251  sudo nano resources/views/welcome.blade.php
  252  php artisan make:migration create_clients_table
  253  sudo chgrp -R www-data ~/smbgen
  254  sudo chmod -R g+w ~/smbgen
  255  php artisan make:migration create_clients_table
  256  sudo groupadd laravel
  257  sudo usermod -aG laravel alex
  258  sudo usermod -aG laravel www-data
  259  sudo chown -R alex:laravel ~/smbgen
  260  sudo find ~/smbgen -type d -exec chmod 2775 {} \;
  261  sudo find ~/smbgen -type f -exec chmod 664 {} \;
  262  sudo nano ~/.bashrc
  263  php artisan make:migration create_clients_table
  264  nano database/migrations/2025_05_06_000121_create_clients_table.php
  265  php artisan migrate
  266  nano routes/web.php
  267  nano resources/views/welcome.blade.php
  268  php artisan tinker
  269  sudo chown -R www-data:www-data /home/alex/smbgen/storage
  270  sudo chown -R www-data:www-data /home/alex/smbgen/bootstrap/cache
  271  sudo chmod -R 775 /home/alex/smbgen/storage
  272  sudo chmod -R 775 /home/alex/smbgen/bootstrap/cache
  273  sudo chgrp -R laravel /home/alex/smbgen
  274  sudo chmod -R g+w /home/alex/smbgen
  275  sudo systemctl restart php8.3-fpm
  276  nano routes/web.php
  277  cat routes/web.php
  278  grep "Your Clients" resources/views/welcome.blade.php
  279  nano resources/views/welcome.blade.php
  280  php artisan view:clear
  281  nano routes/web.php
  282  php artisan route:list | grep GET
  283  nano resources/views/welcome.blade.php
  284  php artisan tinker
  285  php artisan make:model Client
  286  php artisan tinker
  287  cat .env | grep DB_
  288  lkl
  289  ll
  290  nano resources/views/welcome.blade.php
  291  ls storage/
  292  ll
  293  php artisan config:clear
  294  cat .env
  295  nano .env
  296  touch database/database.sqlite
  297  php artisan config:clear
  298  php artisan migrate
  299  php artisan tinker
  300  sqlite3 database/database.sqlite
  301  sudo apt install sqlite3
  302  sqlite3 database/database.sqlite
  303  nano resources/views/welcome.blade.php
  304  ll
  305  nano routes/console.php
  306  nano routes/web.php
  307  php artisan view:clear
  308  sudo systemctl restart php8.3-fpm
  309  nano resources/views/welcome.blade.php
  310  php artisan view:clear
  311  nano routes/web.php
  312  php artisan route:clear
  313  php artisan view:clear
  314  php artisan config:clear
  315  sudo systemctl restart php8.3-fpm
  316  nano routes/web.php
  317  php artisan route:list
  318  nano routes/web.php
  319  sudo systemctl restart php8.3-fpm
  320  grep -rn "Route::view" routes/
  321  cat routes/web.php
  322  php artisan route:clear
  323  php artisan view:clear
  324  php artisan config:clear
  325  cat /etc/nginx/sites-available/houston1
  326  php artisan route:list
  327  nano routes/web.php
  328  sudo systemctl restart php8.3-fpm
  329  nano routes/web.php
  330  php artisan route:cache
  331  php artisan cache:clear
  332  php artisan config:clear
  333  php artisan route:clear
  334  php artisan view:clear
  335  rm -f bootstrap/cache/*.php
  336  sudo rm -f bootstrap/cache/*.php
  337  php artisan config:cache
  338  nano .env
  339  php artisan config:clear
  340  php artisan config:cache
  341  sudo chown -R www-data:www-data storage bootstrap/cache
  342  sudo chmod -R ug+rwX storage bootstrap/cache
  343  php artisan config:clear
  344  php artisan config:cache
  345  php artisan route:list
  346  nano routes/web.php
  347  php artisan route:list
  348  git status
  349  git add .
  350  git status
  351  git commit -m 'clients'
  352  git push
  353  nano resources/views/login.blade.php
  354  nano routes/web.php
  355  nano resources/views/login.blade.php
  356  git status
  357  git add .
  358  git commit -m 'login'
  359  git push
  360  nano resources/views/login.blade.php
  361  git add .
  362  git commit -m 'bg'
  363  git push
  364  git status
  365  composer require laravel/breeze --dev
  366  sudo composer require laravel/breeze --dev
  367  sudo chown -R www-data:www-data storage bootstrap/cache
  368  sudo chmod -R 775 storage bootstrap/cache
  369  php artisan package:discover
  370  sudo touch storage/logs/laravel.log
  371  sudo chown -R www-data:www-data storage bootstrap/cache
  372  sudo chmod -R 775 storage bootstrap/cache
  373  php artisan package:discover
  374  sudo chown -R www-data:www-data .
  375  sudo find . -type d -exec chmod 775 {} \;
  376  sudo find . -type f -exec chmod 664 {} \;
  377  php artisan package:discover
  378  ls -al /home/alex/smbgen/storage/logs/laravel.log
  379  php artisan package:discover
  380  sudo usermod -aG www-data alex
  381  newgrp www-data
  382  clear
  383  sudo apt install -y nginx mysql-server php php-fpm php-mysql php-cli php-curl php-mbstring php-xml php-bcmath php-zip unzip git curl
  384  curl -sS https://getcomposer.org/installer | php
  385  sudo mv composer.phar /usr/local/bin/composer
  386  composer --version
  387  composer create-project laravel/laravel smbgen
  388  ll
  389  sudo chown -R www-data:www-data ~/smbgen
  390  sudo chmod -R 775 ~/smbgen/storage ~/smbgen/bootstrap/cache
  391  sudo nano /etc/nginx/sites-available/smbgen
  392  sudo ln -s /etc/nginx/sites-available/smbgen /etc/nginx/sites-enabled/
  393  sudo nginx -t
  394  sudo systemctl reload nginx
  395  sudo systemctl start nginx
  396  journalctl -xeu nginx.service
  397  sudo systemctl reload nginx
  398  sudo systemctl enable nginx
  399  sudo systemctl start nginx
  400  systemctl status nginx.service
  401  cat /etc/nginx
  402  cat /etc/nginx/sites-available/
  403  cat /etc/nginx/sites-available/smbgen
  404  sudo nginx
  405  sudo lsof -i :80
  406  systemctl status http
  407  systemctl status apache
  408  systemctl status apache2
  409  systemctl disable apache2
  410  sudo systemctl stop apache2
  411  sudo systemctl start nginx
  412  sudo systemctl status nginx
  413  ls /home/alex/smbgen/public
  414  sudo systemctl status php8.3-fpm
  415  sudo apt install certbot python3-certbot-nginx
  416  sudo certbot --nginx -d houston1.smbgen.app
  417  hostnamectl
  418  sudo hostnamectl set-hostname houston1.oldlinecyber.com
  419  sudo nano /etc/hosts
  420  hostnamectl
  421  ping houston1.oldlinecyber.com
  422  sudo ufw status
  423  history
  424  sudo certbot --nginx -d houston1.smbgen.app
  425  sudo systemctl stop apache2
  426  sudo systemctl disable apache2
  427  sudo apt purge apache2 apache2-utils apache2-bin apache2-data -y
  428  sudo apt autoremove
  429  sudo systemctl restart nginx
  430  sudo lsof -i :80
  431  sudo certbot --nginx -d houston1.oldlinecyber.com
  432  sudo rm /etc/nginx/sites-enabled/default
  433  sudo systemctl reload nginx
  434  sudo certbot --nginx -d houston1.oldlinecyber.com
  435  ls -l /etc/nginx/sites-enabled/
  436  sudo nano /etc/nginx/sites-available/smbgen
  437  sudo systemctl status php8.3-fpm
  438  sudo systemctl reload nginx
  439  sudo nano /etc/nginx/sites-available/smbgen
  440  sudo tail -n 40 /var/log/nginx/error.log
  441  sudo ln -sf /etc/nginx/sites-available/smbgen /etc/nginx/sites-enabled/smbgen
  442  ls -ld /home/alex/smbgen/public
  443  sudo chown -R www-data:www-data /home/alex/smbgen
  444  sudo chmod -R 755 /home/alex/smbgen
  445  cd smbgen/
  446  composer install
  447  sudo tail -n 40 /var/log/nginx/error.log
  448  sudo tail -f 40 /var/log/nginx/error.log
  449  sudo chown -R www-data:www-data /home/alex/smbgen
  450  sudo find /home/alex/smbgen -type d -exec chmod 755 {} \;
  451  sudo find /home/alex/smbgen -type f -exec chmod 644 {} \;
  452  sudo systemctl restart php8.3-fpm
  453  sudo systemctl reload nginx
  454  systemctl status nginx
  455  cd /etc/nginx/sites-available/
  456  ll
  457  rm default
  458  sudo rm default
  459  cd ..
  460  cd sites-enabled/
  461  ll
  462  cd ~/smbgen/
  463  ll
  464  history
  465  hostnamectl
  466  ll
  467  cd ..
  468  sudo nano /etc/nginx/sites-available/houston1
  469  sudo mkdir -p /var/www/houston1/public
  470  echo "<h1>Welcome to houston1.oldlinecyber.com</h1>" | sudo tee /var/www/houston1/public/index.html
  471  sudo chown -R www-data:www-data /var/www/houston1
  472  sudo ln -s /etc/nginx/sites-available/houston1 /etc/nginx/sites-enabled/
  473  sudo nginx -t
  474  sudo systemctl reload nginx
  475  dig +short houston1.oldlinecyber.com
  476  ip -6 addr | grep inet6
  477  dig AAAA houston1.oldlinecyber.com
  478  sudo certbot --nginx -d houston1.oldlinecyber.com
  479  sudo nano /etc/nginx/sites-available/houston1
  480  cd smbgen/
  481  git init
  482  sudo git init
  483  git add .
  484  sudo -i
  485  logout
  486  history
  487  git status
  488  ll
  489  cd smbgen/
  490  ll
  491  git status
  492  git pull
  493  npm install
  494  history
  495  ll
  496  ll /var/www/html
  497  npm install
  498  sudo apt install npm
  499  git pull
  500  ll
  501  cat .env
  502  php artisan migrate:fresh --seed
  503  git pull
  504  git pull
  505  cd smbgen/
  506  git pull
  507  git status
  508  ll
  509  vim .env
  510  php artisan config:clear && php artisan config:cache
  511  git status
  512  git pull
  513  sudo systemctl list-timers | grep certbot
  514  git pull
  515  php artisan migrate
  516  php artisan db:seed
  517  php artisan migrate:fresh --seed
  518  git pull
  519  cd smbgen/
  520  git pull
  521  npm install
  522  npm run build
  523  git pull
  524  history
  525  ll
  526  cd smbgen/
  527  ll
  528  git status
  529  git pull
  530  ll
  531  vim .env
  532  git pull
  533  ]tail -n 50 storage/logs/laravel.log
  534  tail -n 50 storage/logs/laravel.log
  535  git pull
  536  tail -n 50 storage/logs/laravel.log
  537  tail -n 200 storage/logs/laravel.log | less
  538  php artisan migrate --force
  539  cat .env | grep GOOGLE
  540  vim .env
  541  php artisan config:clear
  542  php artisan config:cache
  543  php artisan route:clear
  544  tail -n 50 storage/logs/laravel.log
  545  tail -f -n 50 storage/logs/laravel.log
  546  composer require laravel/socialite
  547  composer install
  548  tail -f -n 50 storage/logs/laravel.log
  549  tail -n 100 storage/logs/laravel.log | less
  550  git pull
  551  tail -f storage/logs/laravel.log
  552  history
  553  php artisan route:clear
  554  php artisan config:clear
  555  php artisan cache:clear
  556  git pull
  557  tail -n 50 storage/logs/laravel.log
  558  tail -n 150 storage/logs/laravel.log
  559  composer require laravel/socialite
  560  php artisan config:clear
  561  php artisan cache:clear
  562  tail -n 150 storage/logs/laravel.log
  563  ll
  564  cd storage/
  565  ll
  566  cd logs/
  567  ll
  568  cat google_callback_raw.log
  569  git pull
  570  php artisan route:clear
  571  php artisan config:clear
  572  php artisan cache:clear
  573  cd ..
  574  php artisan cache:clear
  575  git pull
  576  cd storage/logs/
  577  cat google_callback_raw.log
  578  ll
  579  cat laravel.log
  580  cd ../..
  581  git pull
  582  php artisan route:clear
  583  php artisan config:clear
  584  php artisan cache:clear
  585  tail -n 150 storage/logs/laravel.log
  586  tail storage/logs/laravel.log
  587  tail -f -n 65 storage/logs/laravel.log
  588  tail -f /var/log/nginx/access.log
  589  sudo -i
  590  git pull
  591  history
  592  php artisan --version
  593  php artisan config:cache
  594  php artisan route:cache
  595  php artisan view:cache
  596  php artisan route:list
  597  php artisan env
  598  php artisan schedule:list
  599  php artisan migrate:status
  600  git pull
  601  tail -f -n 65 storage/logs/laravel.log
  602  clear
  603  git pull
  604  tail -f -n 65 storage/logs/laravel.log
  605  tail -f /var/log/nginx/access.log
  606  sudo -i
  607  git pull
  608  tail -f -n 65 storage/logs/laravel.log
  609  git pull
  610  tail -f -n 65 storage/logs/laravel.log
  611  sudo -i
  612  ll
  613  cd smbgen/
  614  ll
  615  history
  616  tail -f -n 65 storage/logs/laravel.log
  617  ll
  618  cd smbgen/
  619  ll
  620  git pull
  621  php artisan migrate:fresh --seed
  622  git pull
  623  cd smbgen/
  624  git pull
  625  cd smbgen/
  626  git pull
  627  ll
  628  cat .env
  629  cat storage/logs/
  630  cat storage/logs/laravel.log
  631  ll
  632  cat .env
  633  git pull
  634  cat storage/logs/laravel.log
  635  head  storage/logs/laravel.log
  636  less  storage/logs/laravel.log
  637  php artisan migrate:fresh --seed
  638  less  storage/logs/laravel.log
  639  git pull
  640  tail -f storage/logs/laravel.log
  641  cat routes/web.php
  642  git pull
  643  tail -f storage/logs/laravel.log
  644  uptime
  645  df -h
  646  top -n 5 | head -20
  647  sudo journalctl -xe
  648  sudo systemctl status nginx
  649  tail -n 50 storage/logs/laravel.log
  650  sudo ufw status verbose
  651  sudo apt install fail2ban
  652  sudo ufw allow 80/tcp
  653  sudo ufw status verbose
  654  systemctl status httpd
  655  systemctl status nginx
  656  php artisan route:list
  657  php artisan config:clear
  658  php artisan cache:clear
  659  php artisan view:clear
  660  cat /etc/nginx/sites-enabled/default
  661  which nginx
  662  cd /usr/sbin/nginx
  663  ll
  664  history