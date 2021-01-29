#!/bin/bash

source ~/.server-path.txt && \
if [ -d "$PRODUCTION_SERVER_PATH/old" ]; then
    rm -Rf "$PRODUCTION_SERVER_PATH/old"
else
    echo "$PRODUCTION_SERVER_PATH/old" Folder does not exists.
fi

mkdir -p "$PRODUCTION_SERVER_PATH/old" && \

if [ -d "$PRODUCTION_SERVER_PATH/current" ]; then
    cp -R -a "$PRODUCTION_SERVER_PATH/current/." "$PRODUCTION_SERVER_PATH/old/" && \
    rm -R "$PRODUCTION_SERVER_PATH/current" && \
    rm "$PRODUCTION_SERVER_PATH/to_do" && \
    ln -s "$PRODUCTION_SERVER_PATH/old/public" "$PRODUCTION_SERVER_PATH/to_do"
else
    echo "$PRODUCTION_SERVER_PATH/current" Folder does not exists
fi