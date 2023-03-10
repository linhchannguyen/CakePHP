<?php
namespace App\Repositories\EventTypes;

use App\Repositories\BaseRepository;
use App\Repositories\EventTypes\EventTypeRepository;
use Cake\Cache\Cache;

class EventTypeCacheRepository {
	protected $eventTypeRepository;
	public function __construct($model) {
        $this->eventTypeRepository = new EventTypeRepository($model);
    }

	public function getKeyValuePairsWithCondition ($fields, $conditions) {
		return Cache::remember('getEventTypesKeyValuePairsWithCondition', function () use ($fields, $conditions) {
            return $this->eventTypeRepository->getKeyValuePairsWithCondition($fields, $conditions);
        });
	}
}
