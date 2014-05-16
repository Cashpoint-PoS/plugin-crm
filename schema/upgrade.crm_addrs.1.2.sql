ALTER TABLE crm_addrs
CHANGE COLUMN city city text NOT NULL AFTER postal_code,
CHANGE COLUMN name name text NOT NULL AFTER prefix_line,
CHANGE COLUMN house_nr house_nr text NOT NULL AFTER street,
CHANGE COLUMN country country text NOT NULL AFTER city,
CHANGE COLUMN creator creator int(11) NOT NULL DEFAULT '0' AFTER last_editor,
CHANGE COLUMN line3 line3 text NOT NULL AFTER line2,
CHANGE COLUMN formatted formatted text NOT NULL AFTER country,
CHANGE COLUMN line2 line2 text NOT NULL AFTER name,
CHANGE COLUMN street street text NOT NULL AFTER line3,
CHANGE COLUMN create_time create_time bigint(20) NOT NULL DEFAULT '0' AFTER modify_time,
CHANGE COLUMN modify_time modify_time bigint(20) NOT NULL DEFAULT '0' AFTER creator,
CHANGE COLUMN postal_code postal_code text NOT NULL AFTER house_nr,
CHANGE COLUMN prefix_line prefix_line text NOT NULL AFTER crm_customers_id,
CHANGE COLUMN last_editor last_editor int(11) NOT NULL DEFAULT '0' AFTER formatted;
