<?php

namespace App\Repositories\Criteos;

use App\Repositories\BaseRepository;
use App\Repositories\Criteos\CriteoRepositoryInterface;
use Cake\Log\Log;
use Cake\ORM\Exception\PersistenceFailedException;

class CriteoRepository extends BaseRepository implements CriteoRepositoryInterface
{
    /**
     * Get a list of CRITEO tags
     **/
    public function getListCriteo($courseid)
    {
        // check if it is 9999
        if ($courseid == 9999) {
            $tagList =  $this->model
                ->find('all')
                ->order(['no' => 'DESC'])
                ->enableHydration(false);
        } else {
            $tagList =  $this->model
                ->find('all')
                ->where(['courseid' => $courseid])
                ->order(['no' => 'DESC'])
                ->enableHydration(false);
        }
        return $tagList;
    }

    public function getListCriteoByCooperation($conditions, $fields)
    {
        $tagList =  $this->model
            ->find()
            ->where($conditions)
            ->select($fields)
            ->enableHydration(false);
        return $tagList;
    }

    /**
     * Check if page id is already registered
     * @param $page_id ページID
     * return boolean
     */
    public function checkPageId($page_id)
    {
        $result = array();
        $result = $this->model
            ->find()
            ->where(['id' => $page_id])
            ->all()
            ->first();
        $return = TRUE;

        // Returns FALSE if it exists, TRUE otherwise.
        if (!empty($result)) {
            $return = FALSE;
        }

        return $return;
    }

    /**
     * Register a new Criteo tag
     **/
    public function registCriteo($criteoRegistInfo)
    {
        // trim extra_atp
        $criteoRegistInfo['Criteo']['extra_atp'] = $this->trimExtra_atp($criteoRegistInfo['Criteo']['extra_atp']);
        Log::write('debug', print_r($criteoRegistInfo, true));
        // exit();
        $this->save($criteoRegistInfo['Criteo']);
    }

    public function save($data)
    {
        $criteo = $this->model->newEmptyEntity();
        $criteo = $this->model->patchEntity($criteo, $data);
        $this->model->save($criteo);
    }

    public function saveAll($entity, $data)
    {
        $articles = $entity->getTableLocator()->get($this->model->getAlias());
        $entities = $articles->newEntities($data);
        $result = $articles->saveMany($entities);
    }

    public function destroy($id)
    {
        $entity = $this->model->get($id);
        return $this->model->delete($entity, ['id' => $id]);
    }

    /**
     * Edit Criteo tags
     * @params $editTag array edit value
     * return $result result
     **/
    public function updateTag($editTag)
    {
        // trim extra_atp
        $editTag['extra_atp'] = $this->trimExtra_atp($editTag['extra_atp']);

        $result = $this->model
            ->updateAll(
                array(
                    'name' => $editTag['name'],
                    'url' => $editTag['url'],
                    'bigimage' => $editTag['bigimage'],
                    'description' => $editTag['description'],
                    'price' => $editTag['price'],
                    'recommendable' => $editTag['recommendable'],
                    'cooperation_flag' => $editTag['cooperation_flag'],
                    'extra_atp' => $editTag['extra_atp'],
                    'page_type' => $editTag['page_type']
                ),
                array(
                    'id' => $editTag['id']
                )
            );
        return $result;
    }

    /**
     * Shaping extra_atp
     * $extra_atp string  extra_atp
     * retun string
     **/
    public function trimExtra_atp($extra_atp)
    {
        // If extra_atp exists, remove line feeds and TABs, convert all to full-width
        if (!empty($extra_atp)) {
            $extra_atp = preg_replace('/(\t|\r\n|\r|\n)/s', '', $extra_atp);
            $extra_atp = mb_convert_kana($extra_atp, 'KVRN');
        }

        return $extra_atp;
    }
}
