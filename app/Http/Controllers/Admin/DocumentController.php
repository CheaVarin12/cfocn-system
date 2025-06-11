<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\Document;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class DocumentController extends Controller
{
    //
    private $document;
    private $folder;
    private $fileStorePath;
    private $perPage;

    public function __construct()
    {
        $this->document = 'documents';
        $this->folder = 'document_folders';
        $this->fileStorePath = public_path('documents');
        $this->perPage = 20;
        $this->middleware('permission:document-pac-view', ['only' => ['indexPac']]);
        $this->middleware('permission:document-invoice-view', ['only' => ['indexInvoice']]);
        $this->middleware('permission:document-receipt-view', ['only' => ['indexReceipt']]);
        $this->middleware('permission:document-contract-view', ['only' => ['indexContract']]);
        
        // $this->middleware('permission:document-upload-file', ['only' => ['getFilesPac','uploadFilePac','uploadPac','getFilesInvoice','uploadFileInvoice','uploadInvoice','getFilesReceipt','uploadFileReceipt','uploadReceipt','getFilesContract','uploadFileContract','uploadContract']]);
        // $this->middleware('permission:document-create-folder', ['only' => ['getFoldersPac','createFolderPac','getFoldersInvoice','createFolderInvoice','getFoldersReceipt','createFolderReceipt','getFoldersContract','createFolderContract']]);
        // $this->middleware('permission:document-rename-folder', ['only' => ['renameFolderPac','renameFolderInvoice','renameFolderReceipt','renameFolderContract']]);
        // $this->middleware('permission:document-delete', ['only' => ['deleteFilePac ','deleteFolderPac ','deleteAllPac','deleteFileInvoice','deleteFolderInvoice','deleteAllInvoice','deleteFileReceipt','deleteFolderReceipt','deleteAllReceipt','deleteFileContract','deleteFolderContract','deleteAllContract']]);
        // $this->middleware('permission:document-restore', ['only' => ['restoreAllPac','restoreAllInvoice','restoreAllReceipt','restoreAllContract']]);
    }

    protected $layout = 'admin::pages.document.';

    public function indexPac(Request $req)
    {
        Log::info("Start: Admin/DocumentController > indexPac | admin: ");
        try {
            return view($this->layout . 'pac.index');
        } catch (Exception $error) {
            Log::error("Error: Admin/DocumentController > index | message: ". $error->getMessage());
        }
    }

    public function firstPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > firstPac | admin: ");
        try {
            $data['folders'] = DB::table($this->folder)->where('type', 'pac')
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                                $query->whereParentId(request('folder_id'));
                            })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                                $query->whereNull('parent_id');
                            })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->get();

            $data['files'] = DB::table($this->document)->where('type', 'pac')
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                                $query->whereFolderId(request('folder_id'));
                            })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                                $query->whereNull('folder_id');
                            })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage);

            $data['base_path'] = asset('documents');
            return response()->json($data);
        } catch (Exception $error) {
            Log::error("Error: Admin/DocumentController > firstPac | message: ". $error->getMessage());
        }
    }


    public function getFilesPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > getFilesPac | admin: ".$request);
        try {
            $data['files'] = DB::table($this->document)
                ->when(request('q'), function ($query) {
                    $query->where('name', 'like', '%' . request('q') . '%');
                })
                ->when(!request('only_trash'), function ($query) {
                    $query
                        ->when(request('folder_id'), function ($query) {
                                $query->whereFolderId(request('folder_id'));
                            })
                        ->when(!request('folder_id') && !request('q'), function ($query) {
                                $query->whereNull('folder_id');
                            })->whereNull('deleted_at');
                })
                ->when(request('only_trash'), function ($query) {
                    $query->whereNotNull('deleted_at');
                })
                ->orderByDesc('created_at')
                ->paginate($this->perPage);

            return response()->json($data);
        } catch (Exception $error) {
            Log::error("Error: Admin/DocumentController > getFilesPac | message: ". $error->getMessage());
        }
    }


    public function getFoldersPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > getFoldersPac | admin: ".$request);
        try {
            $folders = DB::table($this->folder);
            if ($request->has('parent_id')) {
                $folders = $folders->where('parent_id', $request->parent_id);
            }
            return response()->json($folders->paginate($request->per_page ?? 10));
        } catch (Exception $error) {
            Log::error("Error: Admin/DocumentController > getFoldersPac | message: ". $error->getMessage());
        }

    }

    public function uploadFilePac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > uploadFilePac | admin: ".$request);
        $validate = Validator::make($request->all(), [
            'file' => 'required',
        ], [
                'file.required' => 'files_required'
            ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }

        $uploadedFile = $this->uploadPac($request->file('file'), $request->folder_name);
        DB::beginTransaction();
        try {
            DB::table($this->document)->insert([
                'folder_id' => $request->folder_id ?? null,
                'name' => $uploadedFile->name,
                'name_new' => $uploadedFile->name_new,
                'path' => $uploadedFile->path,
                'extension' => $uploadedFile->extension,
                'type' => 'pac',
                'date_upload' => date('y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'file_uploaded']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/DocumentController > uploadFilePac | message: ". $error->getMessage());
        }
    }


    private function uploadPac($file, ?string $folder_name)
    {
        Log::info("Start: Admin/DocumentController > uploadPac | admin: ");
        $uploadedFiles = new Collection();
        if ($file == null)
            return response()->json(['message' => 'files_empty'], 404);
        try {
            $fileInfo = $this->fileInfoPac($file);
            $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
            $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
            $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
            $uploadedFiles->push((object) [
                // 'folder_id' => $folder_id ?? null,
                'name' => $fileInfo->original_name,
                'name_new' => $fileInfo->file_name,
                'path' => $pathWithFolder,
                'extension' => $fileInfo->file_extension,
            ]);
            return $uploadedFiles->first();
        } catch (Exception $error) {
            $this->rollbackPac($uploadedFiles->pluck('path'));
            Log::error("Error: Admin/DocumentController > uploadPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function rollbackPac($file)
    {
        Log::info("Start: Admin/DocumentController > rollbackPac | admin: ");
        if ($file == null)
            return response()->json(['message' => '_rollback_empty'], 404);
        try {
            File::delete($file);
        } catch (Exception $error) {
            Log::error("Error: Admin/DocumentController > rollbackPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    private function fileInfoPac($file)
    {
        Log::info("Start: Admin/DocumentController > fileInfoPac | admin: ");
        try{
            if (!is_file($file))
            return response()->json(['message' => 'not_file'], 404);
        $fileExtension = $file->getClientOriginalExtension();
        return (object) [
            'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
            'file_extension' => $fileExtension,
            'original_name' => $file->getClientOriginalName(),
            // 'is_document' => in_array($fileExtension, ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']),
        ]; 
        }catch(Exception $error){
            Log::error("Error: Admin/DocumentController > fileInfoPac | message: ". $error->getMessage());
        }
       
    }

    public function deleteFilePac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > deleteFilePac | admin: ".$request);
        $file = DB::table($this->document)->find($request->file_id);
        if ($file == null)
            return response()->json(['message' => 'file_not_found'], 404);
            DB::beginTransaction();
        try {
            if ($request->to_trash && $request->to_trash == 'true') {
                DB::table($this->document)->whereId($request->file_id)->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table($this->document)->whereId($request->file_id)->delete();
                File::delete($this->fileStorePath . $file->path);
            }
            DB::commit();
            return response()->json(['message' => 'file_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/DocumentController > deleteFilePac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
    public function createFolderPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > createFolderPac | admin: ".$request);
        $validate = Validator::make($request->all(), [
            'name' => [
                'bail',
                'required',
                Rule::unique('document_folders')
                    ->where(function ($query) {
                        $query
                            ->when(request('parent_id'), function ($query) {
                                            $query->where('parent_id', request('parent_id'));
                                        })
                            ->when(!request('parent_id'), function ($query) {
                                            $query->whereNull('parent_id');
                                        });
                    })
                    ->where('name', request('name'))
            ],
        ], [
                'name.required' => 'Please input this field',
                'name.unique' => 'Folder name is already exists',
            ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        DB::beginTransaction();
        try {
            DB::table($this->folder)->insert([
                'parent_id' => $request->parent_id ?? null,
                'name' => $request->name,
                'is_hidden' => 0,
                'type' => 'pac',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'folder_created']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/DocumentController > createFolderPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }


    public function renameFolderPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > renameFolderPac | admin: ".$request);
        $validate = Validator::make($request->all(), [
            'name' => [
                'bail',
                'required',
                Rule::unique('document_folders')
                    ->ignore(request('folder_id'), 'id')
                    ->where(function ($query) {
                        $query
                            ->when(request('parent_id'), function ($query) {
                                            $query->where('parent_id', request('parent_id'));
                                        })
                            ->when(!request('parent_id'), function ($query) {
                                            $query->whereNull('parent_id');
                                        });
                    })
                    ->where('name', request('name'))
            ],
        ], [
                'name.required' => 'Please input this field',
                'name.unique' => 'Folder name is already exists',
            ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()], 422);
        }
        DB::beginTransaction();
        try {
            DB::table($this->folder)->where('id', request('folder_id'))->update([
                'name' => $request->name,
                'updated_at' => now(),
            ]);
            DB::commit();
            return response()->json(['message' => 'folder_updated']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/DocumentController > renameFolderPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }


    public function deleteFolderPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > deleteFolderPac | admin: ".$request);
        $file = DB::table($this->folder)->find($request->folder_id);
        if ($file == null)
            return response()->json(['message' => 'folder_not_found'], 404);
        // if ($file->user_id != auth()->id()) return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
        try {
            if ($request->to_trash && $request->to_trash == 'true') {
                DB::table($this->folder)->whereId($request->folder_id)->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table($this->folder)->whereId($request->folder_id)->delete();
            }
            DB::commit();
            return response()->json(['message' => 'folder_deleted']);
        } catch (Exception $error) {
            DB::rollback();
            Log::error("Error: Admin/DocumentController > deleteFolderPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function deleteAllPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > deleteAllPac | admin: ".$request);
        if (!isset($request->all) && !isset($request->data))
            return response()->json(['message' => 'data_not_found'], 404);
        DB::beginTransaction();
        try {
            if (isset($request->all)) {
                $files = DB::table($this->document)->whereNotNull('deleted_at')->get();
                DB::table($this->document)->whereNotNull('deleted_at')->delete();
                DB::table($this->folder)->whereNotNull('deleted_at')->delete();
                foreach ($files as $file) {
                    File::delete($this->fileStorePath . $file->path);
                }
            } else {
                foreach ($request->data as $item) {
                    if (property_exists((object) $item, 'folder_id')) {
                        DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                        File::delete($this->fileStorePath . $item['path']);
                    } else {
                        DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'all_deleted']);
        } catch (Exception $error) {
            DB::rollBack();
            Log::error("Error: Admin/DocumentController > deleteAllPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function restoreAllPac(Request $request)
    {
        Log::info("Start: Admin/DocumentController > restoreAllPac | admin: ".$request);
        if (!isset($request->all) && !isset($request->data))
            return response()->json(['message' => 'data_not_found'], 404);
        DB::beginTransaction();
        try {
            $restoreData = [
                'deleted_at' => null,
                'updated_at' => now(),
            ];
            if (isset($request->all)) {
                DB::table($this->document)->whereNotNull('deleted_at')->update($restoreData);
                DB::table($this->folder)->whereNotNull('deleted_at')->update($restoreData);
            } else {
                foreach ($request->data as $item) {
                    if (property_exists((object) $item, 'folder_id')) {
                        DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                    } else {
                        DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'all_restored']);
        } catch (Exception $error) {
            DB::rollBack();
            Log::error("Error: Admin/DocumentController > restoreAllPac | message: ". $error->getMessage());
            return response()->json(['message' => $error->getLine()], 500);
        }
    }




//invoice


public function indexInvoice(Request $req)
{
    Log::info("Start: Admin/DocumentController > indexInvoice | admin: ".$req);
    try {
        return view($this->layout . 'invoice.index');
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > indexInvoice | message: ". $error->getMessage());
    }
}

public function firstInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > firstInvoice | admin: ".$request);
    try {
        $data['folders'] = DB::table($this->folder)->where('type', 'invoice')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereParentId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('parent_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->get();

        $data['files'] = DB::table($this->document)->where('type', 'invoice')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $data['base_path'] = asset('documents');
        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > firstInvoice | message: ". $error->getMessage());
    }
}


public function getFilesInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFilesInvoice | admin: ".$request);
    try {
        $data['files'] = DB::table($this->document)
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFilesInvoice | message: ". $error->getMessage());
    }
}


public function getFoldersInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFoldersInvoice | admin: ".$request);
    try {
        $folders = DB::table($this->folder);
        if ($request->has('parent_id')) {
            $folders = $folders->where('parent_id', $request->parent_id);
        }
        return response()->json($folders->paginate($request->per_page ?? 10));
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFoldersInvoice | message: ". $error->getMessage());
    }

}

public function uploadFileInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > uploadFileInvoice | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'file' => 'required',
    ], [
            'file.required' => 'files_required'
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }

    $uploadedFile = $this->uploadPac($request->file('file'), $request->folder_name);
    DB::beginTransaction();
    try {
        DB::table($this->document)->insert([
            'folder_id' => $request->folder_id ?? null,
            'name' => $uploadedFile->name,
            'name_new' => $uploadedFile->name_new,
            'path' => $uploadedFile->path,
            'extension' => $uploadedFile->extension,
            'type' => 'invoice',
            'date_upload' => date('y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'file_uploaded']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > uploadFileInvoice | message: ". $error->getMessage());
    }
}


private function uploadInvoice($file, ?string $folder_name)
{
    Log::info("Start: Admin/DocumentController > uploadInvoice | admin: ");
    $uploadedFiles = new Collection();
    if ($file == null)
        return response()->json(['message' => 'files_empty'], 404);
    try {
        $fileInfo = $this->fileInfoPac($file);
        $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
        $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
        $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
        $uploadedFiles->push((object) [
            // 'folder_id' => $folder_id ?? null,
            'name' => $fileInfo->original_name,
            'name_new' => $fileInfo->file_name,
            'path' => $pathWithFolder,
            'extension' => $fileInfo->file_extension,
        ]);
        return $uploadedFiles->first();
    } catch (Exception $error) {
        $this->rollbackPac($uploadedFiles->pluck('path'));
        Log::error("Error: Admin/DocumentController > uploadInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function rollbackInvoice($file)
{
    Log::info("Start: Admin/DocumentController > rollbackInvoice | admin: ");
    if ($file == null)
        return response()->json(['message' => '_rollback_empty'], 404);
    try {
        File::delete($file);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > rollbackInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function fileInfoInvoice($file)
{
    Log::info("Start: Admin/DocumentController > fileInfoInvoice | admin: ");
    try{
        if (!is_file($file))
        return response()->json(['message' => 'not_file'], 404);
    $fileExtension = $file->getClientOriginalExtension();
    return (object) [
        'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
        'file_extension' => $fileExtension,
        'original_name' => $file->getClientOriginalName(),
        // 'is_document' => in_array($fileExtension, ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']),
    ]; 
    }catch(Exception $error){
        Log::error("Error: Admin/DocumentController > fileInfoInvoice | message: ". $error->getMessage());
    }
   
}

public function deleteFileInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFileInvoice | admin: ".$request);
    $file = DB::table($this->document)->find($request->file_id);
    if ($file == null)
        return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->document)->whereId($request->file_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->document)->whereId($request->file_id)->delete();
            File::delete($this->fileStorePath . $file->path);
        }
        DB::commit();
        return response()->json(['message' => 'file_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFileInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}
public function createFolderInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > createFolderInvoice | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->insert([
            'parent_id' => $request->parent_id ?? null,
            'name' => $request->name,
            'is_hidden' => 0,
            'type' => 'invoice',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_created']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > createFolderInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);

    }
}


public function renameFolderInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > renameFolderInvoice | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->ignore(request('folder_id'), 'id')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->where('id', request('folder_id'))->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_updated']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > renameFolderInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}


public function deleteFolderInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFolderInvoice | admin: ".$request);
    $file = DB::table($this->folder)->find($request->folder_id);
    if ($file == null)
        return response()->json(['message' => 'folder_not_found'], 404);
    // if ($file->user_id != auth()->id()) return response()->json(['message' => 'file_not_found'], 404);
    DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->folder)->whereId($request->folder_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->folder)->whereId($request->folder_id)->delete();
        }
        DB::commit();
        return response()->json(['message' => 'folder_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFolderInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function deleteAllInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteAllInvoice | admin: ".$request);
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        if (isset($request->all)) {
            $files = DB::table($this->document)->whereNotNull('deleted_at')->get();
            DB::table($this->document)->whereNotNull('deleted_at')->delete();
            DB::table($this->folder)->whereNotNull('deleted_at')->delete();
            foreach ($files as $file) {
                File::delete($this->fileStorePath . $file->path);
            }
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                    File::delete($this->fileStorePath . $item['path']);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_deleted']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > deleteAllInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function restoreAllInvoice(Request $request)
{
    Log::info("Start: Admin/DocumentController > restoreAllInvoice | admin: ".$request);
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        $restoreData = [
            'deleted_at' => null,
            'updated_at' => now(),
        ];
        if (isset($request->all)) {
            DB::table($this->document)->whereNotNull('deleted_at')->update($restoreData);
            DB::table($this->folder)->whereNotNull('deleted_at')->update($restoreData);
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_restored']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > restoreAllInvoice | message: ". $error->getMessage());
        return response()->json(['message' => $error->getLine()], 500);
    }
}

//receipt ===========================================================================================

public function indexReceipt(Request $req)
{
    Log::info("Start: Admin/DocumentController > indexReceipt | admin: ".$req);
    try {
        return view($this->layout . 'receipt.index');
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > indexReceipt | message: ". $error->getMessage());
    }
}

public function firstReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > indexReceipt | admin: ".$request);
    try {
        $data['folders'] = DB::table($this->folder)->where('type', 'receipt')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereParentId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('parent_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->get();

        $data['files'] = DB::table($this->document)->where('type', 'receipt')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $data['base_path'] = asset('documents');
        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > indexReceipt | message: ". $error->getMessage());
    }
}


public function getFilesReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFilesReceipt | admin: ".$request);
    try {
        $data['files'] = DB::table($this->document)
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFilesReceipt | message: ". $error->getMessage());
    }
}


public function getFoldersReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFoldersReceipt | admin: ".$request);
    try {
        $folders = DB::table($this->folder);
        if ($request->has('parent_id')) {
            $folders = $folders->where('parent_id', $request->parent_id);
        }
        return response()->json($folders->paginate($request->per_page ?? 10));
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFoldersReceipt | message: ". $error->getMessage());
    }

}

public function uploadFileReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > uploadFileReceipt | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'file' => 'required',
    ], [
            'file.required' => 'files_required'
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }

    $uploadedFile = $this->uploadPac($request->file('file'), $request->folder_name);
    DB::beginTransaction();
    try {
        DB::table($this->document)->insert([
            'folder_id' => $request->folder_id ?? null,
            'name' => $uploadedFile->name,
            'name_new' => $uploadedFile->name_new,
            'path' => $uploadedFile->path,
            'extension' => $uploadedFile->extension,
            'type' => 'receipt',
            'date_upload' => date('y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'file_uploaded']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > uploadFileReceipt | message: ". $error->getMessage());
    }
}


private function uploadReceipt($file, ?string $folder_name)
{
    Log::info("Start: Admin/DocumentController > uploadReceipt | admin: ");
    $uploadedFiles = new Collection();
    if ($file == null)
        return response()->json(['message' => 'files_empty'], 404);
    try {
        $fileInfo = $this->fileInfoPac($file);
        $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
        $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
        $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
        $uploadedFiles->push((object) [
            // 'folder_id' => $folder_id ?? null,
            'name' => $fileInfo->original_name,
            'name_new' => $fileInfo->file_name,
            'path' => $pathWithFolder,
            'extension' => $fileInfo->file_extension,
        ]);
        return $uploadedFiles->first();
    } catch (Exception $error) {
        $this->rollbackPac($uploadedFiles->pluck('path'));
        Log::error("Error: Admin/DocumentController > uploadReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function rollbackReceipt($file)
{
    Log::info("Start: Admin/DocumentController > rollbackReceipt | admin: ");
    if ($file == null)
        return response()->json(['message' => '_rollback_empty'], 404);
    try {
        File::delete($file);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > rollbackReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function fileInfoReceipt($file)
{
    Log::info("Start: Admin/DocumentController > fileInfoReceipt | admin: ");
    try{
        if (!is_file($file))
        return response()->json(['message' => 'not_file'], 404);
    $fileExtension = $file->getClientOriginalExtension();
    return (object) [
        'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
        'file_extension' => $fileExtension,
        'original_name' => $file->getClientOriginalName(),
        // 'is_document' => in_array($fileExtension, ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']),
    ]; 
    }catch(Exception $error){
        Log::error("Error: Admin/DocumentController > fileInfoReceipt | message: ". $error->getMessage());
    }
   
}

public function deleteFileReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFileReceipt | admin: ".$request);
    $file = DB::table($this->document)->find($request->file_id);
    if ($file == null)
        return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->document)->whereId($request->file_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->document)->whereId($request->file_id)->delete();
            File::delete($this->fileStorePath . $file->path);
        }
        DB::commit();
        return response()->json(['message' => 'file_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFileReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}
public function createFolderReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > createFolderReceipt | admin: ");
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->insert([
            'parent_id' => $request->parent_id ?? null,
            'name' => $request->name,
            'is_hidden' => 0,
            'type' => 'receipt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_created']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > createFolderReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);

    }
}


public function renameFolderReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > renameFolderReceipt | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->ignore(request('folder_id'), 'id')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->where('id', request('folder_id'))->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_updated']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > renameFolderReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}


public function deleteFolderReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFolderReceipt | admin: ".$request);
    $file = DB::table($this->folder)->find($request->folder_id);
    if ($file == null)
        return response()->json(['message' => 'folder_not_found'], 404);
    // if ($file->user_id != auth()->id()) return response()->json(['message' => 'file_not_found'], 404);
    DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->folder)->whereId($request->folder_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->folder)->whereId($request->folder_id)->delete();
        }
        DB::commit();
        return response()->json(['message' => 'folder_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFolderReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function deleteAllReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteAllReceipt | admin: ".$request);
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        if (isset($request->all)) {
            $files = DB::table($this->document)->whereNotNull('deleted_at')->get();
            DB::table($this->document)->whereNotNull('deleted_at')->delete();
            DB::table($this->folder)->whereNotNull('deleted_at')->delete();
            foreach ($files as $file) {
                File::delete($this->fileStorePath . $file->path);
            }
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                    File::delete($this->fileStorePath . $item['path']);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_deleted']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > deleteAllReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function restoreAllReceipt(Request $request)
{
    Log::info("Start: Admin/DocumentController > restoreAllReceipt | admin: ");
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        $restoreData = [
            'deleted_at' => null,
            'updated_at' => now(),
        ];
        if (isset($request->all)) {
            DB::table($this->document)->whereNotNull('deleted_at')->update($restoreData);
            DB::table($this->folder)->whereNotNull('deleted_at')->update($restoreData);
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_restored']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > restoreAllReceipt | message: ". $error->getMessage());
        return response()->json(['message' => $error->getLine()], 500);
    }
}


//Contract ===========================================================================================

public function indexContract(Request $req)
{
    Log::info("Start: Admin/DocumentController > indexContract | admin: ".$req);
    try {
        return view($this->layout . 'contract.index');
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > indexContract | message: ". $error->getMessage());
    }
}

public function firstContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > firstContract | admin: ".$request);
    try {
        $data['folders'] = DB::table($this->folder)->where('type', 'contract')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereParentId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('parent_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->get();

        $data['files'] = DB::table($this->document)->where('type', 'contract')
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $data['base_path'] = asset('documents');
        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > firstContract | message: ". $error->getMessage());
    }
}


public function getFilesContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFilesContract | admin: ".$request);
    try {
        $data['files'] = DB::table($this->document)
            ->when(request('q'), function ($query) {
                $query->where('name', 'like', '%' . request('q') . '%');
            })
            ->when(!request('only_trash'), function ($query) {
                $query
                    ->when(request('folder_id'), function ($query) {
                            $query->whereFolderId(request('folder_id'));
                        })
                    ->when(!request('folder_id') && !request('q'), function ($query) {
                            $query->whereNull('folder_id');
                        })->whereNull('deleted_at');
            })
            ->when(request('only_trash'), function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return response()->json($data);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFilesContract | message: ". $error->getMessage());
    }
}


public function getFoldersContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > getFoldersContract | admin: ".$request);
    try {
        $folders = DB::table($this->folder);
        if ($request->has('parent_id')) {
            $folders = $folders->where('parent_id', $request->parent_id);
        }
        return response()->json($folders->paginate($request->per_page ?? 10));
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > getFoldersContract | message: ". $error->getMessage());
    }

}

public function uploadFileContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > uploadFileContract | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'file' => 'required',
    ], [
            'file.required' => 'files_required'
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }

    $uploadedFile = $this->uploadPac($request->file('file'), $request->folder_name);
    DB::beginTransaction();
    try {
        DB::table($this->document)->insert([
            'folder_id' => $request->folder_id ?? null,
            'name' => $uploadedFile->name,
            'name_new' => $uploadedFile->name_new,
            'path' => $uploadedFile->path,
            'extension' => $uploadedFile->extension,
            'type' => 'contract',
            'date_upload' => date('y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'file_uploaded']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > uploadFileContract | message: ". $error->getMessage());
    }
}


private function uploadContract($file, ?string $folder_name)
{
    Log::info("Start: Admin/DocumentController > uploadContract | admin: ");
    $uploadedFiles = new Collection();
    if ($file == null)
        return response()->json(['message' => 'files_empty'], 404);
    try {
        $fileInfo = $this->fileInfoPac($file);
        $fullPath = $folder_name ? $this->fileStorePath . "/$folder_name" : $this->fileStorePath;
        $pathWithFolder = ($folder_name ? "/$folder_name" : '') . "/$fileInfo->file_name.$fileInfo->file_extension";
        $file->move($fullPath, "$fileInfo->file_name.$fileInfo->file_extension");
        $uploadedFiles->push((object) [
            // 'folder_id' => $folder_id ?? null,
            'name' => $fileInfo->original_name,
            'name_new' => $fileInfo->file_name,
            'path' => $pathWithFolder,
            'extension' => $fileInfo->file_extension,
        ]);
        return $uploadedFiles->first();
    } catch (Exception $error) {
        $this->rollbackPac($uploadedFiles->pluck('path'));
        Log::error("Error: Admin/DocumentController > uploadContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function rollbackContract($file)
{
    Log::info("Start: Admin/DocumentController > rollbackContract | admin: ");
    if ($file == null)
        return response()->json(['message' => '_rollback_empty'], 404);
    try {
        File::delete($file);
    } catch (Exception $error) {
        Log::error("Error: Admin/DocumentController > rollbackContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

private function fileInfoContract($file)
{
    Log::info("Start: Admin/DocumentController > fileInfoContract | admin: ");
    try{
        if (!is_file($file))
        return response()->json(['message' => 'not_file'], 404);
    $fileExtension = $file->getClientOriginalExtension();
    return (object) [
        'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . Str::uuid(),
        'file_extension' => $fileExtension,
        'original_name' => $file->getClientOriginalName(),
        // 'is_document' => in_array($fileExtension, ['doc', 'docx', 'pdf', 'txt', 'xls', 'xlsx', 'ppt', 'pptx']),
    ]; 
    }catch(Exception $error){
        Log::error("Error: Admin/DocumentController > fileInfoContract | message: ". $error->getMessage());
    }
   
}

public function deleteFileContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFileContract | admin: ".$request);
    $file = DB::table($this->document)->find($request->file_id);
    if ($file == null)
        return response()->json(['message' => 'file_not_found'], 404);
        DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->document)->whereId($request->file_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->document)->whereId($request->file_id)->delete();
            File::delete($this->fileStorePath . $file->path);
        }
        DB::commit();
        return response()->json(['message' => 'file_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFileContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}
public function createFolderContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > createFolderContract | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->insert([
            'parent_id' => $request->parent_id ?? null,
            'name' => $request->name,
            'is_hidden' => 0,
            'type' => 'contract',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_created']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > createFolderContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);

    }
}


public function renameFolderContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > renameFolderContract | admin: ".$request);
    $validate = Validator::make($request->all(), [
        'name' => [
            'bail',
            'required',
            Rule::unique('document_folders')
                ->ignore(request('folder_id'), 'id')
                ->where(function ($query) {
                    $query
                        ->when(request('parent_id'), function ($query) {
                                        $query->where('parent_id', request('parent_id'));
                                    })
                        ->when(!request('parent_id'), function ($query) {
                                        $query->whereNull('parent_id');
                                    });
                })
                ->where('name', request('name'))
        ],
    ], [
            'name.required' => 'Please input this field',
            'name.unique' => 'Folder name is already exists',
        ]);

    if ($validate->fails()) {
        return response()->json(['message' => $validate->errors()], 422);
    }
    DB::beginTransaction();
    try {
        DB::table($this->folder)->where('id', request('folder_id'))->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);
        DB::commit();
        return response()->json(['message' => 'folder_updated']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > renameFolderContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}


public function deleteFolderContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteFolderContract | admin: ".$request);
    $file = DB::table($this->folder)->find($request->folder_id);
    if ($file == null)
        return response()->json(['message' => 'folder_not_found'], 404);
    // if ($file->user_id != auth()->id()) return response()->json(['message' => 'file_not_found'], 404);
    DB::beginTransaction();
    try {
        if ($request->to_trash && $request->to_trash == 'true') {
            DB::table($this->folder)->whereId($request->folder_id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($this->folder)->whereId($request->folder_id)->delete();
        }
        DB::commit();
        return response()->json(['message' => 'folder_deleted']);
    } catch (Exception $error) {
        DB::rollback();
        Log::error("Error: Admin/DocumentController > deleteFolderContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function deleteAllContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > deleteAllContract | admin: ".$request);
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        if (isset($request->all)) {
            $files = DB::table($this->document)->whereNotNull('deleted_at')->get();
            DB::table($this->document)->whereNotNull('deleted_at')->delete();
            DB::table($this->folder)->whereNotNull('deleted_at')->delete();
            foreach ($files as $file) {
                File::delete($this->fileStorePath . $file->path);
            }
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                    File::delete($this->fileStorePath . $item['path']);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->delete();
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_deleted']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > deleteAllContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getMessage()], 500);
    }
}

public function restoreAllContract(Request $request)
{
    Log::info("Start: Admin/DocumentController > restoreAllContract | admin: ".$request);
    if (!isset($request->all) && !isset($request->data))
        return response()->json(['message' => 'data_not_found'], 404);
    DB::beginTransaction();
    try {
        $restoreData = [
            'deleted_at' => null,
            'updated_at' => now(),
        ];
        if (isset($request->all)) {
            DB::table($this->document)->whereNotNull('deleted_at')->update($restoreData);
            DB::table($this->folder)->whereNotNull('deleted_at')->update($restoreData);
        } else {
            foreach ($request->data as $item) {
                if (property_exists((object) $item, 'folder_id')) {
                    DB::table($this->document)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                } else {
                    DB::table($this->folder)->whereNotNull('deleted_at')->whereId($item['id'])->update($restoreData);
                }
            }
        }
        DB::commit();
        return response()->json(['message' => 'all_restored']);
    } catch (Exception $error) {
        DB::rollBack();
        Log::error("Error: Admin/DocumentController > restoreAllContract | message: ". $error->getMessage());
        return response()->json(['message' => $error->getLine()], 500);
    }
}
}