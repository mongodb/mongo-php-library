<?php

namespace MongoDB\Bulk;

/**
 * Class BulkBuilder
 * @package MongoDB\Bulk
 */
class BulkBuilder
{
    /**
     * @var BulkInputInterface[]
     */
    private $operations = [];

    /**
     * @param array|object $document
     * @return $this
     */
    public function insertOne($document)
    {
        $this->operations[] = new InsertOneInput($document);

        return $this;
    }

    /**
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     * @return $this
     */
    public function updateOne($filter, $update, array $options = [])
    {
        $this->operations[] = new UpdateOneInput($filter, $update, $options);

        return $this;
    }

    /**
     * @param array|object $filter
     * @param array|object $update
     * @param array $options
     * @return $this
     */
    public function updateMany($filter, $update, array $options = [])
    {
        $this->operations[] = new UpdateManyInput($filter, $update, $options);

        return $this;
    }

    /**
     * @param array|object $filter
     * @return $this
     */
    public function deleteOne($filter)
    {
        $this->operations[] = new DeleteOneInput($filter);

        return $this;
    }

    /**
     * @param array|object $filter
     * @return $this
     */
    public function deleteMany($filter)
    {
        $this->operations[] = new DeleteManyInput($filter);

        return $this;
    }

    /**
     * @return BulkInputInterface[]
     */
    public function getOperations()
    {
        return $this->operations;
    }
}