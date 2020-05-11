<?php
class Timer {
    function startTimer() {
        global $starttime;
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
    
    
    function endTimer() {
        global $starttime;
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round(($endtime - $starttime), 5);
        return $totaltime;
    }
}
?>