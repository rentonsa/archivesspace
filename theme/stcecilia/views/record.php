<?php

$author_field = $this->skylight_utilities->getField("Author");
$title_field = $this->skylight_utilities->getField("Title");
$maker_field = $this->skylight_utilities->getField("Maker");
$type_field = $this->skylight_utilities->getField("Type");
$bitstream_field = $this->skylight_utilities->getField("Bitstream");
$thumbnail_field = $this->skylight_utilities->getField("Thumbnail");
$filters = array_keys($this->config->item("skylight_filters"));
$placedisplay = $this->config->item("skylight_placedisplay");
$measurementdisplay = $this->config->item("skylight_measurementdisplay");
$associationdisplay = $this->config->item("skylight_associationdisplay");
$locationdisplay = $this->config->item("skylight_locationdisplay");
$datedisplay = $this->config->item("skylight_datedisplay");
$identificationdisplay = $this->config->item("skylight_identificationdisplay");
$descriptiondatadisplay = $this->config->item("skylight_descriptiondatadisplay");
$typedisplay = $this->config->item("skylight_typedisplay");
$link_uri_field = $this->skylight_utilities->getField("ImageURI");
$short_field = $this->skylight_utilities->getField("Short Description");
$date_field = $this->skylight_utilities->getField("Date");
$media_uri = $this->config->item("skylight_media_url_prefix");
$theme = $this->config->item("skylight_theme");
$acc_no_field = $this->skylight_utilities->getField("Accession Number");

$type = 'Unknown';
$mainImageTest = false;
$numThumbnails = 0;
$bitstreamLinks = array();
$image_id = "";

// booleans for video/audio
$mainImage = false;
$videoFile = false;
$audioFile = false;
$audioLink = "";
$videoLink = "";

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    foreach ($solr[$bitstream_field] as $bitstream_for_array) {
        $b_segments = explode("##", $bitstream_for_array);
        $b_seq = $b_segments[4];
        $bitstream_array[$b_seq] = $bitstream_for_array;
    }

    ksort($bitstream_array);

    $mainImage = false;
    $videoFile = false;
    $audioFile = false;

    $b_seq = "";

    foreach ($bitstream_array as $bitstream) {
        $mp4ok = false;
        $b_segments = explode("##", $bitstream);
        $b_filename = $b_segments[1];
        if ($image_id == "") {
            $image_id = substr($b_filename, 0, 7);
        }
        $b_handle = $b_segments[3];
        $b_seq = $b_segments[4];
        $b_handle_id = preg_replace('/^.*\//', '', $b_handle);
        $b_uri = './record/' . $b_handle_id . '/' . $b_seq . '/' . $b_filename;

        if ((strpos($b_uri, ".mp3") > 0) or (strpos($b_uri, ".MP3") > 0)) {
            $audioLink .= '<audio controls>';
            $audioLink .= '<source src="' . $b_uri . '" type="audio/mpeg" />Audio loading...';
            $audioLink .= '</audio>';
            $audioFile = true;
        } else if ((strpos($b_filename, ".mp4") > 0) or (strpos($b_filename, ".MP4") > 0)) {
            $b_uri = $media_uri . $b_handle_id . '/' . $b_seq . '/' . $b_filename;
            // Use MP4 for all browsers other than Chrome
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == false) {
                $mp4ok = true;
            } //Microsoft Edge is calling itself Chrome, Mozilla and Safari, as well as Edge, so we need to deal with that.
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true) {
                $mp4ok = true;
            }
            if ($mp4ok == true) {
                $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                $videoLink .= '<source src="' . $b_uri . '" type="video/mp4" />Video loading...';
                $videoLink .= '</video>';
                $videoFile = true;
            }
        }
        else if ((strpos($b_filename, ".webm") > 0) or (strpos($b_filename, ".WEBM") > 0))
        {
            //Microsoft Edge needs to be dealt with. Chrome calls itself Safari too, but that doesn't matter.
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == false) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == true) {
                    $b_uri = $media_uri . $b_handle_id . '/' . $b_seq . '/' . $b_filename;
                    // if it's chrome, use webm if it exists
                    $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                    $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                    $videoLink .= '<source src="' . $b_uri . '" type="video/webm" />Video loading...';
                    $videoLink .= '</video>';
                    $videoFile = true;
                }
            }
        }
        else if ((strpos($b_filename, ".json") > 0) or (strpos($b_filename, ".JSON") > 0))
        {
            if(isset($solr[$acc_no_field])) {
                $accno =  $solr[$acc_no_field][0];
            }
            $bitstreamLink = $this->skylight_utilities->getBitstreamLink($bitstream);
            $bitstreamUri = $this->skylight_utilities->getBitstreamUri($bitstream);
            $manifest  = base_url().'stcecilias/record/'.$b_handle_id.'/'.$b_seq.'/'.$b_filename;
            $jsonLink  = '<span class ="json-link-item"><a href="https://librarylabs.ed.ac.uk/iiif/uv/?manifest='.$manifest.'" target="_blank" class="uvlogo" title="View in UV"></a></span>';
            $jsonLink .= '<span class ="json-link-item"><a href="https://images.is.ed.ac.uk/luna/servlet/view/search?search=SUBMIT&q='.$accno.'" class="lunalogo" title="View in LUNA"></a></span>';
            $jsonLink .= '<span class ="json-link-item"><a href="'.$manifest.'" target="_blank"  class="iiiflogo" title="IIIF manifest"></a></span>';
            //$jsonLink .= '<span class ="json-link-item"><a target="_blank" href="http://tomcrane.github.io/scratch/mirador/?manifest='.$manifest.'"><img src="http://digital.nls.uk/da/assets/graphics/misc/logo-mirador-24-32.png" class="iiiflogo" title="View in Mirador"></a></span>';
            $jsonLink .= '<span class ="json-link-item"><a href = "https://creativecommons.org/licenses/by/3.0/" class ="ccbylogo" title="All images CC-BY" target="_blank" ></a></span>';
        }
    }
}
?>

<nav class="navbar navbar-fixed-top second-navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#record-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div>
            <div class="collapse navbar-collapse" id="record-navbar">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section1">Top</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section2">Image</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section3">Description</a></li>
                    <?php if($audioLink != '') {
                        echo '<li ><a href ="'.$_SERVER['REQUEST_URI'].'#stc-section4" >Audio</a ></li >';
                    } ?>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section5">Instrument Data</a></li>
                    <li><a href="<?php echo $_SERVER['REQUEST_URI'];?>#stc-section6">Related Instruments</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<?php


foreach($recorddisplay as $key)
{
    $element = $this->skylight_utilities->getField($key);

    if(isset($solr[$element]))
    {
        foreach($solr[$element] as $index => $metadatavalue)
        {
            // if it's a facet search
            // make it a clickable search link

            if($key == 'Date Made') {
                $date = $metadatavalue;
            }
            if (!(isset($date))){
                $date = 'Undated';
            }
            if($key == 'Maker') {
                $maker = $metadatavalue;
            }
            if (!(isset($maker))){
                $maker = 'Unknown maker';
            }
            if($key == 'Title') {
                $title = $metadatavalue;
            }
            if (!(isset($title))){
                $title = 'Unnamed item';
            }
        }
    }
}

?>

<div id="stc-section1" class="container-fluid record-content">
    <h2 class="itemtitle hidden-sm hidden-xs"><?php echo $title .' | '. $maker. ' | '.$date;?></h2>
    <h4 class="itemtitle hidden-lg hidden-md"><?php echo $title .' | '. $maker. ' | '.$date;?></h4>
</div>

<div id="stc-section2" class="container-fluid">
<?php
if (isset($solr[$link_uri_field]))
{
    $imageCounter = 0;
    foreach($solr[$link_uri_field] as $linkURI)
    {
        $tileSource = str_replace('full/full/0/default.jpg', 'info.json', $linkURI);
        $json =  file_get_contents($tileSource);
        $jobj = json_decode($json, true);
        $error = json_last_error();

        $jsoncontext[$imageCounter] = $jobj['@context'];
        $jsonid[$imageCounter] = $jobj['@id'];
        $jsonheight[$imageCounter] = $jobj['height'];
        $jsonwidth[$imageCounter] = $jobj['width'];
        $jsonprotocol[$imageCounter] = $jobj['protocol'];
        $jsontiles[$imageCounter]= $jobj['tiles'];
        $jsonprofile[$imageCounter] = $jobj['profile'];

        list($width, $height) = getimagesize($linkURI);
        //echo 'WIDTH'.$width.'HEIGHT'.$height
        $portrait = true;
        if ($width > $height)
        {
            $jsontilesize[$imageCounter] = $jsontiles[$imageCounter][0]['width'];
            $portrait = false;
        }
        else
        {
            $jsontilesize[$imageCounter] = $jsontiles[$imageCounter][0]['height'];
        }
        $imageCounter++;
    }

    echo "<div id='imageCounter' style='display:none;'>$imageCounter</div>";
?>


    <div id='toolbarDiv'>
        <div class='toolbarItem' id='zoom-in'></div><div class='toolbarItem' id='zoom-out'></div><div class='toolbarItem' id='home'></div><div class='toolbarItem' id='full-page'></div><?php if($imageCounter > 1){ ?><div class='toolbarItem image-toggler' id='prev' data-image-id="#openseadragon<?php echo ($imageCounter - 1);?>"></div><div class='toolbarItem image-toggler' id='next' data-image-id="#openseadragon1"><?php } ?></div>
    </div>

    <div class="col-lg-12 main-image">

        <?php  $divCounter = 0;
        $freshIn = true;
        while ($divCounter < $imageCounter)
        {?>
            <div id="openseadragon<?php echo $divCounter; ?>" class="image-toggle"<?php if (!$freshIn) { echo ' style="display:none;"'; } else { echo ' style="display:block;"'; }?>>
                <script type="text/javascript">
                    OpenSeadragon({
                        id: "openseadragon<?php echo $divCounter;?>",
                        prefixUrl: "<?php echo base_url() ?>theme/stcecilia/images/buttons/",
                        zoomPerScroll: 1,
                        toolbar:       "toolbarDiv",
                        showNavigator:  true,
                        autoHideControls: false,
                        zoomInButton:   "zoom-in",
                        zoomOutButton:  "zoom-out",
                        homeButton:     "home",
                        fullPageButton: "full-page",
                        nextButton:     "next",
                        previousButton: "previous",
                        tileSources: [{
                            "@context": "<?php echo $jsoncontext[$divCounter] ?>",
                            "@id": "<?php echo $jsonid[$divCounter] ?>",
                            "height": <?php echo $jsonheight[$divCounter] ?>,
                            "width": <?php echo $jsonwidth[$divCounter] ?>,
                            "profile": ["http://iiif.io/api/image/2/level2.json",
                                {
                                    "formats": ["jpg"]
                                }
                            ],
                            "protocol": "<?php echo $jsonprotocol[$divCounter] ?>",
                            "tiles": [{
                                "scaleFactors": [1, 2, 8, 16, 32],
                                "width": "<?php echo $jsontiles[$divCounter][0]['width'];?>",
                                "height": "<?php echo $jsontiles[$divCounter][0]['height'];?>"
                            }],
                            "tileSize":<?php echo $jsontilesize[$divCounter];?>
                        }]
                    });
                </script>
            </div>

            <?php
            $divCounter++;
            $freshIn = false;
        }
        ?>
    </div>


    <?php
    $numThumbnails = 0;
    $imageset = false;
    $thumbnailLink = array();

    $countThumbnails = count($solr[$link_uri_field]);
    echo '<div class="thumb-strip">';
    if ($countThumbnails > 1)
    {
        foreach ($solr[$link_uri_field] as $linkURI)
        {
            $linkURI = $solr[$link_uri_field][$numThumbnails];

            $thumbnailLink[$numThumbnails] = '<label class="image-toggler" data-image-id="#openseadragon'.$numThumbnails.'">';
            $thumbnailLink[$numThumbnails] .= '<input type="radio" name="options" id="option'.$numThumbnails.'">';

            list($width, $height) = getimagesize($linkURI);
            $portrait = true;
            if ($width > $height)
            {
                $portrait = false;
            }
            if ($portrait)
            {
                $thumbnailLink[$numThumbnails] .= '<img src = "' . $linkURI . '" class="record-thumb-strip" title="' . $solr[$title_field][0];
            } else
            {
                $thumbnailLink[$numThumbnails] .= '<img src = "' . $linkURI . '" class="record-thumb-strip" title="' . $solr[$title_field][0];
            }

            $manifest = str_replace("iiif/", "iiif/m/", $linkURI);
            $manifest = str_replace("full/full/0/default.jpg", "manifest", $manifest);

            $json = file_get_contents($manifest);

            $jobj = json_decode($json, true);
            //print_r ($jobj);
            $error = json_last_error();
            $jsonMD = $jobj['sequences'][0]['canvases'][0]['metadata'];
            $rights = '';
            $photographer = '';
            $photoline = '';
            foreach ($jsonMD as $jsonMDPair)
            {

                if ($jsonMDPair['label'] == 'Repro Creator Name')
                {
                    $photographer = str_replace("<span>", "", $jsonMDPair['value']);
                    $photographer = str_replace("</span>", "", $photographer);
                }
                if ($jsonMDPair['label'] == 'Repro Rights Statement')
                {
                    $rights = str_replace("<span>", "", $jsonMDPair['value']);
                    $rights = 'Photograph '.str_replace("</span>", "", $rights);
                }

            }
            if ($photographer !== '')
            {
                $photoline = ' Photo by '.$photographer;
            }
            $thumbnailLink[$numThumbnails] .= '. '. $photoline.' '.$rights.'"/></label>';

            echo $thumbnailLink[$numThumbnails];
            $numThumbnails++;
            $imageset = true;

        }
    }

    ?>
        </div>
    <?php
    }
    ?>
    <div class = "json-link">
        <p>
            <?php if (isset($jsonLink)){echo $jsonLink;} ?>
        </p>
    </div>
</div>

<div id="stc-section3" class="container-fluid">
    <!--TODO Display Short description and description-->
    <div class="col-description">
        <?php foreach($descriptiondisplay as $key) {

            $element = $this->skylight_utilities->getField($key);

            if(isset($solr[$element])) {
                foreach($solr[$element] as $index => $metadatavalue) {
                    if ($key == "Short Description" or $key == "Description") {
                        echo "<span class='description'>";
                        echo $metadatavalue;
                        echo "</span>";
                    }
                }
            }
        } ?>
    </div>

    <?php
    foreach($recorddisplay as $key) {

        $element = $this->skylight_utilities->getField($key);

        if(isset($solr[$element])) {

            foreach($solr[$element] as $index => $metadatavalue) {
                echo '<div class="stc-tags">';

                // if it's a facet search
                // make it a clickable search link
                if(in_array($key, $filters)) {
                    if (!strpos($metadatavalue, "/")> 0)
                    {
                        $orig_filter = urlencode($metadatavalue);
                        $lower_orig_filter = strtolower($metadatavalue);
                        $lower_orig_filter = urlencode($lower_orig_filter);

                        echo '<a href="./search/*:*/' . $key . ':%22' . $lower_orig_filter . '%7C%7C%7C' . $orig_filter . '%22">' . $metadatavalue . '</a>';
                    }
                }
                echo '</div>';

            }
        }
    }
    ?>
</div>
<?php

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    if (!$audioLink == '')
    {
        echo '<div id="stc-section4" class="container-fluid">
            <!--h1 class="itemtitle hidden-sm hidden-xs">Audio/Visual</h1-->
            <!--h4 class="itemtitle hidden-lg hidden-md">Audio/Visual</h4-->'.
            $audioLink . '</div>';
    }
}
?>

<div id="stc-section5" class="panel panel-default container-fluid">
    <div class="panel-heading straight-borders">
        <h2 class="panel-title hidden-sm hidden-xs ">
            <a class="accordion-toggle" data-toggle="collapse" href="#collapse1">Instrument Data <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </a>
        </h2>
        <h4 class="panel-title hidden-md hidden-lg ">
            <a class="accordion-toggle" data-toggle="collapse" href="#collapse1">Instrument Data <i class="fa fa-chevron-down" aria-hidden="true"></i>

            </a>
        </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-6 col-xs-12 col-md-8 col-lg-12 metadata">

                <div id="info-box">

                    <h3>Identification Information</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($identificationdisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!-- main-info -->

                <div id="info-box">
                    <h3>Date Information</h3>
                    <dl class="dl-horizontal">
                        <?php
                            $infofound = false;
                            foreach($datedisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }

                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }

                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }
                        ?>
                    </dl>
                </div> <!-- meta-info -->

                <div id="info-box">
                    <h3>Maker</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($creatordisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!-- creator-info -->

                <div id="info-box">
                    <h3>Production Place</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($placedisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--place-info -->

                <div id="info-box">
                    <h3>Object Type Information</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($typedisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--type-info -->

                <div id="info-box">
                    <h3>Location</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($locationdisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--location-info -->

                <div id="info-box">
                    <h3>Associated Performers</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($associationdisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--association-info -->

                <div id="info-box">
                    <h3>Measurements</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($measurementdisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--measurement-info -->

                <div id="info-box">
                    <h3>Description</h3>
                    <dl class="dl-horizontal">
                        <?php
                        $infofound = false;
                        foreach($descriptiondatadisplay as $key) {

                            $element = $this->skylight_utilities->getField($key);

                            if(isset($solr[$element])) {

                                echo '<dt>' . $key . '</dt>';

                                echo '<dd>';
                                foreach($solr[$element] as $index => $metadatavalue) {
                                    // if it's a facet search
                                    // make it a clickable search link
                                    if(in_array($key, $filters)) {

                                        $orig_filter = urlencode($metadatavalue);
                                        $lower_orig_filter = strtolower($metadatavalue);
                                        $lower_orig_filter = urlencode($lower_orig_filter);

                                        echo '<a href="./search/*:*/' . $key . ':%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$metadatavalue.'</a>';
                                    }
                                    else {
                                        echo $metadatavalue;
                                    }
                                    if($index < sizeof($solr[$element]) - 1) {

                                        echo '; ';
                                    }
                                }
                                $infofound = true;
                                echo '</dd>';
                            }
                        }
                        if (!$infofound) {
                            echo '<p>No information recorded.</p>';
                        }?>
                    </dl>
                </div> <!--description info -->


            </div> <!-- metadata -->
        </div> <!-- panel body -->
    </div>
</div>
