<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('login_model');
        $this->load->model('dashboard_model');
		$this->load->dbforge();
			
    }
    
	public function index()
	{
		$this->load->view('register');
	}
	
public function Register_Admin(){	
		//Recieving post input of email, password from request
		$companyName = $this->input->post('companyName');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$subdomainName = $this->input->post('subdomainName');

		#Input validation
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		$this->form_validation->set_rules('email', 'User Email', 'trim|xss_clean|required|valid_email', array('valid_email' => 'Email is invalid'));
		$this->form_validation->set_rules('companyName', 'Company Name', 'trim|xss_clean|required|min_length[3]', array('min_length' => 'Company Name is invalid'));
		$this->form_validation->set_rules('phone', 'Phone', 'trim|xss_clean|required|min_length[11]|max_length[14]|numeric', array('min_length'=> 'Phone is invalid', 'max_length' => 'Phone is invalid', 'numeric' => 'Phone is invalid'));

		// check if subdomain already exists
		$this->form_validation->set_rules('subdomainName', 'Sub-Domain Name', 'trim|xss_clean|required|min_length[3]|is_unique[tenants.subdomain_name]', array('is_unique' => 'Subdomain already taken'));
		
		if($this->form_validation->run() == FALSE){
			$response = array(
				'success' => false,
				'emailError'=> form_error('email'),
				'companyNameError'=> form_error('companyName'),
				'subdomainNameError'=> form_error('subdomainName'),
				'companyNameError'=> form_error('phone')
			);
			echo json_encode($response);
		}
		else{
			
			$create_status = $this->create_tenant($email, $companyName, $phone, $subdomainName);

			// check if tenant was created
			if ($create_status) {
				
				/** send login details to email*/ 

				// set email config 

				$config = Array( 
					'protocol' => 'smtp', 
					'smtp_host' => 'ssl://smtp.googlemail.com', 
					'smtp_port' => 25, 
					'smtp_user' => 'chukajide@gmail.com', 
					'smtp_pass' => 'ochiabuto6'
				); 		  
				$from_email = "chukajide@gmail.com"; 
				$to_email = 'ochiabuto@zercomsystems.com'; 
		
				//Load email library 
				$this->load->library('email',$config); 
		
				$this->email->from($from_email, 'Dev'); 
				$this->email->to($to_email);
				$this->email->subject('EMail Test'); 
				$message	 =	"Confirm Your Account";
				$message	.=	"Click Here : ".base_url()."Confirm_Account?C=" .'</br>'; 
				$this->email->message($message); 
		
				$this->email->send();
					 
			}
			else{
				$this->session->set_flashdata('feedback','An Error occurred, please try again');
				redirect(base_url() . 'register', 'refresh');
			}
		}
	}

    /**Create Tenants by Ochiabuto Jideofor */

    function create_tenant($email, $companyName, $phone, $subdomainName) {

        // Create Database name with subdomain name
		$mysql_database = "hr_".$subdomainName."_db";

		$db['tenant'] = array(
			'dsn'	=> '',
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
			'database' => $mysql_database,
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'production'),
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE
		);	

		// Database structure file for each tenant
		$filename = 'database\parrothr.sql';
		
		// Create Database
		if ($this->dbforge->create_database($mysql_database))
		{
			// Select database and import database structure
			try {
				// close current database to avoid interferences
				$this->db->close();

				// Load the created database
				$this->load->database($db['tenant']);

				// Temporary variable, used to store current query
				$templine = '';
				// Read in entire file
				$lines = file($filename);
				// Loop through each line
				foreach ($lines as $line)
				{
					// Skip it if it's a comment
					if (substr($line, 0, 2) == '--' || $line == '')
						continue;

					// Add this line to the current segment
					$templine .= $line;
					// If it has a semicolon at the end, it's the end of the query
					if (substr(trim($line), -1, 1) == ';')
					{
						// Perform the query
						$this->db->query($templine);

						// Reset temp variable to empty
						$templine = '';
					}
				}

				// Create/Insert login details for tenant admin to access subdomain

				// create password from subdomain name
				$password = $subdomainName.mt_rand(100,999);
				$data = array(
					'em_id' => '001',
					'em_code' => $subdomainName.'001',
					'des_id' => '2',
					'dep_id' => '2',
					'em_email' => $email,
					'first_name' => $companyName,
					'last_name' => '',
					'em_password' => $password,
					'em_image' => 'Doe17531.jpg',
					'em_role' => 'ADMIN',
				);
				$this->db->insert('employee', $data);

				// Close database tenant database after migration
				$this->db->close();

				// open central database
				$this->load->database();

				// Save tenant details in central Database
				$data = array(
					'tenant_id' => $subdomainName,
					'company_name' => $companyName,
					'company_email' => $email,
					'phone' => $phone,
					'subdomain_name' => $subdomainName,
					'db_name' => $mysql_database,
				);

				$this->db->insert('tenants', $data);

				return true;

			} catch (\Throwable $th) {
				$this->session->set_flashdata('feedback','An Error occurred, please try again');
				redirect(base_url() . 'register', 'refresh');
				// throw $th;
			}
		}else{
			$this->session->set_flashdata('feedback','An Error occurred, please try again');
			redirect(base_url() . 'register', 'refresh');
		}
	}
    
}
