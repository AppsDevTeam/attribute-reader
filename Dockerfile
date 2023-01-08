FROM php:8.0-cli

RUN apt-get update && apt-get install -y unzip && curl --fail -sSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer