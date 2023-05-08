#!/bin/bash

set -e

while [[ $# -gt 0 ]]; do
  case $1 in
    -t|--target)
      TARGETS=$2
      shift
      shift
      ;;
    -o|--operating-systems)
      OPERATING_SYSTEMS="$2"
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

if [ -z "$OPERATING_SYSTEMS" ]
then
  OPERATING_SYSTEMS="buster"
fi

echo "Targets: $TARGETS"
echo "Operating Systems: $OPERATING_SYSTEMS"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PDIR=$(dirname $DIR)

# load the loop kernel module if it is not already loaded
if ! lsmod | grep -q loop
then
  modprobe loop
fi

for T in $TARGETS; do
       if [ $T == "pi" ]
       then
	      A="armhf"
	      P="linux/arm"
       else
	      A="amd64"
	      P="linux/amd64"
       fi
       for O in $OPERATING_SYSTEMS; do
         docker build --platform $P -f $DIR/Dockerfile -t asl-live_builder.$O.$A --build-arg ARCH=$A --build-arg OS=$OS --build-arg USER_ID=$(id -u) --build-arg GROUP_ID=$(id -g) $DIR
         docker run --privileged --platform $P -v $PDIR:/src -e TARGET=$T OS=$O asl-live_builder.$O.$A
         docker image rm --force asl-live_builder.$O.$A
       done
done
