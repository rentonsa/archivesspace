<?php

$config['skylight_appname'] = 'mimed';

$config['skylight_theme'] = 'mimed';

$config['skylight_fullname'] = 'MUSICAL INSTRUMENT MUSEUMS';

$config['skylight_adminemail'] = 'example@example.com';

$config['skylight_oaipmhcollection'] = 'hdl_10683_14558';


// Container ID and the field used in solr index to store this ID. Used for restricting search/browse scope.
$config['skylight_container_id'] = '11';
$config['skylight_container_field'] = 'location.coll';

$config['skylight_fields'] = array('Title' => 'dc.title.en',
    'Maker' => 'dc.contributor.author.en',
    'Author' => 'dc.contributor.author.en',
    'Country' => 'lido.country.en',
    'City' => 'lido.city.en',
    'Subject' => 'dc.subject.en',
    'Instrument' => 'dc.type.en',
    'Abstract' => 'dc.description.abstract.en',
    'Date' => 'dc.date.issued',
    'Bitstream'=> 'dc.format.original.en',
    'Thumbnail'=> 'dc.format.thumbnail.en',
    'Place Made' => 'dc.coverage.spatial.en',
    'Date Made' => 'dc.date.created',
    'Accession Number' => 'dc.identifier.other',
    'Description' => 'dc.description.en',
    'Collection' => 'dc.relation.ispartof.en'
);


$config['skylight_date_filters'] = array();
$config['skylight_filters'] = array('Instrument Type' => 'type_filter', 'Maker' => 'author_filter', 'Place Made' => 'place_filter', 'Collection'=> 'collection_filter' );
$config['skylight_filter_delimiter'] = ':';

$config['skylight_meta_fields'] = array('Title' => 'dc.title',
    'Maker' => 'dc.contributor.author',
    'Author' => 'dc.contributor.author',
    'Subject' => 'dc.subject',
    'Type' => 'dc.type');

$config['skylight_recorddisplay'] = array('Title','Maker','Subject','Type','Abstract', 'Place Made', 'Date Made', 'Accession Number', 'Description', 'Collection');

$config['skylight_searchresult_display'] = array('Title','Maker','Subject','Type','Abstract', 'Bitstream', 'Thumbnail');


$config['skylight_search_fields'] = array(
    'Title' => 'dc.title.en',
    'Type' => 'dc.type',
    'Maker' => 'dc.contributor.author.en',
    'Place Made' => 'dc.coverage.spatial.en',
);

//only by title, no date at the moment
$config['skylight_sort_fields'] = array(
    'Maker' => 'dc.contributor.author_sort ', 'Title' => 'dc.title_sort'
);


$config['skylight_feed_fields'] = array('Title' => 'Title',
    'Author' => 'Author',
    'Maker' => 'Maker',
    'Subject' => 'Subject',
    'Country' => 'Country',
    'Description' => 'Abstract',
    'Date' => 'Date');

$config['skylight_results_per_page'] = 10;
$config['skylight_share_buttons'] = false;

// $config['skylight_homepage_recentitems'] = false;

// Set to the number of minutes to cache pages for. Set to false for no caching.
// This overrides the setting in skylight.php so is commented by Demo
$config['skylight_cache'] = false;

// Digital object management
$config['skylight_display_thumbnail'] = true;
$config['skylight_link_bitstream'] = true;

// Display common image formats in "light box" gallery?
$config['skylight_lightbox'] = true;
$config['skylight_lightbox_mimes'] = array('image/jpeg', 'image/gif', 'image/png');

// Language and locale settings
$config['skylight_language_default'] = 'en';
$config['skylight_language_options'] = array('en', 'ko', 'jp');

$config['skylight_highlight_fields'] = 'dc.title.en,dc.contributor.author,dc.subject.en,lido.country.en,dc.description.en,dc.relation.ispartof.en';

?>