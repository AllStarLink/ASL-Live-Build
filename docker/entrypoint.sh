#!/bin/bash
set -e

cd /src
make $TARGET
ls -l
