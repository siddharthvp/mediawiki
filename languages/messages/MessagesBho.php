<?php
/** Bhojpuri (भोजपुरी)
 *
 * @file
 * @ingroup Languages
 */

$namespaceNames = [
	NS_MEDIA            => 'मीडिया',
	NS_SPECIAL          => 'विशेष',
	NS_TALK             => 'वार्तालाप',
	NS_USER             => 'प्रयोगकर्ता',
	NS_USER_TALK        => 'प्रयोगकर्ता_वार्ता',
	NS_PROJECT_TALK     => '$1_वार्ता',
	NS_FILE             => 'चित्र',
	NS_FILE_TALK        => 'चित्र_वार्ता',
	NS_MEDIAWIKI        => 'मीडियाविकि',
	NS_MEDIAWIKI_TALK   => 'मीडियाविकि_वार्ता',
	NS_TEMPLATE         => 'टेम्पलेट',
	NS_TEMPLATE_TALK    => 'टेम्पलेट_वार्ता',
	NS_HELP             => 'मदद',
	NS_HELP_TALK        => 'मदद_वार्ता',
	NS_CATEGORY         => 'श्रेणी',
	NS_CATEGORY_TALK    => 'श्रेणी_वार्ता',
];

/** @phpcs-require-sorted-array */
$specialPageAliases = [
	'Activeusers'               => [ 'सक्रिय_सदस्य' ],
	'Allmessages'               => [ 'सारा_संदेस' ],
	'Allpages'                  => [ 'सारा_पन्ना' ],
	'Ancientpages'              => [ 'पुरान_पन्ना' ],
	'Badtitle'                  => [ 'खराब_टाइटिल' ],
	'Blankpage'                 => [ 'खाली_पन्ना' ],
	'Categories'                => [ 'श्रेणी_सब' ],
	'Contributions'             => [ 'योगदान' ],
	'Export'                    => [ 'निर्यात' ],
	'Import'                    => [ 'आयात' ],
	'Log'                       => [ 'लॉग' ],
	'Lonelypages'               => [ 'असंयुक्त' ],
	'Longpages'                 => [ 'लम्बा_पन्ना' ],
	'Mypage'                    => [ 'हमार_पन्ना' ],
	'Mytalk'                    => [ 'हमार_बात' ],
	'Newpages'                  => [ 'नया_पन्ना' ],
	'Recentchangeslinked'       => [ 'तुरंत_भइल_परिवर्तन' ],
	'Shortpages'                => [ 'छोटा_पन्ना' ],
	'Specialpages'              => [ 'ख़ाश_पन्ना' ],
	'TrackingCategories'        => [ 'बिनावर्गीकृत_श्रेणी' ],
	'Uncategorizedpages'        => [ 'बिनावर्गीकृत' ],
];

$digitTransformTable = [
	'0' => '०', # U+0966
	'1' => '१', # U+0967
	'2' => '२', # U+0968
	'3' => '३', # U+0969
	'4' => '४', # U+096A
	'5' => '५', # U+096B
	'6' => '६', # U+096C
	'7' => '७', # U+096D
	'8' => '८', # U+096E
	'9' => '९', # U+096F
];

$numberingSystem = 'deva';
