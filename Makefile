NAME = ASL-Live-Build
.PHONY: buildpi

clean:
	sudo rm -f asl-*.*
	sudo lb clean
	sudo rm -rf rootfs bootfs
	sudo mkdir -p chroot/etc
	sudo lb clean
config:
	sudo lb config
configpi:
	sudo lb config build_rpi
pi: clean configpi build
pc: clean config build 
build:
	sudo lb build

