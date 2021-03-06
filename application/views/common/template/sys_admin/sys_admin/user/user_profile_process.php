<?php

	$id = $this->data['user']['id'];

	$this->data['title'] = "Edit User";
        
    if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
        
        redirect('auth', 'refresh');
        
    }
    
    $profile = $this->ion_auth->user($id)->row();
    
    $groups = $this->ion_auth->groups()->result_array();
    
    $currentGroups = $this->ion_auth->get_users_groups($id)->result();
    
    //validate form input
    $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
    
    $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
    
    $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');
    
    $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required');
    
    if (isset($_POST) && !empty($_POST)) {
        
        // do we have a valid request?
        if ($this->data['valid_csrf'] === FALSE || $id != $this->input->post('id')) {
            
            show_error($this->lang->line('error_csrf'));
            
        }
        
        //update the password if it was posted
        if ($this->input->post('password')) {
            
            $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
            
            $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            
        }
        
        if ($this->form_validation->run() === TRUE) {
            
            $data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone')
            );
            
            //update the password if it was posted
            if ($this->input->post('password')) {
                
                $data['password'] = $this->input->post('password');
                
            }
            
            // Only allow updating groups if user is admin
            if ($this->ion_auth->is_admin()) {
                
                //Update the groups user belongs to
                $groupData = $this->input->post('groups');
                
                if (isset($groupData) && !empty($groupData)) {
                    
                    $this->ion_auth->remove_from_group('', $id);
                    
                    foreach ($groupData as $grp) {
                        $this->ion_auth->add_to_group($grp, $id);
                    }
                    
                }
                
            }
            
            //check to see if we are updating the user
            if ($this->ion_auth->update($user->id, $data)) {
                
                //redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                
                if ($this->ion_auth->is_admin()) {
                    
                    redirect('auth', 'refresh');
                    
                } else {
                    
                    redirect('/', 'refresh');
                    
                }
                
            } else {
                
                //redirect them back to the admin page if admin, or to the base url if non admin
                $this->session->set_flashdata('message', $this->ion_auth->errors());
                
                if ($this->ion_auth->is_admin()) {
                    
                    redirect('auth', 'refresh');
                    
                } else {
                    
                    redirect('/', 'refresh');
                    
                }
                
            }
            
        }
        
    }
    
    //set the flash data error message if there is one
    $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    
    $this->data['profile'] = $profile;

    $this->data['groups'] = $groups;
    
    $this->data['currentGroups'] = $currentGroups;
    
    $this->data['first_name'] = array(
        'name' => 'first_name',
        'id' => 'first_name',
        'type' => 'text',
        'value' => $this->form_validation->set_value('first_name', $profile->first_name),
        'class' => 'form-control input-medium'
    );
    
    $this->data['last_name'] = array(
        'name' => 'last_name',
        'id' => 'last_name',
        'type' => 'text',
        'value' => $this->form_validation->set_value('last_name', $profile->last_name),
        'class' => 'form-control input-medium'
    );
    
    $this->data['company'] = array(
        'name' => 'company',
        'id' => 'company',
        'type' => 'text',
        'value' => $this->form_validation->set_value('company', $profile->company),
        'class' => 'form-control input-medium'
    );
    
    $this->data['phone'] = array(
        'name' => 'phone',
        'id' => 'phone',
        'type' => 'text',
        'value' => $this->form_validation->set_value('phone', $profile->phone),
        'class' => 'form-control input-medium'
    );
    
    $this->data['password']         = array(
        'name' => 'password',
        'id' => 'password',
        'type' => 'password',
        'class' => 'form-control input-medium'
    );
    $this->data['password_confirm'] = array(
        'name' => 'password_confirm',
        'id' => 'password_confirm',
        'type' => 'password',
        'class' => 'form-control input-medium'
    );
?>