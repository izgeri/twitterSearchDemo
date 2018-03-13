#!/bin/bash -ex

summon docker run --env-file @SUMMONENVFILE twitter-search ./vendor/bin/phpunit tests/twitterSearchTest.php
