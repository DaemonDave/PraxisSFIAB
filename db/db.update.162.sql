ALTER TABLE `emailqueue_recipients` CHANGE `result` `result` ENUM( 'ok', 'failed', 'cancelled', 'bounced' ) NULL DEFAULT NULL;
