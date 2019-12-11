<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\History;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\ChecklistTemplate;

class ChecklistsTest extends TestCase
{
    /**
     * /checklists [GET]
     * Get list of checklists
     *
     * @return void
     */
    public function testChecklistIndex()
    {
        $this->get('/checklists', ['authorization' => '123456'])
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
                        ]
                ],
            ]);
    }

    /**
     * /checklists/{checklist_id} [GET]
     * Get checklist by given checklist_id. Note: We can include all items in checklist with by passing include=items
     *
     * @return void
     */
    public function testChecklistGet()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $this->get(
                '/checklists/'.$checklist_id, 
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    'type',
                    'id',
                    'attributes',
                    'links'
                ],
            ]);
    }

    /**
     * /checklists/{checklist_id} [PATCH]
     * Update checklist by given checklist_id
     *
     * @return void
     */
    public function testChecklistUpdate()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $parameters = '{"data":{"type":"checklists","id":1,"attributes":{"object_domain":"contact","object_id":"1","description":"Need to verify this guy house.","is_completed":false,"completed_at":null,"created_at":"2018-01-25T07:50:14+00:00"},"links":{"self":"https://dev-kong.command-api.kw.com/checklists/50127"}}}';
        $this->json(
                'PATCH',
                '/checklists/'.$checklist_id,
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' => [
                        "id",
                        "type",
                        "id",
                        "attributes",
                        "links"
                    ]
                ]);
    }

    /**
     * /checklists/{checklist_id} [DELETE]
     * Delete checklist by given checklist_id
     *
     * @return void
     */
    public function testChecklistDelete()
    {
        $checklist_id = App\Models\Checklist::all()->random()->id;
        $this->delete('/checklists/' . $checklist_id, [], ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status',
                'message'
            ]);
    }

    /**
     * /checklists/ [POST]
     * This creates a Checklist object
     *
     * @return void
     */
    public function testChecklistStore()
    {
        $parameters = '{"data":{"attributes":{"object_domain":"contact","object_id":"1","due":"2019-01-25T07:50:14+00:00","urgency":1,"description":"Need to verify this guy house.","items":["Visit his house","Capture a photo","Meet him on the house"],"task_id":"123"}}}';
        $this->json(
                'POST',
                '/checklists',
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' => [
                        "id",
                        "type",
                        "id",
                        "attributes",
                        "links"
                    ]
                ]);
    }
}
