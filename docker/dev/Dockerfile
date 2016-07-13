FROM modpreneur/necktie-dev:0.4.1

MAINTAINER Martin Kolek <kolek@modpreneur.com>

# Install app
ADD . /var/app

RUN docker-php-ext-install pdo_mysql

# Install cron
RUN apt-get install cron

RUN cd web/js \
    && jspm config registries.github.auth "dmxjZWs6NjgwOGM3MzVkZDkwMDZjNjBiOWRmM2RjYjc5MTI5OGUwMjkxNjgzZg==" \
    && jspm install -y \
    && nodejs ./scripts/buildBundles.js

composer config -g github-oauth.github.com 9b41dc4199ceb4611598caa882e06115931d85f8

RUN chmod +x ./docker/dev/entrypoint.sh

ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh", "service postfix start"]