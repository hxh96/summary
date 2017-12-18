±‡“ÎApache
./configure --prefix=/usr/local/lamp/apache2 --enable-modules=all --enable-so --with-apr=/usr/local/apr/ --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre/

±‡“Îphp
./configure --prefix=/usr/local/lamp/php --with-apxs2=/usr/local/lamp/apache2/bin/apxs --with-mysql=mysqlnd --with-pdo-mysql=mysqlnd --with-mysqli=mysqlnd --with-freetype-dir=/usr/local/freetype --with-gd=/usr/local/gd/ --with-zlib --with-jpeg-dir=/usr/local/jpeg --with-png-dir=/usr/local/png --enable-mbstring=all --enable-mbregex --enable-shared --with-libxml-dir=/usr/local --with-xpm-dir=/usr/lib

±‡“Îmysql
cmake -DCMAKE_INSTALL_PREFIX=/usr/local/lamp/mysql -DMYSQL_UNIX_ADDR=/tmp/mysql.sock -DDEFAULT_CHARSET=utf8 -DDEFAULT_COLLATION=utf8_general_ci -DWITH_EXTRA_CHARSETS:STRING=utf8,gbk -DWITH_MYISAM_STORAGE_ENGINE=1 -DWITH_INNOBASE_STORAGE_ENGINE=1 -DWITH_READLINE=1 -DENABLED_LOCAL_INFILE=1 -DMYSQL_DATADIR=/usr/local/lamp/mysql/data -DMYSQL_USER=mysql




