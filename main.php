<?php
  set_time_limit(0);
  if(@$child != true)
  {
    $server = "irc.freenode.net";
    $connection = fsockopen($server,6667); //connect
    $channels = array("#wenetapls","#hexley","##fuck-you","#jurgenbot");
    $self = $_SERVER['PHP_SELF'];
    $nick = "s0m3b0t";
    fputs ($connection, "USER {$nick} {$nick} {$nick} http://leetfiles.com\n");
    fputs ($connection, "NICK {$nick}\n");
    foreach($channels as $channel)
    {
      echo "Joining {$channel}\n";
      fputs ($connection, "JOIN {$channel}\n");
    }
  }
  //stream_set_blocking($connection,0);
  //$we = fopen ("php://stdin","r"); // enable cli input
  //stream_set_blocking($we,0);
  while(1)
  {
    /*$mydata = fgets($we);
    if($mydata)
    {
      fputs($connection, "{$mydata}\n"); // check for input on the CLI and send them
    }*/
    while($data = fgets($connection)){ // check for messages on the irc side
      echo $data;
      flush();
      $data = str_replace("\n","",str_replace("\r","",$data));
      $a1 = explode(' ', $data);
      $a2 = explode(':', @$a1[3]);
      $a3 = explode('@', @$a1[0]);
      $a4 = explode('!', @$a3[0]);
      $a5 = explode(':', @$a4[0]);
      $a6 = explode(':', @$data);
      $command = @substr($a1[3],1); // remove the :
      $argco = sizeof($a1)-4; // argument count
      $arga = Array(); for($i = 4;$i<sizeof($a1);$i++) { $arga[] = $a1[$i]; }// argument array
      $args = implode($arga," "); // argument string
      $user = @$a5[1];
      $channel = @$a1[2];
      $hostmask = @$a3[1];
      $irccloud_uid = @$a4[1];
      $rawcmd = @$a1[1];
      if($a1[0] == "PING"){fputs($connection, "PONG ".$a1[1]."\n");} // respond to pings from the irc server
      /***
      commands section
      ***/

      if($rawcmd == "INVITE") // follow invites
      {
        fputs($connection,"JOIN {$command}\n");
        fputs($connection,"PRIVMSG {$command} :Hello, {$user} invited me to join this channel.\n");
      }

      //////////////////

      if($command == "test")
      {
        fputs($connection,"PRIVMSG {$channel} :{$user}: u r of #tester\n");
      }

      ///////////////////

      if($command == "!say")
      {
        fputs($connection,"PRIVMSG {$channel} :{$args}\n");
      }

      ///////////////////

      if($command == $nick . ":") // if someone says {$nick}: something
      {
        if($arga[0] == "stfu")
        {
          fputs($connection,"PRIVMSG {$channel} :{$user}: Please don't insult me. I am a harmless bot.\n");
        }
      }

      //////////////////

      if($command == "!restart") // the second or third fork always results in a 'Hangup: 1', idk but it works.
      {
        if(function_exists("pcntl_fork"))
        {
          echo "Trying to fork." . PHP_EOL;
          $pid = pcntl_fork();
          if ($pid == -1)
          {
            fputs($connection,"PRIVMSG {$channel} :Error forking.\n");
            echo "Couldn't fork." . PHP_EOL;
          }
          elseif($pid)
          {
            // still parent
            echo "Still parent. Making ready for rebooting." . PHP_EOL;
            fputs($connection,"PRIVMSG {$channel} :Restarting. Notice this goes \002seamlessly\002\n");
            if(@$child == true)
            {
              echo "Child is {$pid}. Killing parent." . PHP_EOL;
              posix_kill(posix_getpid(), SIGHUP);
            }
            else
            {
              echo "Wating for child with PID {$pid}" . PHP_EOL;
              pcntl_wait($a);
              echo $a . PHP_EOL;
              posix_kill(posix_getpid(), SIGHUP);
            }
          }
          else
          {
          // running as child
          echo "Successfully forked, we are the child." . PHP_EOL;
          $me = file($self);
          unset($me[0]);
          $me = implode(PHP_EOL,$me);
          if(get_resource_type($connection) != "Unknown")
          {
            echo "Reusing existing connection.\n";
            $child = true;
          }
          eval($me);
          }
        }
        else
        {
          fputs($connection,"PRIVMSG {$channel} :ERROR: function pcntl_fork doesn't exist.\n");
        }
      }

      /////////////////

      if($command == "!quit")
      {
        if($hostmask == "unaffiliated/idaniel")
        {
          fputs($connection,"QUIT :bye\n");
        }
        else
        {
          echo "{$user}@{$hostmask} with the uid: {$irccloud_uid} wanted to kill me :(\n";
          fputs($connection,"PRIVMSG {$channel} :{$user} haz no #permission.\n");
        }
      }

      ///////////////

      if($command == "!source") // dirty but I wanna get this working quickly
      {
        $ssum = md5_file($self);
        if($ssum != file_get_contents("md5"))
        {
          file_put_contents("md5",$ssum);
          $cmd = popen("leetfiles-cli php={$self}","r"); // not smartest way, I know. https://github.com/danieltroger/leetfiles-cli since jq made this session cookie shit.....
          $ret = Array();
          while(!feof($cmd))
          {
            $line = fgets($cmd);
            $ret[] = $line;
            echo $line;
          }
          $url = substr($ret[sizeof($ret)-2],5,-4);
          fclose($cmd);
          if(substr($url,0,4) == "http")
          {
            fputs($connection,"PRIVMSG {$channel} :{$user}: View my source at {$url}\n");
            file_put_contents("link",$url);
          }
          else
          {
            fputs($connection,"PRIVMSG {$channel} :{$user}: Sorry, something went wrong.\n");
          }
        }
        else
        {
          fputs($connection,"PRIVMSG {$channel} :{$user}: View my source at " . file_get_contents("link") . "\n");
        }
      }

      //////////////////

      if($command == "!join")
      {
        foreach($arga as $chan)
        {
          fputs($connection,"JOIN {$chan}\n");
          if(str_replace("\r","",$channel) == $nick)
          {
            fputs($connection,"PRIVMSG {$chan} :{$user} PM'ed me to join here.\n");
          }
          else
          {
            fputs($connection,"PRIVMSG {$chan} :{$user} in {$channel} wanted me to join here.\n");
          }
        }
      }

      /////////////////

      if($command == "!part")
      {
        fputs($connection,"PART {$channel}\n");
      }

      /////////////////

      /***
      end of commands section
      ***/
      
      if(strpos($data,"ERROR :Closing Link: ") !== false || @$data[0] == "E")
      {
        fclose($connection);
        die("Disconnected.\n");
      }
    }
  }
  ?>
