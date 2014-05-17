<?php if( !defined('BASEPATH')) exit('Erisim engellendi');

class rbot
{
    
    function __construct()
    {
        $this->_rbot =& get_instance();
    }
    
    // GIP CONTROL
    
    function gip()
    {
        // BROWSER CONTROL
        if($this->_rbot->agent->browser() == "Internet Explorer")
        {
            redirect(site_url('security/browser'));
        }
        
        // Aynı anda hem rbot hem gip aktif mi?
        if($this->_rbot_active() == TRUE AND $this->_gip_active() == TRUE)
        {
            // GIP PAGE SECURITY MODUL //
            $gip_online = $this->_rbot->session->userdata('gip_online');
            $this->_rbot->session->set_userdata('referer', uri_string());
            if(empty($gip_online))
            {
                redirect(site_url('security/gip'));
            }
            // GIP PAGE SECURITY MODUL //
        }
    }
    
    // LEVEL CONTROL
    
    function level($data)
    {
        
        // BROWSER CONTROL
        if($this->_rbot->agent->browser() == "Internet Explorer")
        {
            redirect(site_url('security/browser'));
        }
        
        // Aynı anda hem rbot hem level aktif mi?
        if($this->_rbot_active() == TRUE AND $this->_level_active() == TRUE)
        {
            $this->_rbot->session->set_userdata('referer', uri_string());
            
            // LEVEL CONTROL
            if($this->_rbot->admin->level($data) != 1)
            {
                redirect(site_url('security/level'));
            }
                // ADMINS Edit / ADMINS Delete
                $product_page = $this->_rbot->uri->segment('2');
                if($product_page == 'adminEdit' OR $product_page == 'adminDelete')
                {
            
                 // admin edit controller
                $product_id = $this->_rbot->uri->segment('3');
                
                $product_group = $this->_rbot->admin->infoid($product_id, 'group');
                
                $product_glevel = $this->_rbot->admin->glevel($product_group);
                
                $login_group = $this->_rbot->admin->info('group');
                
                $login_glevel = $this->_rbot->admin->glevel($login_group);
                
                if($login_glevel > $product_glevel)
                {   
                    switch($product_page)
                    {
                        case 'adminEdit';
                        $this->_rbot->session->set_flashdata('fmesaj', 
                       '<p class="alert alert-warning"><span class="icon-exclamation-sign"></span> Bu yöneticiyi düzenleyemezsiniz çünkü, yönetici sizden daha üstün bir seviyeye sahip.</span></p>');
                        redirect(site_url('admins/adminList'));
                        break;
                        
                        case 'adminDelete';
                        $this->_rbot->session->set_flashdata('fmesaj', 
                       '<p class="alert alert-warning"><span class="icon-exclamation-sign"></span> Bu yöneticiyi silemezsiniz çünkü, yönetici sizden daha üstün bir seviyeye sahip.</span></p>');
                        redirect(site_url('admins/adminList'));
                        break;
                    }
                    
                }
                
                }
            
            
            
            
        }
    }
    
    function info($data)
    {
        
        $sql = ("SELECT * from rbot_setting");
        $query = $this->_rbot->db->query($sql);
        return $query->row($data);
        
    }
    
    // RBOT ACTIVE ?
    private function _rbot_active()
    {
        $this->_rbot->db->where('ID', '1');
        $this->_rbot->db->where('rbot', '1');
        
        return $this->_rbot->db->count_all_results('rpanel_setting');
    }
    
    // GIP ACTIVE ?
    private function _gip_active()
    {
        $this->_rbot->db->where('ID', '1');
        $this->_rbot->db->where('gip_status', '1');
        
        return $this->_rbot->db->count_all_results('rbot_setting');
    }
    
    // LEVEL ACTIVE ?
    private function _level_active()
    {
        $this->_rbot->db->where('ID', '1');
        $this->_rbot->db->where('level_status', '1');
        
        return $this->_rbot->db->count_all_results('rbot_setting');
    }
    
    private function _rbot_levelcontrol($data)
    {
        // login id
        $admin_id = $this->_rbot->session->userdata('login_id');
        
        $admin_group = $this->_rbot->admin->info('group');
        
        $this->_rbot->db->where('ID', $admin_id);
        $this->_rbot->db->where($data, '1');
        
        return $this->_rbot->db->count_all_results('admins_group');
        
    }
}
