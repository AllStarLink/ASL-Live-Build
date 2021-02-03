#!/bin/bash
echo ####################################
echo #                                  #
echo #                                  #
echo #          post_build.sh           #
echo #                                  #
echo #                                  #
echo ####################################

cat <<EOT > $ROOTFS/setup.sh
#!/bin/bash
systemctl disable asl-asterisk
systemctl enable asterisk
apt -y remove auto-apt-proxy
sed -i 's/https:\/\/kc1kcc\.com/http:\/\/apt\.allstarlink\.org/g' /etc/apt/sources.list.d/live.list
EOT

chroot $ROOTFS bash /setup.sh
rm $ROOTFS/setup.sh

# Finally, zip the image
rm -f $DISKIMAGE.zip 2>/dev/null
zip $DISKIMAGE.zip $DISKIMAGE

