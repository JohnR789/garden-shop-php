<?php
namespace App\Controllers;

use App\Core\View;

class PageController
{
    public function about()
    {
        View::render('about');
    }

    public function contact()
    {
        $success = $error = '';

        // If POST: Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $message = trim($_POST['message'] ?? '');

            // Simple validation
            if (!$name || !$email || !$message) {
                $error = "Please fill in all fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email.";
            } else {
                $success = "Thanks for reaching out! We'll get back to you soon.";
            }
        }

        View::render('contact', [
            'success' => $success,
            'error' => $error,
            'form' => $_POST
        ]);
    }
}
