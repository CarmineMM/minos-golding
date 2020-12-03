<?php


namespace Core\Foundation;


use App\Config\FileSystem;
use Core\Helper\MainHelper;

class File
{
    use FileSystem;

    /**
     * Nombre original del archivo
     *
     * @var string
     */
    private $original_name;

    /**
     * Tipo de archivo
     *
     * @var string
     */
    private $type = false;

    /**
     * Ruta temporal
     *
     * @var string
     */
    private $tmp_name;

    /**
     * Peso del archivo
     *
     * @var integer
     */
    private $size = false;

    /**
     * Cualquier error que pueda reflejar el error
     *
     * @var
     */
    private $error = 0;

    /**
     * Mensaje de errores en la subida de archivos
     *
     * @var
     */
    private $error_message = [];

    /**
     * Ruta de la carpeta donde se almacenara el archivo
     *
     * @var
     */
    private $folder;

    /**
     * Tendrá la clase request
     *
     * @var
     */
    private $request;

    /**
     * Establecer un nombre al archivo
     *
     * @var
     */
    private $file_name = false;


    /**
     * File constructor.
     *
     * @param $file
     */
    public function __construct($file)
    {
        global $gb_request;
        $this->request = $gb_request;

        if ( !is_array($file) && !isset($file['tmp_name']) ){
            $this->error++;
            $this->error_message[] = "No se pudo cargar la imagen";
            return $gb_request->warningApp[] = "No se pudo cargar la imagen";
        }

        $this->original_name = $file['name'];
        $this->type      = $file['type'];
        $this->tmp_name  = $file['tmp_name'];
        $this->error     = $file['error'];
        $this->size      = $file['size'];
        $this->folder    = G_PATH.MainHelper::parseDirectory($this->folder_uploads);

        $this->file_name = $this->original_name;
        return true;
    }

    /**
     * Establece un carpeta a donde guardar
     *
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = MainHelper::parseDirectory($folder);
    }


    /**
     * @return string
     */
    public function getFileType(): string
    {
        $type_file = explode('.', $this->original_name);
        return $type_file[ count($type_file)-1 ];
    }


    /**
     * Obtiene el tipo de archivo
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * Validación del tipo de archivo
     * 1. Imágenes:
     *  1.1. .jpg, .jpeg = image/jpeg
     *  1.2. .png        = image/png
     *  1.3. .webp       = image/png
     *  1.4. .svg        = image/svg+xml
     *
     * 2. Videos:
     *  2.1. .mp4 = video/mp4
     *
     * @param $type
     * @return string
     */
    public function validateType($type)
    {
        if ( $this->type === false || $type !== $this->type ) {
            $this->error++;
            $this->error_message[] = "Tipo de archivo no admitido";
        }

        return $this;
    }


    /**
     * Valida que sea una imagen
     * También valida un tipo especifico de imagen
     *
     * @param bool|string $type - Tipo especifico
     * @return $this
     */
    public function isImage($type = false)
    {
        if (
            $this->type === false ||
            strpos($this->type, 'image') === false ||
            ($type !== false && $this->type !== "image/{$type}")
        ) {
            $this->error++;
            $this->error_message[] = trim("Tipo de archivo no admitido, se espera una imagen/{$type}", '/');
        }

        return $this;
    }


    /**
     * Valida el tamaño maximo de un archivo
     *  1KB   = 1000
     *  30KB  = 30000
     *  500KB = 500000
     *  1MB   = 1000000
     *  22MB  = 22000000
     *  700MB = 700000000
     *
     * @param $size
     * @return $this
     */
    public function validateMaxSize($size)
    {
        if ( $this->size === false || $this->size > $size ){
            $this->error++;
            $this->error_message[] = "Tamaño del archivo supera el limite permitido: ".$this->sizeToConvert($size);
        }

        return $this;
    }


    /**
     * Valida el tamaño mínimo de un archivo
     *  1KB   = 1000
     *  30KB  = 30000
     *  500KB = 500000
     *  1MB   = 1000000
     *  22MB  = 22000000
     *  700MB = 700000000
     *
     *
     * @param $size
     * @return $this
     */
    public function validateMinSize($size)
    {
        if ( $this->size === false || $size > $this->size ){
            $this->error++;
            $this->error_message[] = "Tamaño del archivo esta por debajo de lo esperado: ".$this->sizeToConvert($size);
        }

        return $this;
    }


    /**
     * Devuelve los errores en forma numérico.
     * (cero) 0 si no existen errores
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * Total de errores ocurridos a lo largo de la validación
     *
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }


    /**
     * Convierte los tamaños es expresiones que se entienden
     *
     * @param int $size
     * @return string
     */
    private function sizeToConvert(int $size)
    {
        $convert = $size.' Bytes';
        switch ( $size ){
            case $size > 1000000:
                $convert = round($size/1000000, 2) . ' MB';
            break;
            case $size > 1000:
                $convert = round($size/1000, 2) . ' KB';
            break;
        }

        return $convert;
    }

    /**
     * Establece un nombre al archivo.
     * Si nose establece un nombre, tomara el nombre original,
     * al momento de ser guardado.
     *
     * @param mixed $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }


    /**
     * Mueve el archivo y lo guarda.
     * se puede especificar un nuevo destino si se dese.
     * Por defecto el destino es el configurado
     *
     * @param $newDestiny - destino nuevo
     * @return false
     */
    public function save($newDestiny = false)
    {
        if ( $this->getError() !== 0 ) return false;

        $move = $newDestiny ? $this->folder . DG . MainHelper::parseDirectory($newDestiny) : $this->folder;

        // Crea la carpeta en caso de no existir
        if ( !is_dir($move) ) {
            mkdir($move, 0777, true);
        }

        $file = $this->original_name === $this->file_name ? $this->original_name : $this->file_name . '.' . $this->getFileType();
        $file_to_move = $move . DG . $file;

        $is_moved = move_uploaded_file($this->tmp_name, $file_to_move);

        return $is_moved ? $file : false;
    }
}