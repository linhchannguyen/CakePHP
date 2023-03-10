<?php

namespace App\View\Helper;

use Cake\View\Helper\PaginatorHelper;
use Cake\View\StringTemplate;

class PaginatorCustomHelper extends PaginatorHelper
{
    protected $_defaultConfig = [
        'templates' => [
            'prevActive' => '&nbsp;<a href="{{url}}">{{text}}</a>',
            'prevDisabled' => '<b><u>{{text}}</u></b>',
            'nextActive' => '<a href="{{url}}">{{text}}</a>&nbsp;&nbsp;&nbsp;',
            'nextDisabled' => '<b><u>{{text}}</u></b>',
            'current' => '<b><u>{{text}}</u></b>',
            'number' => '<a href="{{url}}" title="page {{text}}">{{text}}</a>',
            'ellipsis' => '<li class="ellipsis">&hellip;</li>',
            'first' => '&nbsp;&nbsp;&nbsp;<a href="{{url}}">{{text}}</a>',
            'last' => '<a href="{{url}}">{{text}}</a>&nbsp;&nbsp;&nbsp;',
        ]
    ];

    protected function _numbers(StringTemplate $templater, array $params, array $options): string {
        $out = '';
        $out .= $options['before'];
        $separator = $options['separator'] ?? '|';
        for ($i = 1; $i <= $params['pageCount']; $i++) {
            if ($i === $params['page']) {
                $out .= $templater->format('current', [
                    'text' => $this->Number->format($params['page']),
                    'url' => $this->generateUrl(['page' => $i], $options['model'], $options['url']),
                ]);
            } else {
                $vars = [
                    'text' => $this->Number->format($i),
                    'url' => $this->generateUrl(['page' => $i], $options['model'], $options['url']),
                ];
                $out .= $templater->format('number', $vars);
            }
            if ($i < $params['pageCount']) {
                if (isset($options['cms_custom'])) {
                    $out .= "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;";
                } else {
                    $out .= "<span>{$separator}</span>";
                }
            }
        }
        $out .= $options['after'];
        $out = rtrim($out, "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;");
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        return $out;
    }

    protected function _modulusNumbers(StringTemplate $templater, array $params, array $options): string {
        $out = "";
        $ellipsis = $templater->format('ellipsis', []);
        $separator = $options['separator'] ?? '|';

        [$start, $end] = $this->_getNumbersStartAndEnd($params, $options);

        $out .= $this->_firstNumber($ellipsis, $params, $start, $options);
        $out .= $options['before'];
        for ($i = $start; $i < $params['page']; $i++) {
            $out .= $this->_formatNumber($templater, [
                'text' => $i,//$this->Number->format($i),
                'page' => $i,
                'model' => $options['model'],
                'url' => $options['url'],
            ]);
            if (isset($options['cms_custom'])) {
                if ($i < $params['pageCount']) {
                    $out .= "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;";
                }
            }
        }

        $url = $options['url'];
        $url['?']['page'] = $params['page'];
        $out .= $templater->format('current', [
            'text' => $params['page'],//$this->Number->format($params['page']),
            'url' => $this->generateUrl($url, $options['model']),
        ]);
        if (isset($options['cms_custom'])) {
            $out .= "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;";
        }

        $start = $params['page'] + 1;
        $i = $start;
        while ($i < $end) {
            $out .= $this->_formatNumber($templater, [
                'text' => $i,//$this->Number->format($i),
                'page' => $i,
                'model' => $options['model'],
                'url' => $options['url'],
            ]);
            if (isset($options['cms_custom'])) {
                if ($i < $params['pageCount']) {
                    $out .= "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;";
                }
            }
            $i++;
        }

        if ($end !== $params['page']) {
            $out .= $this->_formatNumber($templater, [
                'text' => $i,//$this->Number->format($i),
                'page' => $end,
                'model' => $options['model'],
                'url' => $options['url'],
            ]);
        }

        $out .= $options['after'];

        if (isset($options['cms_custom'])) {
            $out = rtrim($out, "&nbsp;&nbsp;&nbsp;{$separator}&nbsp;&nbsp;&nbsp;");
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $out .= "&nbsp;";
        $out .= $this->_lastNumber($ellipsis, $params, $end, $options);

        return $out;
    }
}
