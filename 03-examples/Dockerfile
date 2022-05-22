FROM php:8.1-cli

ARG ENVIRONMENT_NAME

COPY ./rootfilesystem/ /

RUN docker-php-ext-install sockets

RUN apt-get update && apt-get install vim -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install htop -y && \
    apt-get install vim -y && \
    apt-get install curl -y && \
    apt-get install -y supervisor && \
    apt-get install -y inotify-tools && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install swoole
RUN cd /tmp && git clone https://github.com/openswoole/swoole-src.git && \
    cd swoole-src && \
    git checkout v4.11.1 && \
    phpize  && \
    ./configure --enable-openssl --enable-http2 --enable-sockets --enable-mysqlnd --enable-swoole-json && \
    make && make install
RUN touch /usr/local/etc/php/conf.d/openswoole.ini && \
    echo 'extension=openswoole.so' > /usr/local/etc/php/conf.d/openswoole.ini

# Here goes your git configurations
# RUN git config --global user.email "" \
#     && git config --global user.name ""

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

WORKDIR "/var/www/html"