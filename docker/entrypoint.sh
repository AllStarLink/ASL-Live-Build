#!/bin/bash
set -e

cd /src
make $TARGET OS=$OS
ls -l
