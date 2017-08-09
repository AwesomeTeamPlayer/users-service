#!/usr/bin/env bash

docker run -d --name mysql-for-tests -p="13306:3306" -e MYSQL_ROOT_PASSWORD=root -eMYSQL_DATABASE=testdb mysql:8.0.1

for i in {0..600}
do

    echo ""
    echo ""
    echo "$i sec:"

    if ./env-checker.php 127.0.0.1 13306 root root testdb ; then
        break;
    fi

    sleep 1

done

./vendor/bin/phpunit ./tests/integration
TEST_COMMAND_RESULT=$?

docker stop mysql-for-tests
docker rm mysql-for-tests

exit $TEST_COMMAND_RESULT
