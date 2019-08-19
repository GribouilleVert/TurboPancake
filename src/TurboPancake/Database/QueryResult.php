<?php
namespace TurboPancake\Database;

use TurboPancake\Database\Exceptions\QueryBuilderException;
use TurboPancake\Database\Exceptions\ReadOnlyException;

class QueryResult implements \ArrayAccess, \Iterator {

    /**
     * @var mixed[] Enregistements
     */
    private $records = [];

    /**
     * @var string
     */
    private $entity;

    /**
     * @var int Index requis pour l'implementation de l'interface \Iterator
     */
    private $interatorIndex = 0;

    /**
     * @var object[] Cache des objects deja générés par get()
     */
    private $generatedEntities = [];

    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    /**
     * @param int $index
     * @return mixed
     * @throws QueryBuilderException
     */
    public function get(int $index): object
    {
        if (is_null($this->entity)) {
            return $this->records[$index];
        }

        if (!isset($this->generatedEntities[$index])) {
            $object = Sprinkler::hydrate($this->records[$index], $this->entity);
            $this->generatedEntities[$index] = $object;
        } else {
            $object = $this->generatedEntities[$index];
        }
        return $object;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     * @throws QueryBuilderException
     */
    public function current()
    {
        return $this->get($this->interatorIndex);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->interatorIndex++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): int
    {
        return $this->interatorIndex;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     * @throws QueryBuilderException
     */
    public function valid(): bool
    {
        return isset($this->records[$this->interatorIndex]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->interatorIndex = 0;
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     * @throws QueryBuilderException
     */
    public function offsetExists($offset): bool
    {
        return isset($this->records[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws QueryBuilderException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws ReadOnlyException
     */
    public function offsetSet($offset, $value): void
    {
        throw new ReadOnlyException(self::class . ' array interface is read only !');
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws ReadOnlyException
     */
    public function offsetUnset($offset)
    {
        throw new ReadOnlyException(self::class . ' array interface is read only !');
    }

}
