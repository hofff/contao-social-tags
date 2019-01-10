-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


CREATE TABLE `tl_page` (

  `bbit_st` varchar(255) NOT NULL default '',
  `bbit_st_type` varchar(255) NOT NULL default '',
  `bbit_st_title` varchar(255) NOT NULL default '',
  `bbit_st_site` varchar(255) NOT NULL default '',
  `bbit_st_url` varchar(1022) NOT NULL default '',
  `bbit_st_image` binary(16) NULL,
  `bbit_st_imageSize` varchar(255) NOT NULL default '',
  `bbit_st_description` varchar(1022) NOT NULL default '',
--  `bbit_st_curies` blob NULL,
--  `bbit_st_custom` blob NULL,
  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

