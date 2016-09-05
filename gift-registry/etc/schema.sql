/* MySQL script to create CCHS tables. */

DROP TABLE IF EXISTS `CCHS_PLEDGE`;
DROP TABLE IF EXISTS `CCHS_ITEM`;
DROP TABLE IF EXISTS `CCHS_DONOR`;
DROP TABLE IF EXISTS `CCHS_DEPT`;

DROP TABLE IF EXISTS `CCHS_USER`;
DROP TABLE IF EXISTS `CCHS_ACCESS_TOKEN`;

DROP TABLE IF EXISTS `PL2010_TMP_TABLE`;

COMMIT;

/*--------------------------------------------------------------
 * CCHS_USER {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_USER` (
	ID INTEGER AUTO_INCREMENT,

	NAME VARCHAR(64) NOT NULL,          -- user name
	ROLES VARCHAR(128),                 -- comma-separated list of role names

	UNIQUE INDEX (NAME),
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 100
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_ACCESS_TOKEN {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_ACCESS_TOKEN` (
	ID INTEGER AUTO_INCREMENT,

	TOKEN VARCHAR(128) NOT NULL,        -- external representation of token
	EXPIRATION TIMESTAMP,               -- expiration time
	ATTRS_JSON VARCHAR(512),            -- attributes in JSON

	UNIQUE INDEX (TOKEN),
	INDEX (EXPIRATION),
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_DEPT {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_DEPT` (
	ID INTEGER AUTO_INCREMENT,
	LUD_WHEN TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	LUD_SEQ INTEGER DEFAULT 0,
	LUD_WHO VARCHAR(32),

	NAME VARCHAR(32) NOT NULL,

	UNIQUE INDEX (NAME),
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 10
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_ITEM {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_ITEM` (
	ID INTEGER AUTO_INCREMENT,
	LUD_WHEN TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	LUD_SEQ INTEGER DEFAULT 0,
	LUD_WHO VARCHAR(32),

	NAME VARCHAR(80) NOT NULL,			-- name of the item
	DESCRIPTION VARCHAR(160),			-- description of the item
	DEPT INTEGER NOT NULL,				-- department
	QUANTITY INTEGER DEFAULT 1,			-- quantity
	UNIT_COST INTEGER NOT NULL,			-- unit cost (in dollars)
--	DEADLINE DATE,						-- needed by this data
	IMAGE_URL VARCHAR(250),				-- URL to item image
	DETAIL_URL VARCHAR(250),			-- URL to item detail
	NOTES TEXT,							-- miscellaneous notes

	UNIQUE INDEX (NAME),
--	INDEX (DEADLINE),
	FOREIGN KEY (DEPT) REFERENCES CCHS_DEPT(ID) ON DELETE CASCADE,
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 100
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_DONOR {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_DONOR` (
	ID INTEGER AUTO_INCREMENT,
	LUD_WHEN TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	LUD_SEQ INTEGER DEFAULT 0,
	LUD_WHO VARCHAR(32),

	/* donor status */
	STATUS ENUM(
		'A',	-- active/current
		'X'		-- deleted
	) NOT NULL DEFAULT 'A',

	/* various flags and arbitary tags */
	F_ALUMNUS BOOLEAN DEFAULT FALSE,	-- alumnus
	F_BOARD BOOLEAN DEFAULT FALSE,		-- board member
	F_OTHERS BOOLEAN DEFAULT FALSE,		-- others
	F_MARKED BOOLEAN DEFAULT FALSE,		-- a temporary mark for complex queries
	TAGS VARCHAR(32),					-- arbitrary tag(s)

	/* name and personal data */
	LAST_NAME VARCHAR(32),				-- first name
	FIRST_NAME VARCHAR(32),				-- last name

	/* email address and phone numbers */
	EMAIL VARCHAR(48),					-- email address
--	PHONE_HOME VARCHAR(20),				-- home phone #
--	PHONE_WORK VARCHAR(20),				-- work phone #
--	PHONE_CELL VARCHAR(20),				-- cell phone #

	/* postal address */
--	ADDRESS VARCHAR(48),
--	CITY VARCHAR(32),
--	STATE VARCHAR(2),
--	ZIPCODE VARCHAR(10),

	NOTES TEXT,							-- miscellenous info

	INDEX (STATUS),
	INDEX (LAST_NAME, FIRST_NAME),
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 100
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_PLEDGE {{{
 */
CREATE TABLE IF NOT EXISTS `CCHS_PLEDGE` (
	ID INTEGER AUTO_INCREMENT,
	LUD_WHEN TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	LUD_SEQ INTEGER DEFAULT 0,
	LUD_WHO VARCHAR(32),

	/* pledge status */
	STATUS ENUM(
		'A',	-- active/current
		'X'		-- deleted
	) NOT NULL DEFAULT 'A',

	DONOR INTEGER NOT NULL,				-- donor ID
	ITEM INTEGER NOT NULL,				-- item ID
	AMOUNT INTEGER NOT NULL,			-- dollar amount of pledge

	TSTAMP DATETIME,					-- time stamp
	LSTAMP VARCHAR(64),					-- location stamp (e.g. IP address)
	FULFILL DATE,						-- fulfillment date

	NOTES TEXT,							-- miscellaneous information

	INDEX (DONOR),
	FOREIGN KEY (DONOR) REFERENCES CCHS_DONOR(ID) ON DELETE CASCADE,
	FOREIGN KEY (ITEM) REFERENCES CCHS_ITEM(ID) ON DELETE CASCADE,
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 1000
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * PL2010_TMP_TABLE {{{
 */
CREATE TABLE IF NOT EXISTS `PL2010_TMP_TABLE` (
	ID INTEGER AUTO_INCREMENT,

	TMP_NAME VARCHAR(60) NOT NULL,		-- temporary table name
	GC_IDLE INTEGER NOT NULL,			-- idle time to be eligible for GC
	GC_EXPIRE TIMESTAMP NOT NULL,		-- expiration time

	UNIQUE INDEX (TMP_NAME),
	PRIMARY KEY (ID)
)
	ENGINE = InnoDB
	AUTO_INCREMENT = 10000
	DEFAULT CHARACTER SET = utf8
	COLLATE = utf8_general_ci
;
COMMIT;
/*}}}*/

/* vim: set ts=4 noexpandtab fdm=marker syntax=sql: */
