ALTER TABLE crm_customers
CHANGE COLUMN type type int(11) NOT NULL DEFAULT '0' COMMENT '0: unbekannt 1: Nat. Person 2: Rechtl. Person',
CHANGE COLUMN person_gender person_gender int(11) NOT NULL DEFAULT '0';
