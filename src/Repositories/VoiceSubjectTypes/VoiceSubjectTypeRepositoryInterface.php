<?php
/**
 * To change this template use File | Settings | File Templates.
 *
 */
namespace App\Repositories\VoiceSubjectTypes;

interface VoiceSubjectTypeRepositoryInterface {
    public function getListSelectedFieldsWithKeyValue($keyField, $valueField);
}
