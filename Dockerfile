FROM nimmis/apache-php7:latest
RUN a2enmod rewrite && \
    sed -i '166s/None/All/' /etc/apache2/apache2.conf
