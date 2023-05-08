NAME = ASL-Live-Build
.PHONY: buildpi build config configpi

ifndef $(OS)
OS = buster
endif

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
	lb config --distribution $(OS)
configpi:
	lb config build_rpi --distribution $(OS)
pi: clean configpi build
pc: clean config build 
build:
	lb build

