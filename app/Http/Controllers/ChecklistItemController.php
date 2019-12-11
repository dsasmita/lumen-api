<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Repositories\ChecklistItemRepository;
use App\Repositories\ChecklistRepository;
use App\Http\Transformers\ChecklistItemTransformer;
use App\Http\Transformers\ChecklistTransformer;
use Illuminate\Http\Request;

class ChecklistItemController extends Controller
{
    private $checklistItemRepository;

    private $checklistRepository;

    public function __construct(
        ChecklistItemRepository $checklistItemRepository,
        ChecklistRepository $checklistRepository
    )
    {
        $this->checklistItemRepository = $checklistItemRepository;
        $this->checklistRepository = $checklistRepository;
    }

    public function index(Request $request)
    {
        $query = $request->only(['pagination', 'query']);
        $query = array_merge($query);

        $checklistsItem = $this->checklistItemRepository->get(
            $query
        );

        return $this->paginator($checklistsItem, new ChecklistItemTransformer());
    }

    public function completeAction(Request $request)
    {
        $itemComplete = $this->checklistItemRepository->complete(
            $request
        );
        return response()->json($itemComplete);
    }

    public function incompleteAction(Request $request)
    {
        $itemComplete = $this->checklistItemRepository->incomplete(
            $request
        );
        return response()->json($itemComplete);
    }

    public function itemListAction($checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }
        $checklistTransformer = new ChecklistTransformer();
        $checklistTransformer->includeItems = true;

        return $this->item($checklist, $checklistTransformer);
    }

    public function storeAction(Request $request, $checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }
        
        $checklistItem = $this->checklistItemRepository->store(
            $request, $checklist_id
        );
        return $this->item($checklistItem, new ChecklistItemTransformer);
    }

    public function itemDetailAction($checklist_id, $item_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        $checklistItem = $this->checklistItemRepository->findByColumn('id', $item_id);
        if(!$checklist || !$checklistItem || ($checklist && $checklistItem && $checklist->id != $checklistItem->checklist_id)){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        return $this->item($checklistItem, new ChecklistItemTransformer);
    }

    public function itemUpdateAction(Request $request, $checklist_id, $item_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        $checklistItem = $this->checklistItemRepository->findByColumn('id', $item_id);
        if(!$checklist || $checklist->id != $checklistItem->checklist_id){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $checklistItem = $this->checklistItemRepository->update(
            $request, $checklistItem
        );

        return $this->item($checklistItem, new ChecklistItemTransformer);
    }

    public function itemDeleteAction($checklist_id, $item_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        $checklistItem = $this->checklistItemRepository->findByColumn('id', $item_id);
        if(!$checklist || !$checklistItem || ( $checklist && $checklistItem && $checklist->id != $checklistItem->checklist_id)){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $checklistItem = $this->checklistItemRepository->delete(
            $checklistItem
        );

        return response()->json(['status' => 204, "message" => "template deleted"]);
    }

    public function itemUpdateBulkAction(Request $request, $checklist_id)
    {
        $checklist = $this->checklistRepository->findByColumn('id', $checklist_id);
        if(!$checklist){
            return response()->json(['status' => 404, 'error' => 'not found']);
        }

        $data = $request->json()->all();

        $itemsStatus = $this->checklistItemRepository->bulkUpdate(
            $request, $checklist
        );
        return response()->json($itemsStatus);
    }

    public function summariesAction()
    {
        $result = $this->checklistItemRepository->summaries();

        return response()->json($result);
    }
}
