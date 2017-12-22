
# How to use Elastic Search #

[![Docker Repository on Quay](https://quay.io/repository/maplesyrupgroup/search/status?token=621108c3-7e98-4f69-af1f-a34dd1de8704 "Docker Repository on Quay")](https://quay.io/repository/maplesyrupgroup/search)

First you need to [install Elastic Search](https://quidco.atlassian.net/wiki/display/QPLAT/Elastic+Search+Installation) and once you have everything up and running you can start building the index.

Before we can build the index, add these values to the .env file:

```
#!txt
QUIDCO_API_ENDPOINT=http://trunk.quidco.dev/api/v3/en/
# If you need authentication tokens set uncomment these lines
#QUIDCO_API_SERVICE_KEY=xxxx
#QUIDCO_API_CLIENT_ID=xxxx
QPLATFORM_API_CONTENT_GET_MERCHANT_DETAILS=http://content.app/api/v0/merchants?includes=images,live_deals,categories,best_rates,related_merchant_rates,statistics&status=active
ELASTICSEARCH_INDEX_NAME=shoop_merchants
ELASTICSEARCH_HOST=localhost
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_BUILD_INDEX_EXECUTION_TIME=600

```

# Build the Elastic Search index #

Execute the following command inside the search/php-app directory:

```
#!txt

php artisan search:build-index

```


# Testing

Running the coding style tests

```
vendor/bin/phpcs --standard=PSR2 app/
vendor/bin/phpmd app/ text cleancode,codesize,design,naming,unusedcode
```

Until we can easily build the index during the tests for data we set up, some tests rely on a previously built index
for the servicetest environment. These tests are marked with the `elasticsearch-index` group. To build the index, run:

```
APP_ENV=servicetest php artisan search:build-index --all-domains
```

Running unit & integration tests:

```
./vendor/bin/phpunit
```

Since the indexer is tightly coupled to the database, a simple schema needs to be created to run acceptance tests:

```
eval export" "$(cat .env.servicetest | grep DB_DATABASE)
eval export" "$(cat .env.servicetest | grep DB_PASSWORD | sed -e 's/DB_PASSWORD/MYSQL_PWD/g')
eval export" "$(cat .env.servicetest | grep DB_USERNAME)

mysql -u$DB_USERNAME -e'CREATE DATABASE '$DB_DATABASE
mysql -u$DB_USERNAME $DB_DATABASE < features/Fixtures/db/qplatform.sql
```

Running Behat tests:

```
./vendor/bin/behat
```

Currently, responses from both APIs used to build the index are cached in `features/Fixtures/http`.
Until there's a way to automatically refresh them, these should be manually refreshed when needed.

Should any problems with building the index occur during a behat run, you might need to force refreshing
the index by setting the `always_refresh_index` config option in `features/config/services.yml` to true
(by default it will only be built if it wasn't built before). It will also be needed after mapping configuration
changes.

## Commands ##

The following artisan commands are provided:

**search:build-index --all-domains**
This command builds elastic indexes of merchant information for all configured domains. The "--all-domains" option can
be replaced with an allowed domain id (currently 1, 200) to build index for that specific domain.
`Runs on production every 10 minutes (cron: */10 * * * *)`

## Jobs ##

The search microservice does not processed any queued jobs.


## Multi Node Support (Clusters) ##
The search micro service is configured to support one elastic search server by default which is a single point of failure in the
event that the server fails.

To enable Elasticsearch support for multiple nodes / replicas in a cluster which can be connected to using the round robin strategy,
add the IP addresses and ports of the clusters to the environment configuration as show below. IP address/ports pairs are comma separated.

```
ELASTICSEARCH_CLUSTER_NODES=192.12.12.41:9236,192.25.14.78:4531
```

When configured, the additional nodes plus the default server would be added to a pool and connected to in a round robin fashion.
Note that the additional nodes added to the cluster should be MASTER eligible i.e. node.master is set to true in the server's elastic search configuration

see https://www.elastic.co/guide/en/elasticsearch/reference/current/modules-node.html