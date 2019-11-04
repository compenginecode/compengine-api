<?php

namespace InfrastructureLayer\Query\CastAsInteger;

use Doctrine\ORM\Query\AST\AggregateExpression;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\GeneralCaseExpression;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class CastAsInteger
 * @package InfrastructureLayer\Query\CastAsInteger
 */
class CastAsInteger extends FunctionNode
{
    /** stringPrimary
     *
     *
     *
     * @var string | InputParameter | AggregateExpression | GeneralCaseExpression
     */
    public $stringPrimary;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'CAST(' . $this->stringPrimary->dispatch($sqlWalker) . ' AS UNSIGNED)';
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->stringPrimary = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
