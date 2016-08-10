FROM modpreneur/necktie:1.0

MAINTAINER Jakub Fajkus <fajkus@modpreneur.com>

# Install app
ADD . /var/app
#[RuntimeException] The .git directory is missing from dev packages
#RUN composer install --optimize-autoloader --no-scripts --prefer-dist --no-interaction \

EXPOSE 80 9003

RUN chmod +x entrypoint.sh
ENTRYPOINT ["sh", "entrypoint.sh", "service postfix start"]