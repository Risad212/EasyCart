<?php

if (!function_exists('baseUrl')) {
    /**
     * Get the base URL of the application.
     *
     * @return string
     */
    function baseUrl(): string
    {
        $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host      = $_SERVER['HTTP_HOST'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        return rtrim($protocol . "://" . $host . $scriptDir);
    }
}

if (!function_exists('view')) {
    /**
     * Load and render a view file.
     *
     * @param string $name View file name
     * @param array  $data Data to pass to the view
     * @return void
     */
    function view(string $name, array $data): void
    {
        $path = BASE_PATH . "/src/Views/$name.php";

        if (file_exists($path)) {
            extract($data);
            require $path;
        } else {
            echo 'View not found: ' . $name;
        }
    }
}

if(!function_exists('redirect')){
  /**
   * Redirect to the pat
   * 
   * @param string $path
   * @return void
   */
   function redirect(string $path): void{
      if (filter_var($path, FILTER_VALIDATE_URL)) {
          $redirectUrl = $path;
      }else {
          $redirectUrl = BASE_URL . '/' . ltrim($path, '/'); 
     }
      header('Location: ' . $redirectUrl);
      exit;
   }
}