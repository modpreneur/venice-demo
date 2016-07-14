FROM modpreneur/necktie-dev:0.4.1

MAINTAINER Martin Kolek <kolek@modpreneur.com>

# Install app
ADD . /var/app

RUN docker-php-ext-install pdo_mysql

# Install cron
RUN apt-get install cron

RUN cd web/js \
    && npm install

RUN chmod +x ./docker/dev/entrypoint.sh

ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh", "service postfix start"]