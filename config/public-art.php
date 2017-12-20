<?php

$config['skylight_appname'] = 'public-art';

// Uncomment this if you are using a url of the form http://.../art/...
$config['skylight_url_prefix'] = 'public-art';

$config['skylight_theme'] = 'public-art';

$config['skylight_fullname'] = 'Stuart Exhibition';

$config['skylight_adminemail'] = 'lddt@mlist.is.ed.ac.uk';

$config['skylight_oaipmhcollection'] = 'hdl_10683_53855';

$config['skylight_oaipmhallowed'] = true;

// Container ID and the field used in solr index to store this ID. Used for restricting search/browse scope.

$config['skylight_container_field'] = 'location.coll';
$config['skylight_container_id'] = '64';
$config['skylight_sitemap_type'] = 'internal';

$config['skylight_fields'] = array(
    'ID'                        => 'id',
    'Collection'                => 'collection',
    'Relation'                  => 'dc.relation',
    'Identifier'                => 'dc.identifier',
    'Title'                     => 'dc.title.en',
    'Creator'                   => 'dc.creator',
    'Dates'                     => 'dc.coverage.temporal.en',
    'Country'                   => 'dc.coverage.spatialcountry.en',
    'City'                      => 'dc.coverage.spatialcity.en',
    'Format'                    => 'dc.format',
    'Format Extent'             => 'dc.format.extent',
    'Description'               => 'dc.description.en',
    'Identifier Citation'       => 'dc.identifier.citation',
    'Source URI'                => 'dc.source.uri',
    'License'                   => 'dc.license',
    'Rights Holder'             => 'dc.rights.holder',
    'Artist'                     => 'dc.contributor.authorfull.en',
    'Rights'                    => 'dc.rights',
    'Original'                  => 'dim.original',
    'Identifier URI'            => 'dc.identifier.uri',
    'Subject'                   => 'dc.subject',
    'Spatial Coverage'          => 'dc.coverage.spatial.en',
    'Image Name'                => 'dc.format.original',
    'Image URI'                 => 'dc.identifier.imageUri',
    'Artist Biography'          => 'dc.contributor.authorbio.en',
    'Map Reference'             => 'dc.coverage.spatialcoord.en',
    'Location'                  => 'dc.coverage.spatialloc.en'
);


$config['skylight_date_filters'] = array();
$config['skylight_filters'] = array('Maker' => 'author_filter', 'Place Made' => 'place_filter');
$config['skylight_filter_delimiter'] = ':';

$config['skylight_meta_fields'] = array('Title' => 'dc.title',
    'Alternative Title' => 'dc.title.alternative.en',
    'Artist' => 'dc.contributor.author.en',
    'Subject' => 'dc.subject',
    'Type' => 'dc.type');

$config['skylight_recorddisplay'] = array(
    'Title',
    'Artist',
    'Creator',
    'Dates',
    'City',
    'Country',
    'Format',
    'Format Extent',
    'Description',
    'Subject',
    'Artist Biography'
);

$config['skylight_descriptiondisplay'] = array(
    'Abstract',
    'Decorations',
    //'Provenance',
    'Description',
    'Technical Description',
    'Other Information',
    'Notes',
    'Collection',
    'Instrument Type Info',
    'Instrument Type History');

$config['skylight_creatordisplay'] = array(
    'Artist',
    'Author Biography',
    'Associated Musician Full',
    'Associated Musician',
    'Associated Musician Biography',);

$config['skylight_fullrecorddisplay'] = array(
    'Alternative Title',
    'Instrument',
    'Instrument Family',
    'Artist','Subject',
    'Abstract',
    'Place Made',
    'Date Made',
    'Description',
    'Other Information',
    'Notes',
    'Decorations',
    'Measurements',
    'Provenance',
    'Inscription',
    'Signature',
    'Collection',
    'Rights Holder',
    'Accession Number',
    'Author Biography',
    'Associated Musician Full',
    'Associated Musician',
    'Piccolo Description',
    'Technical Description',
    'Associated Musician Biography',
    'Instrument Type Info',
    'Instrument Type History');

$config['skylight_searchresult_display'] = array('Title','Instrument','Artist','Subject','Abstract', 'Bitstream', 'Thumbnail');


$config['skylight_search_fields'] = array(
    'Title' => 'dc.title',
    'Type' => 'dc.type',
    'Artist' => 'dc.contributor.author',
    'Place Made' => 'dc.coverage.spatial',
    'Accession Number' => 'dc.identifier.en'
);

$config['skylight_related_fields'] = array('Instrument' => 'dc.type.en', 'Genus' => 'dc.type.genus.en');

//only by title, no date at the moment
$config['skylight_sort_fields'] = array(
    'Artist' => 'dc.contributor.author_sort ', 'Title' => 'dc.title_sort'
);
$config['skylight_default_sort'] = 'dc.title_sort+asc';

$config['skylight_feed_fields'] = array('Title' => 'Title',
    'Author' => 'Author',
    'Artist' => 'Maker',
    'Subject' => 'Subject',
    'Country' => 'Country',
    'Description' => 'Abstract',
    'Date' => 'Date');

$config['skylight_related_number'] = 20;
$config['skylight_results_per_page'] = 20;
$config['skylight_show_facets'] = false;
$config['skylight_share_buttons'] = false;

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

$config['skylight_homepage_recentitems'] = false;
$config['skylight_homepage_randomitems'] = false;
$config['skylight_homepage_fullwidth'] = true;

?>
