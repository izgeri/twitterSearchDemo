#!/bin/bash -ex

summon docker run -d -p 4000:80 --env-file @SUMMONENVFILE twitter-search
