<?php

abstract class Controller {
    protected $view;
    
    public function __construct() {
        $this->view = new View();
    }
    
    protected function render($template, $data = []) {
        return $this->view->render($template, $data);
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    protected function requireAuth() {
        if (!Auth::check()) {
            $this->redirect('/index.php');
        }
    }
    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Field {$field} is required";
                continue;
            }
            
            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Field {$field} must be a valid email";
            }
            
            if (preg_match('/min:(\d+)/', $rule, $matches) && strlen($value) < $matches[1]) {
                $errors[$field] = "Field {$field} must be at least {$matches[1]} characters";
            }
        }
        
        return $errors;
    }
}