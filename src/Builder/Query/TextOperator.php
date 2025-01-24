<?php

/**
 * THIS FILE IS AUTO-GENERATED. ANY CHANGES WILL BE LOST!
 */

declare(strict_types=1);

namespace MongoDB\Builder\Query;

use MongoDB\Builder\Type\Encode;
use MongoDB\Builder\Type\OperatorInterface;
use MongoDB\Builder\Type\Optional;
use MongoDB\Builder\Type\QueryInterface;

/**
 * Performs text search.
 *
 * @see https://www.mongodb.com/docs/manual/reference/operator/query/text/
 * @internal
 */
final class TextOperator implements QueryInterface, OperatorInterface
{
    public const ENCODE = Encode::Object;
    public const NAME = '$text';

    public const PROPERTIES = [
        'search' => '$search',
        'language' => '$language',
        'caseSensitive' => '$caseSensitive',
        'diacriticSensitive' => '$diacriticSensitive',
    ];

    /** @var string $search A string of terms that MongoDB parses and uses to query the text index. MongoDB performs a logical OR search of the terms unless specified as a phrase. */
    public readonly string $search;

    /**
     * @var Optional|string $language The language that determines the list of stop words for the search and the rules for the stemmer and tokenizer. If not specified, the search uses the default language of the index.
     * If you specify a default_language value of none, then the text index parses through each word in the field, including stop words, and ignores suffix stemming.
     */
    public readonly Optional|string $language;

    /** @var Optional|bool $caseSensitive A boolean flag to enable or disable case sensitive search. Defaults to false; i.e. the search defers to the case insensitivity of the text index. */
    public readonly Optional|bool $caseSensitive;

    /**
     * @var Optional|bool $diacriticSensitive A boolean flag to enable or disable diacritic sensitive search against version 3 text indexes. Defaults to false; i.e. the search defers to the diacritic insensitivity of the text index.
     * Text searches against earlier versions of the text index are inherently diacritic sensitive and cannot be diacritic insensitive. As such, the $diacriticSensitive option has no effect with earlier versions of the text index.
     */
    public readonly Optional|bool $diacriticSensitive;

    /**
     * @param string $search A string of terms that MongoDB parses and uses to query the text index. MongoDB performs a logical OR search of the terms unless specified as a phrase.
     * @param Optional|string $language The language that determines the list of stop words for the search and the rules for the stemmer and tokenizer. If not specified, the search uses the default language of the index.
     * If you specify a default_language value of none, then the text index parses through each word in the field, including stop words, and ignores suffix stemming.
     * @param Optional|bool $caseSensitive A boolean flag to enable or disable case sensitive search. Defaults to false; i.e. the search defers to the case insensitivity of the text index.
     * @param Optional|bool $diacriticSensitive A boolean flag to enable or disable diacritic sensitive search against version 3 text indexes. Defaults to false; i.e. the search defers to the diacritic insensitivity of the text index.
     * Text searches against earlier versions of the text index are inherently diacritic sensitive and cannot be diacritic insensitive. As such, the $diacriticSensitive option has no effect with earlier versions of the text index.
     */
    public function __construct(
        string $search,
        Optional|string $language = Optional::Undefined,
        Optional|bool $caseSensitive = Optional::Undefined,
        Optional|bool $diacriticSensitive = Optional::Undefined,
    ) {
        $this->search = $search;
        $this->language = $language;
        $this->caseSensitive = $caseSensitive;
        $this->diacriticSensitive = $diacriticSensitive;
    }
}
