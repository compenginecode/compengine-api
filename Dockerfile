FROM ubuntu:xenial
MAINTAINER Adrian Grayson-Widarsito <adrian@vokke.com.au>

# Install apache, PHP, and supplimentary programs. curl and lynx-cur are for debugging the container.
ENV LANG=C.UTF-8
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get upgrade -y

RUN DEBIAN_FRONTEND=noninteractive apt-get -y dist-upgrade
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install python-software-properties software-properties-common

RUN DEBIAN_FRONTEND=noninteractive add-apt-repository ppa:ondrej/php -y

RUN apt-get update
RUN apt-get -y upgrade

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y git apache2 php5.6 libapache2-mod-php5.6 php5.6-mcrypt php5.6-mysql php5.6-curl php5.6-gd libxml2-dev htop python-software-properties \
 python g++ make curl php5.6-dom php5.6-cli php5.6-json php5.6-common php5.6-mbstring php5.6-opcache php5.6-readline php5.6-bcmath php5.6-zip cron

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y libapache2-modsecurity
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y sox
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y libsox-fmt-mp3

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod proxy_http
RUN a2enmod proxy_ajp
RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2enmod headers
RUN a2enmod proxy_balancer
RUN a2enmod proxy_connect
RUN a2enmod proxy_html

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Copy in the API into a non-public web root
COPY ./api/ /var/api
COPY ./features_c/run_features /var/api/bin/

# Copy in the apache2 configuration file
COPY ./docker/apache/apache2.conf /etc/apache2/apache2.conf
COPY ./docker/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./docker/php/php.ini /etc/php/5.6/apache2/php.ini
COPY ./docker/pm2.sh /var/pm2.sh
COPY ./docker/modsecurity/modsecurity.conf /etc/modsecurity/modsecurity.conf
RUN chmod +x /var/pm2.sh

# Setup base files
RUN rm -rf /var/api/private/proxies
RUN rm -rf /var/api/private/logs
RUN rm -rf /var/api/private/temp

RUN mkdir /var/api/private/logs
RUN mkdir /var/api/private/temp
RUN mkdir /var/api/private/proxies
RUN touch /var/api/private/logs/router.log

RUN chown -R www-data:www-data /var/api/private/logs
RUN chown -R www-data:www-data /var/api/private/proxies
RUN chown -R www-data:www-data /var/api/private/temp

#Setup Node
#change it to your required node version
ENV NODE_VERSION 8.0.0
ENV NVM_DIR /home/node/.nvm

# Install nvm with node and npm
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.20.0/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && mkdir -p $NVM_DIR/versions \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/v$NODE_VERSION/bin:$PATH

RUN . $NVM_DIR/nvm.sh && npm install -g pm2

EXPOSE 80

# By default, simply start apache.
CMD /var/pm2.sh \
	&& /usr/sbin/apache2ctl -D FOREGROUND

