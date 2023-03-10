<?php
namespace App\Repositories\Files;

use App\Repositories\BaseRepository;
use App\Repositories\Files\FilesRepository;
use Cake\Cache\Cache;

class FileCacheRepository {
	protected $fileRepository;
	public function __construct($model) {
        $this->fileRepository = new FilesRepository($model);
    }

	public function getByCondition($conditions, $fields = []) {
		return Cache::remember('getFilesByCondition', function () use ($fields, $conditions) {
            return $this->fileRepository->getByCondition($conditions, $fields);
        });
	}

    public function destroyCache() {
		return Cache::delete('getFilesByCondition');
	}
}
