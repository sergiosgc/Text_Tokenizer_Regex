--TEST--
Default regexp tokenizer, simple regexp, match first
--FILE--
<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/lexers/CharacterLexer.php');
$lexer = new CharacterLexer(file_get_contents(dirname(__FILE__) . '/inputs/abcd.txt'));
$lexer->setSelectionCriteria(\sergiosgc\Text_Tokenizer_Regex::SELECTFIRST);
while ($token = $lexer->getNextToken()) {
    printf("Lexer output token {%s, '%s'}\n", $token->getId(), addcslashes($token->getValue(), "\0..\37!@\177..\377"));
}
?>
--EXPECT--
Lexer output token {1, 'a'}
Lexer output token {1, 'b'}
Lexer output token {1, 'c'}
Lexer output token {1, 'd'}
Lexer output token {1, 'e'}
Lexer output token {1, 'f'}
Lexer output token {1, 'g'}
Lexer output token {1, 'h'}
Lexer output token {1, 'i'}
Lexer output token {1, 'j'}
Lexer output token {1, 'k'}
Lexer output token {1, 'l'}
Lexer output token {1, 'm'}
Lexer output token {1, 'n'}
Lexer output token {1, 'o'}
Lexer output token {1, 'p'}
Lexer output token {1, 'q'}
Lexer output token {1, 'r'}
Lexer output token {1, 's'}
Lexer output token {1, 't'}
Lexer output token {1, 'u'}
Lexer output token {1, 'v'}
Lexer output token {1, 'w'}
Lexer output token {1, 'x'}
Lexer output token {1, 'y'}
Lexer output token {1, 'z'}
Lexer output token {1, 'A'}
Lexer output token {1, 'B'}
Lexer output token {1, 'C'}
Lexer output token {1, 'D'}
Lexer output token {1, 'E'}
Lexer output token {1, 'F'}
Lexer output token {1, 'G'}
Lexer output token {1, 'H'}
Lexer output token {1, 'I'}
Lexer output token {1, 'J'}
Lexer output token {1, 'K'}
Lexer output token {1, 'L'}
Lexer output token {1, 'M'}
Lexer output token {1, 'N'}
Lexer output token {1, 'O'}
Lexer output token {1, 'P'}
Lexer output token {1, 'Q'}
Lexer output token {1, 'R'}
Lexer output token {1, 'S'}
Lexer output token {1, 'T'}
Lexer output token {1, 'U'}
Lexer output token {1, 'V'}
Lexer output token {1, 'W'}
Lexer output token {1, 'X'}
Lexer output token {1, 'Y'}
Lexer output token {1, 'Z'}
Lexer output token {1, '0'}
Lexer output token {1, '1'}
Lexer output token {1, '2'}
Lexer output token {1, '3'}
Lexer output token {1, '4'}
Lexer output token {1, '5'}
Lexer output token {1, '6'}
Lexer output token {1, '7'}
Lexer output token {1, '8'}
Lexer output token {1, '9'}
