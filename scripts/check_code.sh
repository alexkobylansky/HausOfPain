#!/bin/bash
./vendor/bin/phpmd ./app/ text codesize,unusedcode
./vendor/bin/phpcbf ./app/ --standard=PSR2
