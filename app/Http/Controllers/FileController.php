<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller {
    public function upload( Request $request ) {
        $request->validate([
            'file' => 'required|file'
        ]);

        $path = $request->file( "file" )->store( "uploads", "public" );

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path
        ]);
    }
}