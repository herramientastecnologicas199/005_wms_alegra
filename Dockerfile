FROM ubuntu:22.04
RUN apt update
RUN DEBIAN_FRONTEND=noninteractive TZ=America/Bogota apt-get -y install tzdata

RUN apt-get update && \
    apt-get install -yq tzdata && \
    ln -fs /usr/share/zoneinfo/America/Bogota /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

ENV TZ="America/Bogota"

RUN apt install -y nano
RUN apt install -y apache2
RUN apt install -y apache2-utils
RUN apt install -y curl
RUN apt install -y mcrypt
RUN apt clean
RUN apt -y install software-properties-common
RUN add-apt-repository ppa:ondrej/php -y
RUN apt-get update
RUN lsb_release -a > version.txt
RUN cat version.txt
RUN apt-get install -y php8.2 php8.2-dev php8.2-xml -y --allow-unauthenticated
RUN php -v > version_php.txt
RUN cat version_php.txt

RUN apt-get install -y php8.2-mysql > php8.2-mysql.txt
RUN cat php8.2-mysql.txt

RUN apt-get install -y php8.2-mbstring

RUN echo "date.timezone =America/Bogota" >> /etc/php/8.2/apache2/php.ini
RUN echo "upload_max_filesize = 15M" >> /etc/php/8.2/apache2/php.ini
RUN echo "post_max_size = 15M" >> /etc/php/8.2/apache2/php.ini

RUN service apache2 restart

COPY . /var/www/html/
RUN rm /var/www/html/index.html
EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
