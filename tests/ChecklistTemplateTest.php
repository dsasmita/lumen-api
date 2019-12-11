<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ChecklistTemplateTest extends TestCase
{
    /**
     * /checklists/templates [GET]
     * List all checklists templates
     *
     * @return void
     */
    public function testChecklistTemplatesIndex()
    {
        $this->get('/checklists/templates', ['authorization' => '123456'])
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
     * /checklists/templates [POST]
     * Create checklist template
     *
     * @return void
     */
    public function testChecklistTemplatesStore()
    {
        $parameters = '{"data":{"attributes":{"name":"foo template","checklist":{"description":"my checklist","due_interval":3,"due_unit":"hour"},"items":[{"description":"my foo item","urgency":2,"due_interval":40,"due_unit":"minute"},{"description":"my bar item","urgency":3,"due_interval":30,"due_unit":"minute"}]}}}';
        $this->json(
                'POST',
                '/checklists/templates',
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'data' => [
                        "id",
                        "attributes" => [
                            "name",
                            "checklist",
                            "items"
                        ]
                    ]
                ]);
    }

    /**
     * /checklists/templates/{template_id} [GET]
     * Get checklist template by given template_id
     *
     * @return void
     */
    public function testChecklistTemplatesGet()
    {
        $template_id = 1;
        $this->get('/checklists/templates/' . $template_id, ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => [
                    "type",
                    "id",
                    "attributes" => [
                        "name",
                        "items",
                        "checklist"
                    ],
                    "links"
                ]
            ]);
    }

    /**
     * /checklists/templates/{template_id} [PATCH]
     * Edit Checklist Template by given template_id
     *
     * @return void
     */
    public function testChecklistTemplatesUpdate()
    {
        $template_id = App\Models\ChecklistTemplate::all()->random()->id;
        $parameters = '{"data":{"name":"foo template","checklist":{"description":"my checklist","due_interval":3,"due_unit":"hour"},"items":[{"description":"my foo item","urgency":2,"due_interval":40,"due_unit":"minute"},{"description":"my bar item","urgency":3,"due_interval":30,"due_unit":"minute"}]}}';
            $this->json(
                    'PATCH',
                    '/checklists/templates/' . $template_id,
                    json_decode($parameters, true),
                    ['authorization' => '123456']
                )
                ->seeStatusCode(200)
                ->seeJsonStructure([
                        'data' => [
                            "type",
                            "id",
                            "attributes" => [
                                "name",
                                "items",
                                "checklist"
                            ]
                        ]
                    ]);
    }

    /**
     * /checklists/templates/{template_id} [DELETE]
     * Edit Checklist Template by given template_id
     *
     * @return void
     */
    public function testChecklistTemplatesDelete()
    {
        $template_id = App\Models\ChecklistTemplate::all()->random()->id;
        $this->delete('/checklists/templates/' . $template_id, ['authorization' => '123456'])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'status'
            ]);
    }

    /**
     * /checklists/templates/{template_id}/assigns [POST]
     * Assign bulk checklists template by given template_id to many domains
     *
     * @return void
     */
    public function testChecklistTemplatesAssigns()
    {
        $template_id = 1;
        
        $parameters = '{"data":[{"attributes":{"object_id":1,"object_domain":"deals"}},{"attributes":{"object_id":2,"object_domain":"deals"}},{"attributes":{"object_id":3,"object_domain":"deals"}}]}';
        $this->json(
                'POST',
                '/checklists/templates/' . $template_id. '/assigns',
                json_decode($parameters, true),
                ['authorization' => '123456']
            )
            ->seeStatusCode(200)
            ->seeJsonStructure([
                    'meta' => [
                        'count',
                        'total'
                    ],
                    'data' => [
                        '*' => [
                        ]
                    ],
                    'included' => [
                        '*' => [
                        ]
                    ]
                ]);
    }
}
