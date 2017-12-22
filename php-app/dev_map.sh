#!/bin/bash
VENDOR_PATH="vendor/maple-syrup-group"
HOSTNAME=`hostname`
if [ "${HOSTNAME}" = "homestead" ]; then
    for MODULE in content sdk-php transaction user wallet common communication laravel-annotations laravel-multi-tenant
    do
        rm -rf $VENDOR_PATH/$MODULE || true
        (cd $VENDOR_PATH && ln -s ../../../$MODULE)
    done
fi