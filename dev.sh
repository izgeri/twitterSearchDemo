#!/bin/bash -ex

# point to the dev docker-compose file and start the containers
summon docker-compose -f dev/docker-compose.yml up -d
