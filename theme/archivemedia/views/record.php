<?php

$author_field = $this->skylight_utilities->getField("Author");
$type_field = $this->skylight_utilities->getField("Type");
$bitstream_field = $this->skylight_utilities->getField("Bitstream");
$thumbnail_field = $this->skylight_utilities->getField("Thumbnail");
$date_field = $this->skylight_utilities->getField("Date");
$filters = array_keys($this->config->item("skylight_filters"));
$link_uri_field = $this->skylight_utilities->getField("Link");
$tags_field = $this->skylight_utilities->getField("Tags");
$media_uri = $this->config->item("skylight_media_url_prefix");

$type = 'Unknown';
$mainImageTest = false;
$numThumbnails = 0;
$bitstreamLinks = array();
$image_id = "";

if(isset($solr[$type_field])) {
    $type = "media-" . strtolower(str_replace(' ','-',$solr[$type_field][0]));
}

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    foreach ($solr[$bitstream_field] as $bitstream_for_array)
    {
        $b_segments = explode("##", $bitstream_for_array);
        $b_seq = $b_segments[4];
        $bitstream_array[$b_seq] = $bitstream_for_array;
    }

    ksort($bitstream_array);

    $mainImage = false;
    $videoFile = false;
    $audioFile = false;
    $audioLink = "";
    $videoLink = "";
    $b_seq =  "";

    foreach($bitstream_array as $bitstream) {
        $mp4ok = false;
        $b_segments = explode("##", $bitstream);
        $b_filename = $b_segments[1];
        if($image_id == "") {
            $image_id = substr($b_filename,0,7);
        }
        $b_handle = $b_segments[3];
        $b_seq = $b_segments[4];
        $b_handle_id = preg_replace('/^.*\//', '',$b_handle);
        $b_uri = './record/'.$b_handle_id.'/'.$b_seq.'/'.$b_filename;

        if ((strpos($b_uri, ".jpg") > 0) or (strpos($b_uri, ".JPG") > 0))
        {
            if (!$mainImage) {

                // we have a main image
                $mainImageTest = true;

                $bitstreamLink = '<div class="main-image">';

                $bitstreamLink .= '<a title = "' . $record_title . '" class="fancybox" rel="group" href="' . $b_uri . '"> ';
                $bitstreamLink .= '<img class="record-main-image" src = "'. $b_uri .'">';
                $bitstreamLink .= '</a>';

                $bitstreamLink .= '</div>';

                $mainImage = true;

            }
            // we need to display a thumbnail
            else {

                // if there are thumbnails
                if(isset($solr[$thumbnail_field])) {
                    foreach ($solr[$thumbnail_field] as $thumbnail) {

                        $t_segments = explode("##", $thumbnail);
                        $t_filename = $t_segments[1];

                        if ($t_filename === $b_filename . ".jpg") {

                            $t_handle = $t_segments[3];
                            $t_seq = $t_segments[4];
                            $t_uri = './record/'.$b_handle_id.'/'.$t_seq.'/'.$t_filename;

                            $thumbnailLink[$numThumbnails] = '<div class="thumbnail-tile';

                            if($numThumbnails % 4 === 0) {
                                $thumbnailLink[$numThumbnails] .= ' first';
                            }

                            $thumbnailLink[$numThumbnails] .= '"><a title = "' . $record_title . '" class="fancybox" rel="group" href="' . $b_uri . '"> ';
                            $thumbnailLink[$numThumbnails] .= '<img src = "'.$t_uri.'" class="record-thumbnail" title="'. $record_title .'" /></a></div>';

                            $numThumbnails++;
                        }
                    }
                }

            }

        }
        else if ((strpos($b_uri, ".mp3") > 0) or (strpos($b_uri, ".MP3") > 0)) {

            $audioLink .= '<audio controls>';
            $audioLink .= '<source src="' . $b_uri . '" type="audio/mpeg" />Audio loading...';
            $audioLink .= '</audio>';
            $audioFile = true;

        }

        else if ((strpos($b_filename, ".mp4") > 0) or (strpos($b_filename, ".MP4") > 0))
        {
            $b_uri = $media_uri.$b_handle_id.'/'.$b_seq.'/'.$b_filename;
            // Use MP4 for all browsers other than Chrome
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == false)
            {
                $mp4ok = true;
            }
            //Microsoft Edge is calling itself Chrome, Mozilla and Safari, as well as Edge, so we need to deal with that.
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == true)
            {
                $mp4ok = true;
            }

            if ($mp4ok == true)
            {
                $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                $videoLink .= '<source src="' . $b_uri . '" type="video/mp4" />Video loading...';
                $videoLink .= '</video>';
                $videoLink .= '</div>';
                $videoFile = true;
            }
        }

        else if ((strpos($b_filename, ".webm") > 0) or (strpos($b_filename, ".WEBM") > 0))
        {
            //Microsoft Edge needs to be dealt with. Chrome calls itself Safari too, but that doesn't matter.
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') == false)
            {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == true)
                {
                    $b_uri = $media_uri . $b_handle_id . '/' . $b_seq . '/' . $b_filename;
                    // if it's chrome, use webm if it exists
                    $videoLink .= '<div class="flowplayer" data-analytics="' . $ga_code . '" title="' . $record_title . ": " . $b_filename . '">';
                    $videoLink .= '<video preload=auto loop width="100%" height="auto" controls preload="true" width="660">';
                    $videoLink .= '<source src="' . $b_uri . '" type="video/webm" />Video loading...';
                    $videoLink .= '</video>';
                    $videoLink .= '</div>';
                    $videoFile = true;
                }
            }
        }

        else if ((strpos($b_uri, ".pdf") > 0) or (strpos($b_uri, ".PDF") > 0)) {

            $bitstreamLink = $this->skylight_utilities->getBitstreamLink($bitstream);
            $bitstreamUri = $this->skylight_utilities->getBitstreamUri($bitstream);

            $pdfLink .= 'Click ' . $bitstreamLink . 'to download. (<span class="bitstream_size">' . getBitstreamSize($bitstream) . '</span>)';
        }

        ?>
    <?php
    }

}
?>

<div class="content">

    <?php if($mainImageTest === true) { ?>
    <div class="full-title">
        <?php } ?>
        <h1 class="itemtitle"><?php echo $record_title ?>
            <?php if(isset($solr[$date_field])) {
                echo " (" . $solr[$date_field][0] . ")";
            } ?>
        </h1>
        <div class="tags">
            <?php

            if (isset($solr[$author_field])) {
                foreach($solr[$author_field] as $author) {

                    $orig_filter = urlencode($author);

                    $lower_orig_filter = strtolower($author);
                    $lower_orig_filter = urlencode($lower_orig_filter);

                    echo '<a class="artist" href="./search/*:*/Artist:%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$author.'</a>';
                }
            }

            ?>
        </div>
        <?php if($mainImageTest === true) { ?>
    </div>
<?php if($mainImage) { ?>
    <div class="full-image">
        <?php echo $bitstreamLink; ?>
    </div>
<?php } ?>
<?php } ?>

    <?php if($mainImageTest === true) { ?>

    <div class="full-metadata">
        <?php } ?>
        <table>
            <tbody>
            <?php $excludes = array(""); ?>
            <?php

            foreach($recorddisplay as $key) {

                $element = $this->skylight_utilities->getField($key);

                if(isset($solr[$element])) {
                    if(!in_array($key, $excludes)) {
                        echo '<tr><th>'.$key.'</th><td>';
                        foreach($solr[$element] as $index => $metadatavalue) {
                            // if it's a facet search
                            // make it a clickable search link
                            if(in_array($key, $filters) && $key != "Artist") {

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
                        echo '</td></tr>';
                    }
                }

            } ?>

            <?php
            $i = 0;
            $lunalink = false;
            if (isset($solr[$link_uri_field])) {
                foreach($solr[$link_uri_field] as $linkURI) {
                    $linkURI = str_replace('"', '%22', $linkURI);
                    $linkURI = str_replace('|', '%7C', $linkURI);

                    if (strpos($linkURI,"images.is.ed.ac.uk") != false)
                    {
                        $lunalink = true;

                        if($i == 0) {
                            echo '<tr><th>Zoomable Image(s)</th><td>';
                        }

                        echo '<a href="'. $linkURI . '" target="_blank"><i class="fa fa-file-image-o fa-lg">&nbsp;</i></a>';

                        $i++;
                    }

                }

                if($lunalink) {
                    echo '</td></tr>';
                }
            }?>
            </tbody>
        </table>
        <?php if($mainImageTest === true) { ?>
    </div>
<?php } ?>
    <div class="clearfix"></div>
    <!-- print out crowdsourced tags -->
    <?php

    if(isset($solr[$tags_field])) {?>
        <div class="crowd-tags"><span class="crowd-title" title="User generated tags created through crowd sourcing games"><i class="fa fa-users fa-lg" >&nbsp;</i>Tags:</span>
         <?php foreach($solr[$tags_field] as $tag) {

             $orig_filter = urlencode($tag);
             $lower_orig_filter = strtolower($tag);
             $lower_orig_filter = urlencode($lower_orig_filter);
            echo '<span class="crowd-tag">' . '<a href="./search/*:*/Tags:%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22"><i class="fa fa-tags fa-lg">&nbsp;</i>'.$tag.'</a>' . '</span>';
         } ?>
            <div class="crowd-info">
                <form id="libraylabs" method="get" action="http://librarylabs.ed.ac.uk/games/gameCrowdSourcing.php" target="_blank">
                    <input type="hidden" name="image_id" value="<?php echo $image_id ?>">
                    <input type="hidden" name="theme" value="art">
                    Add more tags at <a href="#" onclick="document.forms[1].submit();return false;" title="University of Edinburgh, Library Labs Metadata Games">Library Labs Games</a>
                    (Create a login at <a href="https://www.ease.ed.ac.uk/friend/" target="_blank" title="EASE Friend">Edinburgh Friend Account</a>)
                </form>
            </div>
        </div>

    <?php }

    else {

    ?>
    <div class="crowd-tags">
        <div class="crowd-info">
            <form id="libraylabs" method="get" action="http://librarylabs.ed.ac.uk/games/gameCrowdSourcing.php" target="_blank">
                <input type="hidden" name="image_id" value="<?php echo $image_id ?>">
                <input type="hidden" name="theme" value="art">
                Add tags to this image at <a href="#" onclick="document.forms[1].submit();return false;" title="University of Edinburgh, Library Labs Metadata Games">Library Labs Games</a>
                (Create a login at <a href="https://www.ease.ed.ac.uk/friend/" target="_blank" title="EASE Friend">Edinburgh Friend Account</a>)
            </form>
        </div>
    </div>


    <?php
    }

    if(isset($solr[$bitstream_field]) && $link_bitstream) {

        echo '<div class="record_bitstreams">';

        $i = 0;
        $newStrip = false;
        if($numThumbnails > 0) {

            echo '<div class="thumbnail-strip">';

            foreach($thumbnailLink as $thumb) {

                if($newStrip)
                {

                    echo '</div><div class="clearfix"></div>';
                    echo '<div class="thumbnail-strip">';
                    echo $thumb;
                    $newStrip = false;
                }
                else {
                    echo $thumb;
                }

                $i++;

                // if we're starting a new thumbnail strip
                if($i % 4 === 0) {
                    $newStrip = true;
                }
            }

            echo '</div><div class="clearfix"></div>';
        }

        if($audioFile) {


            echo '<br>.<br>'.$audioLink;
        }

        if($videoFile) {

            echo '<br>.<br>'.$videoLink;
        }

        echo '</div><div class="clearfix"></div>';

    }

    echo '</div>';
    ?>

    <input type="button" value="Back to Search Results" class="backbtn" onClick="history.go(-1);">