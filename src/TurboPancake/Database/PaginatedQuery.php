<?php
namespace TurboPancake\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface {

    /**
     * @var string
     */
    private $query;

    /**
     * PaginatedQuery constructor.
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     * @throws Exceptions\QueryBuilderException
     */
    public function getNbResults(): int
    {
        return $this->query->count();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     * @throws Exceptions\NoRecordException
     * @throws Exceptions\QueryBuilderException
     */
    public function getSlice($offset, $length)
    {
        return (clone $this->query)
            ->limit($length, $offset)
            ->fetchAll();
    }

}
