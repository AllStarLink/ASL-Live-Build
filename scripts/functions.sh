#!/bin/bash

# Including common functions
[ -e "${LIVE_BUILD}/scripts/build.sh" ] && . "${LIVE_BUILD}/scripts/build.sh" || . /usr/lib/live/build.sh

openDiskImage() {
    DISKIMAGE="$(cat config/build | grep ^Name:.* | sed 's/^Name: \(.*\)$/\1/g')-${1}.img"
    OFFSET=$(expr $(fdisk -u -l $DISKIMAGE | sed -ne "s|^${DISKIMAGE}1[ *]*\([0-9]*\).*|\1|p") '*' 512)
    OFFSETm1=$(expr $OFFSET '-' 1)

    parted -s $DISKIMAGE rm 1
    parted -s $DISKIMAGE unit B mkpart primary fat32 4194304 $OFFSETm1
    parted -s $DISKIMAGE unit B mkpart primary ext4 $OFFSET 100%

    DEVB=$(losetup -f)
    losetup -o 4194304 --sizelimit $(expr $OFFSET '-' 4194304) $DEVB $DISKIMAGE
    DEVR=$(losetup -f)
    losetup -o $OFFSET $DEVR $DISKIMAGE
    mkdosfs -n boot -F 32 -v $DEVB
    DISKUUID="$(dd if=$DISKIMAGE skip=440 bs=1 count=4 2>/dev/null | xxd -e | cut -f 2 -d' ')"
    ROOTFS=rootfs
    BOOTFS=bootfs

    mkdir $ROOTFS || true
    mkdir $BOOTFS || true

    mount $DEVR $ROOTFS
    mount $DEVB $BOOTFS

    mv $ROOTFS/boot/* $BOOTFS
}

function closeDiskImage()
{
    umount $ROOTFS
    umount $BOOTFS

    losetup -d $DEVR
    losetup -d $DEVB

    rm -rf $ROOTFS || true
    rm -rf $BOOTFS || true
}
