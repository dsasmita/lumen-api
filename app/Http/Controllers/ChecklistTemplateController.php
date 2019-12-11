<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistTemplate;
use App\Http\Transformers\ChecklistTemplateTransformer;
use App\Repositories\ChecklistTemplateRepository;
use Illuminate\Http\Request;

class ChecklistTemplateController extends Controller
{
    private $checklistTemplateRepository;

    public function __construct(ChecklistTemplateRepository $checklistTemplateRepository)
    {
        $this->checklistTemplateRepository = $checklistTemplateRepository;
    }

    public function index(Request $request)
    {
        $query = $request->only(['pagination', 'query']);
        $query = array_merge($query);

        $checklists = $this->checklistTemplateRepository->get(
            $query
        );

        return $this->paginator($checklists, new ChecklistTemplateTransformer());
    }

    public function storeAction(Request $request)
    {
        $checklistTemplate = $this->checklistTemplateRepository->store(
            $request
        );
        return $this->item($checklistTemplate, new ChecklistTemplateTransformer);
    }

    public function detailAction($template_id)
    {
        $checklistTemplate = $this->checklistTemplateRepository->findByColumn('id', $template_id);
        if(!$checklistTemplate){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        return $this->item($checklistTemplate, new ChecklistTemplateTransformer);
    }

    public function updateAction(Request $request, $template_id)
    {
        $checklistTemplate = $this->checklistTemplateRepository->findByColumn('id', $template_id);
        if(!$checklistTemplate){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $checklistTemplate = $this->checklistTemplateRepository->update(
            $request, $checklistTemplate
        );
        return $this->item($checklistTemplate, new ChecklistTemplateTransformer);
    }

    public function deleteAction($template_id)
    {
        $checklistTemplate = $this->checklistTemplateRepository->findByColumn('id', $template_id);
        if(!$checklistTemplate){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $checklistTemplate = $this->checklistTemplateRepository->delete(
            $checklistTemplate
        );
        return response()->json(['status' => 204, "message" => "template deleted"]);
    }

    public function assignsAction(Request $request, $template_id)
    {
        $checklistTemplate = $this->checklistTemplateRepository->findByColumn('id', $template_id);
        if(!$checklistTemplate){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $result = $this->checklistTemplateRepository->assigns(
            $request, $checklistTemplate
        );

        return response()->json($result);
    }
}
