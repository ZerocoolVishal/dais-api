ALTER TABLE `tbl_feasibility_report` ADD `existing_built_up_area` FLOAT NULL AFTER `plot_size`;
ALTER TABLE `tbl_feasibility_report` CHANGE `plot_size` `plot_size` FLOAT NULL DEFAULT NULL;

/* Added on 07 Mar 2021 */
ALTER TABLE `tbl_feasibility_report` ADD `is_payment_processed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_paid`;
