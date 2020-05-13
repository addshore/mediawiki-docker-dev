FROM composer:1.10 as composer

COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock
RUN composer install --no-dev --no-progress


FROM php:7.2-cli

# Just install docker-compose which will allow the control app to interact with the docker setup
RUN apt-get update && \
apt-get install -y \
    docker-compose \
    # TODO does the container really need git? I mean it doesn't even need docker-compose really...
    git

COPY --from=composer /app/vendor /mwdd-vendor

RUN echo "#!/bin/bash" >> /idle && echo "tail -f /dev/null" >> /idle && chmod +x /idle

WORKDIR /mwdd

# We don't need anything to be running, just for the container to be setup and running
# this needs to be running as mwdd uses docker-compose exec... (I couldn't make run work...)
ENTRYPOINT ["/idle"]
