<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
namespace sergiosgc;

class Text_Tokenizer_Regex_Matcher_Php implements Text_Tokenizer_Regex_Matcher
{
    /* cursor field {{{ */
    protected $_cursor = 0;
    /*     consume {{{ */
    /**
     * Consume (discard) characters from input
     *
     * @param int How many characters to discard
     */
    public function consume($n)
    {
        $this->_cursor += $n;
    }
    /* }}} */
    /* {{{ getCurrentCharacter */
    /**
     * Get index of next character to be used when Text_Tokenizer_Regex_Matcher::match gets called
     *
     * @return int Character index (first character is index 0)
     */
    public function getCurrentCharacter()
    {
        return $this->_cursor;
    }
    /* }}} */
    /* }}} */
    /* setInput {{{ */
    protected $_input = null;
    /** 
     * Set the input to be matched
     *
     * @param string input
     */
    public function setInput(&$input)
    {
        $this->_input = &$input;
        $this->_cursor = 0;
    }
    /* }}} */
    /* setSelectionCriteria {{{ */
    protected $_selectionCriteria = Text_Tokenizer_Regex::SELECTFIRST;
    /** 
     * Set the selection criteria to be used when multiple regexes match when Text_Tokenizer_Regex_Matcher::match is called
     *
     * The regexp matcher is able to select from competing regexes using one of two criteria:
     *  - Text_Tokenizer_Regex::SELECTFIRST pick the regex whose index is lower
     *  - Text_Tokenizer_Regex::SELECTLONGEST pick the regex which consumes most characters (and the lowest indexed one, in case of a tie)
     *
     * @param int Selection criteria, one of {Text_Tokenizer_Regex::SELECTFIRST, Text_Tokenizer_Regex::SELECTLONGEST}
     */
    public function setSelectionCriteria($criteria)
    {
        if ($criteria != Text_Tokenizer_Regex::SELECTFIRST && $criteria != Text_Tokenizer_Regex::SELECTLONGEST) throw new Text_Tokenizer_Exception('Invalid selection criteria');
        $this->_selectionCriteria = $criteria;
    }
    /* }}} */
    /* addRegex {{{ */
    protected $_regexes = array();
    /** 
     * Add a new regular expression to be matched. 
     *
     * The first regexp added, will be assigned index 0, the next index 1 and so on and so forth. 
     * These index values will be returned by Text_Tokenizer_Regex_Matcher::match
     *
     * @param string Regular expression
     */
    public function addRegex($regex)
    {
        $test = preg_match($regex, '');
        if ($test === FALSE) throw new Text_Tokenizer_Exception('Regex compilation failed');
        $this->_regexes[] = $regex;
    }
    /* }}} */
    /*     match {{{ */
    /**
     * Match the next token against the global regex and return the submatch index. Returns boolean FALSE if input is EOF
     *
     * @return array Returns an array with ('index' => Submatch index for the regex subexpression that matched the next token, 'value' => Matched string)
     */
    public function match()
    {
        $input = substr($this->_input, $this->_cursor);
        $bestMatch = null;
        for ($i=0; $i < count($this->_regexes); $i++) 
        {
            $matches = array();
            $match = false;
            $match = preg_match($this->_regexes[$i], $input, $matches);
            if ($match === false) throw new Text_Tokenizer_Exception('Error matching regex index ' . $i );
            if ($match == 1) 
            {
                if (is_null($bestMatch) || $bestMatch['length'] < strlen($matches[0])) {
                    $bestMatch = array(
                        'index' => $i,
                        'value' => $matches[0],
                        'length' => strlen($matches[0]));
                }
                if ($this->_selectionCriteria == Text_Tokenizer_Regex::SELECTFIRST) 
                {
                    unset($bestMatch['length']);
                    return $bestMatch;
                }
            }
        }
        if (is_null($bestMatch)) {
            return false;
        } else {
            unset($bestMatch['length']);
            return $bestMatch;
        }
    }
    /* }}} */
}

?>
