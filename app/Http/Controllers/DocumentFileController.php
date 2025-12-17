<?php

namespace App\Http\Controllers;
use App\User;
use App\Models\DocumentFile;
use App\Models\Requistion;
use App\License;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Url;
use Illuminate\Support\Facades\DB;
use Validator;


use Illuminate\Http\Request;

class DocumentFileController extends Controller
{
    public function storeISO( Request $request ) {
        $file = $request->file( "file" );
        
        if( $file ) { 
            $fileName = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);

            $sql = DB::table('iso')->insert(
            [ 
              'type' => $request->type, 
              'details' => $request->details,
              'title' => $fileName,
              'size' => $request->size,
              'userid' => $request->userid,
              'file' => $fileName,
              'organisation' => $request->organisation,
              'entity' => $request->entity,
              'created_by' => $request->created_by,
              'department' => $request->department,
              'path' => "public/uploads/",
              'status' => $request->status,
              'site' => $request->site,
              'folder' => $request->folder,
              'subfolder' => $request->subfolder,
              'remarks' => $request->remarks,
              'created_at' => now(),
              'updated_at' =>  now()
            ]
          );
        }

        return response()->json( [
            "message" => "Record has been added successfully."
        ] );
    }

    public function downloadISO( Request $request ) {
        $file = public_path()."/uploads/".$request->fileName;
        $headers = [
            'Content-Type' => 'application/pdf',
        ];
        
        return response()->download($file, 'Test.pdf', $headers);
    }

    public function storeMemo( Request $request ) {
        $sql = DB::table('memo')->insert(
            [ 
              'from' => $request->from,
              'to' => $request->to,
              'addressed_to' => $request->addressed_to,
              'remarks' => $request->remarks,
              'userid' => $request->user_id,
              'organisation' => $request->organisation,
              'entity' => $request->entity,
              'created_by' => $request->created_by,
              'department' => $request->department,
              'description' => $request->description,
              'uniqueId' => $request->memoId,
              'status' => $request->status,
              'stage' => $request->stage,
              'subject' => $request->subject,
              'memo_attach_title' => $request->title,
              'memo_attach_file' => $request->file,
              'memo_attach_size' => $request->size,
              'memo_attach_type' => $request->type,
              'memo_attach_details' => $request->details,
              'created_at' => now(),
              'updated_at' =>  now()
            ]
        );

        return response()->json( [
            "message" => "A New Memo has been added successfully."
        ] );
    }

    public function ISOAll() {
        $results = DB::select( "SELECT distinct * FROM iso" );
        return response()->json( $results );
    }
    public function memoAll() {
        $results = DB::select( "SELECT distinct * FROM memo" );
        return response()->json( $results );
    }
    public static function store(Request $request){
        $document = new DocumentFile;
        if( sizeof( $request->documents ) > 0 ){
            foreach( $request->documents as $key => $value){
                $sql = DB::table('documents')->insert(
                    [ 
                      'type' => $value[ 'type' ], 
                      'details' => $value[ 'details' ],
                      'name' => $value[ 'title' ],
                      'size' => $value[ 'size' ],
                      'userid' => $value[ 'userid' ],
                      'file' => $value[ 'file' ],
                      'requisition_id' => $value[ 'id' ],
                      'created_at' => now(),
                      'updated_at' =>  now()
                    ]
                );
           }
            return response()->json([
               "message" => "All the files have been saved"
             ], 201);
        } else {
            return response()->json([
                "message" => "No files were attached."
            ]);
        }
    }
    public function upload( Request $request ){

        $validator = Validator::make( $request->all(), [
            "file" => "required|mimes:pdf,doc,docx,csv,text|max:2048"
        ] );

        if( $validator->fails() ) {
            return response()->json( [ "error" => $validator->errors() ], 401 );
        }

        if( $file = $request->file( "file" ) ) {
            $path = $file->store( "public/staff/documents" );
            $name = $file->getClientOriginalName();

            return response()->json( [
                "success" => true,
                "name" => $name
            ] );

            // $file = $request->file->store( "public/staff/documents" );

            // $document = new DocumentFile();
            // $document->type = $request->type;
            // $document->title = $file;
            // $document->details = $request->details;
            // $document->size = $request->size;
            // $document->description = $request->description;
            // $document->name = $request->name;
            // $document->userid = $request->userId;
            // $document->created_at = now();
            // $document->updated_at = now();

            // $document->save();

            // return response()->json( [
            //     "success" => true,
            //     "file" => $file,
            //     "message" => "File was successfully uploaded to the server"
            // ] );
        }
    }

    public function getStaffFiles( $id ){
        $records = DocumentFile::where( 'userid', $id )->get();
        return response()->json( [ "data" => $records ] );
    }
    public function getFilesByTag( $tag ){
        $records = DocumentFile::where( 'details', $tag )->get();
        return response()->json( [ "data" => $records ] );
    }
    public function updateProcurementRequest(Request $request){
        $record = Requistion::where('id', $request->id )->first();
        $record->procure_added = $request->procure_added;
       
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }
    public function fetchUserFiles(Request $request){
        $results = DB::select( "SELECT * FROM documents 
        WHERE requisition_id = ".$request->id."" );
        return response()->json( $results );
    }
    public function add_payroll( Request $request ) {
        $document = new DocumentFile;
        $document->file = $request->file;
        $document->name = $request->title;
        $document->size = $request->size;
        $document->type = $request->type;
        $document->description = $request->details;
        $document->details = $request->details;
        $document->status = $request->status;
        $document->created_at = now();
        $document->updated_at = now();

        $document->save();
        // if( $document->id ){
        return response()->json([
            'result' => $document,
            'id' => $document->id
        ]);
        // }
    }
    public function get_all_payrolls(){
        $record = DB::select("SELECT * FROM documents WHERE details LIKE '%payroll%'");
        return response()->json([
            "success" => true,
            "data" => $record
        ]);
    }
    public function updateRecord(Request $request){
        $record = DocumentFile::where('id', $request->id)->first();
        $record->status = $request->input('status');
        $record->save();
        return response()->json([
            "success" => true,
            "message" => $record
        ]);
    }

    public function downloadDocument(Request $request) {
        $path = explode(DIRECTORY_SEPARATOR , __FILE__);
        $root = $path[0]."/".$path[1]."/".$path[2]."/".$path[3]."/";
        return response()->json( [
            "file" => $root
        ] );
        // require_once $root. '/dms_backend/vendor/autoload.php';
        // $file =  $root.'/dms_backend/documents/file1.pdf';

        // $mpdf = new \Mpdf\Mpdf();
        // $mpdf->WriteHTML('<div>Section 1 text</div>');

        // $mpdf->Output('output.pdf','F');

        // $path = public_path( $file );
        // $data = file_get_contents($path);

        // $base64 = base64_encode($data);

        // $html ='<body style="display: flex; background-color: #ffe087;">
        //     <div style="height:100%; width: 100%;">
        //         <div style="display:flex; justify-content: center; align-items: center;margin: 0 auto; padding">
        //         <img src="'.$root.'/dms_backend/assets/logo.png'.'" alt="NFA Logo" 
        //             style="object-fit: contain;height: 100px; padding: 2px; margin-left: 40mm;" />
        //         </div>

        //         <div>
        //             <h2 style="font-size: 16px; padding-left: 20mm;">THE NATIONAL FORESTRY AND TREE PLANTING ACT No. 8 /2003</h2>
        //             <h4 style="font-size: 16px; padding-left: 20mm; padding-top: 5mm">TREE FARMING LICENSE IN THE CENTRAL FOREST RESERVES</h4>
        //         <div>
        //         <div>
        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 <span style="font-weight: bold;">No:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->licenseID.'</span> 
        //                 <span style="font-weight: bold">Date:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->datePrepared.'</span> 
        //                 <span style="font-weight: bold">Management Area:</span> <span style="padding-left: 5mm; padding-right: 5mm; font-weight: bold; color: #8B0000;">'.$request->range.'</span>, 
        //                 <span style="font-weight: bold">Sector:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->sector.'</span>
        //                 Subject to provisions of the National Forestry and Tree Planting Act( No.8/2003) and 
        //                 any Regulations as saved by the Act or made under and to the terms and conditions stated herein.
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 M/S. <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->name.'</span>
        //                 (Licensees) of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->address.'</span> 
        //                 <span style="font-weight: bold">Tel:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->telephone.'</span>  
        //                 <span style="font-weight: bold">Email Address:</span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->email_address.'</span>
        //                 is hereby granted license by the National Foresty Authority( Licensor ) to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->purpose.'</span>  on an <span style="font-weight:bold;">Area</span> of 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->hectaresAllocated.' hectares</span>
        //                 in <span style="font-weight: bold">Block No: </span> <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->blocknumber.'</span>  
        //                 <span style="margin-left: 0.25mm;padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->reserve.'</span>  <span style="font-weight: bold">Central Forest Reserve</span>.
        //                 This License is valid for a <span style="font-weight: bold;">Period</span> of <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->period.' year(s)</span>
        //                 from <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->startdate.'</span> to 
        //                 <span style="padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->end_date.'</span>
        //             </p>

        //             <p style="line-height: 8mm;padding: 2mm;">
        //                 License fees shall be paid on an annual basis for the area of land allocated or under license at a rate reserved 
        //                 in the license agreement per hectare for purposes of growing trees.
                        
        //                 <div style="padding-bottom: 2mm;">
        //                 <h4>APPROVED BY THE DIRECTOR:</h4>
        //                 Signature:<div style="border-bottom: 1px dotted black; width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="margin-top: 1mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->director.'</span>
        //                 </div>

        //                 <div style="padding-top: 2mm;">
        //                 <h4>AUTHORISED BY THE EXECUTIVE DIRECTOR:</h4>
        //                 Signature: <div style="border-bottom: 1px dotted black;width: 80%;" />
        //                 <span style="padding-bottom: 2mm;"/>
        //                 Name: <span style="padding-top: 3mm; padding-left: 5px; padding-right: 5px; font-weight: bold; color: #8B0000;">'.$request->executive_director.'</span>
        //                 </div>
        //             </p>
                        
        //             <p>
        //                 <div style="border-bottom: 1px solid green; padding-top: 2mm; width: 100%;" />
        //             </p>
        //             <p style="padding-bottom: 2mm;">
        //                 <div><span style="font-weight: bold;">Copies to:</span> Original to Licensee; Range Manager; Finance Department;</div>
        //                 <div>Notes:</div>
        //             </p>

        //         </div>

        //         </div>
        //     </div>
        // </body>';
        // $mpdf->setTitle("List of Approved Requisitions");
        // $mpdf->showWatermarkImage = false;
        // $mpdf->setDisplayMode('fullpage');
        // $mpdf->WriteHTML( $html );
        // $mpdf->Output('output.pdf','F');

        // $path = public_path('output.pdf');
        // $data = file_get_contents($path);

        // $base64 = base64_encode($data);
        // return response()->json( [ "file" => $base64 ] );
    }

    public function getSingleFile( Request $request ) {
        // $record = DocumentFile::where('details', $request->type )->first();
        return response()->json([
            "name" => "Kent"
        ]);
    }

    public function approveMemo( Request $request ) {
        $sql = "UPDATE memo 
            SET stage = ".$request->stage.",
                status = '".$request->status."',
                remarks = '".$request->remarks."'
            WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => "Memo has been updated successfully."
        ]);
    }

    public function shareMemo( Request $request ) {
        $sql = "UPDATE memo 
            SET receivers = '".$request->shared."'
            WHERE id = ".$request->id."";

        $query = DB::select( DB::raw( $sql ) );

        return response()->json([
            "success" => true,
            "message" => "Memo has been shared with the others successfully."
        ]);
    }
}
