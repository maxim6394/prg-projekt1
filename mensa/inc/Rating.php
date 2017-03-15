<?php

class Rating {

    public $id;
    public $dishName;
    public $rating;
    public $timeInserted;
    public $comment;
    public $userFingerprint;
    public static $dbError;
    private $isNewRecord = true;

    const TABLE_NAME = "rating";

    public function __construct($dishName, $rating, $comment) {
        $this->dishName = $dishName;
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function save() {
        if (($mysqli = static::getConnection()) !== null) {

            if ($this->isNewRecord) {
                if ($statement = $mysqli->prepare(
                        "INSERT INTO " . static::TABLE_NAME . " (dish_name, rating, comment, user_fingerprint) VALUES (?, ?, ?, ?)")) {
                    $fingerprint = null;
                    $statement->bind_param("siss", $this->dishName, $this->rating, $this->comment, $fingerprint);
                    if ($statement->execute()) {
                        $this->isNewRecord = false;
                        return true;
                    } else
                        $this->dbError = $statement->error;
                } else
                    $this->dbError = $mysqli->error;
            }
            else {
                if ($statement = $mysqli->prepare(
                        "UPDATE " . static::TABLE_NAME . " SET dish_name=?, rating=?, comment=? WHERE id=?")) {
                    $statement->bind_param("sisi", $this->dishName, $this->rating, $this->comment, $this->id);
                    if ($statement->execute()) {
                        $this->isNewRecord = false;
                        return true;
                    } else
                        $this->dbError = $statement->error;
                } else
                    $this->dbError = $mysqli->error;
            }
        }
        return false;
    }

    public function __toString() {
        return "[" . $this->dishName . "]: " . $this->rating . " | " . $this->comment;
    }
	
	
	public static function getAllComments($dish) {
		$mysqli = static::getConnection();
		if ($mysqli !== null) {
            $result = $mysqli->query("SELECT comment FROM " . Rating::TABLE_NAME . " WHERE comment IS NOT NULL AND dish_name=\"" . $dish . "\"");
			return $result->fetch_all();
        }
		return null;
	}
	
    public function getAllRatings($dish) {
        $mysqli = static::getConnection();
        if ($mysqli !== null) {
            $result = $mysqli->query("SELECT * FROM " . Rating::TABLE_NAME . " WHERE dish_name=\"" . $dish . "\"");

            $ratings = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $rating = new Rating($dish, $result["rating"], $result["comment"]);
                $rating->id = $result["id"];
                $rating->userFingerprint = $result["user_fingerprint"];
                $rating->timeInserted = $result["time_inserted"];
                $rating->isNewRecord = false;
                $ratings [] = $rating;
            }                        
            
            return $ratings;
        }

        return null;
    }

    public static function getRatingFor($dish) {
        $mysqli = static::getConnection();
        if ($mysqli !== null) {
            $result = $mysqli->query("SELECT COUNT(*) as total FROM " . Rating::TABLE_NAME . " WHERE dish_name=\"" . $dish . "\"");
            $totalRatings = $result->fetch_row()['0'];
            $result->close();

            if ($totalRatings == 0)
                return null;

            $rating = 0;

            for ($i = 1; $i <= 5; $i++) {
                $result = $mysqli->query("SELECT COUNT(*) as total FROM " . Rating::TABLE_NAME . " WHERE dish_name=\"" . $dish . "\" AND rating=" . $i);
                $rating += $result->fetch_row()['0'] * $i;
            }

            return $rating / $totalRatings;
        }

        return null;
    }

    
    /**
     * 
     * @global type $dbConfig
     * @return mysqli
     */
    private static function getConnection() {
        global $dbConfig;
        $mysqli = mysqli_connect($dbConfig["host"], $dbConfig["user"], $dbConfig["pass"], $dbConfig["dbName"]);
        if ($mysqli->connect_errno) {
            static::$dbError = $mysqli->error;
            return null;
        }
        return $mysqli;
    }

}
