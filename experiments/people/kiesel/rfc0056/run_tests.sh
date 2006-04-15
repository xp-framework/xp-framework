#!/bin/sh

php -dinclude_path=.:$(realpath skeleton):$(realpath ../../../../skeleton) ../../../../util/tests/run.php tests.ini
