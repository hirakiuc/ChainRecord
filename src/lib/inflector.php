<?php

/*
Copyright (c) Kazuhiro Iizuka All rights reserved.
Code licensed under the BSD License:
http://static.aimy.jp/license.txt
*/

/**
 * inflector.php
 * @author Kazuhiro Iizuka <simplate at gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @package Simframe
 * @version $Id: inflector.php 342 2008-09-04 07:26:29Z kazuhiro $
 */

/**
 * 文字列を単数形、複数形、クラス名、テーブル名などに変更するクラス
 * @author Kazuhiro Iizuka <simplate at gmail.com>
 * @access public
 * @package Simframe
 */
class SInflector
{
  static $plurals = null;
  static $singulars = null;
  static $uncountables = array();

  /**
   * デフォルトルールを設定する初期化関数
   * @access public
   */
  static public function init(){
    if( !self::$plurals ){
      self::plural("/$/",'s');
      self::plural("/s$/i",'s');
      self::plural("/(ax|test)is$/i",'\1es');
      self::plural("/(octop|vir)us$/i",'\1i');
      self::plural("/(alias|status)$/i",'\1es');
      self::plural("/(bu)s$/i",'\1ses');
      self::plural("/(buffal|tomat)o$/i",'\1oes');
      self::plural("/([ti])um$/i",'\1a');
      self::plural("/sis$/i",'ses');
      self::plural("/(?:([^f])fe|([lr])f)$/i",'\1\2ves');
      self::plural("/(hive)$/i",'\1s');
      self::plural("/([^aeiouy]|qu)y$/i",'\1ies');
      self::plural("/(x|ch|ss|sh)$/i",'\1es');
      self::plural("/(matr|vert|ind)ix|ex$/i",'\1ices');
      self::plural("/([m|l])ouse$/i",'\1ice');
      self::plural("/^(ox)$/i",'\1en');
      self::plural("/(quiz)$/i",'\1zes');

      self::singular("/s$/i",'');
      self::singular("/(n)ews$/i",'\1ews');
      self::singular("/([ti])a$/i",'\1um');
      self::singular("/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i",'\1\2sis');
      self::singular("/(^analy)ses$/i",'\1sis');
      self::singular("/([^f])ves$/i",'\1fe');
      self::singular("/(hive)s$/i",'\1');
      self::singular("/(tive)s$/i",'\1');
      self::singular("/([lr])ves$/i",'\1f');
      self::singular("/([^aeiouy]|qu)ies$/i",'\1y');
      self::singular("/(s)eries$/i",'\1\2eries');
      self::singular("/(m)ovies$/i",'\1\2ovie');
      self::singular("/(x|ch|ss|sh)es$/i",'\1');
      self::singular("/([m|l])ice$/i",'\1ouse');
      self::singular("/(bus)es$/i",'\1');
      self::singular("/(o)es$/i",'\1');
      self::singular("/(shoe)s$/i",'\1');
      self::singular("/(cris|ax|test)es$/i",'\1is');
      self::singular("/([octop|vir])i$/i",'\1us');
      self::singular("/(alias|status)es$/i",'\1');
      self::singular("/^(ox)en/i",'\1');
      self::singular("/(vert|ind)ices$/i",'\1ex');
      self::singular("/(matr)ices$/i",'\1ix');
      self::singular("/(quiz)zes$/i",'\1');

      self::irregular("move","moves");
      self::irregular("sex","sexes");
      self::irregular("child","children");
      self::irregular("person","people");
      self::irregular("man","men");

      self::uncountable(array("equipment","information","rice","money","species","series","fish","sheep"));
    }
  }

  /**
   *



Rails:
Specifies a new pluralization rule and its replacement. The rule can either be a string or a regular expression.
The replacement should always be a string that may include references to the matched data from the rule.
   */
  static public function plural($rule,$replacement)
  {
    self::$plurals[] = array($rule,$replacement);
  }

  /**
   *
   */
/*
Rails:
Specifies a new singularization rule and its replacement. The rule can either be a string or a regular expression.
The replacement should always be a string that may include references to the matched data from the rule.
*/
  static public function singular($rule,$replacement)
  {
    self::$singulars[] = array($rule,$replacement);
  }

  /**
   *
   * Exampls:
   * irregular 'person', 'people'
   * irregular 'man', 'men'
   */
/*
Rails:
Specifies a new irregular that applies to both pluralization and singularization at the same time. This can only be used
for strings, not regular expressions. You simply pass the irregular in singular and plural form.
*/
  static public function irregular($singular,$plural){
    $rule = "/({$singular[0]})".substr($singular,1)."$/i";
    self::$plurals[] = array($rule,'\1'.substr($plural,1));
    $rule = "/({$plural[0]})".substr($plural,1)."$/i";
    self::$singulars[] = array($rule,'\1'.substr($singular,1));
  }

  /**
   *
   * Examples:
   * uncountable("money");
   * uncountable(array("money","information"));
   */
/*
Rails:
Add uncountable words that shouldn't be attempted inflected.
*/

  static public function uncountable($word){
    if( is_array($word) ){
      foreach($word as $val){
	self::$uncountables[$val] = $val;
      }
    }else if( is_string($word) ){
      self::$uncountables[$word] = $word;
    }
  }
  /**
   * Examples:
   * clear();
   * clear("plurals");
   */
/*
Rails:
Clears the loaded inflections within a given scope (default is :all). Give the scope as a symbol of the inflection type,
the options are: :plurals, :singulars, :uncountables
*/
  static public function clear($scope="all")
  {
    if( $scope=='all' ){
      self::$plurals = self::$singulars = self::$uncountables = array();
    }else{
      self::${$scope} = array();
    }
  }

  /**
   *
   * Examples:
   * pluralize("post") => "posts"
   * pluralize("octopus") => "octopi"
   * pluralize("sheep") => "sheep"
   * pluralize("words") => "words"
   * pluralize("the blue mailman") => "the blue mailmen"
   * pluralize("CamelOctopus") => "CamelOctopi"
   */
/*
Rails:
Returns the plural form of the word in the string.
*/
  static public function pluralize($word)
  {
    self::init();
//print_r(self::$plurals);

    if( isset(self::$uncountables[$word]) ){
    }else{
      for($i=count(self::$plurals)-1;$i>=0;$i--){
  //    foreach( self::$plurals as $rule=>$replacement )
	list($rule,$replacement) = self::$plurals[$i];
	if( preg_match($rule,$word) ){
	  return preg_replace($rule,$replacement,$word);
	}
      }
    }
    return $word;
  }

  /**
   *
   * Examples:
   * singularize("posts") => "post"
   * singularize("octopi") => "octopus"
   * singularize("sheep") => "sheep"
   * singularize("words") => "word"
   * singularize("the blue mailmen") => "the blue mailman"
   * singularize("CamelOctopi") => "CamelOctopus"
   */
/*
Rails:
The reverse of pluralize, returns the singular form of a word in a string.
*/
  static public function singularize($word)
  {
    self::init();

    if( isset(self::$uncountables[$word]) ){
    }else{
      for($i=count(self::$singulars)-1;$i>=0;$i--){
	list($rule,$replacement) = self::$singulars[$i];
	if( preg_match($rule,$word) ){
	  return preg_replace($rule,$replacement,$word);
	}
      }
    }
    return $word;
  }

  /**
   *
   * Examples:
   * camelize("active_record") => "ActiveRecord"
   * camelize("active_record",false) => "activeRecord"
   * camelize("active_record/errors") => "ActiveRecord_Errors"
   * camelize("active_record/errors",false) => "activeRecord_Errors"
   */
/*
Rails:
By default, camelize converts strings to UpperCamelCase. If the argument to camelize
is set to ":lower" then camelize produces lowerCamelCase.
*/
  static public function camelize($lower_case_and_underscored_word,$first_letter_in_uppercase=true)
  {
    $words = explode("/",str_replace("_"," ",$lower_case_and_underscored_word));
    $class_words = array();
    foreach($words as $word){
      $class_words[] = ucwords($word);
    }
    $camel_word = str_replace(" ","",implode("_",$class_words));
//    $camel_word = str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));

    if( !$first_letter_in_uppercase ){
      $camel_word[0] = strtolower($camel_word[0]);
    }
    return $camel_word;
  }

  /**
   *
   * Examples:
   * titleize("man from the boondocks") => "Man From The Boondocks"
   * titleize("x-men: the last stand") => "X Men: The Last Stand"
   */
/*
Rails:
Capitalizes all the words and replaces some characters in the string to create
a nicer looking title. Titleize is meant for creating pretty output. It is not used in the Rails internals.
*/
  // TODO:still buggy....
  static public function titleize($word)
  {
    // Rails:
    // humanize(underscore(word)).gsub(/\b([a-z])/) { $1.capitalize }
    $word = str_replace("_"," ",$word);
    return ucwords(SInflector::humanize(SInflector::underscore($word)));
//    return preg_replace("/\b(a-z)/","strtoupper(\\1)",SInflector::humanize(SInflector::underscore($word)));
  }

  /**
   *
   * Examples:
   * underscore("ActiveRecord") => "active_record"
   * underscore("ActiveRecord_Errors") => "active_record/errors"
   */
/*
Rails:
The reverse of +camelize+. Makes an underscored form from the expression in the string.
*/
  static public function underscore($camel_cased_word){
    $camel_cased_word = str_replace("_","/",$camel_cased_word);
    $camel_cased_word = preg_replace('/([A-Z]+)([A-Z][a-z])/','\1_\2', $camel_cased_word);
    $camel_cased_word = preg_replace('/([a-z\d])([A-Z])/','\1_\2', $camel_cased_word);
    return strtolower($camel_cased_word);
  }

  /**
   *
   * Examples:
   * dasherize("puni_puni") => "puni-puni"
   */
/*
Rails:
Replaces underscores with dashes in the string.
*/
  static public function dasherize($underscored_word)
  {
    return str_replace("_","-",$underscored_word);
  }

  /**
   *
   * Examples:
   * humanize("employee_salary") => "Employee salary"
   * humanize("author_id") => "Author"
   */
/*
Rails:
Capitalizes the first word and turns underscores into spaces and strips _id.
Like titleize, this is meant for creating pretty output.
*/
  static public function humanize($lower_case_and_underscored_word)
  {
    $lower_case_and_underscored_word = preg_replace('/_id$/','',$lower_case_and_underscored_word);
//    $lower_case_and_underscored_word = ucwords(str_replace("_", " ", $lower_case_and_underscored_word));
    $lower_case_and_underscored_word = str_replace("_", " ", $lower_case_and_underscored_word);
    $lower_case_and_underscored_word[0] = strtoupper($lower_case_and_underscored_word[0]);
    return $lower_case_and_underscored_word;
  }

  /**
   *
   * Examples:
   * tableize("RawScaledScorer") => "raw_scaled_scorers"
   * tableize("egg_and_ham") => "egg_and_hams" // TODO:buggy..
   * tableize("fancyCategory") => "fancy_categories"
   */
/*
Rails:
Create the name of a table like Rails does for models to table names. This method
uses the pluralize method on the last word in the string.
*/
  static public function tableize($class_name){
    return SInflector::pluralize(SInflector::underscore($class_name));
  }

  /**
   *
   * Examples:
   * classify("egg_and_hams") => "EggAndHam"
   * classify("post") => "Post"
   */
/*
Rails:
Create a class name from a table name like Rails does for table names to models.
Note that this returns a string and not a Class. (To convert to an actual class
follow classify with constantize.)
 */
  static public function classify($table_name){
    return SInflector::camelize(SInflector::singularize($table_name));
  }

  /**
   *
   * Examples:
   * foreign_key("Message") => "message_id"
   * foreign_key("Message",false) => "messageid"
   */
/*
Rails:
Creates a foreign key name from a class name.
+separate_class_name_and_id_with_underscore+ sets whether
the method should put '_' between the name and 'id'.
 */
  static public function foreign_key($class_name,$separate_class_name_and_id_with_underscore=true){
    return SInflector::underscore($class_name).($separate_class_name_and_id_with_underscore?"_id":"id");
  }

  /**
   *
   * Examples:
   * ordinalize(1) => "1st"
   * ordinalize(2) => "2nd"
   * ordinalize(1002) => "1002nd"
   * ordinalize(1003) => "1003rd"
   */
/*
Rails:
Ordinalize turns a number into an ordinal string used to denote the
position in an ordered sequence such as 1st, 2nd, 3rd, 4th.
 */
  static public function ordinalize($number)
  {
    $temp = $number%100;
    if( $temp>=11 && $temp<=13 ){
      return $number."th";
    }else{
      switch($number%10){
      case 1: return $number."st";
      case 2: return $number."nd";
      case 3: return $number."rd";
      default: return $number."th";
      }
    }
  }

}

?>
