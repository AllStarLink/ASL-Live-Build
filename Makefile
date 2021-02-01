NAME = ASL-Live-Build
.PHONY: buildpi

clean:
	lb clean
	rm -rf rootfs bootfs
	mkdir -p chroot/etc || true
config:
	lb config
configpi:
	lb config build_rpi
pi: clean configpi build
pc: clean config build 
build:
	lb build

