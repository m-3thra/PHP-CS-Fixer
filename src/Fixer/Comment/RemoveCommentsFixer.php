<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author m-3thra <305653057+m-3thra@users.noreply.github.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class RemoveCommentsFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Removes comments that are preceded by `;` (semicolon).',
            [
                new CodeSample(
                    "<?php echo 123; /* Comment */\n",
                ),
            ],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }

            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];

            if (!$prevToken->equals(';')) {
                continue;
            }

            for ($i = $prevTokenIndex + 1; $i < $index; ++$i) {
                if (false !== strpos($tokens[$i]->getContent(), "\n")) {
                    continue 2;
                }
            }

            $tokens->clearAt($index);
        }
    }
}
