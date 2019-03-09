#!/bin/bash -e

# run this script prefaced by summon
# to inject secrets into the process environment

# add secret data to Kubernetes secrets
# add pg certificates to kubernetes secrets
kubectl --namespace twitter-search-ns \
  create secret generic \
  twitter-search-secrets \
  --from-literal=twitter-api-key=$TWITTER_API_KEY \
  --from-literal=twitter-api-secret=$TWITTER_API_SECRET \
  --from-literal=google-api-key=$GOOGLE_API_KEY
