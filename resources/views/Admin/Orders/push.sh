#!/bin/bash
#
CODEDIR=`pwd`
PROD=`echo ${CODEDIR}|sed 's/dev/store/'`

echo "Pushing code to ${PROD} "
rsync -avz *.php ${PROD}
