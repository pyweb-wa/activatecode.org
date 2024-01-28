ip link add link eno8303 name eno8303.4000 type vlan id 4000
ip link set eno8303.4000 mtu 1400
ip link set dev eno8303.4000 up
ip addr add 192.168.100.20/24 brd 192.168.100.255 dev eno8303.4000

