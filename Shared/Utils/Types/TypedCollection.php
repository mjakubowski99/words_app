<?php

declare(strict_types=1);

namespace Shared\Utils\Types;

/**
 * @template T
 */
abstract class TypedCollection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var T[] */
    private array $items = [];

    private string $type;

    /** @param T[] $items */
    final public function __construct(array $items = [])
    {
        $this->type = $this->getCollectionType();

        foreach ($items as $item) {
            $this->validateType($item);
        }

        $this->items = $items;
    }

    /** @param T[] $items */
    final public static function fromArray(array $items): static
    {
        return new static($items);
    }

    final public function add(object $item): void
    {
        $this->validateType($item);
        $this->items[] = $item;
    }

    /** @return T[] */
    final public function getAll(): array
    {
        return $this->items;
    }

    final public function count(): int
    {
        return count($this->items);
    }

    final public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /** @return ?T */
    final public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    final public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
        } else {
            $this->validateType($value);
            $this->items[$offset] = $value;
        }
    }

    final public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    final public function getIterator(): \ArrayIterator
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
