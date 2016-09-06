/* MySQL script to load base data into CCHS tables. */

/*--------------------------------------------------------------
 * Set up an anonymous donor and a general account
 */
INSERT INTO CCHS_DONOR(ID,LAST_NAME,FIRST_NAME) VALUES
	(1, '*Anonymous', 'Donor');
COMMIT;
INSERT INTO CCHS_USER(ID, NAME,ROLES) VALUES
	(100, 'cchs-friend', 'cap:CCHS_PLEDGE/1:create,cap:CCHS_DONOR/1:view');
COMMIT;

/*--------------------------------------------------------------
 * Create admin user(s) {{{
 */
INSERT INTO CCHS_USER(NAME,ROLES) VALUES
	('admin', 'dba');
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_DEPT {{{
 */
INSERT INTO CCHS_DEPT(ID, NAME) VALUES
	(11, 'Arts'),
	(12, 'English'),
	(13, 'Math'),
	(14, 'Music'),
	(15, 'Middle School'),
	(16, 'Chemistry'),
	(17, 'Media Center/Technology'),
	(18, 'History'),
	(19, 'Literature'),
	(20, 'Foreign Language'),
	(21, 'Sports/Fitness Room'),
	(22, 'Bible'),
	(23, 'Library'),

	(10, 'Miscellaneous');
COMMIT;
/*}}}*/

/* vim: set ts=4 noexpandtab fdm=marker syntax=sql: */
