<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function is_login()
{
    $CI = &get_instance();
    if (!$CI->session->userdata('id')) {
        $CI->session->set_flashdata('error', 'Silahkan Login');
        redirect(base_url('user/login'));
    }
}

function is_superadmin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('level') <> 1) {
        $CI->session->set_flashdata('error', 'Akses tidak ditemukan');
        redirect($_SERVER['HTTP_REFERER']);
    }
}

function is_admin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('level') <> 2) {
        $CI->session->set_flashdata('error', 'Akses tidak ditemukan');
        redirect($_SERVER['HTTP_REFERER']);
    }
}

function is_gudang()
{
    $CI = &get_instance();
    if ($CI->session->userdata('level') <> 3) {
        $CI->session->set_flashdata('error', 'Akses tidak ditemukan');
        redirect($_SERVER['HTTP_REFERER']);
    }
}
