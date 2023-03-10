<?php
namespace App\Repositories\Kouzas;

use App\Repositories\BaseRepository;
use App\Repositories\Kouzas\KouzaRepository;
use Cake\Cache\Cache;

class KouzaCacheRepository {
	protected $kouzaRepository;
	public function __construct($model) {
        $this->kouzaRepository = new KouzaRepository($model);
    }

	public function getKeyValuePairsWithCondition ($fields, $conditions) {
		return Cache::remember('getKouzasKeyValuePairsWithCondition', function () use ($fields, $conditions) {
            return $this->kouzaRepository->getKeyValuePairsWithCondition($fields, $conditions);
        });
	}
}
