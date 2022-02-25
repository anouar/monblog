FROM centos:7

MAINTAINER Anouar SOUID <anouarsouid@hotmail.fr>
ARG uid=1000
ARG gid=1000

# Install Apache
RUN yum -y update
RUN yum -y install httpd httpd-tools

# Install EPEL Repo
RUN rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
 && rpm -Uvh http://rpms.remirepo.net/enterprise/remi-release-7.rpm

# Install PHP
RUN yum --enablerepo=remi-php80 -y install php php-fpm php-bcmath php-cli php-common php-gd php-intl php-ldap php-mbstring \
    php-mysqlnd php-pear php-soap php-xml php-xmlrpc php-zip php-pecl-apcu \
    && rm -rf /var/cache/yum/* \
    && yum clean all


# -----------------------------------------------------------------------------
# Virtual hosts configuration
# -----------------------------------------------------------------------------
RUN sed -E -i -e '/<Directory "\/var\/www\/html">/,/<\/Directory>/s/AllowOverride None/AllowOverride All/' /etc/httpd/conf/httpd.conf
RUN sed -E -i -e 's/DirectoryIndex (.*)$/DirectoryIndex index.php \1/g' /etc/httpd/conf/httpd.conf
ADD conf/httpd/conf.d/ /etc/httpd/conf.d

# -----------------------------------------------------------------------------
# Set default environment variables
# -----------------------------------------------------------------------------
ENV APP_HOME_DIR /var/www/html
ENV HTTPD /usr/sbin/httpd
ENV SERVICE_USER app
ENV SERVICE_USER_GROUP app
# -----------------------------------------------------------------------------
# Set work directory
# -----------------------------------------------------------------------------
WORKDIR ${APP_HOME_DIR}

# -----------------------------------------------------------------------------
# Copy files into place
# -----------------------------------------------------------------------------
COPY web ${APP_HOME_DIR}

# -----------------------------------------------------------------------------
# Install PHPUnit
# -----------------------------------------------------------------------------
RUN curl https://phar.phpunit.de/phpunit.phar -L -o phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

# -----------------------------------------------------------------------------
# Install Composer
# -----------------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php --  --install-dir=/usr/local/bin --filename=composer

# -----------------------------------------------------------------------------
# Global PHP configuration changes
# -----------------------------------------------------------------------------
RUN sed -i \
-e 's~^;date.timezone =$~date.timezone = Europe/Paris~g' \
-e 's~^;user_ini.filename =$~user_ini.filename =~g' \
-e 's~^; max_input_vars.*$~max_input_vars = 4000~g' \
-e 's~^;always_populate_raw_post_data = -1$~always_populate_raw_post_data = -1~g' \
-e 's~^upload_max_filesize.*$~upload_max_filesize = 8M~g' \
-e 's~^post_max_size.*$~post_max_size = 12M~g' \
    -e 's~^memory_limit.*$~memory_limit = -1~g' \
/etc/php.ini

# -----------------------------------------------------------------------------
# Set locale
# -----------------------------------------------------------------------------
RUN localedef -i fr_FR -f UTF-8 fr_FR.UTF-8
ENV LANG fr_FR.UTF-8

# -----------------------------------------------------------------------------
# Install Symfony CLI
# -----------------------------------------------------------------------------
RUN yum install -y wget
RUN yum install -y vim
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

EXPOSE 80 8000
WORKDIR ${APP_HOME_DIR}/
# Start Apache
CMD ["/usr/sbin/httpd","-D","FOREGROUND"]