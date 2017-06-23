<?php
class CRM_SmsConversation_Match{

  function decipherPatternType($pattern){

    // The simplest match to identify is the anything pattern
    if($pattern == '/.*/'){
      return [
        'pattern_type'=>'anything',
        'pattern_raw'=>'/.*/',
        'human_friendly'=>'Anything'
      ];
    }

    // Define and check for existence of valid start and end delimeters
    if(substr($pattern, 0,1) == '/'){
      $startDelimeter = '/';
    }
    if(substr($pattern, -1) == '/'){
      $endDelimeter = '/';
    }elseif(substr($pattern, -2) == '/i'){
      $endDelimeter = '/i';
    }

    // If they are not present, report an error
    if(!isset($startDelimeter) || !isset($endDelimeter)){
      return [
        'pattern_type'=>'error',
        'pattern_raw'=>'error',
        'human_friendly'=>"Invalid pattern! '{$pattern}'"
      ];
    }

    // Create a $stripped variable which is the RE with the delimeters removed
    $stripped = substr($pattern, strlen($startDelimeter), -strlen($endDelimeter));
    // Test to see if this is a contains pattern
    if(preg_match("/^[A-Za-z0-9]+$/", $stripped)){
      return [
        'pattern_type' => 'contains',
        'pattern_raw' => $stripped,
        'human_friendly' => "Contains '{$stripped}'"
      ];
    }

    // Test to see if this is an exact pattern
    if( substr($stripped, 0, 1) == '^' && substr($stripped, -1) == '$'){
      $shaved = substr($stripped, 1, -1);
      if(preg_match("/^[A-Za-z0-9]+$/", $shaved)){
        return [
          'pattern_type' => 'exact',
          'pattern_raw' => $shaved,
          'human_friendly' => "Exact match '{$shaved}'"
        ];
      }
    }

    // If the pattern contains a bar, test to see if it is an list-exact or
    // list-contains
    if (strpos($pattern, '|') !== false) {

      // Strip the opening and closing delimiter and explode all the terms
      $terms = explode('|', $stripped);
      // If the first term starts with a ^, check all terms to see if they are
      // an exact match
      if(substr($terms[0], 0,1)=='^'){
        if(self::checkAllTermsAreExact($terms)){

          // If they are then this is list-exact. Prepare details to return

          // Strip the ^ and $, and surround with quotes
          foreach($terms as $k => $term){
            $terms[$k] = substr($term, 1, -1);
            $quotedTerms[$k] = "'".substr($term, 1, -1)."'";
          }

          // Return an exact match array
          return [
            'pattern_type' => 'list-exact',
            'pattern_raw' => implode(', ',$terms),
            'human_friendly' =>'Exact match: '.implode(' or ',$quotedTerms)
          ];
        }
      // Else the first term does not start with a ^
      }else{

        // Check all terms to ensure that they don't start with a ^ or end with
        // a $
        if(self::checkAllTermsAreContains($terms)){

          // Strip the ^ and $, and surround with quotes
          foreach($terms as $k => $term){
            $quotedTerms[$k] = "'{$term}'";
          }

          return [
            'pattern_type' => 'list-contains',
            'pattern_raw' => implode(', ',$terms),
            'human_friendly' =>'Contains: '.implode(' or ',$quotedTerms)
          ];
        }
      }
    }


    //if there is a bar in it, it must be either list contains or list exact
    // Work out wether this is one of the built in types


    // If you haven't been able to categorise it as a built in type,
    // check that it starts and ends with a / and if so, call is a regexp
    return [
      'pattern_type' => 'regexp',
      'pattern_raw' => $pattern,
      'human_friendly'=>"Advanced mode (regular expression) '{$pattern}'"
    ];
  }
  function checkAllTermsAreExact($terms){

    foreach($terms as $term){
      if(substr($term, 0, 1) != '^'){
        return false;
      }
      if(substr($term, -1) != '$'){
        return false;
      }
      if(!preg_match("/^[A-Za-z0-9]+$/", substr($term, 1, -1))){
        return false;
      }
    }
    return true;
  }
  function checkAllTermsAreContains($terms){

    // TODO Check the inside is just [a-z]
    foreach($terms as $term){
      if(!preg_match("/^[A-Za-z0-9]+$/", $term)){
        return false;
      }
    }
    return true;
  }
}
