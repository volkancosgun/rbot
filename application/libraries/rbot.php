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
        if($this->_rbot->agent->browser() == "Internet Explorer") // [useragent][browser]
        {
            // redirect(site_url('security/browser')); 
        }
        
        // Aynı anda hem rbot hem gip aktif mi?
        if($this->_rbot_active() == TRUE AND $this->_gip_active() == TRUE)
        {
            // GIP PAGE SECURITY MODUL //
            $gip_online = $this->_rbot->session->userdata('gip_online'); // your session data
            $this->_rbot->session->set_userdata('referer', uri_string());
            if(empty($gip_online))
            {
                redirect(site_url('security/gip')); // security controller..
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
            // redirect(site_url('security/browser'));
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
                if($product_page == 'your_admin_edit_page' OR $product_page == 'admin_delete_page')
                {
            
                 // admin edit controller
                $product_id = $this->_rbot->uri->segment('3'); // admin ID
                
                $product_group = $this->_rbot->admin->infoid($product_id, 'group'); // admin group
                
                $product_glevel = $this->_rbot->admin->glevel($product_group); // level
                
                $login_group = $this->_rbot->admin->info('group');
                
                $login_glevel = $this->_rbot->admin->glevel($login_group); 
                
                if($login_glevel > $product_glevel)
                {   
                    switch($product_page)
                    {
                        case 'adminEdit';
                        $this->_rbot->session->set_flashdata('fmesaj', 
                       'Bu yöneticiyi düzenleyemezsiniz çünkü, yönetici sizden daha üstün bir seviyeye sahip.');
                        redirect('your_url');
                        break;
                        
                        case 'adminDelete';
                        $this->_rbot->session->set_flashdata('fmesaj', 
                       'Bu yöneticiyi silemezsiniz çünkü, yönetici sizden daha üstün bir seviyeye sahip.');
                        redirect('your_url');
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
        
        return $this->_rbot->db->count_all_results('your_rbot_table');
    }
    
    // GIP ACTIVE ?
    private function _gip_active()
    {
        $this->_rbot->db->where('ID', '1');
        $this->_rbot->db->where('gip_status', '1');
        
        return $this->_rbot->db->count_all_results('your_rbot_setting_table');
    }
    
    // LEVEL ACTIVE ?
    private function _level_active()
    {
        $this->_rbot->db->where('ID', '1');
        $this->_rbot->db->where('level_status', '1');
        
        return $this->_rbot->db->count_all_results('your_rbot_setting_table');
    }
    
    private function _rbot_levelcontrol($data)
    {
        // login id
        $admin_id = $this->_rbot->session->userdata('login_id'); // User ID
        
        $admin_group = $this->_rbot->admin->info('group');
        
        $this->_rbot->db->where('ID', $admin_id);
        $this->_rbot->db->where($data, '1');
        
        return $this->_rbot->db->count_all_results('your_group_table');
        
    }
}
