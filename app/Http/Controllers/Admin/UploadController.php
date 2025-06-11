<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;
use App\Models\HistoryInvoiceSendDoc;
use App\Models\SendMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\InvoiceDetail;
use NumberToWords\NumberToWords;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Customer;
use App\Services\InvoiceScheduleService;
use Exception;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Rackspace\RackspaceAdapter;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\FTP\FtpConnectionOptions as FtpConnectionOptions;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Ftp\FtpAdapter as FtpFtpAdapter;
use League\Flysystem\Ftp\FtpConnectionProvider;
use League\Flysystem\Ftp\NoopCommandConnectivityChecker;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends Controller
{
    protected $layout = 'admin::pages.dmc_testing_upload.';
    private $disk = null;
    private $invoiceScheduleService = null;
    public function __construct(InvoiceScheduleService $ser)
    {
        $this->disk = Storage::disk('ftp');
        $this->invoiceScheduleService = $ser;
    }
    public function index3333()
    {
        Invoice::where("status",5)->forceDelete();
        return 'done';
    }
    public function index()
    {
        $this->invoiceScheduleService->createInvoiceByPeriodEnd();
        return "success";
        // $ftp_server = env('FTP_HOST');
        // $ftp_user = env('FTP_USERNAME');
        // $ftp_pass = env('FTP_PASSWORD');
        // // The internal adapter
        // $adapter = new FtpFtpAdapter(
        //     // Connection options
        //     FtpConnectionOptions::fromArray([
        //         'host' => $ftp_server, // required
        //         'username' => $ftp_user, // required
        //         'password' => $ftp_pass, // required
        //     ]),
        //     new FtpConnectionProvider,
        //     new NoopCommandConnectivityChecker,
        //     new PortableVisibilityConverter(),
        // );

        // // The FilesystemOperator
        // $filesystem = new Filesystem($adapter);
        // // Check if the connection is successful
        // // if ($filesystem->has($path)) {
        // //     echo "FTP connection successful!";
        // // } else {
        // //     echo "FTP connection failed!";
        // // }

        // dd($filesystem);

        // The FilesystemOperator
        // $filesystem = new League\Flysystem\Filesystem($adapter);
        // $directories = $this->disk->directories();
        // dd($directories);
        $ftp_server = '172.24.200.6';
        $ftp_user = env('FTP_USERNAME');
        $ftp_pass = env('FTP_PASSWORD');
        $con = ftp_connect($ftp_server, 21, 1);
        if (false === $con) {
            return 'unable_to_connect';
        }
        return 'connection';
        // return view($this->layout . 'index');
    }

    public function testfff(Request $req)
    {
        //return $req->file;
        // $disk = Storage::disk('sftp');
        $directories = $this->disk->directories(); // this will only be used for testing to dump and check if the directory exists
        $this->disk->putFileAs($directories[1], $req->file, 'receipt_0011f.pdf');
        //// $disk->put($directories[1],$req->file);
        //$file_list = $this->disk->allFiles($directories[1]);

        //return $file_list;
        return 'success001';
    }

    public function testgjj(Request $req)
    {
        //return $req->file;
        $disk = Storage::disk('sftp');
        $directories = $disk->directories(); // this will only be used for testing to dump and check if the directory exists
        //$disk->putFileAs($directories[1], $req->file, 'receipt_001.pdf');
        //// $disk->put($directories[1],$req->file);
        $file_list = $disk->allFiles($directories[1]);

        return $file_list;
        return 'success001';
    }

    public function getReadFile()
    {
        $disk = Storage::disk('sftp');
    }

    public function testfsfsf(Request $req)
    {
        //$data = $disk->get($directories[1]); 
        // dd($req->file);

        try {
            //Storage::disk('ftp')->put('Report_DMC/DMC_Testing',$req->file);
            // $contents = Storage::disk('ftp')->get('Report_DMC/DMC_testing');
            // return $contents;
            //return 'success';
            $files = Storage::disk('ftp')->directories();
            return $files;
            // return $files;
            // foreach ($files as $file) {

            //     $contents = Storage::disk('ftp')->put('Report_DMC/DMC_testing',$req->file);
            //     //return $file;

            // }
        } catch (\Exception $err) {
            return $err->getMessage();
        }
    }
    public function index11()
    {
        $ftp_server = '172.24.200.6';
        $ftp_user = 'administrator';
        $ftp_pass = 'cfocn@#$123';

        $files = Storage::disk('ftp')->directories(".");
        // set up basic connection
        $ftp = ftp_connect($ftp_server);

        // login with username and password
        $login_result = ftp_login($ftp, $ftp_user, $ftp_pass);

        // get contents of the current directory
        $contents = ftp_nlist($ftp, ".");

        return $contents;

        // output $contents
        var_dump($contents);
    }
    public function indexffff()
    {
        // dd('fsf');
        // $files = Storage::disk('ftp')->directories();
        // try{
        //     return $files;
        //     foreach ($files as $file) {
        //         $contents = Storage::disk('ftp')->get($file);
        //     }
        // }catch(\Exception $err){
        //     return $err->getMessage();
        // }

        $ftp_server = '172.24.200.6';
        $ftp_user = 'administrator';
        $ftp_pass = 'cfocn@#$123';
        $con = ftp_connect($ftp_server);
        if ($con) {
            // login with username and password
            $res = ftp_login($con, $ftp_user, $ftp_pass);
            if ($res) {
                echo "Directory";
            } else {
                echo "Directory Un";
            }
        }
    }
    public function testddddd()
    {
        // $host = 'cfocn.phsartech.com';
        // $user = 'cfocn';
        // $password = '9D2iM91R';

        /// dmc 
        // $host = '103.121.188.7';
        // $user = 'cfocn1';
        // $password = 'CN@2023!';

        // $host = '172.24.200.6';
        // $user = 'administrator';
        // $password = 'cfocn@#$123';

        // $ftp_server = "172.24.200.6";

        // // Establish ftp connection
        // $ftp_connection = ftp_connect($ftp_server)
        //     or die("Could not connect to $ftp_server");

        // if ($ftp_connection) {
        //     echo "Successfully connected to the ftp server!";

        //     // Closing  connection
        //     ftp_close($ftp_connection);
        // }


        // Connect to host
        //$conn = ftp_connect($host);
        $host = '172.24.200.6';
        $user = 'administrator';
        $password = 'cfocn@#$123';
        $ftp_connection = ftp_connect($host);
        // if(!$ftp_connection) {
        //     echo 'Error: could not connect to ftp server';
        //     exit;
        // }
        // echo 'Connected to '.$host.'<br/>';

        $login = ftp_login($ftp_connection, $user, $password);
        if (!$login) {
            echo 'Error: could not connect to ftp serverfsfsffsfsfsfs';
            exit;
        }
        echo 'Connected to ' . $host . '<br/>';

        //     if($login) {
        //        $local_dir = public_path('uploads/invoice/creditNotePdf') ;//File::get(public_path('uploads'));
        //        $remote_server_dir = "/domains/cfocn.phsartech.com/public_html";
        //        print_r($remote_server_dir);
        //        //var_dump($local_dir);
        //        $files = scandir($local_dir);
        //        for($i=0 ; $i< count($files); $i++){

        //         $files_on_server = ftp_nlist($ftp_connection, $remote_server_dir);

        //         if(is_array("$files[$i]")){
        //             //upload files to remote server;
        //             if(ftp_put($ftp_connection, "$remote_server_dir/$files[$i]","$local_dir/$files[$i]",FTP_BINARY)){
        //                 echo "Upload successful $files[$i]";
        //             }else{
        //                 echo "Upload failed $files[$i]";
        //             }
        //         }else{
        //             echo "$remote_server_dir/$files[$i] exists!";
        //         }
        //        // print_r($file_on_server);
        //        // die();
        //        }
        //      //  ftp_close($ftp_connection);
        //        // echo "<br>logged in successfully!";   
        //     } else {
        //    // echo "<br>Error while uploading.";
        //     }
    }
    public function save(Request $request)
    {
        try {
            $file = $request->file;
            $getFile = UploadFile::uploadFile('/invoice/pdf', $file, "", 'name.pdf');
            $docItem = [
                'invoice_id' => $request->invoice_id,
                'file' => $getFile,
                'file_type' => 'pdf'
            ];
            $dataHistory = HistoryInvoiceSendDoc::create($docItem);
            //$dataHistory->fileUrl = 'https://www.junkybooks.com/administrator/thebooks/6354720070e3c-advances-in-quantitative-analysis-of-finance-and-accounting-advances-in-quantitative-analysis-of-finance.pdf'; //url('/uploads/invoice/pdf/' . $dataHistory?->file);
            //sendMail


            $this->sendMail($dataHistory);

            return response()->json([
                'data' => $getFile,
                'message' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function sendMail($dataFile)
    {
        $files = [
            'https://www.africau.edu/images/default/sample.pdf',
            // 'https://www.junkybooks.com/administrator/thebooks/6354720070e3c-advances-in-quantitative-analysis-of-finance-and-accounting-advances-in-quantitative-analysis-of-finance.pdf'
        ];
        //$files[] = $dataFile->fileUrl;
        $files[] = "https://cfocn.phsartech.com/uploads/invoice/pdf/1683091079-03_05_2023_12_17_59.pdf";

        // dd($files);
        $u = Auth::user();
        $dataEmail = SendMail::pluck('email');
        foreach ($dataEmail as $mail) {
            Mail::to($mail)->send(new OrderShipped(Auth::user(), $files, $mail));
        }
    }
    public function viewDetail($id)
    {

        Log::info("Start: Admin/InvoiceController > viewDetail | admin: ");
        try {
            $data['invoice'] = Invoice::with('purchase')->find($id);
            $data['contact'] = Contact::first();
            $data['customer'] = Customer::where("id", $data['invoice']->customer_id)->first();
            $data['rate'] = DB::table('rates')->first();
            $data['invoice_detail'] = InvoiceDetail::with('service')->where("invoice_id", $data['invoice']->id)->get();
            $data['total_grand_kh'] = $data['invoice']->total_grand * $data['rate']->rate;
            $data['total_price_kh'] = $data['total_grand_kh'] / 1.1;
            $data['vat_kh'] = $data['total_grand_kh'] - $data['total_price_kh'];

            $number_to_word = $data['invoice']['total_grand'];
            $data['a'] = NumberToWords::transformCurrency('en', $number_to_word, 'USD');

            $check_rate_first = 0;
            $check_rate_seconde = 0;
            $purchase_type = false;

            if ($data['invoice']->purchase->type_id == 2) {
                $data['invoice'] = Invoice::where('po_id', $data['invoice']->purchase->id)->first();
                $data['count_invoice'] = Invoice::where('po_id', $data['invoice']->purchase->id)->count();
                $purchase_type = true;
            }
            if (isset($data['invoice_detail']) && count($data['invoice_detail']) > 0) {
                foreach ($data['invoice_detail'] as $item) {
                    if ($item->rate_first) {
                        $check_rate_first += 1;
                    }
                    if ($item->rate_second) {
                        $check_rate_seconde += 1;
                    }
                }
            }
            $data['check_rate_first'] = $check_rate_first;
            $data['check_rate_seconde'] = $check_rate_seconde;
            $data['purchase_type'] = $purchase_type;

            return view($this->layout . 'detail', $data);
        } catch (\Exception $error) {
            Log::error("Error: Admin/InvoiceController > viewDetail | message: " . $error->getMessage());
            return redirect()->back();
        }
    }
    public function createFileObject($url)
    {

        $path_parts = pathinfo($url);

        $newPath = $path_parts['dirname'] . '/tmp-files/';

        if (!is_dir($newPath)) {

            mkdir($newPath, 0777);
        }

        $newUrl = $newPath . $path_parts['basename'];

        copy($url, $newUrl);
        // $fileSize = File::size($url);
        // dd($fileSize['mime']);
        //$imgInfo = getFile($newUrl);
        // // dd($imgInfo);

        $file = new UploadedFile(
            $newUrl,
            $path_parts['basename'],
            '',
            filesize($url),
            true,
            TRUE
        );

        return $file;
    }
}
