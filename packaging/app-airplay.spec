
Name: app-airplay
Epoch: 1
Version: 2.0.0
Release: 1%{dist}
Summary: AirPlay
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
AirPlay provides the nessessary tools to stream content to Apple devices on the network.

%package core
Summary: AirPlay - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: avahi

%description core
AirPlay provides the nessessary tools to stream content to Apple devices on the network.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/airplay
cp -r * %{buildroot}/usr/clearos/apps/airplay/


%post
logger -p local6.notice -t installer 'app-airplay - installing'

%post core
logger -p local6.notice -t installer 'app-airplay-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/airplay/deploy/install ] && /usr/clearos/apps/airplay/deploy/install
fi

[ -x /usr/clearos/apps/airplay/deploy/upgrade ] && /usr/clearos/apps/airplay/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-airplay - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-airplay-core - uninstalling'
    [ -x /usr/clearos/apps/airplay/deploy/uninstall ] && /usr/clearos/apps/airplay/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/airplay/controllers
/usr/clearos/apps/airplay/htdocs
/usr/clearos/apps/airplay/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/airplay/packaging
%dir /usr/clearos/apps/airplay
/usr/clearos/apps/airplay/deploy
/usr/clearos/apps/airplay/language
/usr/clearos/apps/airplay/libraries
