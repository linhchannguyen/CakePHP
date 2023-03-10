<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
        /*
         * Here, we are connecting '/' (base path) to a controller called 'Pages',
         * its action called 'display', and we pass a param to select the view file
         * to use (in this case, templates/Pages/home.php)...
         */
        // $builder->connect('/', array('controller' => 'criteos', 'action' => 'index'));

        /*
         * ...and connect the rest of 'Pages' controller's URLs.
         */
        $builder->connect('/pages/*', 'Pages::display');

        // Routes for successful_candidates/*
        $builder->connect('/successful_candidates/login', array('controller' => 'SuccessfulCandidates', 'action' => 'login'));
        $builder->connect('/successful_candidates/logout', array('controller' => 'SuccessfulCandidates', 'action' => 'logout'));
        $builder->connect('/successful_candidates/editForm', array('controller' => 'SuccessfulCandidates', 'action' => 'editForm'));
        //module
        $builder->connect('/successful_candidates/moduleLists', array('controller' => 'SuccessfulCandidates', 'action' => 'moduleLists'));
        $builder->connect('/successful_candidates/editModule', array('controller' => 'SuccessfulCandidates', 'action' => 'editModule'));
        $builder->connect('/successful_candidates/deleteModule', array('controller' => 'SuccessfulCandidates', 'action' => 'deleteModule'));

        // category
        $builder->connect('/successful_candidates/categoryLists', array('controller' => 'SuccessfulCandidates', 'action' => 'categoryLists'));
        $builder->connect('/successful_candidates/addCategory/*', array('controller' => 'SuccessfulCandidates', 'action' => 'addCategory'));
        $builder->connect('/successful_candidates/delete_category/*', array('controller' => 'SuccessfulCandidates', 'action' => 'delete_category'));

        // user
        $builder->connect('/successful_candidates/userLists', array('controller' => 'SuccessfulCandidates', 'action' => 'userLists'));
        $builder->connect('/successful_candidates/add/*', array('controller' => 'SuccessfulCandidates', 'action' => 'add'));
        $builder->connect('/successful_candidates/delete_voice_user/*', array('controller' => 'SuccessfulCandidates', 'action' => 'delete_voice_user'));

        //index
        $builder->connect('/successful_candidates/index', array('controller' => 'SuccessfulCandidates', 'action' => 'index'));
        $builder->connect('/successful_candidates', array('controller' => 'SuccessfulCandidates', 'action' => 'index'));

        // Routes for successful_candidates_control/*
        $builder->connect('/successful_candidates_control/index', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'index'));
        $builder->connect('/successful_candidates_control', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'index'));

        // post data
        $builder->connect('/successful_candidates_control/postData', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'postData'));
        $builder->connect('/successful_candidates_control/download_csv', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'download_csv'));
        $builder->connect('/successful_candidates_control/changeSendMail', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'changeSendMail'));
        $builder->connect('/successful_candidates_control/changeShowPeople', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'changeShowPeople'));
        $builder->connect('/successful_candidates_control/changeLock', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'changeLock'));
        $builder->connect('/successful_candidates_control/deleteUserData/*', array('controller' => 'SuccessfulCandidatesControl', 'action' => 'deleteUserData'));

        $builder->connect('/user/*', array('controller' => 'users', 'action' => 'login',  'plugin' => 'DebugKit', '_ext' => NULL));
        $builder->connect('/criteos/registTags', array('controller' => 'criteos', 'action' => 'registTags'));
        $builder->connect('/criteos/searchCategory', array('controller' => 'criteos', 'action' => 'searchCategory'));
        $builder->connect('/criteos/update', array('controller' => 'criteos', 'action' => 'update'));
        $builder->connect('/criteos/allUpdate', array('controller' => 'criteos', 'action' => 'allUpdate'));
        $builder->connect('/criteos/downloadCsv', array('controller' => 'criteos', 'action' => 'downloadCsv'));
        $builder->connect('/criteos/importCsv', array('controller' => 'criteos', 'action' => 'importCsv'));
        $builder->connect('/criteos/exportCsv', array('controller' => 'criteos', 'action' => 'exportCsv'));

        $builder->connect('/criteoAlerts/searchAlert', array('controller' => 'criteoAlerts', 'action' => 'searchAlert'));
        $builder->connect('/criteoAlerts/exportAlert', array('controller' => 'criteoAlerts', 'action' => 'exportAlert'));

        // News
        $builder->scope('/news_list', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'News', 'action' => 'index']);
            $builder->connect('/index', ['controller' => 'News', 'action' => 'index']);
            $builder->connect('/news_download_csv', ['controller' => 'News', 'action' => 'newsDownloadCsv']);
            $builder->connect('/news_detail', ['controller' => 'News', 'action' => 'newsDetail']);
            $builder->connect('/news_edit_finish', ['controller' => 'News', 'action' => 'newsEditFinish']);
            $builder->connect('/news_add_finish', ['controller' => 'News', 'action' => 'newsAddFinish']);
            $builder->connect('/news_change_records', ['controller' => 'News', 'action' => 'newsChangeRecords']);
        });

        // Events
        $builder->scope('/events_list', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'Events', 'action' => 'index']);
            $builder->connect('/index', ['controller' => 'Events', 'action' => 'index']);
            $builder->connect('/events_download_csv', ['controller' => 'Events', 'action' => 'eventsDownloadCsv']);
            $builder->connect('/events_detail', ['controller' => 'Events', 'action' => 'eventsDetail']);
            $builder->connect('/events_edit_finish', ['controller' => 'Events', 'action' => 'eventsEditFinish']);
            $builder->connect('/events_add_finish', ['controller' => 'Events', 'action' => 'eventsAddFinish']);
            $builder->connect('/events_change_records', ['controller' => 'Events', 'action' => 'eventsChangeRecords']);
        });

        // News
        $builder->scope('/recommends_list', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'Recommends', 'action' => 'index']);
            $builder->connect('/index', ['controller' => 'Recommends', 'action' => 'index']);
            $builder->connect('/recommends_download_csv', ['controller' => 'Recommends', 'action' => 'recommendsDownloadCsv']);
            $builder->connect('/recommends_detail', ['controller' => 'Recommends', 'action' => 'recommendsDetail']);
            $builder->connect('/recommends_edit_finish', ['controller' => 'Recommends', 'action' => 'recommendsEditFinish']);
            $builder->connect('/recommends_add_finish', ['controller' => 'Recommends', 'action' => 'recommendsAddFinish']);
            $builder->connect('/recommends_change_records', ['controller' => 'Recommends', 'action' => 'recommendsChangeRecords']);
        });

        // Preview
        $builder->connect('/preview/index', ['controller' => 'Previews', 'action' => 'index']);
        $builder->connect('/', ['controller' => 'Previews', 'action' => 'index']);

        // Event Types Form
        $builder->connect('/event_types_form/index', ['controller' => 'EventTypesForms', 'action' => 'index']);

        // File list
        $builder->connect('/files_list/index', ['controller' => 'FilesList', 'action' => 'index']);
        $builder->connect('/files_list', ['controller' => 'FilesList', 'action' => 'index']);
        $builder->connect('/files_list/files_change_records', ['controller' => 'FilesList', 'action' => 'filesChangeRecords']);
        $builder->connect('/files_list/files_change_records_delete_confirm', ['controller' => 'FilesList', 'action' => 'filesChangeRecordsDeleteConfirm']);
        $builder->connect('/files_list/files_change_records_delete_finish', ['controller' => 'FilesList', 'action' => 'filesChangeRecordsDeleteFinish']);

        // Upload pdf files
        $builder->connect('/pdf_upload', ['controller' => 'UploadFiles', 'action' => 'pdfUpload']);

        // Csv form
        $builder->scope('/csv_form', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'CsvForms', 'action' => 'index']);
            $builder->connect('/index', ['controller' => 'CsvForms', 'action' => 'index']);
            // News
            $builder->connect('/csv_news_receive', ['controller' => 'CsvForms', 'action' => 'csvNewsReceive']);
            $builder->connect('/csv_news_receive_confirm', ['controller' => 'CsvForms', 'action' => 'csvNewsReceiveConfirm']);
            $builder->connect('/csv_news_receive_finish', ['controller' => 'CsvForms', 'action' => 'csvNewsReceiveFinish']);
            // Recommends
            $builder->connect('/csv_recommends_receive', ['controller' => 'CsvForms', 'action' => 'csvRecommendsReceive']);
            $builder->connect('/csv_recommends_receive_confirm', ['controller' => 'CsvForms', 'action' => 'csvRecommendsReceiveConfirm']);
            $builder->connect('/csv_recommends_receive_finish', ['controller' => 'CsvForms', 'action' => 'csvRecommendsReceiveFinish']);
            // Events
            $builder->connect('/csv_events_receive', ['controller' => 'CsvForms', 'action' => 'csvEventsReceive']);
            $builder->connect('/csv_events_receive_confirm', ['controller' => 'CsvForms', 'action' => 'csvEventsReceiveConfirm']);
            $builder->connect('/csv_events_receive_finish', ['controller' => 'CsvForms', 'action' => 'csvEventsReceiveFinish']);
            // Holidays
            $builder->connect('/csv_holidays_receive', ['controller' => 'CsvForms', 'action' => 'csvHolidaysReceive']);
            $builder->connect('/csv_holidays_receive_confirm', ['controller' => 'CsvForms', 'action' => 'csvHolidaysReceiveConfirm']);
            $builder->connect('/csv_holidays_receive_finish', ['controller' => 'CsvForms', 'action' => 'csvHolidaysReceiveFinish']);
        });

        // Holidays
        $builder->scope('/holidays_list', function (RouteBuilder $builder) {
            $builder->connect('/', ['controller' => 'Holidays', 'action' => 'index']);
            $builder->connect('/index', ['controller' => 'Holidays', 'action' => 'index']);
            $builder->connect('/holidays_change_records', ['controller' => 'Holidays', 'action' => 'holidaysChangeRecords']);
            $builder->connect('/holidays_change_records_delete_confirm', ['controller' => 'Holidays', 'action' => 'holidaysChangeRecordsDeleteConfirm']);
            $builder->connect('/holidays_change_records_delete_finish', ['controller' => 'Holidays', 'action' => 'holidaysChangeRecordsDeleteFinish']);
        });

        /*
         * Connect catchall routes for all controllers.
         *
         * The `fallbacks` method is a shortcut for
         *
         * ```
         * $builder->connect('/{controller}', ['action' => 'index']);
         * $builder->connect('/{controller}/{action}/*', []);
         * ```
         *
         * You can remove these routes once you've connected the
         * routes you want in your application.
         */
        $builder->fallbacks();
    });

    /*
     * If you need a different set of middleware or none at all,
     * open new scope and define routes there.
     *
     * ```
     * $routes->scope('/api', function (RouteBuilder $builder) {
     *     // No $builder->applyMiddleware() here.
     *
     *     // Parse specified extensions from URLs
     *     // $builder->setExtensions(['json', 'xml']);
     *
     *     // Connect API actions here.
     * });
     * ```
     */
};
