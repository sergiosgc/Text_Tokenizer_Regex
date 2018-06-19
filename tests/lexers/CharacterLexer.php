<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

class CharacterLexer extends \sergiosgc\Text_Tokenizer_Regex
{
    /*     __construct {{{ */
    /**
     * Constructor
     *
     * @param string CSS Code
     */
    public function __construct($input = null, $matcher = null)
    {
        parent::__construct($matcher);
        if (!is_null($input)) $this->setInput($input);

        //$this->addRegex('@\\{@', 
        $this->addRegex('@.@', 
                        1);
        $this->addRegex('@..@', 
                        2);
        $this->addRegex('@...@', 
                        2);
    }
    /* }}} */
}
?>

