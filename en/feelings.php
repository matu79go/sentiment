<?php

class Feelings{

    private $path = './sentiment.txt'; // dictionary
    //private $mecab;
    private $permit_only_adjective = false;

    public function __construct($p_permit_only_adjective) {

      // Load mecab module
      //$options = array('-d', IPA_DICTIONARY_PATH);
      //$this->mecab = new \MeCab\Tagger($options);
      $this->permit_only_adjective = $p_permit_only_adjective;

    }

    public function __destruct() {
        //$this->mecab = null;
    }

    // morphological analysis by mecab and sentimental score..
    public function getAnalizedData($p_str){

        $searh_words = array();


        // start
        $text_arr = array();

        $scores = 0;
        $positive_count = 0;
        $negative_count = 0;

        $posi_words = null;
        $nega_words = null;



        // check dictionary
        $hit_words = array();

        // explode by space or, \r, \t, \n , \f
        //$text_arr = explode(' ', $p_str);
        $text_arr = preg_split("/[\s,]+/", $p_str);

        foreach ( $text_arr as $text ) {


          foreach ( file( $this->path ) as $line ) {

            $master = explode(':', $line);

            /*
             * [0] : word
            * [1] : part of speech
            * [2] : score
            *
            */

            // if permitting only adjective or not.
            if ( $this->permit_only_adjective == 1 ) {
              if ( $master[1] !== "a" ) {
                continue;
              }
            }

            // check if words are in dictionary.
            if ( $text === $master[0] ) {

              $float_score = (float)trim($master[2]);

              $scores += $float_score;
              if ( $float_score > 0 ) {
                $positive_count++;
                $posi_words .= " " . $text . $float_score;
              }
              else {
                $negative_count++;
                $nega_words .= " " . $text . $float_score;
              }

            }

          }

        }

        return array(
            'scores'         => $scores,
            'positive_count' => $positive_count,
            'negative_count' => $negative_count,
            'posi'           => $posi_words,
            'nega'           => $nega_words);
    }



}