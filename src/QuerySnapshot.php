<?php

namespace WebsolutionsGroup\Firestore;

class QuerySnapshot implements \IteratorAggregate
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var DocumentSnapshot[]
     */
    private $rows;

    /**
     * @param Query $query The Query which generated this snapshot.
     * @param DocumentSnapshot[] $rows The query result rows.
     */
    public function __construct(
        Query $query,
        array $rows
    ) {
        $this->query = $query;
        $this->rows = $rows;
    }

    /**
     * Check if the result is empty.
     *
     * Example:
     * ```
     * $empty = $snapshot->isEmpty();
     * ```
     *
     * @return bool|null
     */
    public function isEmpty()
    {
        return empty($this->rows);
    }

    /**
     * Returns the size of the result set.
     *
     * Example:
     * ```
     * $size = $snapshot->size();
     * ```
     *
     * @return int|null
     */
    public function size()
    {
        return count($this->rows);
    }

    /**
     * Return the formatted and decoded rows. If the stream is interrupted,
     * attempts will be made on your behalf to resume.
     *
     * Example:
     * ```
     * $rows = $snapshot->rows();
     * ```
     *
     * @return DocumentSnapshot[]
     */
    public function rows()
    {
        return $this->rows;
    }

    /**
     * @access private
     * @return \Generator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }
}
