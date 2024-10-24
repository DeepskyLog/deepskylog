<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->string('country');
            $table->string('code');
        });

        DB::table('countries')->insert(['country' => 'Afghanistan', 'code' => 'AF']);
        DB::table('countries')->insert(['country' => "\xc3\x85land Islands", 'code' => 'AX']);
        DB::table('countries')->insert(['country' => 'Albania', 'code' => 'AL']);
        DB::table('countries')->insert(['country' => 'Algeria', 'code' => 'DZ']);
        DB::table('countries')->insert(['country' => 'American Samoa', 'code' => 'AS']);
        DB::table('countries')->insert(['country' => 'Andorra', 'code' => 'AD']);
        DB::table('countries')->insert(['country' => 'Angola', 'code' => 'AO']);
        DB::table('countries')->insert(['country' => 'Anguilla', 'code' => 'AI']);
        DB::table('countries')->insert(['country' => 'Antarctica', 'code' => 'AQ']);
        DB::table('countries')->insert(['country' => 'Antigua and Barbuda', 'code' => 'AG']);
        DB::table('countries')->insert(['country' => 'Argentina', 'code' => 'AR']);
        DB::table('countries')->insert(['country' => 'Armenia', 'code' => 'AM']);
        DB::table('countries')->insert(['country' => 'Aruba', 'code' => 'AW']);
        DB::table('countries')->insert(['country' => 'Australia', 'code' => 'AU']);
        DB::table('countries')->insert(['country' => 'Austria', 'code' => 'AT']);
        DB::table('countries')->insert(['country' => 'Azerbaijan', 'code' => 'AZ']);
        DB::table('countries')->insert(['country' => 'Bahamas', 'code' => 'BS']);
        DB::table('countries')->insert(['country' => 'Bahrain', 'code' => 'BH']);
        DB::table('countries')->insert(['country' => 'Bangladesh', 'code' => 'BD']);
        DB::table('countries')->insert(['country' => 'Barbados', 'code' => 'BB']);
        DB::table('countries')->insert(['country' => 'Belarus', 'code' => 'BY']);
        DB::table('countries')->insert(['country' => 'Belgium', 'code' => 'BE']);
        DB::table('countries')->insert(['country' => 'Belize', 'code' => 'BZ']);
        DB::table('countries')->insert(['country' => 'Benin', 'code' => 'BJ']);
        DB::table('countries')->insert(['country' => 'Bermuda', 'code' => 'BM']);
        DB::table('countries')->insert(['country' => 'Bhutan', 'code' => 'BT']);
        DB::table('countries')->insert(['country' => 'Bolivia, Plurinational State of', 'code' => 'BO']);
        DB::table('countries')->insert(['country' => 'Bonaire, Sint Eustatius and Saba', 'code' => 'BQ']);
        DB::table('countries')->insert(['country' => 'Bosnia and Herzegovina', 'code' => 'BA']);
        DB::table('countries')->insert(['country' => 'Botswana', 'code' => 'BW']);
        DB::table('countries')->insert(['country' => 'Bouvet Island', 'code' => 'BV']);
        DB::table('countries')->insert(['country' => 'Brazil', 'code' => 'BR']);
        DB::table('countries')->insert(['country' => 'British Indian Ocean Territory', 'code' => 'IO']);
        DB::table('countries')->insert(['country' => 'Brunei Darussalam', 'code' => 'BN']);
        DB::table('countries')->insert(['country' => 'Bulgaria', 'code' => 'BG']);
        DB::table('countries')->insert(['country' => 'Burkina Faso', 'code' => 'BF']);
        DB::table('countries')->insert(['country' => 'Burundi', 'code' => 'BI']);
        DB::table('countries')->insert(['country' => 'Cambodia', 'code' => 'KH']);
        DB::table('countries')->insert(['country' => 'Cameroon', 'code' => 'CM']);
        DB::table('countries')->insert(['country' => 'Canada', 'code' => 'CA']);
        DB::table('countries')->insert(['country' => 'Cabo Verde', 'code' => 'CV']);
        DB::table('countries')->insert(['country' => 'Cayman Islands', 'code' => 'KY']);
        DB::table('countries')->insert(['country' => 'Central African Republic', 'code' => 'CF']);
        DB::table('countries')->insert(['country' => 'Chad', 'code' => 'TD']);
        DB::table('countries')->insert(['country' => 'Chile', 'code' => 'CL']);
        DB::table('countries')->insert(['country' => 'China', 'code' => 'CN']);
        DB::table('countries')->insert(['country' => 'Christmas Island', 'code' => 'CX']);
        DB::table('countries')->insert(['country' => 'Cocos (Keeling) Islands', 'code' => 'CC']);
        DB::table('countries')->insert(['country' => 'Colombia', 'code' => 'CO']);
        DB::table('countries')->insert(['country' => 'Comoros', 'code' => 'KM']);
        DB::table('countries')->insert(['country' => 'Congo', 'code' => 'CG']);
        DB::table('countries')->insert(['country' => 'Congo, The Democratic Republic of the', 'code' => 'CD']);
        DB::table('countries')->insert(['country' => 'Cook Islands', 'code' => 'CK']);
        DB::table('countries')->insert(['country' => 'Costa Rica', 'code' => 'CR']);
        DB::table('countries')->insert(['country' => "C\xc3\xb4te d'Ivoire", 'code' => 'CI']);
        DB::table('countries')->insert(['country' => 'Croatia', 'code' => 'HR']);
        DB::table('countries')->insert(['country' => 'Cuba', 'code' => 'CU']);
        DB::table('countries')->insert(['country' => "Cura\xc3\xa7ao", 'code' => 'CW']);
        DB::table('countries')->insert(['country' => 'Cyprus', 'code' => 'CY']);
        DB::table('countries')->insert(['country' => 'Czech Republic', 'code' => 'CZ']);
        DB::table('countries')->insert(['country' => 'Denmark', 'code' => 'DK']);
        DB::table('countries')->insert(['country' => 'Djibouti', 'code' => 'DJ']);
        DB::table('countries')->insert(['country' => 'Dominica', 'code' => 'DM']);
        DB::table('countries')->insert(['country' => 'Dominican Republic', 'code' => 'DO']);
        DB::table('countries')->insert(['country' => 'Ecuador', 'code' => 'EC']);
        DB::table('countries')->insert(['country' => 'Egypt', 'code' => 'EG']);
        DB::table('countries')->insert(['country' => 'El Salvador', 'code' => 'SV']);
        DB::table('countries')->insert(['country' => 'Equatorial Guinea', 'code' => 'GQ']);
        DB::table('countries')->insert(['country' => 'Eritrea', 'code' => 'ER']);
        DB::table('countries')->insert(['country' => 'Estonia', 'code' => 'EE']);
        DB::table('countries')->insert(['country' => 'Ethiopia', 'code' => 'ET']);
        DB::table('countries')->insert(['country' => 'Falkland Islands (Malvinas)', 'code' => 'FK']);
        DB::table('countries')->insert(['country' => 'Faroe Islands', 'code' => 'FO']);
        DB::table('countries')->insert(['country' => 'Fiji', 'code' => 'FJ']);
        DB::table('countries')->insert(['country' => 'Finland', 'code' => 'FI']);
        DB::table('countries')->insert(['country' => 'France', 'code' => 'FR']);
        DB::table('countries')->insert(['country' => 'French Guiana', 'code' => 'GF']);
        DB::table('countries')->insert(['country' => 'French Polynesia', 'code' => 'PF']);
        DB::table('countries')->insert(['country' => 'French Southern Territories', 'code' => 'TF']);
        DB::table('countries')->insert(['country' => 'Gabon', 'code' => 'GA']);
        DB::table('countries')->insert(['country' => 'Gambia', 'code' => 'GM']);
        DB::table('countries')->insert(['country' => 'Georgia', 'code' => 'GE']);
        DB::table('countries')->insert(['country' => 'Germany', 'code' => 'DE']);
        DB::table('countries')->insert(['country' => 'Ghana', 'code' => 'GH']);
        DB::table('countries')->insert(['country' => 'Gibraltar', 'code' => 'GI']);
        DB::table('countries')->insert(['country' => 'Greece', 'code' => 'GR']);
        DB::table('countries')->insert(['country' => 'Greenland', 'code' => 'GL']);
        DB::table('countries')->insert(['country' => 'Grenada', 'code' => 'GD']);
        DB::table('countries')->insert(['country' => 'Guadeloupe', 'code' => 'GP']);
        DB::table('countries')->insert(['country' => 'Guam', 'code' => 'GU']);
        DB::table('countries')->insert(['country' => 'Guatemala', 'code' => 'GT']);
        DB::table('countries')->insert(['country' => 'Guernsey', 'code' => 'GG']);
        DB::table('countries')->insert(['country' => 'Guinea', 'code' => 'GN']);
        DB::table('countries')->insert(['country' => 'Guinea-Bissau', 'code' => 'GW']);
        DB::table('countries')->insert(['country' => 'Guyana', 'code' => 'GY']);
        DB::table('countries')->insert(['country' => 'Haiti', 'code' => 'HT']);
        DB::table('countries')->insert(['country' => 'Heard Island and McDonald Islands', 'code' => 'HM']);
        DB::table('countries')->insert(['country' => 'Holy See', 'code' => 'VA']);
        DB::table('countries')->insert(['country' => 'Honduras', 'code' => 'HN']);
        DB::table('countries')->insert(['country' => 'Hong Kong', 'code' => 'HK']);
        DB::table('countries')->insert(['country' => 'Hungary', 'code' => 'HU']);
        DB::table('countries')->insert(['country' => 'Iceland', 'code' => 'IS']);
        DB::table('countries')->insert(['country' => 'India', 'code' => 'IN']);
        DB::table('countries')->insert(['country' => 'Indonesia', 'code' => 'ID']);
        DB::table('countries')->insert(['country' => 'Iran, Islamic Republic of', 'code' => 'IR']);
        DB::table('countries')->insert(['country' => 'Iraq', 'code' => 'IQ']);
        DB::table('countries')->insert(['country' => 'Ireland', 'code' => 'IE']);
        DB::table('countries')->insert(['country' => 'Isle of Man', 'code' => 'IM']);
        DB::table('countries')->insert(['country' => 'Israel', 'code' => 'IL']);
        DB::table('countries')->insert(['country' => 'Italy', 'code' => 'IT']);
        DB::table('countries')->insert(['country' => 'Jamaica', 'code' => 'JM']);
        DB::table('countries')->insert(['country' => 'Japan', 'code' => 'JP']);
        DB::table('countries')->insert(['country' => 'Jersey', 'code' => 'JE']);
        DB::table('countries')->insert(['country' => 'Jordan', 'code' => 'JO']);
        DB::table('countries')->insert(['country' => 'Kazakhstan', 'code' => 'KZ']);
        DB::table('countries')->insert(['country' => 'Kenya', 'code' => 'KE']);
        DB::table('countries')->insert(['country' => 'Kiribati', 'code' => 'KI']);
        DB::table('countries')->insert(['country' => "Korea, Democratic People's Republic of", 'code' => 'KP']);
        DB::table('countries')->insert(['country' => 'Korea, Republic of', 'code' => 'KR']);
        DB::table('countries')->insert(['country' => 'Kuwait', 'code' => 'KW']);
        DB::table('countries')->insert(['country' => 'Kyrgyzstan', 'code' => 'KG']);
        DB::table('countries')->insert(['country' => "Lao People's Democratic Republic", 'code' => 'LA']);
        DB::table('countries')->insert(['country' => 'Latvia', 'code' => 'LV']);
        DB::table('countries')->insert(['country' => 'Lebanon', 'code' => 'LB']);
        DB::table('countries')->insert(['country' => 'Lesotho', 'code' => 'LS']);
        DB::table('countries')->insert(['country' => 'Liberia', 'code' => 'LR']);
        DB::table('countries')->insert(['country' => 'Libya', 'code' => 'LY']);
        DB::table('countries')->insert(['country' => 'Liechtenstein', 'code' => 'LI']);
        DB::table('countries')->insert(['country' => 'Lithuania', 'code' => 'LT']);
        DB::table('countries')->insert(['country' => 'Luxembourg', 'code' => 'LU']);
        DB::table('countries')->insert(['country' => 'Macao', 'code' => 'MO']);
        DB::table('countries')->insert(['country' => 'Macedonia, The Former Yugoslav Republic of', 'code' => 'MK']);
        DB::table('countries')->insert(['country' => 'Madagascar', 'code' => 'MG']);
        DB::table('countries')->insert(['country' => 'Malawi', 'code' => 'MW']);
        DB::table('countries')->insert(['country' => 'Malaysia', 'code' => 'MY']);
        DB::table('countries')->insert(['country' => 'Maldives', 'code' => 'MV']);
        DB::table('countries')->insert(['country' => 'Mali', 'code' => 'ML']);
        DB::table('countries')->insert(['country' => 'Malta', 'code' => 'MT']);
        DB::table('countries')->insert(['country' => 'Marshall Islands', 'code' => 'MH']);
        DB::table('countries')->insert(['country' => 'Martinique', 'code' => 'MQ']);
        DB::table('countries')->insert(['country' => 'Mauritania', 'code' => 'MR']);
        DB::table('countries')->insert(['country' => 'Mauritius', 'code' => 'MU']);
        DB::table('countries')->insert(['country' => 'Mayotte', 'code' => 'YT']);
        DB::table('countries')->insert(['country' => 'Mexico', 'code' => 'MX']);
        DB::table('countries')->insert(['country' => 'Micronesia, Federated States of', 'code' => 'FM']);
        DB::table('countries')->insert(['country' => 'Moldova, Republic of', 'code' => 'MD']);
        DB::table('countries')->insert(['country' => 'Monaco', 'code' => 'MC']);
        DB::table('countries')->insert(['country' => 'Mongolia', 'code' => 'MN']);
        DB::table('countries')->insert(['country' => 'Montenegro', 'code' => 'ME']);
        DB::table('countries')->insert(['country' => 'Montserrat', 'code' => 'MS']);
        DB::table('countries')->insert(['country' => 'Morocco', 'code' => 'MA']);
        DB::table('countries')->insert(['country' => 'Mozambique', 'code' => 'MZ']);
        DB::table('countries')->insert(['country' => 'Myanmar', 'code' => 'MM']);
        DB::table('countries')->insert(['country' => 'Namibia', 'code' => 'NA']);
        DB::table('countries')->insert(['country' => 'Nauru', 'code' => 'NR']);
        DB::table('countries')->insert(['country' => 'Nepal', 'code' => 'NP']);
        DB::table('countries')->insert(['country' => 'Netherlands', 'code' => 'NL']);
        DB::table('countries')->insert(['country' => 'New Caledonia', 'code' => 'NC']);
        DB::table('countries')->insert(['country' => 'New Zealand', 'code' => 'NZ']);
        DB::table('countries')->insert(['country' => 'Nicaragua', 'code' => 'NI']);
        DB::table('countries')->insert(['country' => 'Niger', 'code' => 'NE']);
        DB::table('countries')->insert(['country' => 'Nigeria', 'code' => 'NG']);
        DB::table('countries')->insert(['country' => 'Niue', 'code' => 'NU']);
        DB::table('countries')->insert(['country' => 'Norfolk Island', 'code' => 'NF']);
        DB::table('countries')->insert(['country' => 'Northern Mariana Islands', 'code' => 'MP']);
        DB::table('countries')->insert(['country' => 'Norway', 'code' => 'NO']);
        DB::table('countries')->insert(['country' => 'Oman', 'code' => 'OM']);
        DB::table('countries')->insert(['country' => 'Pakistan', 'code' => 'PK']);
        DB::table('countries')->insert(['country' => 'Palau', 'code' => 'PW']);
        DB::table('countries')->insert(['country' => 'Palestine, State of', 'code' => 'PS']);
        DB::table('countries')->insert(['country' => 'Panama', 'code' => 'PA']);
        DB::table('countries')->insert(['country' => 'Papua New Guinea', 'code' => 'PG']);
        DB::table('countries')->insert(['country' => 'Paraguay', 'code' => 'PY']);
        DB::table('countries')->insert(['country' => 'Peru', 'code' => 'PE']);
        DB::table('countries')->insert(['country' => 'Philippines', 'code' => 'PH']);
        DB::table('countries')->insert(['country' => 'Pitcairn', 'code' => 'PN']);
        DB::table('countries')->insert(['country' => 'Poland', 'code' => 'PL']);
        DB::table('countries')->insert(['country' => 'Portugal', 'code' => 'PT']);
        DB::table('countries')->insert(['country' => 'Puerto Rico', 'code' => 'PR']);
        DB::table('countries')->insert(['country' => 'Qatar', 'code' => 'QA']);
        DB::table('countries')->insert(['country' => "R\xc3\xa9union", 'code' => 'RE']);
        DB::table('countries')->insert(['country' => 'Romania', 'code' => 'RO']);
        DB::table('countries')->insert(['country' => 'Russian Federation', 'code' => 'RU']);
        DB::table('countries')->insert(['country' => 'Rwanda', 'code' => 'RW']);
        DB::table('countries')->insert(['country' => "Saint Barth\xc3\xa9lemy", 'code' => 'BL']);
        DB::table('countries')->insert(['country' => 'Saint Helena, Ascension and Tristan Da Cunha', 'code' => 'SH']);
        DB::table('countries')->insert(['country' => 'Saint Kitts and Nevis', 'code' => 'KN']);
        DB::table('countries')->insert(['country' => 'Saint Lucia', 'code' => 'LC']);
        DB::table('countries')->insert(['country' => 'Saint Martin (French part)', 'code' => 'MF']);
        DB::table('countries')->insert(['country' => 'Saint Pierre and Miquelon', 'code' => 'PM']);
        DB::table('countries')->insert(['country' => 'Saint Vincent and the Grenadines', 'code' => 'VC']);
        DB::table('countries')->insert(['country' => 'Samoa', 'code' => 'WS']);
        DB::table('countries')->insert(['country' => 'San Marino', 'code' => 'SM']);
        DB::table('countries')->insert(['country' => 'Sao Tome and Principe', 'code' => 'ST']);
        DB::table('countries')->insert(['country' => 'Saudi Arabia', 'code' => 'SA']);
        DB::table('countries')->insert(['country' => 'Senegal', 'code' => 'SN']);
        DB::table('countries')->insert(['country' => 'Serbia', 'code' => 'RS']);
        DB::table('countries')->insert(['country' => 'Seychelles', 'code' => 'SC']);
        DB::table('countries')->insert(['country' => 'Sierra Leone', 'code' => 'SL']);
        DB::table('countries')->insert(['country' => 'Singapore', 'code' => 'SG']);
        DB::table('countries')->insert(['country' => 'Sint Maarten (Dutch part)', 'code' => 'SX']);
        DB::table('countries')->insert(['country' => 'Slovakia', 'code' => 'SK']);
        DB::table('countries')->insert(['country' => 'Slovenia', 'code' => 'SI']);
        DB::table('countries')->insert(['country' => 'Solomon Islands', 'code' => 'SB']);
        DB::table('countries')->insert(['country' => 'Somalia', 'code' => 'SO']);
        DB::table('countries')->insert(['country' => 'South Africa', 'code' => 'ZA']);
        DB::table('countries')->insert(['country' => 'South Georgia and the South Sandwich Islands', 'code' => 'GS']);
        DB::table('countries')->insert(['country' => 'South Sudan', 'code' => 'SS']);
        DB::table('countries')->insert(['country' => 'Spain', 'code' => 'ES']);
        DB::table('countries')->insert(['country' => 'Sri Lanka', 'code' => 'LK']);
        DB::table('countries')->insert(['country' => 'Sudan', 'code' => 'SD']);
        DB::table('countries')->insert(['country' => 'Suriname', 'code' => 'SR']);
        DB::table('countries')->insert(['country' => 'Svalbard and Jan Mayen', 'code' => 'SJ']);
        DB::table('countries')->insert(['country' => 'Swaziland', 'code' => 'SZ']);
        DB::table('countries')->insert(['country' => 'Sweden', 'code' => 'SE']);
        DB::table('countries')->insert(['country' => 'Switzerland', 'code' => 'CH']);
        DB::table('countries')->insert(['country' => 'Syrian Arab Republic', 'code' => 'SY']);
        DB::table('countries')->insert(['country' => 'Taiwan, Province of China', 'code' => 'TW']);
        DB::table('countries')->insert(['country' => 'Tajikistan', 'code' => 'TJ']);
        DB::table('countries')->insert(['country' => 'Tanzania, United Republic of', 'code' => 'TZ']);
        DB::table('countries')->insert(['country' => 'Thailand', 'code' => 'TH']);
        DB::table('countries')->insert(['country' => 'Timor-Leste', 'code' => 'TL']);
        DB::table('countries')->insert(['country' => 'Togo', 'code' => 'TG']);
        DB::table('countries')->insert(['country' => 'Tokelau', 'code' => 'TK']);
        DB::table('countries')->insert(['country' => 'Tonga', 'code' => 'TO']);
        DB::table('countries')->insert(['country' => 'Trinidad and Tobago', 'code' => 'TT']);
        DB::table('countries')->insert(['country' => 'Tunisia', 'code' => 'TN']);
        DB::table('countries')->insert(['country' => 'Turkey', 'code' => 'TR']);
        DB::table('countries')->insert(['country' => 'Turkmenistan', 'code' => 'TM']);
        DB::table('countries')->insert(['country' => 'Turks and Caicos Islands', 'code' => 'TC']);
        DB::table('countries')->insert(['country' => 'Tuvalu', 'code' => 'TV']);
        DB::table('countries')->insert(['country' => 'Uganda', 'code' => 'UG']);
        DB::table('countries')->insert(['country' => 'Ukraine', 'code' => 'UA']);
        DB::table('countries')->insert(['country' => 'United Arab Emirates', 'code' => 'AE']);
        DB::table('countries')->insert(['country' => 'United Kingdom of Great Britain and Northern Ireland', 'code' => 'GB']);
        DB::table('countries')->insert(['country' => 'United States of America', 'code' => 'US']);
        DB::table('countries')->insert(['country' => 'United States', 'code' => 'US']);
        DB::table('countries')->insert(['country' => 'United States Minor Outlying Islands', 'code' => 'UM']);
        DB::table('countries')->insert(['country' => 'Uruguay', 'code' => 'UY']);
        DB::table('countries')->insert(['country' => 'Uzbekistan', 'code' => 'UZ']);
        DB::table('countries')->insert(['country' => 'Vanuatu', 'code' => 'VU']);
        DB::table('countries')->insert(['country' => 'Venezuela, Bolivarian Republic of', 'code' => 'VE']);
        DB::table('countries')->insert(['country' => 'Viet Nam', 'code' => 'VN']);
        DB::table('countries')->insert(['country' => 'Virgin Islands, British', 'code' => 'VG']);
        DB::table('countries')->insert(['country' => 'Virgin Islands, U.S.', 'code' => 'VI']);
        DB::table('countries')->insert(['country' => 'Wallis and Futuna', 'code' => 'WF']);
        DB::table('countries')->insert(['country' => 'Western Sahara', 'code' => 'EH']);
        DB::table('countries')->insert(['country' => 'Yemen', 'code' => 'YE']);
        DB::table('countries')->insert(['country' => 'Zambia', 'code' => 'ZM']);
        DB::table('countries')->insert(['country' => 'Zimbabwe', 'code' => 'ZW']);
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
