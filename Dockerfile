FROM modpreneur/necktie:1.0.9

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

ADD . /var/app

EXPOSE 80 9003

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]