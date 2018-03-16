#!/bin/bash -ex

# wrap the command in summon and pass --env-file to load secrets in env
# docker run ... twitter-search runs a container from the twitter-search image
# -d run in detached mode (returns the container id, or CID)
# -p 4000:80 maps the host port 4000 to the container port 80 
# --volume mounts the cwd as a volume in the container in /var/www/app/
# --workdir sets the work dir in the container to /var/www/app/
# --entrypoint ./dev-configure.sh overrides the entrypoint with our
#   configure script, which configures the container to use
#   our workdir as the DocumentRoot instead of the default /var/www/html/

CID=$(summon docker run -d -p 4000:80 --env-file @SUMMONENVFILE \
  --volume $PWD:/var/www/app \
  --workdir /var/www/app/ --entrypoint ./dev-configure.sh \
  twitter-search)

# this command allows you to bash into the container so you can run commands
# -it runs the container so you can interact it in a terminal

docker exec -it $CID bash
