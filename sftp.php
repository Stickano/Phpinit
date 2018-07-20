<?php

class Sftp {

    private $host;
    private $port;
    private $session;
    private $sftp;

    private $privateKey;
    private $publicKey;

    public function __construct(string $host, int $port=22) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Sets, and activates, the path to public and private SSH key
     * @param string $public  Path to public-key (~/.ssh/id_rsa.pub)
     * @param string $private Path to private-key (~/.ssh/id_rsa)
     */
    public function Keys(string $public, string $private) {
        $this->privateKey = $private;
        $this->publicKey  = $public;
    }

    /**
     * Connect to a server
     * @param string      $username The username to connect with
     * @param string|null $password The password to use. Can be left
     *                              blank, ie. if using keys
     */
    public function Connect(string $username, string $password=null) {
        $failed = false;
        if (!$this->session = ssh2_connect($this->host, $this->port)){
            $failed = true;
            throw new Exception("Cannot connect to server");
        }

        # Password
        if (!isset($this->privateKey)){
            if (!ssh2_auth_password($this->session, $username, $password)){
                $failed = true;
                throw new Exception("Invalid username or password");
            }
        }

        # Priv/pub keys
        if (isset($this->privateKey)){
            if (!ssh2_auth_pubkey_file($this->session,
                                        $username,
                                        $this->publicKey,
                                        $this->privateKey,
                                        $password )){
                $failed = true;
                throw new Exception("Authentication rejected by server");
            }
        }

        if(!$failed)
            $this->sftp = ssh2_sftp($this->session);
    }

    /**
     * Scans a given directory on the connected host
     * @param string $dir The directory to scan
     */
    public function ScanDir(string $dir){
        if(isset($this->sftp))
            return scandir('ssh2.sftp://'.(int)$this->sftp . $dir);
    }

    /**
     * Send a file to the host
     * @param string      $src   Source destination (path/local)
     * @param string      $dst   Remote destination (path/remote)
     * @param int|integer $chmod Permissions. Default rw, r, r
     */
    public function SendFile(string $src, string $dst, int $chmod=644) {
        $chmod = "0"+$chmod;
        if(isset($this->sftp))
            return ssh2_scp_send($this->session, $src, $dst, $chmod);
    }

    /**
     * Request a file from the host
     * @param string $src Source destination (path/remote)
     * @param string $dst Local destination (path/local)
     */
    public function ReceiveFile(string $src, string $dst) {
        if(isset($this->sftp))
            return ssh2_scp_recv($this->session, $src, $dst);
    }

    /**
     * Open a file on the host
     * @param string $dst The destination of the file (remote)
     */
    public function OpenFile(string $dst) {
        if(isset($this->sftp))
            return fopen("ssh2.sftp://".(int)$sftp . $dst, 'r');
    }

    /**
     * Deletes a file from the host
     * @param string $dst The destination file (path/remote)
     */
    public function RemoveFile(string $dst) {
        if ($this->sftp)
            return ssh2_sftp_unlink($this->sftp, $dst);
    }

    /**
     * Creates a new folder on host
     * @param string $dst Destination for the foler (path/remote)
     */
    public function CreateFolder(string $dst) {
        if ($this->sftp)
            return ssh2_sftp_mkdir($this->sftp, $dst);
    }

    /**
     * Deletes an EMPTY folder
     * @param string $dst The folder to remove (Needs to be empty)
     */
    public function RemoveFolder(string $dst) {
        if ($this->sftp)
            return ssh2_sftp_rmdir($this->sftp, $dst);
    }

}

?>