#!/bin/bash
# Inicia o cron em background
cron &
# Inicia o Apache
apache2-foreground
