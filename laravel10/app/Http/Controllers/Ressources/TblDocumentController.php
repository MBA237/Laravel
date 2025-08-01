<?php

namespace App\Http\Controllers\Ressources;

use App\Http\Controllers\Controller;
use App\Models\TblDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\FileUploadService;
use App\Models\TblProjet;

class TblDocumentController extends Controller
{

    private $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @OA\Get(
     *     path="/api/ressources/documents",
     *     summary="Get list of all documents",
     *     tags={"Documents"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of documents",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/TblDocument"))
     *     )
     * )
     */
    public function index()
    {
        $document = TblDocument::all();
        return response()->json($document);
    }

    /**
     * @OA\Post(
     *     path="/api/ressources/documents",
     *     summary="Create a new document",
     *     tags={"Documents"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TblDocument")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document created",
     *         @OA\JsonContent(ref="#/components/schemas/TblDocument")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_doc' => 'required|unique:tbl_documents,nom_doc|max:255',
            'tbl_projet_id' => 'required|exists:tbl_projets,id',
            'document' => 'required|file|mimes:pdf,doc,docx',
            'user_id' => 'required|exists:users,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $project = TblProjet::find($request->tbl_projet_id);
    
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }
    
        if ($project->type == "Projet") {
            $documentUrl = $this->fileUploadService->uploadFile($request->file('document'), 'public/Projets');
        } elseif ($project->type == "Memoire") {
            $documentUrl = $this->fileUploadService->uploadFile($request->file('document'), 'public/Memoires');
        } else {
            $documentUrl = $this->fileUploadService->uploadFile($request->file('document'), 'public/Articles');
        }
    
        $document = TblDocument::create([
            'nom_doc' => $request->nom_doc,
            'lien_doc' => $documentUrl,
            'tbl_projet_id' => $request->tbl_projet_id,
            'user_id' => $request->user_id,
        ]);
    
        return response()->json($document, 201);
    }


    /**
     * @OA\Get(
     *     path="/api/ressources/documents/{id}",
     *     summary="Get a document by ID",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document details",
     *         @OA\JsonContent(ref="#/components/schemas/TblDocument")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $document = TblDocument::where('id', $id)->firstOrFail();
        return response()->json($document);
    }

    /**
     * @OA\Put(
     *     path="/api/ressources/documents/{id}",
     *     summary="Update a document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TblDocument")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document updated",
     *         @OA\JsonContent(ref="#/components/schemas/TblDocument")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nom_doc' => 'required|unique:tbl_documents,nom_doc|max:255',
            'tbl_projet_id' => 'required|exists:tbl_projets,id',
            'document' => 'nullable|file|mimes:pdf,doc,docx',
            'user_id' => 'required|exists:users,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $project = TblProjet::find($request->tbl_projet_id);
    
        $document = TblDocument::where('id', $id)->firstOrFail();
    
        $project = TblProjet::find($request->tbl_projet_id);
    
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }
    
        if ($request->hasFile('document')) {
            $documentUrl = $this->fileUploadService->uploadFile($request->file('document'), 'public/' . ucfirst($project->type) . 's');
            $document->lien_doc = $documentUrl;
        }
    
        $document->nom_doc = $request->nom_doc;
        $document->tbl_projet_id = $request->tbl_projet_id;
        $document->user_id = $request->user_id;
        $document->save();
    
        return response()->json($document);
   }

    

    /**
     * @OA\Delete(
     *     path="/api/ressources/documents/{id}",
     *     summary="Delete a document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Document deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $document = TblDocument::findOrFail($id);
        Storage::disk('public')->delete($document->lien_doc);
        $document->delete();
        return response()->noContent();
    }
}
