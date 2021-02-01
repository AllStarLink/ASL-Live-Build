#!/bin/bash

echo ####################################
echo #                                  #
echo #                                  #
echo #          rpi_setup.sh            #
echo #                                  #
echo #                                  #
echo ####################################

# Including common functions
[ -e "${LIVE_BUILD}/scripts/build.sh" ] && . "${LIVE_BUILD}/scripts/build.sh" || . /usr/lib/live/build.sh

# ASL Functions
. scripts/functions.sh

# Checking stage file
Check_stagefile .build/binary_rpi

install -m 644 rpi_files/config.txt $BOOTFS
install -m 644 rpi_files/cmdline.txt $BOOTFS
install -m 644 rpi_files/fstab $ROOTFS/etc
install -m 644 rpi_files/hosts $ROOTFS/etc/hosts
install -m 755 rpi_files/resize2fs_once $ROOTFS/etc/init.d

mv $ROOTFS/etc/asterisk $ROOTFS/etc/asterisk.old
cp -a asterisk $ROOTFS/etc/asterisk

# Always enable SSH
touch $BOOTFS/ssh

HOST=repeater

openDiskImage

sed -i "s/BOOTDEV/PARTUUID=${DISKUUID}-01/" "${ROOTFS}/etc/fstab"
sed -i "s/ROOTDEV/PARTUUID=${DISKUUID}-02/" "${ROOTFS}/etc/fstab"
sed -i "s/ROOTDEV/PARTUUID=${DISKUUID}-02/" "${BOOTFS}/cmdline.txt"
sed -i "s/localhost\.localdomain/${HOST}/" "${ROOTFS}/etc/hostname"
sed -i "s/localhost/${HOST}/" "${ROOTFS}/etc/hosts"

cp rpi_files/asterisk.service $ROOTFS/etc/systemd/system

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
systemctl enable asterisk
systemctl disable asl-asterisk
systemctl disable hciuart.service
systemctl disable bluealsa.service
systemctl disable bluetooth.service
EOT

chmod 755 $ROOTFS/setup.sh
chroot $ROOTFS /setup.sh
rm $ROOTFS/setup.sh

closeDiskImage

#zip the image
rm -f $DISKIMAGE.zip 2>/dev/null
zip $DISKIMAGE.zip $DISKIMAGE

# Creating stage file
Create_stagefile .build/binary_rpi
