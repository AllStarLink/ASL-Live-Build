#!/bin/bash

# Including common functions
[ -e "${LIVE_BUILD}/scripts/build.sh" ] && . "${LIVE_BUILD}/scripts/build.sh" || . /usr/lib/live/build.sh

# Checking stage file
Check_stagefile .build/binary_rpi

DISKIMAGE="$(cat config/build | grep ^Name:.*rpi | sed 's/^Name: \(.*\)$/\1/g')-armhf.img"

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

install -m 644 rpi_files/config.txt $BOOTFS
install -m 644 rpi_files/cmdline.txt $BOOTFS
install -m 644 rpi_files/fstab $ROOTFS/etc
install -m 644 rpi_files/hosts $ROOTFS/etc/hosts
install -m 755 rpi_files/resize2fs_once $ROOTFS/etc/init.d
install -m 755 rpi_files/check_for_wpa_supplicant $ROOTFS/etc/init.d

# Always enable SSH
touch $BOOTFS/ssh

HOST=repeater

sed -i "s/BOOTDEV/PARTUUID=${DISKUUID}-01/" "${ROOTFS}/etc/fstab"
sed -i "s/ROOTDEV/PARTUUID=${DISKUUID}-02/" "${ROOTFS}/etc/fstab"
sed -i "s/ROOTDEV/PARTUUID=${DISKUUID}-02/" "${BOOTFS}/cmdline.txt"
sed -i "s/localhost\.localdomain/${HOST}/" "${ROOTFS}/etc/hostname"
sed -i "s/localhost/${HOST}/" "${ROOTFS}/etc/hosts"


cat <<EOT > $ROOTFS/setup.sh
#!/bin/bash
adduser --disabled-password --gecos "" repeater
echo "repeater:allstarlink" | chpasswd

for GRP in input spi i2c gpio; do
	groupadd -f -r "\$GRP"
done

for GRP in adm dialout cdrom audio users sudo video games plugdev input gpio spi i2c netdev; do
  adduser repeater "\$GRP"
done

systemctl enable regenerate_ssh_host_keys
systemctl enable resize2fs_once
systemctl enable check_for_wpa_supplicant
EOT

chmod 755 $ROOTFS/setup.sh
chroot $ROOTFS /setup.sh
rm $ROOTFS/setup.sh

umount $ROOTFS
umount $BOOTFS

losetup -d $DEVR
losetup -d $DEVB

rmdir $ROOTFS || true
rmdir $BOOTFS || true

#zip the image
rm -f $DISKIMAGE.zip 2>/dev/null
zip $DISKIMAGE.zip $DISKIMAGE

# Creating stage file
Create_stagefile .build/binary_rpi
