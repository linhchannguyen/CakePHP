<?php
namespace App\Policy;

use Authorization\Policy\RequestPolicyInterface;
use Cake\Http\Exception;
use Cake\Http\ServerRequest;

class RequestPolicy implements RequestPolicyInterface
{

    /**
     * Define variables
     *
     */
    public $ignoreNormalAccess = [
        'SuccessfulCandidates' => [
            'moduleLists', 'editModule', 'userLists', 'categoryLists', 'addCategory', 'deleteCategory', 'add', 'deleteVoiceUser', 'editForm'
        ],
        'SuccessfulCandidatesControl' => [
        ]
    ];

    public $ignoreAdminAccess = [
        'SuccessfulCandidates' => [
            'userLists', 'categoryLists', 'addCategory', 'deleteCategory', 'add', 'deleteVoiceUser'
        ],
        'SuccessfulCandidatesControl' => [
            'deleteUserData'
        ]
    ];

    public $ignoreHighesAccess = [
        'SuccessfulCandidatesControl' => [
            'deleteUserData'
        ]
    ];

    public $mapConditions = [
        HIGHEST => [
            'SuccessfulCandidatesControl'
        ],
        ADMIN => [
            'SuccessfulCandidates',
            'SuccessfulCandidatesControl'
        ],
        NOMAL => [
            'SuccessfulCandidates',
            'SuccessfulCandidatesControl'
        ],
    ];

    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null $identity Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return bool
     */
    public function canAccess($identity, ServerRequest $request) {
        if (isset($identity)) {
            $access = [];
            switch ($identity->getOriginalData()->role_id) {
                case HIGHEST:
                    $access = array_filter($this->mapConditions[HIGHEST], function ($item) use ($request) {
                        if ($request->getParam('controller') === $item
                        && in_array($request->getParam('action'), $this->ignoreHighesAccess[$item])) {
                            return true;
                        }
                    });
                    break;
                case ADMIN:
                    $access = array_filter($this->mapConditions[ADMIN], function ($item) use ($request) {
                        if ($request->getParam('controller') === $item
                        && in_array($request->getParam('action'), $this->ignoreAdminAccess[$item])) {
                            return true;
                        }
                    });
                    break;
                case NOMAL:
                    $access = array_filter($this->mapConditions[NOMAL], function ($item) use ($request) {
                        if ($request->getParam('controller') === $item
                        && in_array($request->getParam('action'), $this->ignoreNormalAccess[$item])) {
                            return true;
                        }
                    });
                    break;
                default:
                    return true;
            }
            if (!empty($access)) {
                throw new Exception\NotFoundException;
                return false;
            }
        }
        return true;
    }
}

?>
