##############################################################################
######			USEFUL DOCKER COMMANDS:
######
######		- docker build -t demolab-img -f Dockerfile .
######		- docker run -p 80:80 -p 3306:3306 --name=demolab -d demolab-img
######		- docker exec -it demolab /bin/bash
######		- Per svuotare la cache: docker builder prune
######		- Per elencare i volumi: docker volume ls
##############################################################################

###### Start from an official Php release with Apache ######
FROM php:8.3-apache

################# CONFIGURATIONS (define ARG after FROM) #####################
ARG WP_ARCHIVE=wordpress-6.8.3.tar.gz
ARG DLI_THEME=https://github.com/ScuolaNormaleSuperiore/design-laboratori-wordpress-theme.git
ARG ADMINER=https://github.com/vrana/adminer/releases/download/v5.4.1/adminer-5.4.1.php
ARG BASE_PLUGIN_URL=https://downloads.wordpress.org/plugin

ARG PLG_ACF_OPENSTRRETMAP=acf-openstreetmap-field.1.6.1.zip
ARG PLG_ACF=advanced-custom-fields.6.6.2.zip
ARG PLG_ARIA_LABEL=better-aria-label-support.0.1.zip
ARG PLG_POLYLANG=polylang.3.7.5.zip
ARG PLG_CAPTCHA=really-simple-captcha.zip
ARG PLG_SMTP=wp-mail-smtp.4.7.0.zip

# Plugion facoltativi o di sviluppo
ARG PLG_LOCO_TRANSLATE=loco-translate.2.8.0.zip
ARG PLG_REG_THUMBNAILS=regenerate-thumbnails.3.1.6.zip
##############################################################################


# Install useful packages
RUN apt-get update \
	&& apt-get install -y vim git bash-completion libxml2-dev unzip wget \
	# Install gd extension
	&& apt-get install -y libfreetype-dev libjpeg62-turbo-dev libpng-dev \
 	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
	# Install and enable useful modules
	&& docker-php-ext-install mysqli \
	&& docker-php-ext-install soap \
	&& a2enmod rewrite

# Copy the file of the database files.
COPY configure/createUserAndDatabase.sql /tmp/createUserAndDatabase.sql
COPY src/demolabdb.sql /tmp/demolabdb.sql
COPY configure/configureWP.sql /tmp/configureWP.sql

# Copy source code and configure.
COPY configure/startServicesScript.sh /root/startServicesScript.sh
COPY configure/wp-config.php /tmp/wp-config.php
COPY configure/.htaccess /tmp/.htaccess
COPY src/uploads.zip /tmp/uploads.zip

# Install mariadb, create and init the database.
RUN apt-get install -y mariadb-server && \
	/etc/init.d/mariadb start && \
	mysql < /tmp/createUserAndDatabase.sql && \
	mysql demolabdb < /tmp/demolabdb.sql && \
	mysql demolabdb < /tmp/configureWP.sql

# Install Wordpress and the needed plugin
WORKDIR /tmp
RUN curl -o /tmp/${WP_ARCHIVE} https://wordpress.org/${WP_ARCHIVE} && \
	rm -rf /var/www/html && \
	tar -xvzf /tmp/${WP_ARCHIVE} --directory /var/www/ && \
	mv /var/www/wordpress /var/www/html	&& \
	rm -rf /var/www/html/wp-content/themes/twentytwentyone && \
	rm -rf /var/www/html/wp-content/themes/twentytwentytwo && \
	rm -rf /var/www/html/wp-content/plugins/akismet && \
	rm -rf /var/www/html/wp-content/plugins/hello.php && \
	cd /var/www/html/wp-content/themes && \
	git clone --branch dev --single-branch ${DLI_THEME} && \
	cd /var/www/html/wp-content/plugins && \
	curl -o /tmp/${PLG_ACF_OPENSTRRETMAP} ${BASE_PLUGIN_URL}/${PLG_ACF_OPENSTRRETMAP} && \
	unzip /tmp/${PLG_ACF_OPENSTRRETMAP}  && \
	curl -o /tmp/${PLG_ACF} ${BASE_PLUGIN_URL}/${PLG_ACF} && \
	unzip /tmp/${PLG_ACF}  && \
	curl -o /tmp/${PLG_ARIA_LABEL} ${BASE_PLUGIN_URL}/${PLG_ARIA_LABEL} && \
	unzip /tmp/${PLG_ARIA_LABEL}  && \
	curl -o /tmp/${PLG_LOCO_TRANSLATE} ${BASE_PLUGIN_URL}/${PLG_LOCO_TRANSLATE} && \
	unzip /tmp/${PLG_LOCO_TRANSLATE}  && \
	curl -o /tmp/${PLG_REG_THUMBNAILS} ${BASE_PLUGIN_URL}/${PLG_REG_THUMBNAILS} && \
	unzip /tmp/${PLG_REG_THUMBNAILS}  && \
	curl -o /tmp/${PLG_POLYLANG} ${BASE_PLUGIN_URL}/${PLG_POLYLANG} && \
	unzip /tmp/${PLG_POLYLANG}  && \
	curl -o /tmp/${PLG_SMTP} ${BASE_PLUGIN_URL}/${PLG_SMTP} && \
	unzip /tmp/${PLG_SMTP}  && \
	curl -o /tmp/${PLG_CAPTCHA} ${BASE_PLUGIN_URL}/${PLG_CAPTCHA} && \
	unzip /tmp/${PLG_CAPTCHA}  && \
	cp -f /tmp/wp-config.php  /var/www/html/wp-config.php && \
	cp /tmp/.htaccess /var/www/html/.htaccess && \
	unzip /tmp/uploads.zip -d /var/www/html/wp-content/uploads/ && \
	wget ${ADMINER} -O /var/www/html/adminer.php && \
	chown -R www-data:www-data /var/www/html/* \
	# Remove the temp file.
	&& rm -rf /tmp/* \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

#Copy utility files
COPY src/phpinfo.php /var/www/html/phpinfo.php

EXPOSE 80 3306

######  Start Server Services with the bash shell ######
ENTRYPOINT ["/bin/bash"]
CMD ["/root/startServicesScript.sh"]
