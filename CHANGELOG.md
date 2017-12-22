## Microservice / component

Search Microservice

## Changelog (2017-05-04)

### v1.4.0

+ QP-3017: add method to check new index exists before swapping
+ QP-3063: updated stragety order
+ QP-3011: add sort order
+ QP-3006: return all merchant prefix matches


## Changelog (2017-03-27)

### v1.3.0

+ QP-1329: Added merchant prefix strategy
+ QP-2890: Aligned french mapping and index with quidco


## Changelog (2017-03-10)

### v1.2.0

+ QP-2938: Improve merchant search results with prefix search terms


## Changelog (2017-02-13)

### v1.1.0

+ QP-2879: Add the best_rate field for the Quidco search similar/related merchant search results
+ QP-2858: fix in-store merchant filter


## Changelog (2017-01-24) 

### v0.36

+ Fix the missing cashback rates when merchants searched          - SF-1058


## Changelog (2017-01-19) 

### v0.35

+ Add Error Codes To Error Messages in search microservice          - QP-1627
+ Add a is_in_store filter in the search ms                         - QP-2486
+ Fix the best rate in the type head script for Quidco search index	- QP-2488
+ Update search to include best rate in similar retailers           - QP-2601


## Changelog

### v0.34

+ Explicitly cast keywords imported as an array.

#### SQL Statements

None

#### Configuration values

None

#### Related cases

 QP-2508

### v0.33

+ Corrected a bug in ElasticSearchClientProvider

#### SQL Statements

None

#### Configuration values

Set the following in production:

ELASTICSEARCH_HOST=qcoelasticq01.quidco.lan
ELASTICSEARCH_PORT=9200
ELASTICSEARCH_CLUSTER_NODES=qcoelasticq02.quidco.lan:9200,qcoelasticq03.quidco.lan:9200

#### Related cases
QP-1961


### v0.32

+ use either keyword or id for category exact match [QP-1885](https://quidco.atlassian.net/browse/QP-1885)
+ shoop support for category endpoint [QP-1866](https://quidco.atlassian.net/browse/QP-1866)
+ best match strategy investigation [QP-1530](https://quidco.atlassian.net/browse/QP-1530)
+ Added support for multi server configuration [QP-1968](https://quidco.atlassian.net/browse/QP-1968)
+ moved keywords and rates text to fallback [QP-1625](https://quidco.atlassian.net/browse/QP-1625)

#### SQL Statements

None

#### Configuration values

<!--- Set this to csv values IP:port of elasticsearch cluster nodes e.g. 192.168.1.45:1234,192.168.1.46:1235 -->

ELASTICSEARCH_CLUSTER_NODES=

#### Related cases
 QP-1885
 QP-1866
 QP-1530
 QP-1968
 QP-1625


### v0.31

+ New Relic attributes [QP-1973](https://quidco.atlassian.net/browse/QP-1973), [QP-1972](https://quidco.atlassian.net/browse/QP-1972)

#### SQL statements

None

#### Configuration values

None

#### Related cases

QP-1973
QP-1972

### v0.30

+ Add option to enable strategy logs, set default log_level to error explicitly instead of DEBUG [QP-1861](https://quidco.atlassian.net/browse/QP-1861)

#### SQL Statements

None

#### Configuration values

None

#### Related cases

QP-1861

### v0.29

+ Handle exception thrown within the try/catch block [QP-1883](https://quidco.atlassian.net/browse/QP-1883)

#### SQL Statements

None

#### Configuration values

None

#### Related cases

QP-1883

### v0.28

+ Handle logic exception when no result is found [QP-1883](https://quidco.atlassian.net/browse/QP-1883)

#### SQL Statements

None

#### Configuration values

None

#### Related cases

QP-1883

### v0.27


+ Added category prefix match strategy [QP-1728](https://quidco.atlassian.net/browse/QP-1728)
+ Update readme - Added artisan commands section[QP-1822](https://quidco.atlassian.net/browse/QP-1822)
+ Refactoring elastic search index builder and importer[QP-1378](https://quidco.atlassian.net/browse/QP-1378)
+ Create merchant category endpoint[QP-1615](https://quidco.atlassian.net/browse/QP-1615)
+ Added option to pass strategy to merchant search endpoint[QP-1616](https://quidco.atlassian.net/browse/QP-1616)
+ Rfc5424 formatter return value[QP-1540](https://quidco.atlassian.net/browse/QP-1540)
+ Add unit tests for document hydrator and validator[QP-1378](https://quidco.atlassian.net/browse/QP-1378)
+ Defer instantiation of http client[QP-1716](https://quidco.atlassian.net/browse/QP-1716)
+ Implement interface instead of subclassing[QP-1540](https://quidco.atlassian.net/browse/QP-1540)
+ Refactored search controller and type builders to allow testing[QP-1404](https://quidco.atlassian.net/browse/QP-1404)
+ Modify behat tests to call api endpoints[QP-1570](https://quidco.atlassian.net/browse/QP-1570)
+ Resolve issues raised by scrutinizer[QP-1436](https://quidco.atlassian.net/browse/QP-1436)
+ Updating to healthcheck v1.22. Removing dependency on qcommon base controller[QP-1556](https://quidco.atlassian.net/browse/QP-1556)
+ Removing hard coded credentials for Redis and ElasticSearch, and hard coded ES index names[QP-1551](https://quidco.atlassian.net/browse/QP-1551)


#### SQL Statements

None

#### Configuration values

None

#### Related cases
QP-1728
QP-1822
QP-1378
QP-1615
QP-1616
QP-1540
QP-1378
QP-1716
QP-1540
QP-1404
QP-1570
QP-1436
QP-1556
QP-1551

### v0.26

+ Make scrutinizer slightly more happy [QP-1436](https://quidco.atlassian.net/browse/QP-1436)
+ Cleaning up redis configuration [QP-1537](https://quidco.atlassian.net/browse/QP-1537)
+ Implement redis index status tracker [QP-1507](https://quidco.atlassian.net/browse/QP-1507)
+ Replace info level logs with debug [QP-1410](https://quidco.atlassian.net/browse/QP-1410)
+ Update q-common with removed beta env domain_id [QP-1528](https://quidco.atlassian.net/browse/QP-1528)
+ Updating healthcheck dependency. Correct healthcheck exit codes [QP-1509](https://quidco.atlassian.net/browse/QP-1509)
+ Added weighted category scores to merchants [QP-1377](https://quidco.atlassian.net/browse/QP-1377)
+ Add migrations automation for kubernetes [QP-1462](https://quidco.atlassian.net/browse/QP-1462)
+ Remove bower from the build process [QP-1440](https://quidco.atlassian.net/browse/QP-1440)
+ Refactor search index status implementation [QP-1382](https://quidco.atlassian.net/browse/QP-1382)
+ Boost active rates text search [QP-1328](https://quidco.atlassian.net/browse/QP-1328)
+ Replace app.php class_exists() parameter with ::class version [QP-1425](https://quidco.atlassian.net/browse/QP-1425)
+ Add the domain option to the build index command [QP-1368](https://quidco.atlassian.net/browse/QP-1368)
+ Implement category exact match strategy [QP-910](https://quidco.atlassian.net/browse/QP-910)
+ Remove and stop tracking public/components [QP-1391](https://quidco.atlassian.net/browse/QP-1391)
+ Debug search queries [QP-1358](https://quidco.atlassian.net/browse/QP-1358)
+ Adding Dockerfile and Makefile [QP-1370](https://quidco.atlassian.net/browse/QP-1370)
+ Removing bower from composer post install/update command [QP-1369](https://quidco.atlassian.net/browse/QP-1369)

#### SQL Statements

None

#### Configuration values

IMPORTER_TRACKER_ALIAS=redis
IMPORTER_LOCK_FILE_LOCATION=/tmp/
IMPORTER_LOCK_FILE_TTL=300

REDIS_SCHEME=tcp
REDIS_CLUSTER=false
REDIS_DATABASE=4
REDIS_HOST=10.0.16.121
REDIS_PORT=6379
REDIS_PREFIX=prod

#### Related cases
QP-1436
QP-1537
QP-1507
QP-1410
QP-1528
QP-1509
QP-1377
QP-1462
QP-1382
QP-1328
QP-1425
QP-1368
QP-910
QP-1391
QP-1358
QP-1370
QP-1369

### v0.25

+ Catch url validation error return400 [QP-1303](https://quidco.atlassian.net/browse/QP-1303)
+ Acceptance tests for searching merchants by link [QP-1258](https://quidco.atlassian.net/browse/QP-1258)
+ Add scenarios for category name match [QP-1175](https://quidco.atlassian.net/browse/QP-1175)
+ Create docker machines-for-local [QP-1279](https://quidco.atlassian.net/browse/QP-1279)
+ Add merchant not found exception to exclusion list [QP-1300](https://quidco.atlassian.net/browse/QP-1300)
+ Healthcheck migration status [QP-1287](https://quidco.atlassian.net/browse/QP-1287)

#### SQL Statements

None

#### Configuration values

None

#### Related cases
QP-1030
QP-1258
QP-1175
QP-1279
QP-1300
QP-1287


### v0.24

+ Handle toolbar links containing hyphens [QP-1257](https://quidco.atlassian.net/browse/QP-1257)
+ Add acceptance test scenario for in-store best match [QP-1182](https://quidco.atlassian.net/browse/QP-1182)
+ improve http status response [QP-1138](https://quidco.atlassian.net/browse/QP-1138)
+ Split healthcheck into liveness [QP-1281](https://quidco.atlassian.net/browse/QP-1281)
+ Add healthcheck endpoint [QP-433](https://quidco.atlassian.net/browse/QP-433)
+ Excluded filtered merchants from search results [QP-1095](https://quidco.atlassian.net/browse/QP-1095)
+ Make sure keywords coming from the quidco api are indexed [QP-1180](https://quidco.atlassian.net/browse/QP-1180)
+ Order by name in ascending order if scores are the same [QP-895](https://quidco.atlassian.net/browse/QP-895)
+ Add swagger api config for exclude_merchants, add filter to elastic search query [QP-1098](https://quidco.atlassian.net/browse/QP-1098)

#### SQL Statements

None

#### Configuration values

None

#### Related cases
QP-1164
QP-1177
QP-1178
QP-1179
QP-1180
QP-1176
QP-1257
QP-1182
QP-1138
QP-1281
QP-433
QP-1095
QP-895
QP-1098

### v0.23
Removed rates text search query from exact and best match [QP-1164](https://quidco.atlassian.net/browse/QP-1164)

#### SQL Statements

None

#### Configuration values

None

#### Related cases
QP-1164


### v0.22

QP-1109: Do not throw a new exception when trying to delete an index with no available connection
QP-957: Create a new endpoint for the Shoop toolbar so we can notify customers about cashback more reliably
QP-1076: Create Merchant Searchable Rate Field in Elastic Search

#### SQL Statements

None

#### Configuration values

.env update:

QPLATFORM_API_CONTENT_GET_MERCHANT_DETAILS=http://apigateway.maplesyrupmedia.com/search/merchants?includes=links,images,live_deals,categories,best_rates,related_merchant_rates,statistics&status=active

#### Other Notes

None

#### Related cases

QP-1109
QP-957
QP-1076

### v0.21

QP-965: add is in store to search index
QP-965 ensure is_in_store field is set
QP-965: added is_in_store field to mapping for and merchant search properties

QP-962: Index merchant links
QP-962 Index the toolbar opt out field

QP-948: added assertions to improve code coverage
QP-948 use static query method to generate ES query

QP-945 Build the index before behat scenarios are executed
QP-945: Change the way fixtures are stored on the filesystem

QP-945: Cache composer files on CI
QP-945: Improve readability of the QuidcoApi service provider
QP-945: Update the http fixtures
QP-945: Finish early if there is a problem with connection
Qp-945: Prevent continious appending of page and page size
QP-945: Make it possible to disable http caching
QP-945: Configure a console logger for the importer to prevent behat giving no output for a long time
QP-945: Extract methods to improve readability
QP-945: Create the database user
QP-945: Refresh fixtures
QP-945: Separate the test database from the dev database
QP-945: Update q-common to prevent a fatal error
QP-945: Build the index before behat scenarios are executed

QP-942 Created separate query test class
QP-942 added unit tests for search transformer classes

QP-882: Remove skyscanner and pet plannet from exact match acceptance tests
QP-882: Remove skyscanner and pet plannet from exact match acceptance tests
QP-882 added unit tests for contentApiSearchableModel
QP-882 added unit tests for contentApi and quidcoApi search models

QP-820: Add a guard checking if the fixtures index is up to date
QP-820 Add a guard checking if the fixtures index is up to date
QP-820: Add a guard checking if the fixtures index is up to date
QP-820 add acceptance tests
QP-820: Improve readability
QP-820: Implement fixture loading in an event listener
QP-820: Check if merchants exist
QP-820: Simplify dependency management in behat
QP-820: Extract search service creation to a factory
QP-820: Make the fixtures available to elasticsearch on scrutinizer
QP-820: Set up a snapshot repository before using it
QP-820: Run behat on scrutinizer
QP-820: Add elasticsearch fixtures
QP-820: Automate the exact match scenarios
QP-820: Add missing servicetest configuration
QP-820: Provide default page and page size
QP-820: Remove a failing behat suite implementation

QP-824 Add logger unit tests
QP-824 add localn facilites to class attribute, change attribute to static
QP-824 Fix PHP-CS coding standards
QP-824 added tests for Rfs5424 log formatter
QP-824 Add logger unit tests

QP-688 Tests for merchant search
QP-688: Fix the bug in calculating time for search streatgies
QP-688: Document an empty catch block
QP-688: Move elasticsearch search implementation into the Elasticsearch namespace
QP-688: Move query implementations into the Elasticsearch namespace
QP-688: Move the base ElasticsearchQuery to the Merchants package
QP-688: Disable redis and postgresql on scrutinizer
QP-688: Enable elasticsearch on scrutinizer
QP-688: Refactor the merchant search and add tests
QP-688: Construct the ElasticsearchQuery form a Query
QP-688: Add a type to elasticsearch query
QP-688: Expose the domain id
QP-688: Remove the unnecessary query interface

QP-744 add unit tests for business event class
QP-744 add unit tests for business event class

QP-702: Update q-common to benefit from a fix for determining the domain id on local environment

QP-738: Escape special characters properly for elasticsearch
QP-738: Escape special characters properly for elasticsearch

Only the last character on the list of special characters was ever escaped. Also, the backslash needs to be first on the list to prevent double escaping.
[WIP][QP-688] Add tests and improve the design
FIX: Live logs are filled with debug messages
Added latest versions of laravel annotations and q-common
Query is a simple value object and there is no need for it to be an interface. It does some language  validation but this is currently ES specific and should be moved out (or implemented differently).
FIX: Live logs are filled with debug messages


#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

QP-965
QP-962
QP-948
QP-945
QP-942
QP-882
QP-820
QP-824
QP-688
QP-744
QP-702
QP-738

### v0.20

QP-696:
The autoloader only works when optimised
* Before this fix the autoloader only worked when an optimised map was dumped (composer dump-autoload -o).
  To be PSR-4 compliant directory names need to match parts of the namespace, and the file name need to match
  the class name.

QP-697:
Set up scrutinizer
* Remove unused tools, tests and improve the phpunit config
* Add a workaround for a phpunit bug
* Add a simple test for a good start of the test suite
* Remove unused tools, tests and improve the phpunit config


#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

QP-696
QP-697

### v0.19

Update guzzle wrapper
Stop code overriding access token in guzzle request

### v0.18

Change url for for content ms to go through the gateway

### v0.17

QP-542:
Set global domain id required for inter service communication
Added access token to Guzzle call used to access content MS 

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

QP-542

### v0.16

QP-538:
Fix missing "content" DB connection config required by q-common get domain functionality 

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

QP-538

### v0.15

QP-498:
* Refactored search indexing to use separate analysers and mapping for shoop and quidco indexes merchant type.
* Refactored search implementation to use separate language dependent strategies
* Fixed rates_text indexing for quidco based domain elasticsearch index
* Refactored class names to be more meaningful and clean up usage of refactored mapper classes 

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

QP-498

### v0.14

QP-386:  Return Time taken for each attempted search strategy as part of merchant search response
required for elk logging

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

* QP-386

### v0.13

QP-378:  Added field "attempted strategies" as part of hit result for merchant search.
Quidco web app uses this field to generate log information for search stats

FIX: Remove commented out code

UPDATE: Updated to latest version of laravel annotations

FIX: Body shop isn't showing up in searches
When you search for "The Body Shop" or "Bodyshop" you get results but not "Body Shop".
To resolve this I fixed the whitespace stripping regex we use when normalising a search term for the word splitting filter

QP-379: API version not being picked up in the response headers
updated composer.lock to get latest version of q-common, to fix the API version response header



#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

* QP-378
* QP-379

### v0.12

QP-375: Added php5.5 compatable version of laravel-annotations

Fixes problems on beta.

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

None

#### Related cases

* QP-375

### v0.11

QP-164: Added revised swagger compatability to search MS

Improved swagger performance by aggressively caching routes and swagger 
registry inside bootstrap/cache, and removed redundant routing code from
microservice

---

QP-356: Fix name population for similar merchants

Currently we're not populating the names correctly for similar and 
related merchants. This should resolve that issue.

---

QP-356: Ensure whitespace doesn't create odd token

User search queries were being fed into a whitespace analyser for 
merchant names.

This was causing unusual token matches. Changing to a keyword analyser.

---

QP-356: Also normalise "." to spaces

Currently a user would get different results for quico.com and quidco 
com. This will prevent that

---

QP-356: Log what searches get executed.

When at "Debug" Log level, store the queries we send to elasticsearch.

This means we can turn them off when we're out of development mode but 
when we're ready for production, we can skip them.

---

QP-255: Add additional logging to papertrail

Ensure that we can log things to Papertrail. We need to be able to alert
when import scripts fail.


---

QP-221: No exact match for duplicate start of name

For merchants with a duplicate starting name, e.g.

* FNAC
* FNAC Pro
* FNAC Spectacles

When a user searches for FNAC

Make sure all 3 are listed rather than just the first

#### SQL Statements

None

#### Configuration values

Ensure that papertrail gets logs on live

```
PAPERTRAIL_SOCKET=udp://logs3.papertrailapp.com:23013
PAPERTRAIL_SYSTEM_NAME=search-microservice
```

The setting LOGSTASH_SOCKET has been removed

#### Other Notes

None

#### Related cases

* QP-356
* QP-255
* QP-221
* QP-164

### v0.10

QP-255: Add ability to log events to ELK
    
Currently our business events are only logged locally, this is okay, but
in practice we want to ship these logs to a proper log server. This adds
that functionality.
    
Changes:

* Updated the format of the messages to work with elk
* Added an optional "LOGSTASH_SOCKET" environment variable, which will 
  cause the elk logs to there

---

QP-255: Added logging to the search service

Now log every time we do a search. Store a whole bunch of details such 
as time it took to do the search, userid, term, top results

---

QP-255: 
    
We want to be able to analyse the logs of this application in Kibana, 
this change allows us to do that.
    
Firstly it does some house keeping and switches out generic PHP 
exceptions for more specific ones that will be easy to search for, it 
also ensures we can pass a logger to each of our classes.
    
It then adds log events for:
    
* All Errors
* Retries
* Successful imports from a specific document source
* Completed script
* Initializing the script

---

QP-125: Longer keywords not being indexed
    
There was a problem which manifested itself as some keywords with spaces
in not being searchable. This turned out to be the keywords being too 
long, and not being analysed for their full length. The max length was 
simply doubled from 20 to 40.

---

QP-357: Split indexes by DomainID
    
This change has two benefits:
    
1. We remove the danger of merchant IDs clashing.
2. We make the index smaller for faster searches, as we never look 
   outside our index

#### SQL Statements

None

#### Configuration values

None

#### Other Notes

Please run the Build Index script from this version BEFORE doing the 
release. This version will add swap to using 2 newly named indexes, 
and they need to exist before the code will work.

#### Related cases

* QP-255
* QP-357
* QP-125

### v0.9

FIX: Upgrade the API Client

This upgrades the API Client for the current quidco api, so that it 
allows connections via api-relay.quidco.com.

---

FIX: Pass credentials to Quidco client

Currently we call build rather than make for our API client. Build 
creates a new instance every time to the when we create the Quidco API 
Client, meaning it does not have the credentials.

This change fixes that, and ensures we're not creating a new instance 
every time.

Currently we call build rather than make for our API client. Build 
creates a new instance every time to the when we create the Quidco API 
Client, meaning it does not have the credentials.

This change fixes that, and ensures we're not creating a new instance 
every time.

---

FIX: Unhelpful errors from Quidco API

Currently we get a very strange response from the Quidco API when we 
pull in the merchants from Quidco.

In the case that this happens and there are no errors we now output the 
whole response.

---

FIX: Stop index rebuilding in every container

This change will fix the problem where an index will be rebuilt at 
midnight for every instance of the search container that we have 
running.

We do this by moving to running the script by an script in the DevOps 
repo which will run on a per deployment basis for Kubernetes.

#### SQL Statements

None

#### Configuration values

None

#### Related cases

None

### v0.8

Quidco Errors aren't consistent

Sometimes Quidco's API returns us inconsistent errors. This will harden 
the error handling against that

---

ElasticSearch configuration ignored

The code was working fine on all environments outside of Kubernetes. 
This was caused by all other environments running ElasticSearch locally,
and the client we use for ElasticSearch (Elastica) not being initialized
new for the import and search services.

To fix this I changed  "build" to "make" on the container, which means 
if there is an existing (and with config) client available we'll use 
that.

---

Ensure we normalise all search terms

Make sure all user queries are lower cased and have diaritics and &'s 
normalised out

---

Add a generic way to show external refs

Currently Quidco has a single external reference on some of it's 
merchants. A  "become_id". This is used to tie products to a specific 
merchant on Connexity product information stream, which means we can 
show products that are relevant to a particular merchant.

We may in the future move away from become, so we want a way to 
represent this ID in a generic too.

This adds this functionality.

#### Related cases

* QP-119
* QP-135

### v0.7

Added retries to the index builder
    
Now removes the temporary index on a failure

---

Added support for importing keywords
      
* Decomposed the original script, as it was turning into a mess
* Changed default replicas to 2
* Changed default shards to 5
* Added support for importing keywords from both Quidco and the Content 
  API

#### SQL Statements

None

#### Configuration values

* INDEX_BUILDER_RETRIES  - Number of retries to make before giving up on 
  the import, defaults to 3.
* ELASTICSEARCH_SHARDS   - Number of shards to have - defaults to 5
* ELASTICSEARCH_REPLICAS - Number of replicas to have - defaults to 5

#### Related cases

* QP-212
* QP-125

### v0.6

The results for a search can now be derived in a number of ways.

1. First we go for an exact match on the name.
2. Then we go for a "best match" strategy where we try a number of 
   different searches on the deal and on the category and name and try 
   and get a result
3. If we still don't get a result we have a super permissive search that
   will at least return SOMETHING. Even if it's not amazing.

Which one of these is now shown in the "strategy" in the meta data. This
allows a user to determine if a search was more or less an exact hit, 
and change the display based on that.

This also fixes the escaping in the Elasticsearch term, rather than just
stripping the characters it'll now correctly escape them. It also no 
longer just lower cases the query, as this can make it harder to 
implement some kinds of search.

---

Ensure index contains what's inserted
Check that the index has the documents we added. In the case of failure 
the code will fail with a non-zero return code

---

Search/internationalization improvements

* Normalizes the "exact match" a little to ignore diacritics
* Supports french version of & to " et "
* Normalizes the "-" to a space (" ")
* Removes apostrophes
* Changes the "exact match" to strip out all spaces from the term

#### Related cases

* QP-121
* QP-126
* QP-185
* QP-186
