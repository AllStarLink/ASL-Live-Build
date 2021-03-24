# ASL-Live-Build Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0-beta.6] - 2021-03-24

* Add dynamic MOTD on `/etc/issue` and `/etc/motd`
* Move customizations to .deb packages (ASL-Asterisk)
* Update versions in app_rpt
* Bug fixes

## [2.0.0-beta.5] - 2021-02-27

* Change default hangtimes to favor simplex nodes in rpt.conf
* Add bashrc alias for `first-time` script to work without sudo
* Fix Register string behavior weirdness in asl-menu
* Add tomorse script to convert text to morse code wav files. Morse code generation in ASL isn't always consistent, so allowing static generation fixes this. use `tomorse -h` for help.
* Don't bother with root password in `first-time` - just set `repeater`'s password.
* Bring over sounds dir from ASL 1.01 Fix #10
* Tweak menus and add `/etc/motd`
* Add credits for contributors
* Fix paths in asl-menu and first-time. Fix #12 and #13
* Cleanup. Fix paths. Add CPANM and locate. Fix paths for astdb.txt.
* Remove asl images on `make clean`
* Fix greps for asterisk ports, ssh, and mylanip for supermon
* Reformat code for supermon. Fix several paths to work on Debian 10. Add www user to sudo. Add extra supermon scripts. Add config file for apache2 cgi-bin. Fix #9.

## [2.0.0-beta.4] - 2021-02-18

* Use log2ram, tmpfs (memory) for /var/log to relieve SD card stress on Pi
* Use tmpfs (memory) for /tmp on Pi
* Use differential node updater (asl-nodes-diff) to improve registration
* Add Broadcastify streaming support for Raspberry Pi
* Make version bumps easier
* Fix kernel headers issue with Pi
* Fix echolink.conf warning in ast-menu
* Fix URL path for allmondb

## [2.0.0-beta.3] - 2021-02-11

* Add nomodeset option for Intel Graphics issues on BIOS (Non-EFI) PCs
* Add timeouts to Intel/AMD bootloaders. Remove linux-headers because it breaks the binary stage
* Fix menu on EFI machines. Customize menu on non-EFI
* asl-menu bug fixes resolve #3
* Fix bug with raspberrypi-kernel-headers causing build Pi to fail

## [2.0.0-beta.2] - 2021-02-05

- Add Supermon by default
- Add Allmon2
- Armhf fixes
- Use live-build hooks instead of scripts so PC and Pi can have parity
- Use systemd service instead of init.d service
- Refactoring of build scripts

## [2.0.0-beta] - 2021-01-31

What’s new in ASL 2.0.0-beta?

- Runs on Raspberry Pi 2, 3 and 4 as well as Intel-AMD.
- One OS and code base across all platforms.
- Survives kernel updates.
- New cli> command rpt lookup <node#> (ala HamVoIP) to resolve IP address of any node.
- Most of the C code has been refactored to compile with current compiler.
- Http registration with failover to IAX2.
- Multi-thread
- Use libcurl for statpost instead of shell call to wget
- Update startup/shutdown scripts to query systemctl for service status
- Clean up output from cli>rpt local nodes
- Updates to asterisk service management convenience scripts
- Fix compiler warnings, perhaps better system stability
- Collect perceived ip, port and refresh time from http registration
- Add libi2c-dev to list of build dependencies
- Add registerhttp and registeriax directives to chan_iax2
- Inclusion of systemd service file for updatenodelist
- Implementation of console command rpt lookup
- Fix ASTDATADIR location
- Implement http(s) registration
- Include app_rpt in the build package
- Merge in telemetry ducking
- Merge in upstream alignment
- Merge in simple voter
- Merge in http registration
- Change naming convention to asl-asterisk
- Depend on new asl-dahdi packages
- Compile with modern gcc
- Repackage various AllStar pieces into new Debian packages
