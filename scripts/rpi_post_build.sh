#!/bin/bash
echo ####################################
echo #                                  #
echo #                                  #
echo #          post_build.sh           #
echo #                                  #
echo #                                  #
echo ####################################

# Finally, zip the image
rm -f $DISKIMAGE.zip 2>/dev/null
zip $DISKIMAGE.zip $DISKIMAGE

