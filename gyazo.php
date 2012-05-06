<?php
namespace net\chobie;

/**
 * the gyazo server
 */
class Gyazo
{
    /* @var string $uri */
    protected $uri;

    /* @var string $uri */
    protected $path;

    public function __construct($path)
    {
        $this->uri = 'http://' . $_SERVER['HTTP_HOST'] . '/grab/';;
        $this->path = rtrim($path,"/");
    }

    public function handleRequest()
    {
        if (!isset($_FILES['imagedata']['error']) || $_FILES['imagedata']['size'] < 1) {
            echo $this->uri, 'invalid.png';
            exit;
        }

        $file = file_get_contents($_FILES['imagedata']['tmp_name']);
        $hash = sha1($file);
        unset($file);

        $filename = sprintf("%s/%s",substr($hash,0,2),substr($hash,2) . ".png");
        $filepath = sprintf("%s/%s",$this->path,$filename);

        if (!is_dir(sprintf("%s/%s",$this->path,substr($hash,0,2)))) {
            mkdir(sprintf("%s/%s",$this->path,substr($hash,0,2)));
        }

        if ( !move_uploaded_file($_FILES['imagedata']['tmp_name'], $filepath) ) {
            echo $this->uri, 'error.png';
            exit;
        }

        $image = imagecreatefrompng($filepath);
        imagepng($image, $filepath, 9);
        imagedestroy($image);

        echo $this->uri, $filename;
    }

}

$gyazo = new Gyazo('/home/chobie/gyazo/grab');
$gyazo->handleRequest();
