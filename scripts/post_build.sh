#!/bin/bash
echo ####################################
echo #                                  #
echo #                                  #
echo #          post_build.sh           #
echo #                                  #
echo #                                  #
echo ####################################

mv $ROOTFS/etc/asterisk $ROOTFS/etc/asterisk.old
cp -a asterisk $ROOTFS/etc/asterisk

cp scripts/asterisk.service $ROOTFS/etc/systemd/system

cat <<EOT > $ROOTFS/setup.sh
#!/bin/bash
systemctl disable asl-asterisk
systemctl enable asterisk
sed -i 's/https:\/\/kc1kcc\.com/http:\/\/apt\.allstarlink\.org/g' /etc/apt/sources.list.d/live.list
EOT

chroot $ROOTFS bash /setup.sh
rm $ROOTFS/setup.sh

# Finally, zip the image
rm -f $DISKIMAGE.zip 2>/dev/null
zip $DISKIMAGE.zip $DISKIMAGE

