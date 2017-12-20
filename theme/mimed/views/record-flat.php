<?php

$author_field = $this->skylight_utilities->getField("Author");
$maker_field = $this->skylight_utilities->getField("Maker");
$type_field = $this->skylight_utilities->getField("Type");
$bitstream_field = $this->skylight_utilities->getField("Bitstream");
$thumbnail_field = $this->skylight_utilities->getField("Thumbnail");
$filters = array_keys($this->config->item("skylight_filters"));
$link_uri_field = $this->skylight_utilities->getField("Link");
$media_uri = $this->config->item("skylight_media_url_prefix");

$type = 'Unknown';
$mainImageTest = false;
$numBitstreams = 0;
$bitstreamLinks = array();

if(isset($solr[$type_field])) {
    $type = "media-" . strtolower(str_replace(' ','-',$solr[$type_field][0]));
}

if(isset($solr[$bitstream_field]) && $link_bitstream) {

    $bitstream_array = array();

    foreach ($solr[$bitstream_field] as $bitstream_for_array)
    {
        $b_segments = explode("##", $bitstream_for_array);
        $b_seq = $b_segments[4];
        $bitstream_array[$b_seq] = $bitstream_for_array;
    }

    ksort($bitstream_array);

    $videoFile = false;
    $audioFile = false;
    $audioLink = "";
    $videoLink = "";
    $b_seq =  "";

    foreach($bitstream_array as $bitstream) {

        $mp4ok = false;
        $b_segments = explode("##", $bitstream);
        $b_filename = $b_segments[1];
        $b_handle = $b_segments[3];
        $b_seq = $b_segments[4];
        $b_handle_id = preg_replace('/^.*\//', '',$b_handle);


        if ((strpos($b_filename, ".jpg") > 0) or (strpos($b_filename, ".JPG") > 0))
        {
            $b_uri = './record/'.$b_handle_id.'/'.$b_seq.'/'.$b_filename;

            $bitstreamLinks[$numBitstreams] = '<div class="bitstream-image">';

            $bitstreamLinks[$numBitstreams] .= '<a title = "' . $record_title . '" class="fancybox" rel="group" href="' . $b_uri . '"> ';

            if($numBitstreams == 0) {
                $bitstreamLinks[$numBitstreams] .= '<img id="main-image"';
            }
            else if($numBitstreams == 1) {
                $bitstreamLinks[$numBitstreams] .= '<img id="second-image" class="record-image"';
            }
            else {
                $bitstreamLinks[$numBitstreams] .= '<img class="record-image"';
            }
            $bitstreamLinks[$numBitstreams] .= ' src = "'. $b_uri .'">';
            $bitstreamLinks[$numBitstreams] .= '</a>';

            $bitstreamLinks[$numBitstreams] .= '</div>';
            $numBitstreams++;

        }
        else if ((strpos($b_filename, ".mp3") > 0) or (strpos($b_filename, ".MP3") > 0)) {

            $b_uri = './record/'.$b_handle_id.'/'.$b_seq.'/'.$b_filename;
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

        ?>
    <?php
    }

}
?>

<div class="content">

    <h1 class="itemtitle"><?php echo $record_title ?></h1>
    <div class="tags">
    <?php

    if (isset($solr[$author_field])) {
        foreach($solr[$author_field] as $author) {

            $orig_filter = urlencode($author);

            $lower_orig_filter = strtolower($author);
            $lower_orig_filter = urlencode($lower_orig_filter);

            echo '<a class="maker" href="./search/*:*/Maker:%22'.$lower_orig_filter.'%7C%7C%7C'.$orig_filter.'%22">'.$author.'</a>';
        }
    }

    ?>
    </div>

    <div class="record-content">
    <?php
    if($numBitstreams > 0) { ?>

        <div id="left-metadata">

    <?php } else { ?>

        <div id="full-metadata">

    <?php } ?>

        <table>
            <tbody>
            <?php foreach($recorddisplay as $key) {

                $element = $this->skylight_utilities->getField($key);
                if(isset($solr[$element])) {
                   echo $key == "Maker" ? '<tr><td class="first">' : '<tr><td>';

                    echo '<h4>' . $key . '</h4>';
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
                    echo '</td></tr>';
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
                            echo '<tr><td><h4>Zoomable Image(s)</h4>';
                        }

                        echo '<a href="'. $linkURI . '" target="_blank"><i class="fa fa-file-image-o fa-2x">&nbsp;</i></a>';

                        $i++;
                    }

                }

                if($lunalink) {
                    echo '</td></tr>';
                }
            }?>

            </tbody>
        </table>
    </div>

    <?php

        foreach($bitstreamLinks as $bitstreamLink) {

            echo $bitstreamLink;
        }

    ?>

    <div class="clearfix"></div>

    <?php

    if(isset($solr[$bitstream_field]) && $link_bitstream) {

        echo '<div class="record_bitstreams">';

        if($audioFile) {

            echo $audioLink;
        }

        if($videoFile) {

            echo $videoLink;
        }

        echo '</div><div class="clearfix"></div>';

    }

    echo '</div>';
echo '</div>';
?>

<input type="button" value="Back to Search Results" class="backbtn" onClick="history.go(-1);">
