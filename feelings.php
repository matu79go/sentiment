<?php

class Feelings{

    private $path = './sentiment.txt'; // dictionary
    private $mecab;
    private $permit_only_adjective = false;

    public function __construct($p_permit_only_adjective) {

      // Load mecab module
      $options = array('-d', IPA_DICTIONARY_PATH);
      $this->mecab = new \MeCab\Tagger($options);
      $this->permit_only_adjective = $p_permit_only_adjective;

    }

    public function __destruct() {
        $this->mecab = null;
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

        if ($node = $this->mecab->parseToNode($p_str)){

          while($node){

            if ( $node->getSurface() ) {
              // insert word and type
              $text_arr[] = array('type' => explode(',', $node->getFeature())[0], 'word' => $node->getSurface());
            }

            // next
            $node = $node->getNext();

          }


          // check dictionary
          $hit_words = array();

          foreach ( $text_arr as $rows ) {

            foreach ( file( $this->path ) as $line ) {

              $master = explode(':', $line);
              /*
               * [0] : include kanji
               * [1] : only hiragana
               * [2] : tyope
               * [3] : score
               *
               */

              // if permitting only adjective or not.
              if ( $this->permit_only_adjective == 1 ) {
                if ( $master[2] !== "形容詞" ) {
                  continue;
                }
              }

              // check if words are in dictionary.
              if ( $rows['type'] === $master[2] && ($rows['word'] === $master[0] || $rows['word'] === $master[1]) ) {

                $float_score = (float)trim($master[3]);

                $scores += $float_score;
                if ( $float_score > 0 ) {
                  $positive_count++;
                  $posi_words .= " " . $rows['word'] . $float_score;
                }
                else {
                  $negative_count++;
                  $nega_words .= " " . $rows['word'] . $float_score;
                }

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