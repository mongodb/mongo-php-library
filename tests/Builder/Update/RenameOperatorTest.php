<?php

declare(strict_types=1);

namespace MongoDB\Tests\Builder\Update;

use MongoDB\Builder\Update;
use MongoDB\Tests\Builder\UpdateTestCase;

/**
 * Test $rename update
 */
class RenameOperatorTest extends UpdateTestCase
{
    public function testRenameAField(): void
    {
        $update = Update::rename(nmae: 'name');

        $this->assertSameUpdate(Pipelines::RenameRenameAField, $update);
    }

    public function testRenameAFieldInAnEmbeddedDocument(): void
    {
        $update = Update::rename(...['name.first' => 'name.fname']);

        $this->assertSameUpdate(Pipelines::RenameRenameAFieldInAnEmbeddedDocument, $update);
    }

    public function testRenameAFieldThatDoesNotExist(): void
    {
        $update = Update::rename(wife: 'spouse');

        $this->assertSameUpdate(Pipelines::RenameRenameAFieldThatDoesNotExist, $update);
    }

    public function testRenameMultipleFields(): void
    {
        $update = Update::rename(nickname: 'alias', cell: 'mobile');

        $this->assertSameUpdate(Pipelines::RenameRenameMultipleFields, $update);
    }
}
