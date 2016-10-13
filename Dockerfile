FROM modpreneur/necktie:1.0.4

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

ADD . /var/app

EXPOSE 80 9003

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]