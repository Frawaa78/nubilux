<?php

class ModuleLoader {
    private $modules = [];
    private $router;
    
    public function __construct($router) {
        $this->router = $router;
        $this->loadModuleConfig();
    }
    
    public function loadModules() {
        foreach ($this->modules as $name => $config) {
            if ($config['enabled']) {
                $this->loadModule($name, $config);
            }
        }
    }
    
    private function loadModule($name, $config) {
        $modulePath = __DIR__ . "/../modules/{$name}";
        
        // Load module bootstrap if exists
        $bootstrapFile = "{$modulePath}/bootstrap.php";
        if (file_exists($bootstrapFile)) {
            require_once $bootstrapFile;
        }
        
        // Register module routes
        if (isset($config['routes'])) {
            foreach ($config['routes'] as $route => $callback) {
                $this->router->get($route, $callback);
            }
        }
    }
    
    private function loadModuleConfig() {
        $configFile = __DIR__ . '/../config/modules.php';
        if (file_exists($configFile)) {
            $this->modules = require $configFile;
        }
    }
    
    public function getLoadedModules() {
        return array_keys(array_filter($this->modules, fn($config) => $config['enabled']));
    }
    
    public function isModuleLoaded($name) {
        return isset($this->modules[$name]) && $this->modules[$name]['enabled'];
    }
}