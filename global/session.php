<?php
    final class Session
    {
        public static function initialisation_session () : void
        {
            if(!session_id())
            {
                session_start();
                session_regenerate_id();
            }
        }

        public static function effacer_session () : void
        {
            // On enlève aussi les cookies
            if (isset($_SERVER['HTTP_COOKIE'])) 
            {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);

                foreach($cookies as $cookie) 
                {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                }
            }

            unset($_SESSION);
            session_unset();
            session_destroy();
        }
    }
?>