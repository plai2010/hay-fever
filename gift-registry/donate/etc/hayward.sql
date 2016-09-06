/* MySQL script to load Hayward campaign data to CCHS tables. */

/*--------------------------------------------------------------
 * CCHS_ITEM {{{
 */
INSERT INTO CCHS_ITEM(
	ID, DEPT,
	QUANTITY, UNIT_COST,
	NAME,
	DESCRIPTION,
	IMAGE_URL,
	DETAIL_URL,
	NOTES
) VALUES(
	100, 23,
	1, 1164,
	'Library Book Return',
	'A locking book return system so library items can be returned at any time the campus is available.',
	'http://www.thelibrarystore.com/images/uploads/indoor_outdoor_book_returns/AD92-40194.jpg',
	'http://www.thelibrarystore.com/product/ad92-40194/indoor_returns',
	NULL
), (
	101, 23,
	1, 550,
	'Touch screen circulation computer monitor.',
	'Lenovo - 700-22ISH 21.5\" Touch-Screen All-In-One - Intel Pentium - 8GB Memory - 1TB Hard Drive - Black',
	'http://pisces.bbystatic.com/image2/BestBuy_US/images/products/5196/5196200_sd.jpg;canvasHeight=550;canvasWidth=642',
	'http://www.bestbuy.com/site/lenovo-700-22ish-21-5-touch-screen-all-in-one-intel-pentium-8gb-memory-1tb-hard-drive-black/5196200.p?id=bb5196200&skuId=5196200',
	NULL
), (
	102, 23,
	1, 290,
	'Voyager MS9520 Laser Bar Code Scanner with PS USB Cable',
	'Automatic trigger barcode scanner, formatted for Follette circulation system',
	'http://www.thelibrarystore.com/images/uploads/library_automation/AD13-08120.jpg',
	'http://www.thelibrarystore.com/product/ad13-08120/bar_code_scanners',
	NULL
), (
	103, 23,
	1, 499,
	'The Emancipation Project',
	'Primary sources that give students access to slave narratives collected in the 1930s, from the lasting surviving slaves, born before 1865',
	'https://cdn.nexternal.com/wgen/images/Quests_EmancipationProjectKitContents_Main1.jpg',
	'https://store.amplify.com/quests-for-the-core-the-emancipation-project-p385.aspx',
	NULL
), (
	104, 23,
	6, 1085,
	'Library Store GLOBAL Sirena(TM) Lounge Seating - Lounge Chair with Tablet',
	'Single-seat armchair, with work surface; Color - Flare',
	'http://www.thelibrarystore.com/images/uploads/library_furniture/19-01015.jpg',
	'http://www.thelibrarystore.com/product/19-01015/h',
	NULL
), (
	105, 23,
	1, 23,
	'African American Almanac: 400 Years of Triumph, Courage, and Excellence by Brack',
	'A detailed account of the African American lives from when they first arrived in America.',
	'http://ecx.images-amazon.com/images/I/51R8e-1fPVL._SL160_.jpg',
	'http://www.booklistonline.com/African-American-Almanac-400-Years-of-Triumph-Courage-and-Excellence-Leantin-Bracks/pid=5204855',
	NULL
), (
	106, 23,
	1, 50,
	'African Americans in Science, Math, and Invention by Spangenburg, Ray and Kit Mo',
	'An extensive account of African American achievements in science, math, and their inventions.',
	'http://ecx.images-amazon.com/images/I/512WsIPwnfL._SL160_.jpg',
	'http://www.booklistonline.com/African-Americans-in-Science-Math-and-Invention-Ray-Spangenburg/pid=5204832',
	NULL
), (
	107, 23,
	1, 20,
	'Collapse: How Societies Choose to Fail or Succeed: Revised Edition by Jared Diam',
	'Diamond explains how Western societies developed technology in order to dominate the world.',
	'https://images-na.ssl-images-amazon.com/images/I/51a97YF7wML.jpg',
	'https://www.amazon.com/Collapse-Societies-Choose-Succeed-Revised-ebook/dp/B004H0M8EA/ref=sr_1_1?s=books&ie=UTF8&qid=1470764188&sr=1-1&keywords=collapse+jared+diamond',
	NULL
), (
	108, 23,
	1, 17,
	'1491: New Revelations of the Americas Before Columbus by Charles C. Mann',
	'Mann describes the life of America before the arrival of Christopher Columbus.',
	'https://images-na.ssl-images-amazon.com/images/I/51H78uwFowL._SX323_BO1,204,203,200_.jpg',
	'https://www.amazon.com/1491-Revelations-Americas-Before-Columbus/dp/1400032059/ref=sr_1_1?ie=UTF8&qid=1470763883&sr=8-1&keywords=1491',
	NULL
), (
	109, 23,
	1, 18,
	'1493: Uncovering the New World Columbus Created by Charles C. Mann',
	'Mann describes how European settlement on America changed the world',
	'https://images-na.ssl-images-amazon.com/images/I/51qTzgAVajL._SX322_BO1,204,203,200_.jpg',
	'https://www.amazon.com/1493-Uncovering-World-Columbus-Created/dp/0307278247/ref=pd_bxgy_14_img_2?ie=UTF8&psc=1&refRID=BQQ1XQE2WGQRQFRD5HWB',
	NULL
), (
	110, 23,
	1, 30,
	'Guns, Germs, and Steel: The Fates of Human Societies by Jared Diamond',
	'Diamond explains the strategies certain continents used to conquer others.',
	'https://images-na.ssl-images-amazon.com/images/I/51c2SFXFpzL._SX327_BO1,204,203,200_.jpg',
	'https://www.amazon.com/Guns-Germs-Steel-Fates-Societies/dp/0393061310/ref=mt_hardcover?_encoding=UTF8&me=',
	NULL
), (
	111, 23,
	1, 30,
	'Tesla: Inventor of the Electrical Age by W. Bernard Carlson',
	'Detailed account of the inventor Tesla, a large contributor for the electrical revolution',
	'https://images-na.ssl-images-amazon.com/images/I/51LB4VCxphL._SX329_BO1,204,203,200_.jpg',
	'https://www.amazon.com/Tesla-Inventor-Electrical-Bernard-Carlson/dp/0691057761/ref=mt_hardcover?_encoding=UTF8&me=',
	NULL
), (
	112, 23,
	1, 2000,
	'Library Circulation Database',
	'An online school library management system software',
	NULL,
	'http://www.follettlearning.com/technology/products/library-management-system',
	NULL
), (
	113, 11,
	1, 2384,
	'Multiplex Swinging Panel Display',
	'Panel Size 24\" x 36\" 20 Panels. Space-saving units display student art, pictures, activities, sports, etc.',
	'http://cdn.dick-blick.com/items/509/14/50914-1005-2ww-m.jpg',
	'http://www.dickblick.com/products/multiplex-swinging-panel-display/#description',
	NULL
), (
	114, 11,
	2, 369,
	'Wacom Intuos Creative Pen Tablet',
	'Tablets with pen for art and design students who want professional results.',
	'http://cdn.dick-blick.com/items/227/24/22724-1008-1-2ww-m.jpg',
	'http://www.dickblick.com/products/wacom-intuos-pro-pen-and-touch-medium-tablet/#description',
	NULL
), (
	115, 11,
	1, 750,
	'Canon EOS Rebel T6 Digital SLR Camera Kit',
	'High performance camera kit for expert picture quality and picture taking.',
	'http://shop.usa.canon.com/wcsstore/ExtendedSitesCatalogAssetStore/eos-rebel-double-zoom-lens-kit_1_xl.jpg',
	'https://www.amazon.com/Canon-Digital-Camera-18-55mm-75-300mm/dp/B01CQJHJ2E/ref=cm_sw_em_r_dp3PQMxbM0SF3EE_lm',
	NULL
), (
	116, 11,
	1, 21,
	'Wasabi Power Battery (2-Pack) and Charger',
	'Battery and charger for Canon cameras.',
	'https://s-media-cache-ak0.pinimg.com/736x/ee/d1/4e/eed14e92e4dac392da6b07c7299080be.jpg',
	'https://www.amazon.com/gp/product/B00BBE3ABA/ref=cm_sw_em_r_dp3PQMxbM0SF3EEtt_',
	NULL
), (
	117, 11,
	1, 15,
	'Lexar Professional 633x 16GB SDHC UHS-I/U1 Card',
	'Storage cards for camera photos; 16GB',
	'http://www.bhphotovideo.com/images/images1000x1000/lexar_lsd16gcbnl633_lexar_1131702.jpg',
	'https://www.amazon.com/gp/product/B00VBNQK0E/ref=cm_sw_em_r_dp3PQMxbM0SF3EEtt_',
	NULL
), (
	118, 11,
	1, 22,
	'Lexar Professional 633x 64GB SDXC UHS-I Card',
	'Storage cards for camera photos; 64GB',
	'https://www.selloscope.com/hrimg/ecx.images-amazon.com/images/I/61%252BncmEpajL.jpg/Lexar-Professional-633x-64GB-SDXC-UHS-I-Card-wImage-Rescue-5-Software-LSD64GCB1NL633-2-hires.jpg',
	'https://www.amazon.com/gp/product/B012PL6K8M/ref=cm_sw_em_r_dp3PQMxbM0SF3EEtt_',
	NULL
), (
	119, 11,
	1, 15,
	'58MM Professional Lens Filter Accessory Kit',
	'Camera accessory kit including: Vivitar Filter Kit (UV, CPL, FLD) + Carry Pouch + Tulip Lens Hood + Snap-On Lens Cap w/ Cap Keeper Leash + MagicFiber Microfiber',
	'https://images-na.ssl-images-amazon.com/images/I/711gTqt4yxL._SY355_.jpg',
	'https://www.amazon.com/gp/product/B0053V5MF4/ref=cm_sw_em_r_dp3PQMxbM0SF3EEtt_',
	NULL
), (
	120, 14,
	4, 2375,
	'4-Step Choral Risers',
	'Choral risers for music and chorale rehearsals (including siderails)',
	'https://www.wengercorp.com/img/product/SignatureRisers_Larger.jpg',
	'https://www.wengercorp.com/risers/signature-choral-risers.php',
	NULL
), (
	121, 14,
	1, 350,
	'Music stand cart',
	'Cart to easily move music stands from the rehearsal room to the performance stage.',
	'https://www.wengercorp.com/img/stands/small-stand-move-store-cart.jpg',
	'https://www.wengercorp.com/stands/small-music-stand-move-and-store-cart.php',
	NULL
), (
	122, 14,
	1, 500,
	'Congas',
	'Wood drum instruments',
	'http://www.sweetwater.com/images/items/750/LPA646AW-large.jpg',
	'http://www.guitarcenter.com/LP/Aspire-Conga-Set-with-Free-Bongos-Natural-1389714412585.gc',
	NULL
), (
	123, 14,
	1, 9000,
	'Music Library System',
	'Cabinet system to create optimal organization of music sheets and other materials.',
	'https://www.wengercorp.com/img/storage/MusicLibrary_Maple.jpg',
	'https://www.wengercorp.com/storage/music-library-system.php',
	NULL
), (
	124, 14,
	10, 66,
	'Music Stands',
	'Music stands for sheet music',
	'https://www.wengercorp.com/img/stands/classic-50-stand.jpg',
	'https://www.wengercorp.com/stands/classic-50-stand.php',
	NULL
), (
	125, 14,
	1, 9000,
	'Instrument storage',
	'Place to store instruments and create space.',
	'https://www.wengercorp.com/websiteimages/storage/full/acoustic-cabinetsingleSML_C.jpg',
	'https://www.wengercorp.com/storage/acousticabinets.php',
	NULL
), (
	126, 14,
	1, 1500,
	'Choral folder cabinet',
	'Music cabinet for storage.',
	'https://www.schoolsin.com/Merchant5/graphics/00000001/89356_485520_C.jpg',
	'https://www.schoolsin.com/choral_music_storage/STE-89356-485520-D.html',
	NULL
), (
	127, 14,
	1, 600,
	'Piano Rack',
	'Piano dollie with locking wheels to assist with moving',
	'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRiNXZc28RCu7R11NAyiYKGKQypqwaBifcVLNR1-jluCsnVnvARbg',
	'http://www.vandaking.com/schaff-grand-truck-dollies-do-s-4012.html',
	NULL
), (
	128, 14,
	1, 115,
	'Piano Lock',
	'Piano lock to help secure a piano; comes with built in Masterlock',
	'https://images-na.ssl-images-amazon.com/images/I/418FQFsebPL.jpg',
	'https://www.amazon.com/Hands-Off-Piano-Fallboard-Lock-Absolute/dp/B005FUKBLS/ref=sr_1_1?ie=UTF8&qid=1469463202&sr=8-1&keywords=Piano+lock',
	NULL
), (
	129, 15,
	1, 175,
	'Djembe',
	'Meinl Percussion HDJ3-M Black River Series Headliner Rope Tuned Djembe, Medium: 10-Inch Diameter',
	'http://ak1.ostkcdn.com/images/products/9188390/Meinl-Percussion-HDJ3-M-River-Series-Headliner-Medium-Rope-Tuned-10-inch-Diameter-Djembe-21f31dea-535f-47e0-b176-0bfae2969ef7_600.jpg',
	'https://www.amazon.com/Meinl-Percussion-HDJ3-M-Headliner-Djembe/dp/B004LRZC6A/ref=sr_1_2?s=musical-instruments&ie=UTF8&qid=1470812545&sr=1-2&keywords=djembe',
	NULL
), (
	130, 15,
	1, 230,
	'Remo Talking Drum',
	'18\" x 8\" African fabric striped drum.',
	'http://media.musiciansfriend.com/is/image/MMGS7/Talking-Drum-Fabric-African-Stripe/442253000357000-00-120x120.jpg',
	'https://www.amazon.com/dp/B0002ODT0W?psc=1',
	NULL
), (
	131, 15,
	1, 45,
	'Axatse African Shaker',
	'Handmade African maraca made in Ghana.',
	'https://images-na.ssl-images-amazon.com/images/I/41UTHsZiQaL.jpg',
	'https://www.amazon.com/Axatse-African-Shaker-Maraca-Shekere/dp/B003ZJJS6U/ref=sr_1_47?s=musical-instruments&ie=UTF8&qid=1470813629&sr=1-47&keywords=shaker',
	NULL
/*
), (
	132, 18,
	0, 0,
	'',
	'',
	NULL,
	NULL,
	NULL
*/
), (
	133, 10,
	6, 120,
	'Table Top Lectern',
	'Oklahoma Sound 22-MY Table Top Lectern, 23-3/4\" Width x 13-3/4\" Height x 19-7/8\" Depth, Mahogany',
	'https://images-na.ssl-images-amazon.com/images/I/51GWrUzxHQL._SX425_.jpg',
	'https://www.amazon.com/Oklahoma-Sound-22-MY-Lectern-Mahogany/dp/B007KX1QI0/ref=sr_1_1?s=office-products&ie=UTF8&qid=1463277159&sr=1-1&refinements=p_n_location_browse-bin%3A2729396011',
	NULL
), (
	134, 15,
	1, 46,
	'Exercise Ball with Pump, Gym Quality Fitness Ball by DynaPro Direct',
	'Exercise ball for students who want to concentrate on studying.',
	'https://images-na.ssl-images-amazon.com/images/I/51ewPHtnioL._SY300_.jpg',
	'https://www.amazon.com/dp/B00YI8W60C/ref=twister_dp_update?_encoding=UTF8&psc=1',
	NULL
), (
	135, 10,
	1, 58,
	'Easel Pad',
	'Office DepotÂ® Brand Bleed Resistant Self-Stick Easel Pads, 25\" x 30\", 40 Sheets, 30% Recycled, White, Pack Of 2',
	'http://s7d1.scene7.com/is/image/officedepot/210981_p_od_st40r_2_cfop_cmyk?$OD-Dynamic$&wid=450&hei=450',
	'http://www.officedepot.com/a/products/210981/Office-Depot-Brand-Bleed-Resistant-Self/',
	NULL
), (
	136, 13,
	50, 150,
	'TI-84 Plus CE Graphing Calculator',
	'Black graphing calculator, with full color back lit display and a rechargeable battery.',
	'https://images-na.ssl-images-amazon.com/images/I/51oI9bAtCvL._SL500_AA130_.jpg',
	'https://www.amazon.com/gp/product/B00XOLOOPY?ref_=sr_1_2&s=office-electronics&qid=1472508006&sr=1-2&keywords=ti-84&pldnSite=1',
	NULL
), (
	137, 17,
	4, 850,
	'House Speakers',
	'Professional speakers for enhanced sound quality and improved durability.',
	'http://www.qsc.com/fileadmin/user_upload/q_loudspeakers_kseries_K12_img_heroFront.jpg',
	'http://www.qsc.com/live-sound/products/loudspeakers/powered-loudspeakers/k-series/k12/',
	NULL
), (
	144, 17,
	4, 150,
	'Speaker Stands',
	'Strong and lightweight speaker stands with automated speaker height adjustment capabilities.',
	'http://smhttp.41820.nexcesscdn.net/8016262/magento/media/catalog/product/cache/1/image/9df78eab33525d08d6e5fb8d27136e95/u/l/ult-ts100b_1.jpg',
	'http://www.ultimatesupport.com/products/speaker-lighting-stands/air-powered-series/ts-100b.html',
	NULL
), (
	146, 17,
	4, 973,
	'Wireless Vocal Microphone System',
	'Professional microphone system to be used in large gatherings and school events.',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/configuration/zoomable_image/198/max_desktop_QLXD14_WA305.jpg',
	'http://www.shure.com/americas/products/wireless-systems/qlxd-digital-wireless-systems/qlxd14',
	NULL
), (
	147, 17,
	4, 500,
	'Wireless Vocal Handheld Microphone',
	'Wireless microphone for school events',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/product/zoomable_image/9100/max_desktop_QLXD2_Beta-87A_Beta-87C.jpg',
	'http://www.shure.com/americas/products/wireless-systems/qlxd-digital-wireless-systems/qlxd2-beta87a',
	NULL
), (
	148, 17,
	20, 50,
	'XLR Microphone Cables',
	'Durable, flexible, and long lasting microphone cables.',
	'http://whirlwindusa.com/media/uploads/mk4.jpg',
	'http://whirlwindusa.com/catalog/pre-wired-cables/microphone/mk-series',
	NULL
), (
	149, 17,
	2, 150,
	'PC Cable Interface',
	'hirlwind pcDI Box',
	'http://whirlwindusa.com/media/uploads/pcdi_600w.jpg',
	'http://whirlwindusa.com/catalog/black-boxes-effects-and-dis/direct-boxes/pcdi',
	NULL
), (
	150, 17,
	2, 320,
	'Stage Curtains',
	'Easy to set up pipe and drape black stage background.',
	'https://images-na.ssl-images-amazon.com/images/I/61ZkdbmxuAL._SX522_.jpg',
	'https://www.amazon.com/OnlineEEI-BBD9990820-Pipe-Framework-Drapes/dp/B00EAR1XR8',
	NULL
), (
	151, 17,
	2, 2199,
	'Presentation Projector',
	'InFocus IN5312a Projector',
	'http://www.infocus.com/tmp/imagethumbs/e1405ec67b8cbb80218464c6377d5e05_InFocus-IN5312A-Hero.png',
	'http://www.infocus.com/projectors/large-venue/IN5312a',
	NULL
), (
	152, 17,
	2, 950,
	'Projection Screen',
	'120-inch, 16:9 Matte White Fabric Ceiling-Recessed Motorized Projection Screen',
	'http://images.monoprice.com/productlargeimages/73381.jpg',
	'http://www.monoprice.com/product?p_id=7338',
	NULL
), (
	153, 17,
	2, 475,
	'Stage Monitor Speakers',
	'QSC E10 Two Way Loud Speaker',
	'http://www.qsc.com/fileadmin/user_upload/q_loudspeakers_E_E10img_heroFront.jpg',
	'http://www.qsc.com/live-sound/products/loudspeakers/passive-loudspeakers/e-series/e10/',
	NULL
), (
	154, 17,
	4, 536,
	'Compressor / Limiter / Gate Sound Processors',
	'dbx 1066 Compressor / Limiter / Gate',
	'http://adn.harmanpro.com/product_attachments/product_attachments/496_1412965821/1066front_lg_tiny_square.jpg',
	'http://dbxpro.com/en-US/products/1066',
	NULL
), (
	155, 17,
	4, 350,
	'Reverb / Effects processor',
	'Lexicon MX400XL',
	'http://adn.harmanpro.com/product_attachments/product_attachments/876_1443632210/mx400xl_full_width.png',
	'http://lexiconpro.com/en-US/products/mx400xl',
	NULL
), (
	156, 17,
	4, 250,
	'Vocal Wired Microphones',
	'Shure BETA 87A Microphone; wired',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/product/zoomable_image/9410/max_desktop_Beta87A.jpg',
	'http://www.shure.com/americas/products/microphones/beta/beta-87a-vocal-microphone',
	NULL
), (
	157, 17,
	4, 150,
	'Active Direct Box for Instrument Interface',
	'Whirlwind DI Hotbox',
	'http://whirlwindusa.com/media/uploads/jpgs/hotboxd1.jpg',
	'http://whirlwindusa.com/catalog/black-boxes-effects-and-dis/direct-boxes/hotbox',
	NULL
), (
	158, 17,
	6, 799,
	'In-Ear Monitor System for Worship',
	'Shure P3TRA215CL',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/configuration/zoomable_image/210/max_desktop_P3T-P3RA-With-215-Earphones-Family_HR.jpg',
	'http://www.shure.com/americas/products/personal-monitor-systems/psm-300-stereo-personal-monitor-system/p3tra215cl',
	NULL
), (
	159, 17,
	1, 999,
	'Mixing Console for In-Ear Monitors for Worship',
	'Soundcraft GB2R16',
	'http://soundcraft.com.s3.amazonaws.com/products/main/gb2r.png?1420177534',
	'http://www.soundcraft.com/products/gb2r',
	NULL
), (
	161, 17,
	1, 2500,
	'Keyboard',
	'Roland RD-800 Stage Piano',
	'https://static.roland.com/products/rd-800/images/rd-800_hero.jpg',
	'https://www.roland.com/us/products/rd-800/',
	NULL
), (
	163, 17,
	1, 300,
	'Base Amp',
	'Fender Rumble 115 Cabinet',
	'http://www.fmicassets.com/Damroot/ZoomJpg/10001/2370900000_amp_frt_001_nr.jpg',
	'http://shop.fender.com/en-US/bass-amplifiers/rumble/rumble-115-cabinet/2370900000.html#start=1',
	NULL
), (
	164, 17,
	4, 630,
	'Headset Microphones for Drama',
	'DPA D:fine 66 Omnidirectional Headset Microphone',
	'http://www.dpamicrophones.com/DPA/media/DPA-Images/FIO66-dfine-Headset-Microphones-DPA-Microphones-L_1.jpg?ext=.jpg',
	'http://www.dpamicrophones.com/microphones/dfine/66-omnidirectional-headset-microphone',
	NULL
), (
	165, 17,
	4, 179,
	'Shure Instrument Microphones for Concerts',
	'Shure SM137',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/product/zoomable_image/8518/max_desktop_SM137_MedHR.jpg',
	'http://www.shure.com/americas/products/microphones/sm/sm137-instrument-microphone',
	NULL
), (
	194, 17,
	8, 279,
	'Sennheiser Instrument Microphones for Concerts',
	'Sennheiser e614',
	'http://en-us.sennheiser.com/img/1294/product_detail_x1_desktop_square_louped_e-614-sq-01-sennheiser.jpg',
	'http://en-us.sennheiser.com/instrument-microphone-polarized-condenser-percussion-woodwind-string-e-614',
	NULL
), (
	195, 17,
	2, 189,
	'Kick Drum Mic',
	'Shure BETA 52A',
	'https://522bb370f5443d4fe5b9-f62de27af599bb6703e11b472beadbcc.ssl.cf2.rackcdn.com/product/zoomable_image/8674/max_desktop_Beta52A.jpg',
	'http://www.shure.com/americas/products/microphones/beta/beta-52a-kick-drum-microphone',
	NULL
), (
	196, 17,
	6, 500,
	'Stage Lights',
	'Chauvet COLORdash Par-Quad 18; Lights for stage performances',
	'https://www.chauvetprofessional.com/wp-content/uploads/2015/06/prod_colordash_par_quad18_right.jpg',
	'https://www.chauvetprofessional.com/products/colordash-par-quad-18/',
	NULL
), (
	197, 17,
	20, 45,
	'Lighting Cables',
	'Chauvet DMX Cables (5\'-50\')',
	'https://www.chauvetprofessional.com/wp-content/uploads/2015/06/feat_dmx3f5m.jpg',
	'https://www.chauvetprofessional.com/accessories/',
	NULL
), (
	198, 17,
	1, 250,
	'Stage Light Controller',
	'Chauvet Stage Designer 50',
	'https://www.chauvetdj.com/wp-content/uploads/2015/12/Stage-Designer-50-FRONT.jpg',
	'https://www.chauvetdj.com/products/stage-designer-50/',
	NULL
), (
	199, 17,
	4, 700,
	'Stage Spot Light',
	'Chauvet COLORRado 1 Solo',
	'https://www.chauvetprofessional.com/wp-content/uploads/2015/09/prod_colorado_1_solo_right.jpg',
	'https://www.chauvetprofessional.com/products/colorado-1-solo/',
	NULL
), (
	200, 21,
	2, 799,
	'Airbike',
	'Assault Airbike-Black;Cardio training for fitness room',
	'http://www.roguefitness.com/media/catalog/product/cache/1/image/9df78eab33525d08d6e5fb8d27136e95/4/_/4__87790.1387843079.1280.1280.jpg',
	'http://www.assaultfitnessproducts.com/products/assault-airbike.html',
	NULL
), (
	201, 21,
	2, 2995,
	'S-DRIVE Treadmill',
	'Matrix - S-Drive Performance Trainer; Cardio training for fitness room',
	'http://productload.johnsonfit.com/inc/uploaded_media/86d3b4770549c11fd535328d2be54f64/image/0d50a8cc7046a16141ba4db901f82c9b.png',
	'http://www.matrixfitness.com/en/cardio/s-drive/s-drive',
	NULL
), (
	202, 21,
	1, 5995,
	'4 Station Strength Training',
	'Hoist- CMJ-6000-1;(Single Lat Station, Single Low Row, Tricep, HiLo)',
	'http://www.hoistfitness.com/content/images/equipment/360view/CMJ-6000-1/000014.jpg',
	'http://www.hoistfitness.com/commercial/equipment/cmj-6000-1_4-station-single-pod',
	NULL
), (
	203, 21,
	1, 3195,
	'Leg Curls',
	'Hoist-RS-1408, ROC-IT Selectorized Prone Leg Curls; Strength training for fitness room ',
	'http://www.hoistfitness.com/content/images/equipment/360view/RS-1408/000042.jpg',
	'http://www.hoistfitness.com/commercial/equipment/rs-1408_prone-leg-curl',
	NULL
), (
	204, 21,
	1, 4795,
	'Leg Press',
	'Hoist-RS-1403A, ROC-IT Selectorized Leg Press; Strength training for fitness room',
	'http://www.hoistfitness.com/content/images/equipment/360view/RS-1403/000045.jpg',
	'http://www.hoistfitness.com/commercial/equipment/rs-1403_leg-press',
	NULL
), (
	205, 21,
	1, 3195,
	'Chest Press',
	'Hoist - RS-1301A,ROC-IT Selectorized Chest Press; Strength training for fitness room',
	'http://www.hoistfitness.com/content/images/equipment/360view/RS-1301/000004.jpg',
	'http://www.hoistfitness.com/commercial/equipment/rs-1301_chest-press',
	NULL
), (
	206, 21,
	1, 5195,
	'Functional Trainer',
	'Matrix-G3MSFT3; Strength training for fitness room',
	'http://productload.johnsonfit.com/inc/uploaded_media/419057ba2a0b1c129885737eab5db27e/image/4c498ee138b85a49d7c4f8ac28a0a364.png',
	'http://www.matrixfitness.com/en/strength/multi-station/g3-msft3-functional-trainer',
	NULL
), (
	207, 21,
	2, 499,
	'TRIPLEPLYO',
	'Torque - XTP-20-24-30; Tripleplyo 20-24-30',
	'https://www.torquefitness.com/wp-content/uploads/XTP-20-24-30-web-550x550.jpg',
	'https://www.torquefitness.com/solutions/tripleplyo-20-24-30/',
	NULL
), (
	208, 21,
	3, 200,
	'Suspension Trainer',
	'TRX - TRXCLUB3; Commercial Suspension  Trainer (rubber handles, locking  carabiner)',
	'http://www.bestgymequipment.co.uk/media/catalog/product/cache/1/image/700x700/9df78eab33525d08d6e5fb8d27136e95/s/c/screen_shot_2011-10-08_at_12.16.55_1.jpg',
	'https://www.torquefitness.com/solutions/trx-commercial-suspension-trainer/',
	NULL
), (
	209, 21,
	1, 5995,
	'X-LAB EDGE Training Station',
	'Torque - XLE-X1-03; X-Lab Edge - X1 Package - Illusion Orange',
	'https://www.torquefitness.com/wp-content/uploads/XLE-X1-web1-550x550.jpg',
	'https://www.torquefitness.com/solutions/x-lab-edge-x1-package/',
	NULL
), (
	212, 21,
	1, 1995,
	'X-LAB Accessory Package',
	'Torque - XLAP1; X-Lab Accessory Package 1',
	'https://www.torquefitness.com/wp-content/uploads/XLAP1-web3-550x550.jpg',
	'https://www.torquefitness.com/solutions/x-lab-accessory-package/',
	NULL
), (
	213, 21,
	1, 335,
	'Olympic Plate Tree',
	'Torque - MOPT; Plate Tree',
	'https://www.torquefitness.com/wp-content/uploads/MOPT-web-550x550.jpg',
	'https://www.torquefitness.com/solutions/olympic-plate-tree/',
	NULL
), (
	214, 21,
	1, 1595,
	'Portable Training System',
	'Prism Fit - 400-130-290; Smart Cart Training System',
	'http://st.hzcdn.com/simgs/0611297905ca536a_4-8882/contemporary-home-gym-equipment.jpg',
	'http://www.houzz.com/photos/24683353/Prism-Fitness-Smart-Cart-Training-Systems-400-130-290-contemporary-home-gym-equipment',
	NULL
), (
	215, 21,
	2, 25,
	'Workout Mat',
	'Aeromat - 20-inch Width Workout  Mat Hanging Rack - Black',
	'http://thumbs.ebaystatic.com/images/g/Kz0AAOSwr7ZW4IES/s-l225.jpg',
	'https://www.amazon.com/Aeromat-74611-Mount-Hanging-Width/dp/B001HHEUOW',
	NULL
), (
	216, 21,
	12, 26,
	'Workout Fitness Mats',
	'EcoWise - 3/8 EcoWise Essential  Workout Fitness Mat 20 x 48 w/  Eyelets - Onyx',
	'http://www.aeromats.com/uploads/5/1/2/5/51252433/s729994238649277370_p233_i13_w1280.jpeg',
	'http://www.overstock.com/Sports-Toys/Aeromat-Workout-Mat-with-Eyelets-48-inch-long/10272712/product.html?refccid=QQ2D62I7B3632Q2DJOZE4ROEPU&searchidx=7',
	NULL
), (
	217, 21,
	4, 63,
	'25 LB Green Bumper Plates',
	'Torque - 25 LB Rubber Colored  Bumper Plate - Green',
	'https://www.torquefitness.com/wp-content/uploads/BPTCR-25-550x550.jpg',
	'https://www.torquefitness.com/solutions/rubber-colored-bumper-plates/',
	NULL
), (
	220, 21,
	2, 105,
	'45 LB Yellow Bumper Plates',
	'Torque - 45 LB Rubber Colored  Bumper Plate - Yellow',
	'https://www.torquefitness.com/wp-content/uploads/BPTCR-35-550x550.jpg',
	'https://www.torquefitness.com/solutions/rubber-colored-bumper-plates/',
	NULL
), (
	221, 21,
	1, 749,
	'Ab Bench',
	'Hoist -CF-3264; Adjustable Decline Bench',
	'http://www.hoistfitness.com/content/images/equipment/CF-3264-PLATINUM-DOVEGREY.JPG?width=348',
	'http://www.hoistfitness.com/commercial/equipment/cf-3264_ab-bench',
	NULL
), (
	223, 21,
	1, 1650,
	'Dumbbell Set (5-50LB) ',
	'Hampton - 5-50 lbs DURA-PRO  Urethane Pro-Style Dumbbell Set, (5  lbs increments, 10 pairs)',
	'http://i.ebayimg.com/00/s/NDE3WDQ3OQ==/z/Rx4AAOSw0UdXtinN/$_35.JPG?set_id=8800005007',
	'http://www.fitnessedgeonline.com/Hampton-Dura-Pro-5-50lb-Urethane-Pro-Style-DB-Set-p/dpu-5-50.htm',
	NULL
), (
	224, 21,
	1, 929,
	'Dumbbell Rack (2-Tier)',
	'Hoist - 2-Tier Horizontal Dumbbell  Rack (10 pairs)',
	'http://www.hoistfitness.com/content/images/equipment/CF-3461-2_PLATINUM.JPG?width=348',
	'http://www.hoistfitness.com/commercial/equipment/cf-3461-2_2-tier-dumbbell-rack',
	NULL
), (
	227, 21,
	2, 649,
	'Incline Bench',
	'Hoist - Flat / Incline Bench',
	'http://www.hoistfitness.com/content/images/equipment/CF-3160-PLATINUM-DOVEGREY.JPG?width=348',
	'http://www.hoistfitness.com/commercial/equipment/cf-3160_super-flat-incline-bench',
	NULL
), (
	228, 10,
	10, 28,
	'DVD Players ',
	'DVD players for each classroom',
	'https://smedia.webcollage.net/rwvfp/wc/live/21581248/module/samsungus//www.samsung.com/us/system/consumer/product/dv/de/36/dvde360za/dvd-e360_l30_m_1.jpg.w960.jpg',
	'https://www.neweggbusiness.com/product/product.aspx?item=9b-117-000c-00011',
	NULL
), (
	229, 10,
	3, 850,
	'VIZIO LED-LCD TV ',
	'65\" or larger TV monitors/screens for classrooms',
	'http://s7d1.scene7.com/is/image/officedepot/593693_vw_etz00_1032849572?$OD-Med$',
	'http://www.officedepot.com/a/products/593693/VIZIO-D-D65-D2-65-1080p/?cm_mmc=PLA-_-Google-_-TVs_Home_Theater-_-593693-VQ6-51194819876-VQ16-c-VQ17-pla_with_promotion-VQ18-online-VQ19-593693-VQ20-101648567636-VQ21--VQ22-624117322-VQ27-10575894956&gclid',
	NULL
), (
	230, 10,
	1, 650,
	'Samsung 55\" Class (54.6\" Diag.) 1080p Smart LED LCD TV ',
	'TV for staff lounge',
	'http://images.costco-static.com/image/media/350-995566-847__2.jpg',
	'http://www.costco.com/.product.100212117.html',
	NULL
), (
	232, 10,
	1, 100,
	'Video Editing Software',
	'CyberLink PowerDirector 14 Ultimate Suite Student & Teacher Edition',
	'http://www.cyberlink.com/stat/events/enu/2013/Q3/Student-Program/images/box_pdr_ultimatesuite.png',
	'http://www.cyberlink.com/stat/events/enu/2013/Q3/Student-Program/index.jsp',
	NULL
), (
	233, 10,
	5, 30,
	'Camera Memory Card with WiFi Capabilities',
	'Eyefi Mobi 8GB SDHC Class 10 Wi-Fi Memory Card with 90-day Eyefi Cloud Service',
	'https://images-na.ssl-images-amazon.com/images/I/51XV6Cgj74L._SY450_.jpg',
	'https://www.amazon.com/Memory-Service-Frustration-Packaging-MOBI-8-FF/dp/B00CS4WPD6?ie=UTF8&camp=1789&creative=390957&creativeASIN=B00CS4WPD6&linkCode=as2&linkId=MQ7PRHCQDJ7D57TS&redirect=true&ref_=as_li_tl&tag=eberopolis-20',
	NULL
), (
	234, 10,
	10, 36,
	'Wireless Presenter with Laser Pointer',
	'Logitech Wireless Presenter R400, Presentation Wireless Presenter with Laser Pointer',
	'https://images-na.ssl-images-amazon.com/images/I/41m21qlrvuL._SX300_.jpg',
	'https://www.amazon.com/gp/product/B002GHBUTK?ref_=sr_1_2&qid=1470113983&sr=8-2&keywords=wireless%20presenter%20with%20laser%20pointer&pldnSite=1',
	NULL
), (
	235, 10,
	16, 60,
	'Color Paper Rolls',
	'36x100 Dual surface color paper rolls.',
	'http://di5cp8ixdolg4.cloudfront.net/images/products/16894b.jpg',
	'http://www6.discountschoolsupply.com/Product/ProductDetail.aspx?product=16894&Category=785',
	NULL
), (
	236, 10,
	2, 539,
	'Large Paper Roll Rack ',
	'Bulman R995 36\" Vertical 8 Roll Paper Rack',
	'http://www.webstaurantstore.com/images/products/main/74549/78336/bulman-r995-36-vertical-8-roll-paper-rack.jpg',
	'http://www.webstaurantstore.com/bulman-r995-36-vertical-8-roll-paper-rack/188R995.html?utm_source=Google&utm_medium=cpc&utm_campaign=GoogleShopping&gclid=CPimlvbWoc4CFdKGfgodmvIA-A',
	NULL
);
COMMIT;
/*}}}*/

/* vim: set ts=4 noexpandtab fdm=marker syntax=sql: */
