<?php
class Video_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function save_video_path($path) {
        $data = array('video_path' => $path);
        return $this->db->insert('videos', $data);
    }
}
