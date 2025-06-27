<?php

class Accounts extends BaseController
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = $this->model('AccountModel');
    }

    // Add default index method
    public function index()
    {
        // Redirect to login page when accessing /accounts
        header('Location: ' . URLROOT . '/accounts/login');
        exit();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form submission
            $data = [
                'voornaam' => trim($_POST['voornaam']),
                'achternaam' => trim($_POST['achternaam']),
                'email' => trim($_POST['email']),
                'telefoon' => trim($_POST['telefoon']),
                'geboortedatum' => $_POST['geboortedatum'],
                'gebruikersnaam' => trim($_POST['gebruikersnaam']),
                'wachtwoord' => $_POST['wachtwoord'],
                'error' => '',
                'success' => ''
            ];

            // Validate input
            if (empty($data['voornaam']) || empty($data['achternaam']) || 
                empty($data['email']) || empty($data['gebruikersnaam']) || 
                empty($data['wachtwoord'])) {
                $data['error'] = 'Vul alle verplichte velden in.';
            }

            // Check if username already exists
            if (empty($data['error']) && $this->accountModel->findUserByUsername($data['gebruikersnaam'])) {
                $data['error'] = 'Gebruikersnaam bestaat al.';
            }

            // Register user if no errors
            if (empty($data['error'])) {
                if ($this->accountModel->register($data)) {
                    $data['success'] = 'Registratie succesvol!';
                    // Clear form data
                    $data = array_merge($data, [
                        'voornaam' => '',
                        'achternaam' => '',
                        'email' => '',
                        'telefoon' => '',
                        'geboortedatum' => '',
                        'gebruikersnaam' => '',
                        'wachtwoord' => ''
                    ]);
                } else {
                    $data['error'] = 'Er is iets misgegaan bij de registratie.';
                }
            }

            $this->view('accounts/register', $data);
        } else {
            // Show registration form
            $data = [
                'voornaam' => '',
                'achternaam' => '',
                'email' => '',
                'telefoon' => '',
                'geboortedatum' => '',
                'gebruikersnaam' => '',
                'wachtwoord' => ''
            ];

            $this->view('accounts/register', $data);
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form submission
            $data = [
                'email' => trim($_POST['email']),
                'wachtwoord' => $_POST['wachtwoord'],
                'error' => '',
                'success' => ''
            ];

            // Validate input
            if (empty($data['email']) || empty($data['wachtwoord'])) {
                $data['error'] = 'Vul alle velden in.';
            }

            // Authenticate user if no errors
            if (empty($data['error'])) {
                $user = $this->accountModel->findUserByEmail($data['email']);
                
                if ($user && password_verify($data['wachtwoord'], $user->WachtwoordHash)) {
                    // Login successful - start session
                    session_start();
                    $_SESSION['user_id'] = $user->GebruikerID;
                    $_SESSION['username'] = $user->Gebruikersnaam;
                    $_SESSION['user_email'] = $user->Email; // Renamed from 'email' to 'user_email'
                    
                    // Redirect to dashboard
                    header('Location: ' . URLROOT . '/homepages/index');
                    exit();
                } else {
                    $data['error'] = 'Onjuiste email of wachtwoord.';
                }
            }

            $this->view('accounts/login', $data);
        } else {
            // Show login form
            $data = [
                'email' => '',
                'error' => '',
                'success' => ''
            ];

            $this->view('accounts/login', $data);
        }
    }

    // Add logout method to handle user logout
    public function logout()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        // Redirect to homepage
        header('Location: ' . URLROOT . '/homepages/index');
        exit();
    }
}
