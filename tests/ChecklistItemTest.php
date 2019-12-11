<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChecklistItemTest extends TestCase
{
    /**
     * /checklists/items [GET]
     * This endpoint will get all available items.
     *
     * @return void
     */
    public function testChecklistItemIndex()
    {
        $this->get('/checklists/items', ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'meta' => [
                    'count',
                    'total',
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
                'data' => [
                    '*' =>
                        [
                            'type',
                            'id',
                            'attributes',
                            'links'
                        ]
                ],
            ]);
    }

    /**
     * /checklists/complete [POST]
     * Complete item(s)
     *
     * @return void
     */
    public function testChecklistItemComplete()
    {
        $parameters = [
            'data' => [
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ],
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ],
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ]
            ]
        ];
        $this->json(
                'POST',
                '/checklists/complete/',
                $parameters,
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' =>[
                        '*' => [
                            "id",
                            "item_id",
                            "is_completed",
                            "checklist_id"
                        ]
                    ]
                ]);
    }

    /**
     * /checklists/incomplete [POST]
     * Complete item(s)
     *
     * @return void
     */
    public function testChecklistItemIncomplete()
    {
        $parameters = [
            'data' => [
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ],
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ],
                [
                    'item_id' => App\Models\ChecklistItem::all()->random()->id,
                ]
            ]
        ];
        $this->json(
                'POST',
                '/checklists/incomplete/',
                $parameters,
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' =>[
                        '*' => [
                            "id",
                            "item_id",
                            "is_completed",
                            "checklist_id"
                        ]
                    ]
                ]);
    }

    /**
     * /checklists/{checklistId}/items [GET]
     * Get all items by given {checklistId}
     *
     * @return void
     */
    public function testChecklistItemList()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $this->get('/checklists/'.$checklist_id.'/items', ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes' => [
                        'description',
                        'is_completed',
                        'due',
                        'urgency',
                        'completed_at',
                        'created_at',
                        'object_domain',
                        'object_id',
                        'last_update_by',
                        'update_at',
                        'items'
                    ],
                    'links'
                ]
            ]);
    }

    /**
     * /checklists/{checklistId}/items [POST]
     * Create item by given checklistId
     *
     * @return void
     */
    public function testChecklistItemStore()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $parameters = '{"data":{"attribute":{"description":"Need to verify this guy house.","due":"2019-01-19 18:34:51","urgency":"2","assignee_id":123}}}';
        
        $this->json(
                'POST',
                '/checklists/'.$checklist_id.'/items',
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' =>[
                        "type",
                        "id",
                        "attributes",
                        "links"
                    ]
                ]);
    }

    /**
     * /checklists/{checklistId}/items/{itemId} [GET]
     * Get checklist item by given {checklistId} and {itemId}
     *
     * @return void
     */
    public function testChecklistItemGet()
    {
        $checklistItem = App\Models\ChecklistItem::all()->random();
        $checklist_id = $checklistItem->checklist_id;
        $item_id = $checklistItem->id;
        $this->get('/checklists/'.$checklist_id.'/items/'.$item_id, ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes',
                    'links'
                ]
            ]);
    }

    /**
     * /checklists/{checklistId}/items/{itemId} [PATCH]
     * Edit Checklist Item on given {checklistId} and {itemId}
     *
     * @return void
     */
    public function testChecklistItemUpdate()
    {
        $checklistItem = App\Models\ChecklistItem::all()->random();
        $checklist_id = $checklistItem->checklist_id;
        $item_id = $checklistItem->id;
        $parameters = '{"data":{"attribute":{"description":"Need to verify this guy house.","due":"2019-01-19 18:34:51","urgency":"2","assignee_id":123}}}';
        $this->json(
                'PATCH',
                '/checklists/'.$checklist_id.'/items/'. $item_id,
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' =>[
                        "type",
                        "id",
                        "attributes",
                        "links"
                    ]
                ]);
    }

    /**
     * /checklists/{checklistId}/items/{itemId} [DELETE]
     * Delete checklist item by given {checklistId} and {itemId}
     *
     * @return void
     */
    public function testChecklistItemDelete()
    {
        $checklistItem = App\Models\ChecklistItem::all()->random();
        $checklist_id = $checklistItem->checklist_id;
        $item_id = $checklistItem->id;
        $this->delete('/checklists/' . $checklist_id.'/items/'. $item_id, ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status'
            ]);
    }

    /**
     * /checklists/{checklistId}/items/_bulk [POST]
     * 
     *
     * @return void
     */
    public function testChecklistItemUpdateBulk()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $parameters = '{"data":[{"id":"64","action":"update","attributes":{"description":"","due":"2019-01-19 18:34:51","urgency":"2"}},{"id":"205","action":"update","attributes":{"description":"{{data.attributes.description}}","due":"2019-01-19 18:34:51","urgency":"2"}}]}';
        $this->json(
                'POST',
                '/checklists/'.$checklist_id.'/items/_bulk',
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' => 
                        ['*' => [
                            "id",
                            "action",
                            "status"
                        ]
                    ]
                ]);
    }

    /**
     * /checklists/items/summaries [GET]
     * Count summary of checklist’s item
     *
     * @return void
     */
    public function testChecklistSummaries()
    {
        $this->get('/checklists/items/summaries', ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    "today",
                    "past_due",
                    "this_week",
                    "past_week",
                    "this_month",
                    "past_month",
                    "total"
                ]
            ]);
    }
}
