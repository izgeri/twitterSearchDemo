#!/bin/bash -ex

# start minikube
minikube start

# import the minikube environment
# this ensures I am using the Minikube Docker daemon
# so my images will be accessible to my cluster
eval $(minikube docker-env)

# now build the app image
./build-image.sh

# prepare to deploy the app by creating a namespace
kubectl create namespace twitter-search-ns

# this script always specifies the namespace
# you could skip the namespace altogether and use default
# or you could set your preference by running
#   kubectl config set-context $(kubectl config current-context) \
#     --namespace=twitter-search-ns
# and not have to specify each time at all

# add secret data to Kubernetes secrets
# add pg certificates to kubernetes secrets
# to load with summon instead, call
#   summon ./load-k8s-secrets.sh
kubectl --namespace twitter-search-ns \
  create secret generic \
  twitter-search-secrets \
  --from-literal=twitter-api-key=$TWITTER_API_KEY \
  --from-literal=twitter-api-secret=$TWITTER_API_SECRET \
  --from-literal=google-api-key=$GOOGLE_API_KEY

# at this point we have a namespace and secrets loaded
# let's prepare the application manifest

# deploy the application
kubectl --namespace twitter-search-ns \
  apply -f manifest.yml

# verify that it works

# check that the pods are running
kubectl --namespace twitter-search-ns \
  get pods

# start the dashboard, navigate to the twitter-search-ns
# namespace, and verify the application is up
minikube dashboard

# visit the app in the browser using its exposed nodePort
# at $(minikube ip):30002

# and that's it! you've deployed an app to kubernetes!

# protip: unset your minikube docker env by running
#   eval $(minikube docker-env -u)
