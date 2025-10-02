<?php

class View {
    private $templatePath;
    private $layoutPath;
    
    public function __construct() {
        $this->templatePath = __DIR__ . '/../templates/pages/';
        $this->layoutPath = __DIR__ . '/../templates/layout/';
    }
    
    public function render($template, $data = [], $layout = 'base') {
        // Extract data variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the template
        $templateFile = $this->templatePath . $template . '.php';
        if (file_exists($templateFile)) {
            include $templateFile;
        } else {
            throw new Exception("Template not found: {$template}");
        }
        
        // Get template content
        $content = ob_get_clean();
        
        // If layout is specified, wrap content in layout
        if ($layout) {
            $layoutFile = $this->layoutPath . $layout . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                include $layoutFile;
                return ob_get_clean();
            }
        }
        
        return $content;
    }
    
    public function component($name, $data = []) {
        extract($data);
        
        $componentFile = __DIR__ . "/../templates/components/{$name}.php";
        if (file_exists($componentFile)) {
            include $componentFile;
        } else {
            echo "<!-- Component not found: {$name} -->";
        }
    }
    
    public function asset($path) {
        return "/shared/{$path}";
    }
    
    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}