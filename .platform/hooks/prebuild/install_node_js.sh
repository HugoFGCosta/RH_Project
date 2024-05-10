#!/bin/sh
set -e  # Exit on error

# Check if Node.js is installed and get the version
node_version=$(node --version 2>/dev/null || echo "none")

# Check if the version is not the one required
if [[ "$node_version" != "v16."* ]]; then
  echo "Installing Node.js v16.x"

  # Remove existing Node.js and clean caches
  sudo yum remove -y nodejs
  sudo rm -rf /var/cache/yum/*
  sudo yum clean all

  # Install Node.js v16
  curl -sL https://rpm.nodesource.com/setup_16.x | sudo -E bash -
  sudo yum install -y nodejs
else
  echo "Node.js v16.x is already installed."
fi
