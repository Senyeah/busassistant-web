FROM nimmis/apache-php7:latest
RUN sed -i '166s/None/All/' /etc/apache2/apache2.conf
