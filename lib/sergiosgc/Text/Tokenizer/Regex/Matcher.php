<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
namespace sergiosgc;

interface Text_Tokenizer_Regex_Matcher
{
    /* setInput {{{ */
    /** 
     * Set the input to be matched
     *
     * @param string input
     */
    public function setInput(&$input);
    /* }}} */
    /* setSelectionCriteria {{{ */
    /** 
     * Set the selection criteria to be used when multiple regexes match when Text_Tokenizer_Regex_Matcher::match is called
     *
     * The regexp matcher is able to select from competing regexes using one of two criteria:
     *  - Text_Tokenizer_Regex::SELECTFIRST pick the regex whose index is lower
     *  - Text_Tokenizer_Regex::SELECTLONGEST pick the regex which consumes most characters (and the lowest indexed one, in case of a tie)
     *
     * @param int Selection criteria, one of {Text_Tokenizer_Regex::SELECTFIRST, Text_Tokenizer_Regex::SELECTLONGEST}
     */
    public function setSelectionCriteria($criteria);
    /* }}} */
    /* addRegex {{{ */
    /** 
     * Add a new regular expression to be matched. 
     *
     * The first regexp added, will be assigned index 0, the next index 1 and so on and so forth. 
     * These index values will be returned by Text_Tokenizer_Regex_Matcher::match
     *
     * @param string Regular expression
     */
    public function addRegex($regex);
    /* }}} */
    /*     match {{{ */
    /**
     * Match the next token against the global regex and return the submatch index. Returns boolean FALSE if input is EOF
     *
     * @return array Returns an array with ('index' => Submatch index for the regex subexpression that matched the next token, 'value' => Matched string)
     */
    public function match();
    /* }}} */
    /*     consume {{{ */
    /**
     * Consume (discard) characters from input
     *
     * @param int How many characters to discard
     */
    public function consume($n);
    /* }}} */
    /* {{{ getCurrentCharacter */
    /**
     * Get index of next character to be used when Text_Tokenizer_Regex_Matcher::match gets called
     *
     * @return int Character index (first character is index 0)
     */
    public function getCurrentCharacter();
    /* }}} */
}

?>
