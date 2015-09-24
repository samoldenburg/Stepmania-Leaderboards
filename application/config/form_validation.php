<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
        'register' => array(
            array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required|min_length[4]|max_length[32]|username_unique'
            ),
            array(
                'field' => 'pass',
                'label' => 'Password',
                'rules' => 'required|min_length[4]'
            ),
            array(
                'field' => 'confirm_pass',
                'label' => 'Password Confirmation',
                'rules' => 'required|matches[pass]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|valid_email'
            ),
            array(
                'field' => 'display_name',
                'label' => 'Display Name',
                'rules' => 'required|min_length[4]|max_length[32]'
            )
        ),
        'edit_profile' => array(
            array(
                'field' => 'pass',
                'label' => 'Password',
                'rules' => 'min_length[4]'
            ),
            array(
                'field' => 'confirm_pass',
                'label' => 'Password Confirmation',
                'rules' => 'matches[pass]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'required|valid_email'
            ),
            array(
                'field' => 'display_name',
                'label' => 'Display Name',
                'rules' => 'required|min_length[4]|max_length[32]'
            )
        ),
        'pass_reset' => array(
            array(
                'field' => 'pass',
                'label' => 'Password',
                'rules' => 'required|min_length[4]'
            ),
            array(
                'field' => 'confirm_pass',
                'label' => 'Password Confirmation',
                'rules' => 'matches[pass]'
            )
        ),
        'add_pack' => array(
            array(
                'field' => 'name',
                'label' => 'Pack Name',
                'rules' => 'required|packname_unique'
            ),
            array(
                'field' => 'download_link',
                'label' => 'Download Link',
                'rules' => 'valid_url'
            ),
        ),
        'edit_pack' => array(
            array(
                'field' => 'name',
                'label' => 'Pack Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'download_link',
                'label' => 'Download Link',
                'rules' => 'valid_url'
            ),
        ),
        'parser' => array(
            array(
                'field' => 'file',
                'label' => 'File Contents',
                'rules' => 'required'
            ),
            array(
                'field' => 'rate',
                'label' => 'Rate',
                'rules' => 'required|greater_than_equal_to[0.5]|less_than_equal_to[2]'
            ),
        ),
        'announcement' => array(
            array(
                'field' => 'title',
                'label' => 'Announcement Title',
                'rules' => 'required'
            ),
            array(
                'field' => 'text',
                'label' => 'Announcement Text',
                'rules' => 'required'
            ),
        ),
        'suggest_file' => array(
            array(
                'field' => 'title',
                'label' => 'Song Title',
                'rules' => 'required'
            ),
            array(
                'field' => 'pack',
                'label' => 'Pack Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'rate',
                'label' => 'Rate',
                'rules' => 'required|greater_than_equal_to[0.5]|less_than_equal_to[2]'
            ),
        ),
        'submit_score' => array(
            array(
                'field' => 'song_name',
                'label' => 'Chart Name',
                'rules' => 'required'
            ),
            array(
                'field' => 'song_rate',
                'label' => 'Rate',
                'rules' => 'required'
            ),
            array(
                'field' => 'score_achieved',
                'label' => 'Date Score Was Achieved',
                'rules' => 'required'
            ),
            array(
                'field' => 'marvelous_count',
                'label' => 'Marvelous Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'perfect_count',
                'label' => 'Perfect Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'great_count',
                'label' => 'Great Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'good_count',
                'label' => 'Good Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'boo_count',
                'label' => 'Boo Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'miss_count',
                'label' => 'Miss Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'ok_count',
                'label' => 'OK Count',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'mines_hit',
                'label' => 'Mines Hit',
                'rules' => 'greater_than_equal_to[0]'
            ),
            array(
                'field' => 'screenshot_url',
                'label' => 'Screenshot',
                'rules' => 'required'
            ),
        ),
        'rank_chart' => array(
            array(
                'field' => 'title',
                'label' => 'Title',
                'rules' => 'required'
            ),
            array(
                'field' => 'rate',
                'label' => 'Rate',
                'rules' => 'required|greater_than_equal_to[0.5]|less_than_equal_to[2]'
            ),
            array(
                'field' => 'length',
                'label' => 'Length',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'dance_points',
                'label' => 'Dance Points',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'notes',
                'label' => 'Notes',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'taps',
                'label' => 'Taps',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'jumps',
                'label' => 'Jumps',
                'rules' => 'required'
            ),
            array(
                'field' => 'hands',
                'label' => 'Hands',
                'rules' => 'required'
            ),
            array(
                'field' => 'quads',
                'label' => 'Quads',
                'rules' => 'required'
            ),
            array(
                'field' => 'mines',
                'label' => 'Mines',
                'rules' => 'required'
            ),
            array(
                'field' => 'holds',
                'label' => 'Holds',
                'rules' => 'required'
            ),
            array(
                'field' => 'peak_nps',
                'label' => 'Peak NPS',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'avg_nps',
                'label' => 'Average NPS',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'difficulty_score',
                'label' => 'Calculated Difficulty Score',
                'rules' => 'required|greater_than[0]'
            ),
            array(
                'field' => 'raw_file',
                'label' => 'Raw File',
                'rules' => 'required'
            ),
            array(
                'field' => 'stamina_file',
                'label' => 'Is this a stamina intensive file?',
                'rules' => 'required'
            ),
            array(
                'field' => 'file_type',
                'label' => 'File Type',
                'rules' => 'required'
            ),
            array(
                'field' => 'date_ranked',
                'label' => 'Time File Was Ranked',
                'rules' => 'required'
            ),
            array(
                'field' => 'pack_id',
                'label' => 'Pack This File Is In',
                'rules' => 'required'
            ),
        )
    );
