/* MySQL script to add test/example data to CCHS tables. */

/*--------------------------------------------------------------
 * CCHS_ITEM {{{
 */
INSERT INTO CCHS_ITEM(DEPT,NAME,DESCRIPTION,UNIT_COST,QUANTITY,IMAGE_URL) VALUES
	(11, 'Mac PC',      'High-end Apple iMac PC', 1500,   4, 'https://store.storeimages.cdn-apple.com/8748/as-images.apple.com/is/image/AppleInc/aos/published/images/i/ma/imac/gallery4/imac-gallery4-2015'),
	(14, 'Music Stand', 'Music Stands',             30,   8, 'https://images-na.ssl-images-amazon.com/images/I/31VrzVVnmdL._AC_US160_.jpg'),

	(10, 'Rams 111',    'Some item #111',            1,  10, NULL),
	(10, 'Rams 222',    'Some item #222',            2,  20, NULL),
	(10, 'Rams 333',    'Some item #333',            3,  30, NULL),
	(10, 'Rams 444',    'Some item #444',            4,  40, NULL),
	(10, 'Rams 555',    'Some item #555',            5,  50, NULL),
	(10, 'Rams 666',    'Some item #666',            6,  60, NULL),
	(10, 'Rams 777',    'Some item #777',            7,  70, NULL),
	(10, 'Rams 888',    'Some item #888',            8,  80, NULL),
	(10, 'Rams 999',    'Some item #999',            9,  90, NULL),
	(10, 'Rams AAA',    'Some item #AAA',           10, 100, NULL),
	(10, 'Rams BBB',    'Some item #BBB',           11, 110, NULL),
	(10, 'Rams CCC',    'Some item #CCC',           12, 120, NULL),
	(10, 'Rams DDD',    'Some item #DDD',           13, 130, NULL),
	(10, 'Rams EEE',    'Some item #EEE',           14, 140, NULL),
	(10, 'Foobar',      'Food Bars',                 1,  10, NULL);
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_DONOR {{{
 */
INSERT INTO CCHS_DONOR(LAST_NAME,FIRST_NAME,EMAIL) VALUES
  ('Doe',     'John', 'john.doe@example.com'),
  ('Jane',    'Mary', 'mary.jane@yahoo.local'),
  ('Eckert',  'Edna', 'edna.eckert@yahoo.local');
COMMIT;
/*}}}*/

/*--------------------------------------------------------------
 * CCHS_PLEDGE {{{
 */

/* vim: set ts=2 expandtab fdm=marker nowrap syntax=sql: */
