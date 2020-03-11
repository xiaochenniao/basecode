#!/bin/bash
ps aux | grep $1 | grep -v grep | grep -v "kill.sh" | awk '{print$2}' | xargs kill -9
