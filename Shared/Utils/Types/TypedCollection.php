<?php

namespace Shared\Utils\Types;

/**
 * @template T
 */
abstract class TypedCollection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var  T[]  */
    private array $items = [];

    private string $type;

    /** @param T[] $items */
    public function __construct(array $items = [])
    {
        $this->type = $this->getCollectionType();

        foreach ($items as $item) {
            $this->validateType($item);
        }

        $this->items = $items;
    }

    /** @param T[] $items */
    public static function fromArray(array $items): static
    {
        return new static($items);
    }

    public function add(object $item): void
    {
        $this->validateType($item);
        $this->items[] = $item;
    }

    /** @return T[] */
    public function getAll(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /** @return ?T */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
        } else {
            $this->validateType($value);
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    private function validateType(object $item): void
    {
        if (!$item instanceof $this->type) {
            throw new \InvalidArgumentException(sprintf(
                'All elements in %s must be instances of %s, but %s given.',
                static::class,
                $this->type,
                get_class($item)
            ));
        }
    }

    private function getCollectionType(): string
    {
        $reflection = new \ReflectionClass($this);
        $attributes = $reflection->getAttributes(CollectionType::class);

        if (empty($attributes)) {
            throw new \LogicException(sprintf('Class %s must have a #[CollectionType] attribute.', static::class));
        }

        return $attributes[0]->newInstance()->type;
    }
}
