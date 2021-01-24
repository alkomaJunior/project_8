#!/bin/bash

source ~/.server-path.txt
if [ -h "$PRODUCTION_SERVER_PATH/to_do" ]; then
    rm "$PRODUCTION_SERVER_PATH/to_do"
else
  echo "$PRODUCTION_SERVER_PATH/to_do" symlink does not exists
fi
ln -fs "$PRODUCTION_SERVER_PATH/current/public" "$PRODUCTION_SERVER_PATH/to_do" && \

#cd $PRODUCTION_SERVER_PATH/current && \
#php bin/console cache:clear && \
chmod -R 755 "$PRODUCTION_SERVER_PATH/current/public/" && \
chmod -R 777 "$PRODUCTION_SERVER_PATH/current/var/" && \
rm ~/.server-path.txt