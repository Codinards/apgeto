<?php

namespace App\Twig;

use App\Tools\Routes\Annotations\RouteActionManager;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * @author Jean Fils de Ntouoka 2 <nguimjeaner@gmail.com>
 * @version 1.0.0
 */
class AuthTokenParser extends AbstractTokenParser
{
    public function __construct(private RouteActionManager $annotationManager)
    {
    }
    public function parse(Token $token)
    {
        $line = $token->getLine();
        $stream = $this->parser->getStream();
        $key = $this->parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideAuthFork']);
        if ('else' == $stream->next()->getValue()) {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse([$this, 'decideAuthEnd'], true);
        } else {
            $else = new Node();
        }
        $stream->expect(Token::BLOCK_END_TYPE);
        return new AuthNode($key, $body, $else, $line, $this->getTag());
    }

    public function decideAuthEnd(Token $token): bool
    {
        return $token->test('endauth');
    }

    public function decideAuthFork(Token $token): bool
    {
        return $token->test(['else', 'endauth']);
    }

    public function getTag()
    {
        return 'auth';
    }
}
