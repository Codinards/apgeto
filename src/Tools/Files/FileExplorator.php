<?php

namespace App\Tools\Files;

class FileExplorator
{
    public static function render($data)
    {
        $style = 'width:10wh;background-color:grey;';
        return <<<HTML
<div style= $style >
    $data
<div>
HTML;
    }

    private static function forbidden($name)
    {
        return stripos($name, '.Bin') or stripos($name, '$');
    }

    public static function opendir(string $file)
    {
        $path = explode('?', $_SERVER['REQUEST_URI']);
        $path = $path[0];
        $isForbidden = self::forbidden($file);
        if ($isForbidden) {
            return '';
        }
        $string = '<ul>';
        //dir(exec('echo %PUBLIC%') . '/Desktop')
        if ($dir = opendir($file)) {
            /*$stat = json_encode(stat($file));
            $string .= dump($stat);*/
            $string .= "<h2>Pointeur : ' . $dir . '</h2><br/>\n";
            $string .= "<h3>Chemin : ' . $file . '</h3><br/>\n";
            while ($entry = readdir($dir)) {
                if ($entry !== '.' and $entry !== '..') {
                    if (is_dir($subDir = $file . '\\' . $entry) and is_writable($subDir) and !self::forbidden($subDir)) {
                        $href = $path . '?file=' . $subDir;
                        $string .= "<li><a href='$href'>$subDir</a></li>";
                        $entry . "<br/>\n";
                        //$string .= self::opendir($subDir, $i++);
                        //$i = $init;
                    }
                }
            }
            closedir($dir);
        }

        return $string . '</ul>';
    }
}
