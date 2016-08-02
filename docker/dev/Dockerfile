FROM modpreneur/necktie-dev:1.0

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

# Install app
ADD . /var/app

RUN cd web/js \
    && npm install

EXPOSE 80 9003

RUN chmod +x ./docker/dev/entrypoint.sh
ENTRYPOINT ["sh", "./docker/dev/entrypoint.sh", "service postfix start"]