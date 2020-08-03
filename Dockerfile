# docker image build -t ubuntu20-nginx .
# docker run --name groupsplanner_api -dit -p 8080:8080 -v /mnt/DATA/Zakelijk/Code/Web/projects/groupsplanner:/var/www ubuntu20-nginx:latest
FROM ubuntu:20.04
MAINTAINER Sander Tuinstra
# Don't ask for country
ENV DEBIAN_FRONTEND=noninteractive

RUN apt update
RUN apt upgrade -y
RUN apt autoremove -y
RUN apt update

RUN apt install -y -q \
        nginx \
        php7.4 \
        php7.4-common \
        php7.4-fpm \
        php7.4-cli \
        php7.4-mysql \
        mysql-server

# Make nginx pass PHP Scripts.
RUN echo 'rm -f /etc/nginx/sites-available/default' > /start.sh
RUN echo 'cp -f /var/www/default /etc/nginx/sites-available/' >> /start.sh
RUN echo 'mv /etc/nginx/sites-available/default-nginx-conf /etc/nginx/sites-available/default' >> /start.sh
RUN echo 'service nginx start' >> /start.sh
RUN echo 'service php7.4-fpm start' >> /start.sh
RUN echo 'service mysql start' >> /start.sh

CMD bash -C '/start.sh';'bash'

EXPOSE 8080
