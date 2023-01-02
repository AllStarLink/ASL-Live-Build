NAME = ASL-Live-Build
.PHONY: buildpi build config configpi

reallyclean:
	rm -f asl-*.*
	lb clean
	rm -rf rootfs bootfs
	mkdir -p chroot/etc
	lb clean
clean:
	rm -f asl-*.*
	lb clean
config:
	lb config
configpi:
	lb config build_rpi
pi: clean configpi build
pc: clean config build 
build:
	lb build

