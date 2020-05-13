FROM php:7.2-cli

# Just install docker-compose which will allow the control app to interact with the docker setup
RUN apt-get update && \
apt-get install -y \
    docker-compose \
    # TODO does the container really need git? I mean it doesn' even need docker-compose really...
    git

RUN echo "tail -f /dev/null" >> /idle.sh

WORKDIR /mwdd

# We don't need anything to be running, just for the container to be setup and running
# this needs to be running as mwdd uses docker-compose exec... (I couldn't make run work...)
ENTRYPOINT ["tail", "-f", "/dev/null"]
