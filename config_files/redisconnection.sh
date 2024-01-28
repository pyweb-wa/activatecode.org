#!/bin/bash
redis-cli -h 192.168.100.20 -a foobared.123 CLIENT LIST | wc -l | tail -n 1
