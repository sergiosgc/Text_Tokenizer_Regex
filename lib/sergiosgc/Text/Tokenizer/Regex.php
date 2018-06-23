<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
namespace sergiosgc;

class Text_Tokenizer_Regex implements \sergiosgc\Text_Tokenizer
{
    const SELECTFIRST = 1;
    const SELECTLONGEST = 2;

    /*     Constructor {{{ */
    /**
     * Constructor
     *
     */
    public function __construct($matcher = null)
    {
        if (!is_null($matcher)) $this->setMatcher($matcher);
    }
    /* }}} */
    /* EOFToken field {{{ */
    protected $_eof = null;
    public function setEOFToken($id) {
        $this->_eof = $id;
    }
    /* }}} */
    /* regex field {{{ */
    /** Regex array
     */
    protected $_regex = array();
    /*     addRegex {{{ */
    /**
     *     addRegex. Adds a new regular expression to be matched
     *
     * Accepts the regular expression, and optionally:
     *  - A token id to be returned when this regex matches (defaults to a sequential id)
     *  - A callback function to process the match and return the token data (defaults to returning the matched text)
     * The callback function itself can be one of:
     *  - A valid function name as a string
     *  - A valid non static object function name as an array (object, string)
     *  - A static class function name as an array (string, string)
     *  - An integer, referencing a submatch to be returned as the token data
     *
     * @param string Regular expression
     * @param mixed Token id
     * @param string|int|array Token match processing callback
     */
    public function addRegex($regex, $tokenid = null, $callback = null)
    {
        if (preg_match($regex, '') === FALSE) throw new Exception(sprintf('Invalid regular expression: \'%s\'', $regex));
        if (is_null($tokenid)) $tokenid = count($this->_regex);
        if (!is_null($callback) && ((int) $callback) == $callback) $callback = (int) $callback;
        if (!is_null($callback) && !is_int($callback) && !is_string($callback) && !is_array($callback)) throw new Exception('Callback must be either an integer, or a function representing string/array');
        if (is_string($callback) && !function_exists($callback)) throw new Exception('Function referenced by callback does not exist');
        unset($this->_compoundregex);
        $this->getMatcher()->addRegex($regex);
        $this->_regex[] = array(
         'regex' => $regex,
         'token' => $tokenid,
         'callback' => $callback);
    }
    /* }}} */
    /* }}} */
    /* input field {{{ */
    /** Text to be lexed
     */
    /**
     * input setter
     *
     * @param string input
     */
    public function setInput($value)
    {
        $this->getMatcher()->setInput($value);
        $this->_cursor = 0;
    }
    /* }}} */
    /* matcher field {{{ */
    /** Regex matcher object
     */
    protected $_matcher;
    /**
     * matcher Getter
     *
     * @return mixed _matcher current value
     */
    protected function &getMatcher()
    {
        if (!isset($this->_matcher)) {
            if (extension_loaded('Text_Tokenizer_Regex_Matcher_Ext')) {
                $this->_matcher = new Text_Tokenizer_Regex_Matcher_Ext();
            } else {
                $this->_matcher = new Text_Tokenizer_Regex_Matcher_Php();
            }
            $this->_matcher->setSelectionCriteria(self::SELECTLONGEST);
            $this->_matcher->setInput($this->_input);
            foreach ($this->_regex as $key => $item) {
                $this->_matcher->addRegex($item['regex']);
            }
        }
        return $this->_matcher;
    }
    /**
     * matcher Setter
     *
     * @param Text_Tokenizer_Regex_Matcher New value
     */
    public function setMatcher($matcher)
    {
        $this->_matcher = $matcher;
    }
    /* }}} */
    /* setSelectionCriteria {{{ */
    /**
     * Change the selection criteria for the regex matcher
     *
     * @param int New selection criteria. One of (Text_Tokenizer_Regex::SELECTFIRST, Text_Tokenizer_Regex::SELECTLONGEST)
     */
    public function setSelectionCriteria($selectionCriteria)
    {
        $this->getMatcher()->setSelectionCriteria($selectionCriteria);
    }
    /* }}} */

    /* stripRegex {{{ */
    protected static function stripRegex($regex, &$submatchcount)
    {
        $submatchcount = 0;
        $delimiter = $regex[0];
        $regex = preg_replace('/^./', '', $regex);
        $regex = preg_replace('/.$/', '', $regex);

        $regex = strtr($regex, array('\\\\' => '\\\\ ')); // This and the reverse are needed so that we do remove unescaped parenthesis preceded by escaped slashes
        if ($delimiter != '/') {
            $regex = strtr($regex, array('\\' . $delimiter => $delimiter)); // No more need to escape the delimiter
            $regex = strtr($regex, array('/' => '\\/')); // But escape slash instead
        }
        $matches = array();
        if (preg_match_all('/[^\\\\]\\(\\(*/', $regex, $matches)) {
            foreach ($matches[0] as $p) $submatchcount += strlen($p) - 1;
        }
        if (preg_match('/^\\(\\(*/', $regex, $matches)) {
            $submatchcount += strlen($matches[0]);
        }
        $regex = strtr($regex, array('\\\\ ' => '\\\\'));
        return $regex;
    }
    /* }}} */
    /*     getNextToken {{{ */
    /**
     *     Get the next lexer token
     *
     * @return Text_Tokenizer_Token|false The next matched token, or false if EOF
     */
    public function getNextToken()
    {
        $token = $this->getMatcher()->match();
        if ($token === false) return false;
        $regex =& $this->_regex[$token['index']];
        if (is_int($regex['callback'])) { // Extract submatch as token value
            $matches = array();
            if (!preg_match($regex['regex'], $match, $matches)) throw new Exception('Unexpected regex mismatch. Compound matched but singular regex did not.');
            if (!array_key_exists($regex['callback'] + 1, $matches)) throw new Exception(sprintf('Callback required submatch %d but there are only %d submatches. Matching regex \'%s\' on string \'%s\'.', $regex['callback'], count($matches) - 1, $regex['regex'], $match));
            $token['value'] = $matches[$regex['callback'] + 1];
        } elseif (!is_null($regex['callback'])) { // Use callback function to calc token value
            $token['value'] = call_user_func($regex['callback'], $token['value'], $regex['regex']);
        }
        $token['token'] = $regex['token'];
        unset($token['index']);
        $this->getMatcher()->consume(strlen($token['value']));
        $this->_cursor += strlen($token['value']);
//        print("Read token '" . $token['token'] . "'\n");
        return new Text_Tokenizer_Token($token['token'], $token['value']);
    }
    /* }}} */
    /*     getLine {{{ */
    /**
     *     getLine. Get current line
     *
     * @return int Current line
     */
    public function getLine()
    {
        $matches = array();
        preg_match_all('/\\n/', substr($this->_input, 0, $this->_cursor), $matches);
        return count($matches[0]);
    }
    /* }}} */
}
?>
