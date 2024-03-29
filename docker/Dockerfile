ARG ARCH
ARG OS

FROM $ARCH/debian:$OS

ARG ARCH
ARG OS
ARG USER_ID
ARG GROUP_ID

RUN addgroup --gid $GROUP_ID user; exit 0
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user; exit 0

# Install build dependencies
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    build-essential \
    binfmt-support \
    cpio \
    curl \
    devscripts \
    fakeroot \
    fdisk \
    zip \
    dosfstools \
    parted \
    debhelper \
    automake \
    autotools-dev \
    pkg-config \
    git \
    ca-certificates \
    qemu-user-static qemu-utils qemu-system-arm\
    live-build debootstrap\
    wget \
    --no-install-recommends

# hack to make live-build/debootstrap work in docker
RUN sed -i '1161s%umount%#umount%' /usr/share/debootstrap/functions

# add the allstarlink repo
RUN echo "deb https://apt.allstarlink.org/repos/asl_builds $OS main" > /etc/apt/sources.list.d/allstarlink.list
RUN wget -O - https://apt.allstarlink.org/repos/repo_signing.gpg | apt-key add -

# Import entrypoint script
COPY ./entrypoint.sh /entrypoint.sh

# Make Executable
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
