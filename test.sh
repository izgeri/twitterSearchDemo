#!/bin/bash -ex

function finish {
  echo 'Removing test environment'
  echo '---'
  docker-compose down --rmi 'local' --volumes
}
trap finish EXIT

summon docker-compose up -d

docker exec $(docker-compose ps -q twitter-search) ./vendor/bin/phpunit tests/twitterSearchTest.php
