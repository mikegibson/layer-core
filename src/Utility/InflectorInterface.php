<?php

namespace Layer\Utility;

interface InflectorInterface {

	/**
	 * Return $word in plural form.
	 *
	 * @param string $word Word in singular
	 * @return string Word in plural
	 */
	public function pluralize($word);

	/**
	 * Return $word in singular form.
	 *
	 * @param string $word Word in plural
	 * @return string Word in singular
	 */
	public function singularize($word);

	/**
	 * Returns the given lower_case_and_underscored_word as a CamelCased word.
	 *
	 * @param string $lowerCaseAndUnderscoredWord Word to camelize
	 * @return string Camelized word. LikeThis.
	 */
	public function camelize($lowerCaseAndUnderscoredWord);

	/**
	 * Returns the given camelCasedWord as an underscored_word.
	 *
	 * @param string $camelCasedWord Camel-cased word to be "underscorized"
	 * @return string Underscore-syntaxed version of the $camelCasedWord
	 */
	public function underscore($camelCasedWord);

	/**
	 * Returns the given underscored_word_group as a Human Readable Word Group.
	 * (Underscores are replaced by spaces and capitalized following words.)
	 *
	 * @param string $lowerCaseAndUnderscoredWord String to be made more readable
	 * @return string Human-readable string
	 */
	public function humanize($lowerCaseAndUnderscoredWord);

	/**
	 * Returns corresponding table name for given entity $className. ("people" for the entity class "Person").
	 *
	 * @param string $className Name of class to get database table name for
	 * @return string Name of the database table for given class
	 */
	public function tableize($className);

	/**
	 * Returns entity class name ("Person" for the database table "people".) for given database table.
	 *
	 * @param string $tableName Name of database table to get class name for
	 * @return string Class name
	 */
	public function classify($tableName);

	/**
	 * Returns camelBacked version of an underscored string.
	 *
	 * @param string $string
	 * @return string in variable form
	 */
	public function variable($string);

	/**
	 * Returns a string with all spaces converted to underscores (by default), accented
	 * characters converted to non-accented characters, and non word characters removed.
	 *
	 * @param string $string the string you want to slug
	 * @param string $replacement will replace keys in map
	 * @return string
	 */
	public function slug($string, $replacement = '_');

}