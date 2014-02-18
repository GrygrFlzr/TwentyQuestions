<?php
/**
 * Twenty Questions IRC Bot Example
 * 
 * @author      GrygrFlzr
 * @copyright   (c) Martin Krisnanto Putra
 * @link        https://github.com/GrygrFlzr/TwentyQuestions
 * @license     MIT
 */
require_once 'config_default.php';

require_once 'TwentyQuestions.php';
require_once 'osu.php';

set_time_limit(0);
date_default_timezone_set('UTC');
// Used for uptime
$start = time();

$tq = new \TwentyQuestions\TQBot('tls://irc.esper.net', 6697);

$tq->identify('TestTQ', 'TestTQ', 'TestTQ');
//$tq->ns_identify(PASSWORD);

while(1) {
    $data = $tq->parseline();
    if($data !== false) {
        // Outputs data in console
        echo $data->raw;
        flush();
        
        if($data->special) {
            // Respond to PING - should be handled in core once respond() is implemented
            if($data->command === 'PING') {
                $tq->pong($data->message);
            }
        } else {
            // Join a channel!
            if($data->command === '001') {
                $tq->join('#vazkii');
            }
            // Debug test for future user tracking
            if($data->command === '353') {
                var_dump($data);
            }
            // CTCP VERSION request - should be handled in core once respond() is implemented
            if($data->message === "\x01VERSION\x01") {
                // Should have version as a variable instead of hardcoding
                $tq->notice($data->sender->nick, "\x01VERSION TwentyQuestions 0.0.2\x01");
            }
            // Simple example uptime
            if($data->message === '!uptime') {
                $uptime = time()-$start;
                $s = $uptime % 60;
                $m = (floor($uptime / 60)) % 60;
                $h = floor($uptime / 3600);
                $tq->privmsg($data->reciever, "Uptime: {$h}h{$m}m{$s}s");
            }
            // Use of the osu! API module
            if(startsWith($data->message, '!stats')) {
                $ex = explode(' ', $data->message);
                array_shift($ex);
                
                // If no username specified, use nickname of sender
                if(empty($ex)) {
                    $username = $data->sender->nick;
                } else {
                    $username = implode(' ', $ex);
                }
                
                // Alias handling
                $username = mapUsername($username);
                
                $user_data = array_shift(osuStat($username));
                var_dump($user_data);
                if($user_data !== null) {
                    $tq->privmsg($data->reciever,
                            "[\x0313osu!\x03] " .
                            $user_data['username'] .
                            ' | Rank #' . number_format($user_data['pp_rank']) .
                            ' | Score ' . number_format($user_data['ranked_score']) . ' with ' .
                            $user_data['count_rank_ss'] . 'SS/' .
                            $user_data['count_rank_s'] . 'S/' .
                            $user_data['count_rank_a'] . 'A' .
                            ' | Acc ' . $user_data['accuracy'] . '%' .
                            ' | ' . number_format($user_data['playcount']) . ' plays' .
                            ' | Lv' . $user_data['level'] .
                            ' | ' . $user_data['pp_raw'] . 'PP'
                            );
                } else {
                    $tq->privmsg($data->reciever, 'That name does not exist in the osu! database...');
                }
            }
            // This is an insecure way to check, should check for IDENTIFY and/or host mask
            if($data->message === '!quit' && $data->sender->nick === 'GrygrFlzr') {
                break;
            }
        }
    }
}

$tq->quit('Test complete');

/**
 * Function I use to alias osu! usernames
 * Note that this means actual accounts with the aliased names cannot be checked for
 * 
 * @param string $username The requested username
 * @return string The final username
 */
function mapUsername($username) {
    $toMap = array(
        'lauri^^'      => 'Ladexi',
        'luckyalfa'    => 'lalfa',
        'krudda'       => 'Imperf3kt',
        'chhotu_uttam' => 'Chhotu uttam',
        'satenruiko'   => 'dbx10',
        'lizbeth'      => 'Vazkii',
        'fenn'         => 'Pokefenn',
    );
    if(isset($toMap[strtolower($username)])) {
        $username = $toMap[strtolower($username)];
    }
    return $username;
}