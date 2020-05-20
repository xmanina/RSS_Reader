<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manina RSS</title>
    <link rel="stylesheet" href="stylesheets/main.css">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>
<script>
    // This calls the function
    //DOYPSort('#wrapper', '.element', value, order);

    // Parameters must be strings
    // Order of must be either 'H' (Highest) or 'L' (Lowest)
    function DOYPSort(orderof) {

        if (orderof === 'H'){
            document.getElementById("tlacidlo").setAttribute("onclick","DOYPSort('L')");
        }
        else {
            document.getElementById("tlacidlo").setAttribute("onclick","DOYPSort('H')");
        }
        AttrToSort= 'data-text';

            $('#prispevky').find('.post').sort(function (a, b) {
            if (orderof === 'H') {
                return +b.getAttribute(AttrToSort) - +a.getAttribute(AttrToSort);
            }
            if (orderof === 'L') {
                return +a.getAttribute(AttrToSort) - +b.getAttribute(AttrToSort);
            }
        }).appendTo('#prispevky');
    }

</script>

<div class="content">

    <form method="post" action="#">
        <input type="text" name="feedurl" placeholder="Vlož URL adresu">&nbsp;<input type="submit" value="Odoslať" name="submit">
    </form>
    <?php

    $url = "https://www.sme.sk/rss-title";
    if (isset($_POST['submit'])) {
        if ($_POST['feedurl'] != '') {
            $url = $_POST['feedurl'];
        }
    }

    $invalidurl = false;
    if (@simplexml_load_file($url)) {
        $entries = array();
        $feeds = simplexml_load_file($url);
        $entries = array_merge($entries, $feeds->xpath("//item"));
    } else {
        $invalidurl = true;
        echo "<h2>Invalid RSS feed URL.</h2>";
    }


    if (!empty($feeds)){

    usort($entries, function ($feed1, $feed2) {
        return strtotime($feed2->pubDate) - strtotime($feed1->pubDate);
    });


    $site = $feeds->channel->title;
    $desc = $feeds->channel->description;
    $sitelink = $feeds->channel->link;


    echo "<h1>" . $site . "</h1> <p>" . $desc . "</p><br>";
    ?>
    <button id="tlacidlo" onclick="DOYPSort('H')">Usporiadať  &#8593;&#8595;</button>
    <div id="prispevky">
        <?php
        $count = 0;
        foreach ($entries as $item) {

            $title = $item->title;
            $link = $item->link;
            $description = $item->description;
            $postDate = $item->pubDate;
            $piclink = $item->enclosure->attributes();
            $pubDate = date('D, d M Y', strtotime($postDate));s


            ?>

            <div class="post" data-text="<?php echo $count; ?>">
                <img src="<?php echo $piclink; ?>"  alt="obrazocek">
                <div class="post-head">
                    <h2><a class="feed_title" href="<?php echo $link; ?>"><?php echo $title; ?></a></h2>
                    <span><?php echo $pubDate; ?></span>
                </div>
                <div class="post-content">
                    <?php echo implode(' ', array_slice(explode(' ', $description), 0, 20)) . "..."; ?> <a
                            href="<?php echo $link; ?>" target="_blank">Čítaj viac</a>
                </div>
            </div>

            <?php
            $count++;
        }
        } else {
            if (!$invalidurl) {
                echo "<h2>No item found</h2>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
