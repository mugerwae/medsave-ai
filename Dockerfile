FROM php:7.4-apache-bullseye
LABEL maintainer=""
LABEL version="0.0.1"
LABEL description="Medsave"
RUN apt update
RUN apt install -y vim cron tzdata git
RUN ln -fs /usr/share/zoneinfo/Africa/Kampala /etc/localtime
RUN dpkg-reconfigure -f noninteractive tzdata
RUN docker-php-ext-install mysqli
RUN cd /root/
RUN git clone https://github.com/mugerwae/medsave-ai.git
RUN mv medsave-ai/medsave/php/* /var/www/html/
RUN rm -r medsave-ai
WORKDIR /var/www/html/