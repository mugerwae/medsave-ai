FROM php:7.4-bullseye
LABEL maintainer=""
LABEL version="0.0.1"
LABEL description="Medsa"

RUN apt update
RUN apt install -y vim cron tzdata
RUN ln -fs /usr/share/zoneinfo/Africa/Kampala /etc/localtime
RUN dpkg-reconfigure -f noninteractive tzdata
RUN docker-php-ext-install mysqli
WORKDIR /var/www/html/

ADD medsave .