FROM php:7.2-cli

# Just install docker-compose which will allow the control app to interact with the docker setup
RUN apt-get update && \
apt-get install -y docker-compose

# Run the container, and keep it running etc, but doing nothing
# TODO does this even give us any kind of performance gain? or should the container just not be running?
WORKDIR /mwdd
ENTRYPOINT ["tail", "-f", "/dev/null"]
