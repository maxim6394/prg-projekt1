<?php
require 'inc/config.php';
require 'inc/Rating.php';
require 'inc/Dish.php';
require 'inc/helpers.php';
require 'inc/phpQuery.php';

//$rating = new Rating("asdf", 1, "yo yo");
//var_dump($rating->save());
//var_dump(Rating::getRatingFor("asdf"));




        const DOMAIN = 'http://speiseplan.studierendenwerk-hamburg.de';



$page = phpQuery::newDocument(file_get_contents(DOMAIN));
$finkenauLink = $page->find("a:contains('Mensa Finkenau')");
$page = phpQuery::newDocument(file_get_contents(DOMAIN . '/' . $finkenauLink->attr("href")));

$todayLink = $page->find("#content ul:first-of-type li:first-child a:first-child");

if ($todayLink->length == 1) {
    $page = phpQuery::newDocument(file_get_contents(DOMAIN . '/' . $todayLink->attr("href")));

    $table = $page->find("#plan table");

    
    foreach ($table->find("img[src]") as $el) {
        pq($el)->attr("src", DOMAIN . "/" . pq($el)->attr("src"));
    }

    $table->find("#headline")->append("<th>Bewertung</th>");

    $rows = $page->find("#plan table tbody tr:not(#headline)");

    foreach ($rows as $row) {
        $row = pq($row);
        $description = trim($row->find(".dish-description")->text());
        
        if (isset($_POST["rating"]) && isset($_POST["rating"][$description]) && is_numeric($_POST["rating"][$description])) {

            $num = max(min(5, intval($_POST["rating"][$description])), 1);

            $rating = new Rating($description, $num, null);
            $rating->save();
        }
        
        pq($row)->append("<td>" . getRatingForm($description) . "</td>");
    }

    ob_start();
    ?>
    <tfoot>
        <tr>
            <td colspan="4"></td>
            <td><button>Senden</button></td>
        </tr>
    </tfoot>
    <?php
    $table->append(ob_get_clean());

    
    $additives = $page->find("ul.additives");
}
?>


<!DOCTYPE html>
<html>
    <head>        

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" media="all" href="<?= DOMAIN ?>/css/main.css">
        <meta charset="UTF-8">
        <title>Mensameter</title>
    </head>
    <body>
        <div id="plan-page">
        <form method="POST">
            <?php
            echo $table;
            ?>
        </form>
            <?php
            foreach ($additives as $list)
                echo pq($list)->html();



            /*
              for($i = 1.0; $i <= 5.0; $i+= 0.2) {
              showStars($i);
              echo " (".$i.")<br>";
              } */
            ?>

</div>  
    </body>
</html>