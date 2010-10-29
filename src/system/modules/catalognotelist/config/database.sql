-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- 
-- Table `tl_catalog_fields`
-- 

CREATE TABLE `tl_catalog_fields` (
  `notelistvariants` text NULL
  `notelistselamount` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `tl_form_field` (
  `catalog` int(10) NOT NULL default '0',
  `catalog_visible` text NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
