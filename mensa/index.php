<?php
require 'inc/config.php';
require 'inc/Rating.php';
require 'inc/helpers.php';
require 'inc/phpQuery.php';

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

    <?php
    $table->append(ob_get_clean());

    $additives = $page->find("ul.additives");
}
?>

<!DOCTYPE HTML>
<html lang=de>
	<head>
		<meta charset=UTF-8 name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="design.css" type="text/css" rel="stylesheet" style="example1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<title>Mensameter</title>
	</head>
	<body>
		<header>
		<header/>
		<main>
			<div class="nonsense">
			<h1>Mensameter</h1>
			<h2>Speiseplan der Mensa Finkenau </h2>

			<div class="erlaeuterungen">
				<i> Nicht <span class="after">vergessen:</span></i>
				<i>	Nach dem <span class="after">Essen</span></i>
				<i> Bewertung <span class="after">abgeben!</span></i>
			</div>

			<img class="pfeil" src="pfeil.png" alt="Pfeil" width="7,3%" height="5,3%">
			<p>Mit dem <b class="mensa">Mensameter</b> weißt du <span class="after">immer,</span> was es zu essen gibt und wie's schmeckt.</p>

			<div class="speiseplan">
      	<form method="POST">
            <?php
            echo $table;
            ?>
						<input type="submit" class="button" value="Bewertung abgeben">
      	</form>
				<div class="liste">
            <?php
            foreach ($additives as $list)
                echo pq($list)->html();
            ?>
				</div>
			</div>
		</div>
		</main>
	</body>
</html>
