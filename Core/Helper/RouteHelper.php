<?php


namespace Core\Helper;



class RouteHelper
{
    /**
     * Request global
     *
     */
    private $global_request;

    /**
     * Instancia de el helper Main
     *
     * @var MainHelper
     */
    private $main_helper;


    /**
     * RouteHelper constructor.
     */
    public function __construct()
    {
        global $gb_request;
        $this->global_request = $gb_request;

        $this->main_helper = new MainHelper();
    }


    /**
     * Redirect hacia un ruta especifica
     *
     * @param $to
     * @param int $status
     * @return mixed
     */
    public function redirect($to, $status = 302)
    {
        $this->global_request->status = $status;

        if ( stripos($to, 'http') === false ){
            $to = $this->main_helper->getUrl($to);
            $this->global_request->statusText = 'Found';
        }
        else {
            $this->global_request->statusText = 'Permanent Redirect';
            $this->global_request->status = 308;
        }

        $this->global_request->response = $to;
        return $this->global_request;
    }


    // TODO: Realizar el redirect hacia atrás
    public function back($status = 308)
    {
        showDev( getenv('HTTP_REFERER') );

        return 'hola';
    }
}