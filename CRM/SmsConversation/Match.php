<?php
class CRM_SmsConversation_Match{

  function decipherPatternType($pattern){

    // If it doesn't start and end with a /, treat it as invalid
    if(substr($pattern, 0,1) != '/' || substr($pattern, -1) != '/'){
      return [
        'pattern_type'=>'error',
        'pattern_raw'=>'error',
        'human_friendly'=>"Invalid pattern! '{$pattern}'"
      ];
    }

    // Remove the delimeters
    $stripped = substr($pattern, 1, -1);

    // The simplest match to identify is the anything pattern
    if($pattern == '/.*/'){
      return [
        'pattern_type'=>'anything',
        'pattern_raw'=>'/.*/',
        'human_friendly'=>'Anything'
      ];
    }

    // Followed by the contains pattern
    if( substr($stripped, 0, 1) == '^' && substr($stripped, -1) == '$'){
      $shaved = substr($stripped, 1, -1);
      if(preg_match("/^[a-z0-9]+$/", $shaved)){
        return [
          'pattern_type' => 'exact',
          'pattern_raw' => $shaved,
          'human_friendly' => "Exact match '{$shaved}'"
        ];
      }
    }

    //
    if(preg_match("/^[a-z0-9]+$/", $stripped)){
      return [
        'pattern_type' => 'contains',
        'pattern_raw' => $stripped,
        'human_friendly' => "Contains '{$stripped}'"
      ];
    }

    if(preg_match("/^[a-z0-9]+$/", $stripped)){
      return [
        'pattern_type' => 'contains',
        'pattern_raw' => $stripped,
        'human_friendly' => "Contains '{$stripped}'"
      ];
    }


    // If it contains a bar, it might be list-exact or list-contains
    if (strpos($pattern, '|') !== false) {

      // Strip the opening and closing delimiter and explode all the terms
      $terms = explode('|', $stripped);
      // If the first term starts with a ^, check all terms to see if they are
      // an exact match
      if(substr($terms[0], 0,1)=='^'){
        if(self::checkAllTermsAreExact($terms)){

          // If they are then this is list-exact. Prepare details to return

          // Strip the ^ and $, and surround with quotes
          foreach($terms as &$term){
            $term = "'".substr($term, 1, -1)."'";
          }

          // Return an exact match array
          return [
            'pattern_type' => 'list-exact',
            'pattern_raw' => implode(', ',$terms),
            'human_friendly' =>'Exact match: '.implode(' or ',$terms)
          ];
        }
      // Else the first term does not start with a ^
      }else{

        // Check all terms to ensure that they don't start with a ^ or end with
        // a $
        if(self::checkAllTermsAreContains($terms)){

          // Strip the ^ and $, and surround with quotes
          foreach($terms as &$term){
            $term = "'{$term}'";
          }

          return [
            'pattern_type' => 'list-contains',
            'pattern_raw' => implode(', ',$terms),
            'human_friendly' =>'Contains: '.implode(' or ',$terms)
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
      'human_friendly'=>"Regular expression '{$pattern}'"
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
      if(!preg_match("/^[a-z0-9]+$/", substr($term, 1, -1))){
        return false;
      }
    }
    return true;
  }
  function checkAllTermsAreContains($terms){

    // TODO Check the inside is just [a-z]
    foreach($terms as $term){
      if(!preg_match("/^[a-z0-9]+$/", $term)){
        return false;
      }
    }
    return true;
  }
}
