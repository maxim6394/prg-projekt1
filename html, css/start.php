<?php
require 'inc/config.php';
require 'inc/Rating.php';
//require 'inc/Dish.php';
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

<!DOCTYPE HTML>
<html lang=de>
	<head>
		<meta charset=UTF-8 name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="design.css" type="text/css" rel="stylesheet" style="example1">
		<title>Mensameter</title>
	</head>
	<body>
		<header>
		<header/>
		<main>
			<h1>Mensameter</h1>
			<h2>Speiseplan der Mensa Finkenau </h2>
			<div class="erlaeuterung">
				<i> Nicht <span class="after">vergessen:</span></i>
				<i>	Nach dem <span class="after">Essen,</span></i>
				<i> Bewertung <span class="after">abgeben!</span></i>
				<p>Mit dem <b class="mensa">Mensameter</b> weißt du <span class="after">immer,</span> was es zu essen gibt und wie's schmeckt.</p>

			</div>

			<!-- <select class="dropdown">
    	<option value="hide">-- Was hast du gegessen? --</option>
    	<option>essen 1 </option>

			</br></br></br>

					</br>

					<input type="submit" class="button" value="Bewertung abgeben">

				</br></br> !-->

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

					<table class="table">
					<tr>
    				<th colspan="2">Speiseplan </th>

  				</tr>
  				<tr>
    				<td>Pizza</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>2 Quarkkeulchen (14,16,20) , Apfelmus (3)</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Gemüsepfanne Thai Red (2,14,19,22,24,25) , Basmatireis Klima TellerlaktosefreiVegan</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					<tr>
    				<td>Peter</td>
    				<td>Griffin</td>
  				</tr>
  				<tr>
    				<td>Lois</td>
    				<td>Griffin</td>
  				</tr>
					</table>



		</main>
	</body>
</html>
