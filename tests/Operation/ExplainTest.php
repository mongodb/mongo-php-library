<?php

namespace MongoDB\Tests\Operation;

use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Operation\Explain;
use MongoDB\Operation\Explainable;
use PHPUnit\Framework\Attributes\DataProvider;

class ExplainTest extends TestCase
{
    #[DataProvider('provideInvalidConstructorOptions')]
    public function testConstructorOptionTypeChecks(array $options): void
    {
        $explainable = $this->getMockBuilder(Explainable::class)->getMock();
        $this->expectException(InvalidArgumentException::class);
        new Explain($this->getDatabaseName(), $explainable, $options);
    }

    public static function provideInvalidConstructorOptions()
    {
        return self::createOptionDataProvider([
            'readPreference' => self::getInvalidReadPreferenceValues(),
            'session' => self::getInvalidSessionValues(),
            'typeMap' => self::getInvalidArrayValues(),
            'verbosity' => self::getInvalidStringValues(),
        ]);
    }
}
