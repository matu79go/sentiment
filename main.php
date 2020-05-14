<?php


// ini_set('display_errors', 1);
// error_reporting(E_ALL);


//set_time_limit(120);

require_once('./config.php');
require_once('./feelings.php');

$dir = str_replace('batch', 'func', dirname(__FILE__));


require_once './vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;



// create instance of TwitterOAuth .
$obj = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, ACCESS_TOKEN, ACCESS_SECRET);

// get keyword.
$keyword = $_GET["keyword"];

// q is search word, count is search number, lang is written language. result_type is wheather recent or not. count is number for serching.
$options = array('q'=>$keyword,'result_type' => 'recent', 'count' => SEARCH_COUNT );

$json = $obj->get("search/tweets", $options);

$statuses = null;


if ($json){
  $statuses = $json->statuses; // get status
}


$sum_array = array(
  'scores' => 0,
  'positive_count' => 0,
  'negative_count' => 0,
);


if ($statuses && is_array($statuses)) {
  foreach($statuses as $value){

    $feelings = new Feelings(PERMIT_ONLY_ADJECTIVE);
    $data = $feelings->getAnalizedData($value->text);

    $scores         = $data['scores'];
    $positive_count = $data['positive_count'];
    $negative_count = $data['negative_count'];

    $sum_array['scores']         = $scores + $sum_array['scores'];
    $sum_array['positive_count'] = $positive_count + $sum_array['positive_count'];
    $sum_array['negative_count'] = $negative_count + $sum_array['negative_count'];

//     $sum_array['posi'][]         = $data['posi'];
//     $sum_array['nega'][]         = $data['nega'];

  }

}

//var_dump($sum_array);

?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>test sentiment analysis</title>
</head>
<body>

<?php

  echo "keyword : {$keyword}<br>";
  echo "score : {$sum_array['scores']} <br>";
  echo "positive word count : {$sum_array['positive_count']} <br>";
  echo "negative word count : {$sum_array['negative_count']} <br>";

?>

</body>
</html>


