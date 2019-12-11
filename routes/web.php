<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['middleware' => ['auth']], function () use ($router) {
    // Templates
    $router->get('/checklists/templates', [
        'as' => 'checklists.templates.index', 'uses' => 'ChecklistTemplateController@index'
    ]);
    $router->post('/checklists/templates', [
        'as' => 'checklists.templates.store', 'uses' => 'ChecklistTemplateController@storeAction'
    ]);
    $router->get('/checklists/templates/{template_id:[0-9]+}', [
        'as' => 'checklists.templates.detail', 'uses' => 'ChecklistTemplateController@detailAction'
    ]);
    $router->patch('/checklists/templates/{template_id:[0-9]+}', [
        'as' => 'checklists.templates.update', 'uses' => 'ChecklistTemplateController@updateAction'
    ]);
    $router->delete('/checklists/templates/{template_id:[0-9]+}', [
        'as' => 'checklists.templates.delete', 'uses' => 'ChecklistTemplateController@deleteAction'
    ]);
    $router->post('/checklists/templates/{template_id:[0-9]+}/assigns', [
        'as' => 'checklists.templates.assigns', 'uses' => 'ChecklistTemplateController@assignsAction'
    ]);

    // Checklist
    $router->get('/checklists', [
        'as' => 'checklists.index', 'uses' => 'ChecklistController@index'
    ]);
    $router->get('/checklists/{checklist_id:[0-9]+}', [
        'as' => 'checklists.detail', 'uses' => 'ChecklistController@detailAction'
    ]);
    $router->post('/checklists', [
        'as' => 'checklists.store', 'uses' => 'ChecklistController@storeAction'
    ]);
    $router->patch('/checklists/{checklist_id:[0-9]+}', [
        'as' => 'checklists.update', 'uses' => 'ChecklistController@updateAction'
    ]);
    $router->delete('/checklists/{checklist_id:[0-9]+}', [
        'as' => 'checklists.delete', 'uses' => 'ChecklistController@deleteAction'
    ]);

    // Items
    $router->get('/checklists/items', [
        'as' => 'checklists.items.index', 'uses' => 'ChecklistItemController@index'
    ]);
    $router->post('/checklists/complete', [
        'as' => 'checklists.items.complete', 'uses' => 'ChecklistItemController@completeAction'
    ]);
    $router->post('/checklists/incomplete', [
        'as' => 'checklists.items.incomplete', 'uses' => 'ChecklistItemController@incompleteAction'
    ]);
    $router->get('/checklists/{checklist_id:[0-9]+}/items', [
        'as' => 'checklists.items.list', 'uses' => 'ChecklistItemController@itemListAction'
    ]);
    $router->post('/checklists/{checklist_id:[0-9]+}/items', [
        'as' => 'checklists.items.store', 'uses' => 'ChecklistItemController@storeAction'
    ]);
    $router->get('/checklists/{checklist_id:[0-9]+}/items/{item_id:[0-9]+}', [
        'as' => 'checklists.items.get', 'uses' => 'ChecklistItemController@itemDetailAction'
    ]);
    $router->patch('/checklists/{checklist_id:[0-9]+}/items/{item_id:[0-9]+}', [
        'as' => 'checklists.items.update', 'uses' => 'ChecklistItemController@itemUpdateAction'
    ]);
    $router->delete('/checklists/{checklist_id:[0-9]+}/items/{item_id:[0-9]+}', [
        'as' => 'checklists.items.delete', 'uses' => 'ChecklistItemController@itemDeleteAction'
    ]);
    $router->post('/checklists/{checklist_id:[0-9]+}/items/_bulk', [
        'as' => 'checklists.items.update.bulk', 'uses' => 'ChecklistItemController@itemUpdateBulkAction'
    ]);
    $router->get('/checklists/items/summaries', [
        'as' => 'checklists.items.summaries', 'uses' => 'ChecklistItemController@summariesAction'
    ]);

    // history
    $router->get('/checklists/histories', [
        'as' => 'checklists.history.index', 'uses' => 'HistoryController@index'
    ]);
    $router->get('/checklists/histories/{history_id:[0-9]+}', [
        'as' => 'checklists.history.detail', 'uses' => 'HistoryController@detailAction'
    ]);
});