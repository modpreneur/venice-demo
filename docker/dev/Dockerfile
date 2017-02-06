FROM modpreneur/necktie-dev:1.0.16

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng12-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd


# Install app
ADD . /var/app

RUN cd web/js \
    && npm install

EXPOSE 80 9003

RUN chmod +x ./docker/dev/entrypoint.sh
ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh"]