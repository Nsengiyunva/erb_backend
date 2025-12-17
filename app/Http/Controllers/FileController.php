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


    public function countryList() {
        return response()->download( public_path('nec_logo.png'), "User File" );
    }

    public function index() {
        $files = StaffFile::all();
        return response()->json( [ 
            "status" => "success", 
            "count" => count( $files ),
            "data" => $files
        ] );
    }

    public function getByReferenceId( $id ) {
        $files = StaffFile::where( "reference_id", $id  )->get();
        return response()->json( [ 
            "success" => true,
            "data" => $files
        ] );
    }

    public function upload( Request $request ) {
        $filesName = [];
        $response = [];

        $validator = Validator::make( $request->all(), [
            "files" => "required",
            // "files.*" => "required|mimes:pdf,doc,docx,ppt,xlsx,csv,xls|max:3000"
        ] );

        if( $validator->fails() ) {
            return response()->json( [ 
                "status" => "failed", 
                "message" => "Validation error",
                "errors" => $validator->errors()
            ] );
        }

        if( $request->has( "files" ) ) {
            $destinationPath = public_path().'/uploads';

            if (!file_exists($destinationPath)) {
                File::makeDirectory($destinationPath, $mode = 0755, true, true);
            }

            $ids = [];

            //submit an array of files to the api
            foreach( $request->file( "files" ) as $key => $file ) {
                $filename = time().rand().".".$file->getClientOriginalExtension();
                $file->move( $destinationPath, $filename );

                $staff = StaffFile::create( [
                    "file_name" => $filename,
                    "name" => $request->name,
                    "size" => $request->size,
                    "reference_id" => $request->reference_id,
                    "slug" => $request->slug,
                    "title" => $request->title
                ] );
                $response[ "name" ] = $file;
            }
            //display a response
            $response[ "status" ] = "success";
            $response[ "message" ] = "Success! files(s) uploaded";
            // $response[ "id" ] = $staff->id;
        }
        return response()->json( [
            'response' => $response
        ] );
    } 

    public function getFileDownload( $filename ) {
        $file = public_path()."/uploads"."/".$filename;

        $headers = [
            "Content-Type" => "application/pdf"
        ];

        return response()->download( $file, "File1.pdf", $headers );
    }

    public function uploadImages( Request $request ) {
        $imagesName = [];
        $response = [];

        $validator = Validator::make($request->all(),
            [
                'images' => 'required',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]
        );

        if($validator->fails()) {
            return response()->json( [ 
                "status" => "failed", 
                "message" => "Validation error", 
                "errors" => $validator->errors()
            ] );
        }

        if( $request->has('images') ) {
            foreach($request->file('images') as $image) {
                $filename = time().rand(3). '.'.$image->getClientOriginalExtension();
                $image->move( 'uploads/', $filename );

                Image::create([
                    'image_name' => $filename
                ]);
            }

            $response["status"] = "successs";
            $response["message"] = "Success! image(s) uploaded";
        }

        else {
            $response["status"] = "failed";
            $response["message"] = "Failed! image(s) not uploaded";
        }
        return response()->json( $response );
    }

    public function uploadCertificationFiles( Request $request ) {
        
    }
}