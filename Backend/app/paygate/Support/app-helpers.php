<?php
/*
    * Get Config data
    * @return array.
    *-------------------------------------------------------- */

if (!function_exists('getConfig')) {
    function getConfig($item = null)
    {
        try {
            if (!@include_once(PAY_PAGE_CONFIG)) {
                throw new Exception('file does not exist');
            } else {
                return require PAY_PAGE_CONFIG;
            }
        } catch (\Exception $e) {
            throw new \Exception("PAY_PAGE_CONFIG - Missing config path constant", 1);
        }
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('configItem')) {
    function configItem()
    {
        $getConfig  = getConfig();
        $getItem    = $getConfig['techAppConfig'];
        return $getItem;
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('configItemData')) {
    function configItemData($key, $default = null)
    {
        $getConfig  = getConfig();
        $data    = $getConfig['techAppConfig'];
        return getArrayItem($data, $key, $default);
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getPublicConfigItem')) {
    function getPublicConfigItem()
    {
        $getConfig  = getConfig();
        $getItem    = $getConfig['techAppConfig']['payments']['gateway_configuration'];

        foreach ($getItem as $itemKey => $item) {
            if (!empty($item['privateItems'])) {
                foreach ($item['privateItems'] as $privateItem) {
                    if (isset($getItem[$itemKey][$privateItem])) {
                        unset($getItem[$itemKey][$privateItem]);
                        unset($getItem[$itemKey]['privateItems']);
                    }
                }
            }
        }
        $configItem['payments']['gateway_configuration'] = $getItem;
        return $configItem;
    }
}

/*
      * Get the paytm merchant
      *
      * @param string   $paymentData
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getPaytmMerchantForm')) {
    function getPaytmMerchantForm($paymentData)
    {
        ob_start();
        include "paytm-merchant-form.php";
        $html_content = ob_get_contents();
        ob_end_clean();
        return $html_content;
    }
}

/*
    * Get the payU merchant
    *
    * @param string   $paymentData
    *
    * @return mixed
    *-------------------------------------------------------- */
if (!function_exists('getPayUmoneyMerchantForm')) {
    function getPayUmoneyMerchantForm($paymentData)
    {
        ob_start();
        include "payu-merchant-form.php";
        $html_content = ob_get_contents();
        ob_end_clean();
        return $html_content;
    }
}

/*
      * Get App Url
      *
      * @param string   $paymentData
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getAppUrl')) {
    function getAppUrl($item = null, $path = '')
    {
        $configData = getConfig();
        $basePath = $configData['techAppConfig']['base_url'];

        if (!empty($item)) {
            return $basePath . $path . $item;
        } else {
            return $basePath . $path;
        }
    }
}


/**
 * Redirect using post
 *
 * @param  string // https://www.codexworld.com/how-to/get-user-ip-address-php/
 * @param  array postData data to post
 *-------------------------------------------------------- */
if (!function_exists('getUserIpAddr')) {
    function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

/*
      * Get the technical items from tech items
      *
      *
      * @return mixed
      *-------------------------------------------------------- */

if (!function_exists('getArrayItem')) {
    function getArrayItem($array, $key, $default = null)
    {
        // @assert $key is a non-empty string
        // @assert $array is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($array)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $array[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $array)) {
                    return $default;
                }

                $array = $array[$innerKey];
            }

            return $array;
        }

        // @fallback returning value of $key in $array or $default value
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}

/**
 * Debugging function for debugging javascript side.
 *
 * @param  N numbers of params can be sent
 *-------------------------------------------------------- */
if (!function_exists('__dd')) {
    function __dd()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__dd() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        if (isset($backtrace[0])) {
            $args['debug_backtrace'] = str_replace(__DIR__, '', $backtrace[0]['file']) . ':' . $backtrace[0]['line'];
        }

        echo "";
        // Editors Supported: "phpstorm", "vscode", "vscode-insiders","sublime", "atom"
        $editor = 'vscode';
        echo '</pre><br/><a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="' . $editor . '://file' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '">Open in Editor</a><br/><br/>';

        echo '<pre>';

        array_map(function ($argument) {
            print_r($argument, false);
            echo "<br/><br/>";
        }, $args);
        echo '</pre>';
        exit();
    }
}

/*
    * Debugging function for debugging javascript as well as PHP side, work as likely print_r but accepts unlimited parameters
    *
    * @param  N numbers of params can be sent
    * @return void
    *-------------------------------------------------------- */

if (!function_exists('__pr')) {
    function __pr()
    {
        $args = func_get_args();

        if (empty($args)) {
            throw new Exception('__pr() No arguments are passed!!');
        }

        $backtrace = debug_backtrace();

        // print_r($backtrace);
        // exit();

        echo "";
        // Editors Supported: "phpstorm", "vscode", "vscode-insiders","sublime", "atom"
        $editor = 'vscode';
        echo '</pre><br/><a style="background: lightcoral;font-family: monospace;padding: 4px 8px;border-radius: 4px;font-size: 12px;color: white;text-decoration: none;" href="' . $editor . '://file' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '">' . $backtrace[0]['file'] . ':' . $backtrace[0]['line'] . '</a><br/><br/>';

        echo '<pre>';

        return array_map(function ($argument) {
            print_r($argument, false);
            echo "<br/><br/>";
        }, $args);
    }
}

if (!function_exists('__logDebug')) {
    /**
     * Log helper
     * Writes data in php log file
     *-------------------------------------------------------- */
    function __logDebug() {
        $args = func_get_args();
        $backtrace = debug_backtrace();
        $log_file_path = dirname(__DIR__, 1).'/php.log';
        $log_message = date('Y-m-d H:i:s') . "\n";
        array_map(function ($argument) use (&$log_message, $args) {
            $log_message .= print_r($argument, true) . "\n";
        }, $args);

        $log_message .= 'vscode://file/'. $backtrace[0]['file'] .':'.$backtrace[0]['line'] . "\n";
        return file_put_contents($log_file_path, $log_message, FILE_APPEND);
    }
}

if (!function_exists('lw_current_func')) {
    function lw_current_func($item) {
       if(is_object($item)) {
        if(function_exists('get_mangled_object_vars')) {
            return current(get_mangled_object_vars($item));
        }
       }
       return current($item);
    }
}