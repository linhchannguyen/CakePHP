<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repositories\CriteoAlerts\CriteoAlertRepository;

class CriteoAlertsController extends AppController
{
    public $name = 'CriteosAlert';
    const FILE_NAME = 'alert.csv';
    const OUTPUT_DIR = '/var/www/html/cakephp/app/tmp/';

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel("CriteoAlert");
        $this->criteoAlertRepository = new CriteoAlertRepository($this->CriteoAlert);

        // Get course id from value existence check
        // for editing
        $searchDate = date('Y-m-d');
        $conditions = array('browsetime >= ' => $searchDate);
        $feedList = $this->criteoAlertRepository->getCriteoAlertByCondition($conditions)->toArray();

        $session = $this->request->getSession();
        $session->renew();

        $optionTimeList = array(
            1 => '本日',
            2 => '3日間',
            3 => '1週間',
            4 => '1ヶ月',
            5 => '1年'
        );

        $this->set('optionTimeList', $optionTimeList);
        $this->set('feedList', $feedList);
    }

    public function index()
    {
    }

    /**
     * Check alert feed
     **/
    public function searchAlert()
    {
        // Switch the display by search date
        $today = date('Y-m-d');
        switch ($this->request->getData('browsetime')) {
            case 2:
                $searchDate = date('Y-m-d', strtotime($today . "-3 day"));
                break;
            case 3:
                $searchDate = date('Y-m-d', strtotime($today . "-7 day"));
                break;
            case 4:
                $searchDate = date('Y-m-d', strtotime($today . "-31 day"));
                break;
            case 5:
                $searchDate = date('Y-m-d', strtotime($today . "-365 day"));
                break;
            default:
                $searchDate = $today;
        }
        // search
        $conditions = array('browsetime >= ' => $searchDate);
        $feedList = $this->criteoAlertRepository->getCriteoAlertByCondition($conditions)->toArray();
        $this->set('feedList', $feedList);
        $this->render('index');
    }

    /**
    * Alert feed CSV output
     **/
    public function  exportAlert()
    {
        $today = date('Y-m-d');
        switch ($this->request->getData('CriteoCsvExportAlert')) {
            case 2:
                $searchDate = date('Y-m-d', strtotime($today . "-3 day"));
                break;
            case 3:
                $searchDate = date('Y-m-d', strtotime($today . "-7 day"));
                break;
            case 4:
                $searchDate = date('Y-m-d', strtotime($today . "-31 day"));
                break;
            case 5:
                $searchDate = date('Y-m-d', strtotime($today . "-365 day"));
                break;
            default:
                $searchDate = $today;
        }

        // search
        $conditions = array('browsetime >= ' => $searchDate);
        $feedList = $this->criteoAlertRepository->getCriteoAlertByCondition($conditions)->toArray();

        // CSV file creation
        $fileName = TMP . self::FILE_NAME;
        $fp = fopen($fileName, 'w');

        foreach ($feedList as $feed) {

            $outputRecord = '"",' . $feed['id'] . ',' . $feed['name'] . ',' . $feed['url'] . ',"","",0,0,1,1,2';

            $record = mb_convert_encoding($outputRecord, 'SJIS', 'UTF-8');

            fwrite($fp, $record . "\n");
        }

        fclose($fp);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=alert.csv');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($fileName));
        readfile($fileName);
        exit();
    }
}
