<?php

namespace Core\Sessions;

use SessionHandlerInterface;
use Core\Files\FileHandler;
use Symfony\Component\Finder\Finder;
use Carbon\Carbon;

class FileSessionHandler implements SessionHandlerInterface
{
    /**
     * File handler instance
     * 
     * @var Core\Files\FileHandler
     */
    protected $fileHandler;

    /**
     * Path to save sessions
     * 
     * @var string
     */
    protected $savePath;

    /**
     * Minutes of session expiration
     * 
     * @var int
     */
    protected $expiration;

    /**
     * Should encrypt session data
     * 
     * @var bool
     */
    protected $encrypt;

    /**
     * Class construct to use as session handler driver
     * 
     * @param Core\Files\FileHandler Handle filesystem actions
     * @param string $savePath Path to save sessions
     * @return void
     */
    public function __construct(FileHandler $fileHandler, string $savePath, int $expiration, bool $encrypt=false)
    {
        $this->fileHandler = $fileHandler;
        $this->savePath = $savePath;
        $this->expiration = $expiration;
        $this->encrypt = $encrypt;
    }

    /**
     * {@inheritdoc}
     */
    public function close() 
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($session_id)
    {
        if ( $this->fileHandler->isFile($path = $this->savePath .DIRECTORY_SEPARATOR. $session_id) ) {
            unlink($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        $files = Finder::create()->in($this->savePath)
                    ->files()
                    ->date("<= now - $maxlifetime seconds");
        foreach($files as $file) {
            unlink($files);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($session_id)
    {
        if ( $this->fileHandler->isFile($path = $this->savePath .DIRECTORY_SEPARATOR. $session_id) ) {
            if ( filemtime($path) >= Carbon::now()->subMinutes($this->expiration)->timestamp ) {
                return $this->fileHandler->get($path);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data)
    {
        return $this->fileHandler->append($this->savePath .DIRECTORY_SEPARATOR. $session_id, $session_data);
    }

    /**
     * Indicates wheter session should be encrypted
     * 
     * @return bool
     */
    public function shouldEncrypt()
    {
        return $this->encrypt;
    }

    public function getSavePath()
    {
        return $this->savePath;
    }
}