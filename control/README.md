## mwdd control application

This application has been written as a second iteration of the mediawiki-docker-development environment.

This has been written in PHP to be as close as possible to the MediaWiki world, in the hopes of PRs etc.

Having a single command to do all of the things in a nice way has been [talked about for a while](https://github.com/addshore/mediawiki-docker-dev/issues/84)

The future plan would be that the only requirement is docker and or docker-compose in order to use this solution (no local PHP needed), but the details of that have not yet been finalized.

#### Directories

 - Command - CLI commands that the application exposes
 - DockerCompose - Classes that relate to the value docker-composer yml files used by the system
 - Files - Classes that relate to the various files and directories that the system interacts with
 - Shell - Classes that relate to the various applications this application shells out to
