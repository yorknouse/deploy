<?php

date_default_timezone_set('Europe/London');

class Deploy {

    /**
    * A callback function to call after the deploy has finished.
    * 
    * @var callback
    */
    public $post_deploy;

    /**
    * The name of the file that will be used for logging deployments. Set to 
    * FALSE to disable logging.
    * 
    * @var string
    */
    private $_log = FALSE; # '/var/log/deployments.log'

    /**
    * The timestamp format used for logging.
    * 
    * @link http://www.php.net/manual/en/function.date.php
    * @var  string
    */
    private $_date_format = 'Y-m-d H:i:sP';

    /**
    * The name of the branch to pull from.
    * 
    * @var string
    */
    private $_branch = 'master';

    /**
    * The name of the remote to pull from.
    * 
    * @var string
    */
    private $_remote = 'origin';

    /**
    * The directory where your website and git repository are located, can be 
    * a relative or absolute path
    * 
    * @var string
    */
    private $_directory;

    /**
    * Sets up defaults.
    * 
    * @param  string  $directory  Directory where your website is located
    * @param  array   $data       Information about the deployment
    */
    public function __construct($directory, $options = array())
    {
        // Determine the directory path
        $this->_directory = realpath($directory).DIRECTORY_SEPARATOR;

        $available_options = array('log', 'date_format', 'branch', 'remote');

        foreach ($options as $option => $value)
        {
           if (in_array($option, $available_options))
            {
                $this->{'_'.$option} = $value;
            }
        }

        $this->log('Attempting deployment...');
    }

    /**
    * Writes a message to the log file.
    * 
    * @param  string  $message  The message to write
    * @param  string  $type     The type of log message (e.g. INFO, DEBUG, ERROR, etc.)
    */
    public function log($message, $type = 'INFO')
    {
        if ($this->_log)
        {
            // Set the name of the log file
            $filename = $this->_log;

            if ( ! file_exists($filename))
            {
                // Create the log file
                file_put_contents($filename, '');

                // Allow anyone to write to log files
                chmod($filename, 0666);
            }

            // Write the message into the log file
            // Format: time --- type: message
            file_put_contents($filename, date($this->_date_format).' --- '.$type.': '.$message.PHP_EOL, FILE_APPEND);
        }
    }

    /**
    * Executes the necessary commands to deploy the website.
    */
    public function execute()
    {
        try
        {
            $output = array();
                    
            // Make sure we're in the right directory
            chdir($this->_directory);
            $this->log('Changing working directory...');

            // Update the local repository
            $this->log('Fetching repository... ');
            $this->log(shell_exec('git fetch --all 2>&1'));
            $this->log(shell_exec('git checkout --force '.$this->_remote.'/'.$this->_branch.' 2>&1'));

            // Update submodules
            $this->log('Updating submodules...');
            $this->log(shell_exec('git submodule sync; git submodule update --init --recursive 2>&1'));

            // Secure the .git directory
            exec('chmod -R og-rx .git');
            $this->log('Securing .git directory... ');

            if (is_callable($this->post_deploy))
            {
                    call_user_func($this->post_deploy, $this->_data);
            }

            $this->log('Deployment successful.');
        }
        catch (Exception $e)
        {
            $this->log($e, 'ERROR');
        }
    }

}
