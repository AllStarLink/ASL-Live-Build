#!/bin/bash

set -e

while [[ $# -gt 0 ]]; do
  case $1 in
    -t|--target)
      TARGETS=$2
      shift
      shift
      ;;
    -*|--*|*)
      echo "Unknown option $1"
      exit 1
      ;;
  esac
done

if [ -z "$TARGETS" ]
then
  TARGETS="pc rpi"
fi

echo "Targets: $TARGETS"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PDIR=$(dirname $DIR)

for T in $TARGETS; do
       if [ $T == "pi" ]
       then
	      A="arm64"
	      P="linux/arm"
       else
	      A="amd64"
	      P="linux/amd64"
       fi
       docker build --platform $P -f $DIR/Dockerfile.$A -t asl-live_builder.$A --build-arg USER_ID=$(id -u) --build-arg GROUP_ID=$(id -g) $DIR
       docker run --privileged --platform $P -v $PDIR:/src -e TARGET=$T asl-live_builder.$A
       docker image rm --force asl-live_builder.$A
done
