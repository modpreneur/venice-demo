FROM modpreneur/necktie:1.0.9

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng12-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd

RUN apt-get update && apt-get -y install \
    nano \
    && echo "max_execution_time=60" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit=-1" >> /usr/local/etc/php/php.ini

ADD . /var/app

EXPOSE 80 9003

ENV TERM xterm

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]