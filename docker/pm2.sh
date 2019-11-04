#!/bin/bash

. /home/node/.nvm/nvm.sh && cd /var/api/private/cron
pm2 start bulk_contribution_job.php
pm2 start daily_notification_job.php
pm2 start time_series_nightly_export.php
pm2 start time_series_rolling_purge.php
pm2 start weekly_notification_job.php
pm2 start daily_recalculate_site_attributes.php
pm2 start weekly_admin_report.php
pm2 start weekly_contributor_report.php