<?php
/**
 * Twenty Questions IRC Bot
 * 
 * @author      GrygrFlzr
 * @copyright   (c) Martin Krisnanto Putra
 */

namespace TwentyQuestions;

/**
 * TQBot
 * 
 * Main bot class
 * 
 * @package TwentyQuestions
 */
class TQBot {
    /**
     * Class properties
     */
    
    /**
     * The socket resource of the bot
     *
     * @var resource
     * @access protected
     */
    protected $socket;
    
    /**
     * The username of the bot
     *
     * @var string 
     * @access protected
     */
    protected $user;
    
    /**
     * The nickname of the bot
     *
     * @var string 
     * @access protected
     */
    protected $nick;
    
    /**
     * The real name of the bot
     *
     * @var string 
     * @access protected
     */
    protected $name;
    
    /**
     * Array of the responses to match on dispatch
     *
     * @var array
     * @access protected
     */
    protected $responses;
    
    /**
     * List of channels
     *
     * @var array
     * @access protected
     */
    protected $channels;
    
    /**
     * Methods
     */
    
    /**
     * Constructor
     * 
     * Create a new bot instance
     * 
     * @param string $server Address of the IRC server.
     * @param int $port Port of the IRC server. Defaults to 6667
     * @access public
     */
    public function __construct($server='', $port=6667) {
        if(!empty($server)) {
            $this->connect($server, $port);
        }
    }
    
    /**
     * Destructor
     * 
     * Closes the socket of the bot
     * @access public
     */
    public function __destruct() {
        sleep(1);
    }
    
    /**
     * Connect to an IRC server
     * 
     * @param string $server Address of the IRC server.
     * @param int $port Port of the IRC server. Defaults to 6667
     * @access public
     */
    public function connect($server,$port=6667) {
        $this->socket = fsockopen($server, $port);
        stream_set_blocking($this->socket, false);
    }
    
    /**
     * Disconnect from the IRC server
     * @param string $reason Optional reason for quitting
     * @access public
     */
    public function quit($reason='') {
        $this->send("QUIT :$reason");
    }
    
    /**
     * Sends a message to the IRC server
     * 
     * @param string $message
     * @access public
     * @return bool <b>TRUE</b> if successfully sent, <b>FALSE</b> otherwise
     */
    public function send($message) {
        if(isset($this->socket)) {
            if(fwrite($this->socket, "$message\n") !== false) {
                echo ":$this->user $message\n";
                return true;
            }
        }
        return false;
    }
    
    /**
     * Identifies to the IRC server
     * 
     * @param string $user
     * @param string $nick
     * @param string $name
     * @param bool $invisible
     * @access public
     */
    public function identify($user, $nick, $name='', $invisible=false) {
        $invisible_flag = $invisible ? '8' : '0';
        $this->user = $user;
        $this->nick = $nick;
        $this->name = $name;
        $this->send("USER $this->user $invisible_flag * :$this->name");
        $this->send("NICK $this->nick");
    }
    
    /**
     * Sends a Private Message to the target.
     * Includes channels.
     * @param string $target
     * @param string $message
     * @access public
     */
    public function privmsg($target, $message) {
        $this->send("PRIVMSG $target :$message");
    }
    
    /**
     * Sends a NOTICE to the target.
     * Includes channels.
     * @param string $target
     * @param string $message
     * @access public
     */
    public function notice($target, $message) {
        $this->send("NOTICE $target :$message");
    }
    
    /**
     * Joins a channel
     * @param string $channel The channel name - include the hashtag
     * @access public
     */
    public function join($channel) {
        $this->send("JOIN $channel");
    }
    
    /**
     * Parts a channel
     * @param string $channel The channel name - include the hashtag
     * @param string $reason Optional reason for leaving
     * @access public
     */
    public function part($channel, $reason='') {
        $this->send("PART $channel :$reason");
    }
    
    /**
     * Respond to a PING
     * 
     * @param string $target
     */
    public function pong($target) {
        $this->send("PONG :$target");
    }
    
    /**
     * Sends a CTCP ACTION to the target.
     * Example: * TwentyQuestions slaps victim with a trout
     * @param string $target
     * @param string $action
     * @access public
     */
    public function action($target, $action) {
        $this->privmsg($target, "\x01ACTION $action\x01");
    }
    
    /**
     * Sends a CTCP VERSION to the target.
     * 
     * @param string $target
     * @access public
     */
    public function version($target) {
        $this->privmsg($target, "\x01VERSION\x01");
    }
    
    /**
     * Identify to NickServ
     * 
     * @param string $password
     * @access public
     */
    public function ns_identify($password) {
        $this->privmsg('NickServ', "IDENTIFY $password");
    }
    
    /**
     * Return any incoming messages
     * 
     * @access public
     * @return string
     */
    public function readline() {
        return fgets($this->socket, 512);
    }
    
    /**
     * Parse incoming messages
     * 
     * @access public
     * @return \stdClass|bool An object containing the message,
     * <b>FALSE</b> otherwise
     */
    public function parseline() {
        $data = $this->readline();
        if(!empty($data)) {
            $message = new \stdClass();
            
            $pieces = explode(' ', trim($data));
            
            if($data{0} === ':') {
                $sender = new \stdClass();
                $sender->raw = ltrim(array_shift($pieces), ':');
                
                $sender_piece1 = explode('!', $sender->raw);
                $sender_piece2 = explode('@', array_pop($sender_piece1));

                $sender->nick = array_shift($sender_piece1);
                $sender->user = ltrim(array_shift($sender_piece2), '~');
                $sender->host = array_pop($sender_piece2);
                
                $message->sender = $sender;
                $message->command = array_shift($pieces);
                $message->reciever = array_shift($pieces);
                $message->message = ltrim(implode(' ', $pieces), ':');
                $message->special = false;
            } else {
                //Other commands like PING
                $message->command = array_shift($pieces);
                $message->message = ltrim(implode(' ', $pieces), ':');
                $message->special = true;
            }
            $message->raw = $data;
            
            return $message;
        }
        return false;
    }
    
    /**
     * Add to response list
     * 
     * TODO: Doesn't even work yet
     * 
     * @param string $query Query to match
     * @param callable $callback
     * @access public
     * @return callable $callback
     */
    public function respond($query='*', $callback = null) {
        return $callback;
    }
}
