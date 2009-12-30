--TEST--
Extension-based regexp tokenizer, simple regexp, match first
--FILE--
<?php
ini_set('include_path', sprintf('%s:%s:%s', 
    realpath(dirname(__FILE__) . '/../'),
    realpath(dirname(__FILE__) . '/../../Text_Tokenizer'),
    ini_get('include_path')));
require_once(dirname(__FILE__) . '/lexers/CharacterLexer.php');
if (!extension_loaded('Text_Tokenizer_Regex_Matcher_Ext')) dl('Text_Tokenizer_Regex_Matcher_Ext.so');
$lexer = new CharacterLexer(file_get_contents(dirname(__FILE__) . '/inputs/abcd.txt'), new Text_Tokenizer_Regex_Matcher_Ext());
$lexer->setSelectionCriteria(Text_Tokenizer_Regex::SELECTLONGEST);
while ($token = $lexer->getNextToken()) {
    printf("Lexer output token {%s, '%s'}\n", $token->getId(), addcslashes($token->getValue(), "\0..\37!@\177..\377"));
}
?>
--EXPECT--
Lexer output token {2, 'abc'}
Lexer output token {2, 'def'}
Lexer output token {2, 'ghi'}
Lexer output token {2, 'jkl'}
Lexer output token {2, 'mno'}
Lexer output token {2, 'pqr'}
Lexer output token {2, 'stu'}
Lexer output token {2, 'vwx'}
Lexer output token {2, 'yzA'}
Lexer output token {2, 'BCD'}
Lexer output token {2, 'EFG'}
Lexer output token {2, 'HIJ'}
Lexer output token {2, 'KLM'}
Lexer output token {2, 'NOP'}
Lexer output token {2, 'QRS'}
Lexer output token {2, 'TUV'}
Lexer output token {2, 'WXY'}
Lexer output token {2, 'Z01'}
Lexer output token {2, '234'}
Lexer output token {2, '567'}
Lexer output token {2, '89'}
