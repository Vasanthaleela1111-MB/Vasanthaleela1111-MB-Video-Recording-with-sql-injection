<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load Composer autoload for Twilio
require_once APPPATH . '../vendor/autoload.php';

use Twilio\Rest\Client;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
// use RobThree\Auth\TwoFactorAuth;
use OTPHP\TOTP;
// use Endroid\QrCode\Builder\Builder;

// use Endroid\QrCode\Encoding\Encoding;
// use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
// use Endroid\QrCode\Writer\SvgWriter;
class Otp extends CI_Controller {
    public function __construct()
{
    parent::__construct();
    $this->load->database();
}

    public function index() {
        $this->load->view('otp_form');
        // $this->load->view('record');
    }
public function send_otp() {
    $mobile = $this->input->post('mobile');

    if (!$mobile) {
        echo "Mobile number is required";
        return;
    }

    if (!isset($_FILES['user_image']) || $_FILES['user_image']['error'] != 0) {
        echo "Image upload is required";
        return;
    }

    require_once APPPATH . 'config/constants.php';
require APPPATH . '../vendor/autoload.php';

$s3 = new S3Client($awsConfig);


    $bucket = 'vasanthaleela-07082025';
    $imageName = 'photo_' . time() . '.' . pathinfo($_FILES['user_image']['name'], PATHINFO_EXTENSION);

    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $imageName,
        'SourceFile' => $_FILES['user_image']['tmp_name'],
        'ContentType' => mime_content_type($_FILES['user_image']['tmp_name'])
    ]);

    $imageUrl = $result['ObjectURL'];

    $this->db->insert('video_auth', [
        'mobile' => $mobile,
        'video_url' => null,
        'photo_url' => $imageUrl,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $this->session->set_userdata('mobile', $mobile);

    $otp = rand(100000, 999999);
    $this->session->set_userdata('otp', $otp);

    $account_sid = TWILIO_ACCOUNT_SID;
    $auth_token = TWILIO_AUTH_TOKEN;
    $twilio_number = TWILIO_NUMBER;

    $client = new Client($account_sid, $auth_token);
    $message = $client->messages->create(
        '+91' . $mobile,
        [
            'from' => $twilio_number,
            'body' => "Your OTP is $otp"
        ]
    );

    if ($message && $message->sid) {
        $this->load->view('verify_otp');
    } else {
        echo "Failed to send OTP.";
    }
}


public function record()
{
    $mobile = $this->session->userdata('mobile');
    if (!$mobile) {
        show_error("No mobile number in session.");
        return;
    }

    $photo = $this->db
        ->where('mobile', $mobile)
        ->order_by('created_at', 'DESC')
        ->limit(1)
        ->get('video_auth')
        ->row();
    $data['mobile'] = $mobile;
    $data['photo_url'] = $photo && !empty($photo->photo_url) 
        ? $photo->photo_url
        : base_url('uploads/no-logo.png');

    $this->load->view('record', $data);
}


public function upload() {
    if (!$this->session->userdata('verified')) {
        redirect('otp');
    }

    $config['upload_path']   = './uploads/';
    $config['allowed_types'] = 'mp4|mov|avi|mkv';
    $config['max_size']      = 1024000; 

    $this->load->library('upload', $config);

    if (empty($_FILES['video']['name'])) {
        $data['error'] = "No file selected. Please choose a video to upload.";
        return $this->load->view('upload_video', $data);
    }

    if (!$this->upload->do_upload('video')) {
        $data['error'] = $this->upload->display_errors();
        return $this->load->view('upload_video', $data);
    } else {
        $upload_data = $this->upload->data();
        $video_path = base_url('uploads/' . $upload_data['file_name']);
        $this->load->database();
        $this->db->insert('videos', ['video_path' => $video_path]);
        // $data['video_path'] = base_url($video_path);
        // $this->load->view('upload_success', $data);
        // $this->session->set_flashdata('video_path', base_url($video_path));
        $data['video_path'] = $video_path;
        echo json_encode([
  'status' => 'success',
  'redirect' => site_url('otp/success?video=' . urlencode($video_path)),    
    ]);
    }
}

public function success()
{
    $video_path = $this->input->get('video');

    if (!$video_path) {
        $this->load->view('upload_success', ['error' => 'Video path missing. Please re-upload.']);
        return;
    }

    $this->load->view('upload_success', ['video_path' => $video_path]);
}
public function upload_video() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit; 
    }
    if (!isset($_FILES['video'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No video uploaded'
        ]);
        return;
    }
    $mobile = $this->session->userdata('mobile');
    if (!$mobile) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Mobile number not found in session'
        ]);
        return;
    }
    if (!getenv('AWS_ACCESS_KEY_ID') || !getenv('AWS_SECRET_ACCESS_KEY')) {
        echo json_encode([
            'status' => 'error',
            'message' => 'AWS credentials not configured'
        ]);
        return;
    }

    $file = $_FILES['video']['tmp_name'];
    $fileName = 'video_' . time() . '.webm';

    require APPPATH . '../vendor/autoload.php';
    require_once APPPATH . 'config/constants.php';
    $s3 = new S3Client($awsConfig);

    $bucket = 'vasanthaleela-07082025';
    $buckets = $s3->listBuckets();
    $bucketNames = array_column($buckets['Buckets'], 'Name');

    if (!in_array($bucket, $bucketNames)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bucket does not exist'
        ]);
        return;
    }
    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $fileName,
        'SourceFile' => $file,
        'ContentType' => 'video/webm'
    ]);

    if (!empty($result['ObjectURL'])) {
        $this->db->where('mobile', $mobile);
        $this->db->update('video_auth', [
            'video_url' => $result['ObjectURL']
        ]);

        echo json_encode([
            'status' => 'success',
            'url' => $result['ObjectURL']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to upload video'
        ]);
    }
}

public function upload_photo() {
    $record_id = $this->input->post('record_id'); 
    $mobile    = $this->input->post('mobile');

    // if (!$record_id) {
    //     echo json_encode(['status' => 'error', 'message' => 'Missing record ID']);
    //     return;
    // }

    if (!isset($_FILES['photo'])) {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
        return;
    }

    $fileTmp  = $_FILES['photo']['tmp_name'];
    $fileName = 'photo_' . time() . '.jpg';
    $s3_url   = $this->s3_upload($fileTmp, $fileName);
    if (!$record_id) {
        $this->db->insert('video_auth', [
            'mobile'     => $mobile,
            'photo_url'  => $s3_url,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $record_id = $this->db->insert_id();
    } else {
        $this->db->where('id', $record_id);
        $this->db->update('video_auth', ['photo_url' => $s3_url]);
    }
    // $this->db->where('id', $record_id);
    // $this->db->update('video_auth', ['photo_url' => $s3_url]);

    // echo json_encode(['status' => 'success', 'url' => $s3_url]);
    echo json_encode([
        'status'    => 'success',
        'record_id' => $record_id,
        'url'       => $s3_url
    ]);
}


private function s3_upload($fileTmp, $fileName)
{
    $bucketName = 'vasanthaleela-07082025'; 
    $region     = 'us-east-1';
    require_once APPPATH . 'config/constants.php';
require APPPATH . '../vendor/autoload.php';

$s3 = new S3Client($awsConfig);

    try {
        $result = $s3Client->putObject([
            'Bucket'     => $bucketName,  
            'Key'        => $fileName,
            'SourceFile' => $fileTmp,      
            // 'ACL'        => 'public-read',
        ]);
        return $result['ObjectURL'];

    } catch (Aws\Exception\AwsException $e) {
        log_message('error', 'S3 Upload Error: ' . $e->getAwsErrorMessage());
        return false;
    }
}

private function s3_upload($fileTmp, $fileName)
{
    $bucketName = 'vasanthaleela-07082025'; 
    require_once APPPATH . 'config/constants.php';
    require APPPATH . '../vendor/autoload.php';

    $s3 = new S3Client($awsConfig);
    $buckets = $s3->listBuckets();
    $result = $s3->putObject([
        'Bucket'     => $bucketName,
        'Key'        => $fileName,
        'SourceFile' => $fileTmp,
        // 'ACL'        => 'public-read',
    ]);

    if (!empty($result['ObjectURL'])) {
        return $result['ObjectURL'];
    } else {
        log_message('error', 'S3 Upload Error: No ObjectURL returned.');
        return false;
    }
}

public function generate_qr() {
    $mobile = $this->input->post('mobile');
    $totp = TOTP::create();
    $totp->setLabel('VideoUser'); 
    $totp->setIssuer('VideoAuthApp');
    $qrUri = $totp->getProvisioningUri();
    $result = \Endroid\QrCode\Builder\Builder::create()
        ->writer(new \Endroid\QrCode\Writer\SvgWriter())
        ->data($qrUri)
        ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
        ->errorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(10)
        ->roundBlockSizeMode(new \Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin())
        ->build();

    $fileName = 'qr_' . time() . '.svg';
    $tmpFile = tmpfile();
    fwrite($tmpFile, $result->getString());
    $metaData = stream_get_meta_data($tmpFile);
    $tmpFilePath = $metaData['uri'];

    $s3_url = $this->s3_upload($tmpFilePath, $fileName);
    fclose($tmpFile);

    $this->db->insert('video_auth', [
        'mobile'       => $mobile,
        'entered_code' => null,
        'status'       => null,
        'video_url'    => null,
        'photo_url'    => $s3_url,
        'created_at'   => date('Y-m-d H:i:s')
    ]);

    $imageData = base64_encode($result->getString());

    echo json_encode([
        'status'   => 'success',
        'secret'   => $totp->getSecret(),
        'qr_code'  => 'data:image/svg+xml;base64,' . $imageData,
        'qr_url'   => $s3_url
    ]);
}
public function save_user_video()
{
    $mobile    = $this->input->post('mobile', true);
    $code      = trim($this->input->post('code', true));
    $secret    = $this->input->post('secret', true);
    $video_url = $this->input->post('video_url', true);

    $injection_detected = false;
    $tautology_match = false;


    if (preg_match("/\bOR\b\s*'?([A-Za-z0-9]+)'?\s*=\s*'?\\1'?/i", $code)) {
        $injection_detected = true;
        $tautology_match = true;
    }
    if (preg_match("/^'?([A-Za-z0-9]+)'?\s*=\s*'?\\1'?$/", $code)) {
        $injection_detected = true;
        $tautology_match = true;
    }

    if (preg_match("/^(\d+)\s*([\+\-\*\/])\s*(\d+)\s*=\s*(\d+)$/", $code, $m)) {
        $a = (int)$m[1];
        $op = $m[2];
        $b = (int)$m[3];
        $c = (int)$m[4];
        $result = null;
        switch ($op) {
            case '+': $result = $a + $b; break;
            case '-': $result = $a - $b; break;
            case '*': $result = $a * $b; break;
            case '/': $result = ($b != 0) ? $a / $b : null; break;
        }
        if ($result === $c) {
            $injection_detected = true;
            $tautology_match = true;
        }
    }
    $is_six_digits = preg_match('/^\d{6}$/', $code);

    if ($injection_detected && $tautology_match) {
        $this->db->insert('video_auth', [
            'mobile'       => $mobile,
            'entered_code' => $code,
            'status'       => 'sql_injection_detected',
            'video_url'    => $video_url,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'status'  => 'sql_injection_detected',
            'message' => 'Suspicious input detected. Video allowed to play'
        ]);
        return;
    }
    if ($is_six_digits) {
        $totp = TOTP::create($secret);
        if ($totp->verify($code)) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'OTP verified'
            ]);
        } else {
            $this->db->insert('video_auth', [
                'mobile'       => $mobile,
                'entered_code' => $code,
                'status'       => 'wrong',
                'video_url'    => $video_url,
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            echo json_encode([
                'status'  => 'wrong_code',
                'message' => 'OTP code is wrong, video not allowed'
            ]);
        }
        return;
    }

    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid code. Video not allowed.'
    ]);
}
public function upload_logo()
{
    $mobile = $this->session->userdata('mobile'); 
    if (!$mobile) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        return;
    }

    if (!empty($_FILES['photo']['name'])) {
        $uploadPath = './uploads/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $config['upload_path']   = $uploadPath;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['file_name']     = time().'_'.$_FILES['photo']['name'];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('photo')) {
            $uploadData = $this->upload->data();
            $url = base_url('uploads/'.$uploadData['file_name']);

            $exists = $this->db->get_where('video_auth', ['mobile' => $mobile])->row();
            if ($exists) {
                $this->db->where('mobile', $mobile)->update('video_auth', [
                    'photo_url'  => $url,
                    'created_at' => date('Y-m-d H:i:s') 
                ]);
            } else {
                $this->db->insert('video_auth', [
                    'mobile'     => $mobile,
                    'photo_url'  => $url,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            echo json_encode(['status' => 'success', 'url' => $url]);
            return;
        } else {
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
            return;
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
}

public function list_user_videos()
{
    $mobile = $this->session->userdata('mobile');
    if (!$mobile) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No mobile in session'
        ]);
        return;
    }

    $videos = $this->db
        ->select('video_url, created_at')
        ->from('video_auth')
        ->where('mobile', $mobile)
        ->where('video_url IS NOT NULL', null, false)
        ->order_by('created_at', 'DESC')
        ->get()
        ->result();
    $uniqueVideos = [];
    $seen = [];
    foreach ($videos as $video) {
        $key = $video->video_url . '|' . $video->created_at;
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $uniqueVideos[] = $video;
        }
    }

    if ($uniqueVideos) {
        echo json_encode([
            'status' => 'success',
            'videos' => $uniqueVideos
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No videos found'
        ]);
    }
}


public function phpinfo()
{
    phpinfo();
}

}
