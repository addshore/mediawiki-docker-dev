Run the control container:
```
docker-compose --project-directory . -f ./docker-compose/control.yml up -d
```

Run the control container with a rebuild:
```
docker-compose --project-directory . -f ./docker-compose/control.yml up -d --build
```

Run a command in the control container:
```
docker-compose --project-directory . -f ./docker-compose/control.yml exec mwdd-control echo foo
```

Run a docker-compose command in the control container:
```
docker-compose --project-directory . -p mediawiki-docker-dev -f docker-compose/control.yml exec mwdd-control \
    docker-compose --project-directory . -p mediawiki-docker-dev -f docker-compose/control.yml ps
```
