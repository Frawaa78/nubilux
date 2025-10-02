<?php

// Module configuration
return [
    'weather' => [
        'enabled' => false,
        'path' => 'modules/weather',
        'routes' => [
            '/weather' => 'WeatherController@index',
            '/api/weather' => 'WeatherController@api'
        ],
        'dependencies' => [],
        'version' => '1.0.0'
    ],
    
    'calendar' => [
        'enabled' => false,
        'path' => 'modules/calendar',
        'routes' => [
            '/calendar' => 'CalendarController@index',
            '/api/calendar' => 'CalendarController@api'
        ],
        'dependencies' => [],
        'version' => '1.0.0'
    ],
    
    'tasks' => [
        'enabled' => false,
        'path' => 'modules/tasks',
        'routes' => [
            '/tasks' => 'TaskController@index',
            '/api/tasks' => 'TaskController@api'
        ],
        'dependencies' => [],
        'version' => '1.0.0'
    ]
];