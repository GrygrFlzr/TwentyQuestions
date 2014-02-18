# TwentyQuestions

**TwentyQuestions** is a PHP IRC bot originally designed for private use.

As you can probably tell from the name, its original purpose was to host a Twenty Questions game.

## API

```php
/* Core IRC commands */
connect($server, $port = 6667)                          // Connect to an IRC server. Alternatively the constructor can do the same.
quit($reason='')                                        // Disconnect from the IRC server with an optional reason
send($message)                                          // Sends a raw IRC message (end newline included) - used internally by the core.
identify($user, $nick, $name = '', $invisible = false)  // Identify to the IRC server - not to be confused with NickServ IDENTIFY
privmsg($target, $message)                              // Sends a PRIVMSG to the nick or #channel
notice($target, $message)                               // Sends a NOTICE to the nick or #channel
join($channel)                                          // Join a channel
part($channel, $reason = '')                            // Part from a channel with an optional reason
pong($target)                                           // Respond to a PING

/* CTCP protocol */
action($target, $action)                                // Also known as /me
version($target)                                        // Requests the target's IRC Client VERSION

/* NickServ protocol */
ns_identify($password)                                  // Identifies to NickServ

/* Internal commands */
readline()                                              // Reads the last unread message in queue
parseline()->                                           // Parses the last unread message - returns FALSE if none exists
    raw         // Raw IRC text (with newline)
    special     // Whether the parse data should be treated specially or not

    /* special = false */
    sender->    // Data about the sender
        raw         // The raw sender information
        nick        // The nickname of the sender
        user        // The username of the sender
        host        // The hostmask of the sender
    command     // The IRC command - eg. PRIVMSG or NOTICE
    receiver    // The receiver - either the bot name or the #channel it's inside
    message     // The IRC message

    /* special = true */
    command     // The IRC command - eg. PING
    message     // The IRC message
```

## License

(MIT License)

Copyright (c) 2014 Martin Krisnanto Putra

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.