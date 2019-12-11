<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Repositories\ChecklistRepository;
use App\Http\Transformers\ChecklistTransformer;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    private $checklistRepository;

    public function __construct(ChecklistRepository $checklistRepository)
    {
        $this->checklistRepository = $checklistRepository;
    }

    public function index(Request $request)
    {
        $query = $request->only(['pagination', 'query']);
        $query = array_merge($query);

        $checklists = $this->checklistRepository->get(
            $query
        );
        $checklistTransformer = new ChecklistTransformer();
        if($request->get('includes') == 'items'){
            $checklistTransformer->includeItems = true;
        }

        return $this->paginator($checklists, $checklistTransformer);
    }

    public function detailAction($checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        return $this->item($checklist, new ChecklistTransformer);
    }

    public function storeAction(Request $request)
    {
        $checklist = $this->checklistRepository->store(
            $request
        );
        return $this->item($checklist, new ChecklistTransformer);
    }

    public function updateAction(Request $request, $checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }
        
        $checklist = $this->checklistRepository->update(
            $request, $checklist
        );
        return $this->item($checklist, new ChecklistTransformer);
    }

    public function deleteAction($checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $checklist = $this->checklistRepository->delete(
            $checklist
        );
        return response()->json(['status' => 204, "message" => "template deleted"]);
    }
}
