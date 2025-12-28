<?php

namespace App\Services\User;

class UserTracking
{


    public function UserPlatform()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $platform = 'Unknown';
        if (preg_match('/android/i', $user_agent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $user_agent)) {
            $platform = 'iOS';
        } elseif (preg_match('/windows phone/i', $user_agent)) {
            $platform = 'Windows Phone';
        } elseif (preg_match('/linux/i', $user_agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'Windows';
        }
        return $platform;
    }
    public function UserBrowser()
    {
            $user_agent =  $_SERVER['HTTP_USER_AGENT'];
            $bname = 'Unknown';
            $ub = 'Unknown';

            // Detect Browser
            if (preg_match('/MSIE|Trident/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
                $bname = 'Internet Explorer';
                $ub = "MSIE";
            } elseif (preg_match('/Edge/i', $user_agent)) {
                $bname = 'Microsoft Edge';
                $ub = "Edge";
            } elseif (preg_match('/Firefox/i', $user_agent)) {
                $bname = 'Mozilla Firefox';
                $ub = "Firefox";
            } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edg/i', $user_agent)) {
                $bname = 'Google Chrome';
                $ub = "Chrome";
            } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Chrome/i', $user_agent)) {
                $bname = 'Apple Safari';
                $ub = "Safari";
            } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
                $bname = 'Opera';
                $ub = "Opera";
            } elseif (preg_match('/Netscape/i', $user_agent)) {
                $bname = 'Netscape';
                $ub = "Netscape";
            }
            return  $bname;
    }

    public function  MacAddress(){
        $output = shell_exec("ipconfig /all");
            preg_match('/Physical Address[. ]+: ([\w-]+)/i', $output, $matches);
            $mac_address = $matches[1] ?? 'Not Found';
            return $mac_address;
    }

    public function IpAddress(){
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

    public function IpLocation(){
        $ip = $_SERVER['REMOTE_ADDR'];
        $location_data = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);
        return $location_data;
    }

}
