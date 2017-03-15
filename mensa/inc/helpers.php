<?php

/**
 * 
 * @param Dish $dish
 */
function showDish($dish) {
    ?>

    <tr>
        <td><?= $dish->name; ?></td>
        <td><?php showStars($dish->getAverageRating()); ?> (<?= $dish->getAverageRating(); ?>)</td>
        <td><?= $dish->getTotalRatings(); ?> Bewertungen gesamt</td>    
    </tr>

    <?php
}

function getRandomComments($comments, $max=3) {
	$arr = [];
	$max = min(sizeof($comments), $max);
	
	for($i = 0 ; $i<$max; $i++){
		$rand = rand(0, sizeof($comments) - 1 - $i);
		
		$arr[] = $comments[$rand][0];	
		unset($comments[$rand]);
		$comments = array_values($comments);
	}
	
	return $arr;
}

function getRatingForm($dishName) {
    ob_start();
    $rating = Rating::getRatingFor($dishName);
    
    $whole = floor($rating);
    
    echo "<div class='rating-container'>";    
    for ($i = 1; $i <= 5; $i++) {
         
        if ($i <= $whole || $i - 0.25 <= $rating)
            echo "<span class='fa fa-star'></span>";        //voller stern
        else if ($i - 0.75 <= $rating)
            echo "<span class='fa fa-star-half-full'></span>";  //halber stern
        else
            echo "<span class='fa fa-star-o'></span>";          //leerer stern

    }    
    echo "</div>";
    
    ?>
    <div class="rating-form-container">
        <select name="rating[<?= $dishName ?>]">
            <option> - </option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>            
        
        </select>
    </div>
    <?php    
    return ob_get_clean();
}

function showStars($rating) {
    $whole = floor($rating);

    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $whole || $i - 0.25 <= $rating)
            echo "<span class='fa fa-star'></span>";        //voller stern
        else if ($i - 0.75 <= $rating)
            echo "<span class='fa fa-star-half-full'></span>";  //halber stern
        else
            echo "<span class='fa fa-star-o'></span>";          //leerer stern
    }
}
