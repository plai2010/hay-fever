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
	'ann describes the life of America before the arrival of Christopher Columbus.',
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
	'etailed account of the inventor Tesla, a large contributor for the electrical revolution',
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
);
COMMIT;
/*}}}*/

/* vim: set ts=4 noexpandtab fdm=marker syntax=sql: */
