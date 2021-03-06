<?php

/**
 * @license https://github.com/simple-es/event-sourcing/blob/master/LICENSE MIT
 */

namespace SimpleES\EventSourcing\Aggregate\Factory;

use SimpleES\EventSourcing\Event\AggregateHistory;
use SimpleES\EventSourcing\Exception\IdNotMappedToAggregate;
use SimpleES\EventSourcing\Exception\InvalidTypeInCollection;

/**
 * @copyright Copyright (c) 2015 Future500 B.V.
 * @author    Jasper N. Brouwer <jasper@future500.nl>
 */
final class MappingAggregateFactory implements ReconstitutesAggregates
{
    /**
     * @var array
     */
    private $map;

    /**
     * @param array $map
     */
    public function __construct(array $map)
    {
        foreach ($map as $idClass => $aggregateClass) {
            $this->guardIdClass($idClass);
            $this->guardAggregateClass($aggregateClass);

            $this->map[$idClass] = $aggregateClass;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reconstituteFromHistory(AggregateHistory $aggregateHistory)
    {
        $idClass        = get_class($aggregateHistory->aggregateId());
        $aggregateClass = $this->mapIdClassToAggregateClass($idClass);

        return call_user_func([$aggregateClass, 'fromHistory'], $aggregateHistory);
    }

    /**
     * @param string $class
     * @return string
     */
    private function mapIdClassToAggregateClass($class)
    {
        if (!isset($this->map[$class])) {
            throw IdNotMappedToAggregate::create($class);
        }

        return $this->map[$class];
    }

    /**
     * @param string $class
     * @throws InvalidTypeInCollection
     */
    private function guardIdClass($class)
    {
        $interface = 'SimpleES\EventSourcing\Identifier\Identifies';

        if (!is_string($class) || !is_subclass_of($class, $interface)) {
            throw InvalidTypeInCollection::create($class, $interface);
        }
    }

    /**
     * @param string $class
     * @throws InvalidTypeInCollection
     */
    private function guardAggregateClass($class)
    {
        $interface = 'SimpleES\EventSourcing\Aggregate\TracksEvents';

        if (!is_string($class) || !is_subclass_of($class, $interface)) {
            throw InvalidTypeInCollection::create($class, $interface);
        }
    }
}
