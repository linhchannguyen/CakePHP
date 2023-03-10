<?php
namespace App\Repositories\Schools;

use App\Repositories\BaseRepository;
use App\Repositories\Schools\SchoolRepository;
use Cake\Cache\Cache;

class SchoolCacheRepository {
	protected $schoolRepository;
	public function __construct($model) {
        $this->schoolRepository = new SchoolRepository($model);
    }

	public function getKeyValuePairsWithCondition ($fields, $conditions) {
		return Cache::remember('getSchoolsKeyValuePairsWithCondition', function () use ($fields, $conditions) {
            return $this->schoolRepository->getKeyValuePairsWithCondition($fields, $conditions);
        });
	}
}
