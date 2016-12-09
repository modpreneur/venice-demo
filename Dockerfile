FROM modpreneur/apache-framework:1.0.3

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

ADD . /var/app

RUN apt-get update && apt-get -y install supervisor

EXPOSE 80 9003

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh"]