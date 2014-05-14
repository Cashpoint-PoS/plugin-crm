ALTER TABLE crm_addrs
CHANGE COLUMN creator creator int(11) NOT NULL DEFAULT '0',
CHANGE COLUMN create_time create_time bigint(20) NOT NULL DEFAULT '0',
CHANGE COLUMN modify_time modify_time bigint(20) NOT NULL DEFAULT '0',
CHANGE COLUMN last_editor last_editor int(11) NOT NULL DEFAULT '0';
