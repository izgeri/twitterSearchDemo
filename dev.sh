#!/bin/bash -ex

# point to the dev docker-compose file and start the containers
summon docker-compose -f docker-compose-dev.yml up -d
