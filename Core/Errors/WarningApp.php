<?php


namespace Core\Errors;


use Core\Routing\ValidationRequest;

class WarningApp
{
    /**
     * Muestra advertencias relacionadas al tiempo de ejection
     *
     * @return string
     */
    public function showWarningsApp()
    {
        global $gb_request;
        $show = '';

        if ( count($gb_request->warningApp) && $gb_request->environment !== 'production' && !ValidationRequest::isAjax() )
        {
            $show .= "
                <style>
                    .warning-app-minos-golding {
                        position: fixed;
                        background-color: #51172d;
                        color: white;
                        font-family: Arial, sans-serif;
                        font-weight: lighter;
                        padding: 8px; 
                        width: 100%;
                        bottom: 0;
                        left: 0; 
                        z-index: 10; 
                   
                    }
                    .warning-app-minos-golding ul {
                        list-style: none;
                        line-height: 1.5;
                        font-size: 13px;
                    }
                    .warning-app-minos-golding li {
                        padding: 4px 0;
                        border-bottom: 1px solid white;
                    }
                    .warning-app-minos-golding li:last-of-type {
                        border: none                    
                    }
                    .warning-app-minos-golding ul li {
                        line-height: 1.7em;
                    }
                    .warning-app-minos-golding b { font-weight: bold; }
                </style>
            ";

            $show .= '<div class="warning-app-minos-golding">';
            $show .= '<ul>';
            foreach ( $gb_request->warningApp as $warning ) {
                $show .= "<li>{$warning}</li>";
            }
            $show .= '</ul>';
            $show .= '</div>';
        }

        return $show;
    }


    public function showTimingApp( $show, $start_time, $name_framework, $version_framework )
    {
        global $gb_request;
        if ( $gb_request->environment !== 'production' && !ValidationRequest::isAjax() && $show )
        {
            // Finaliza el tiempo de ejecución
            $end_time = microtime(true);

            // Calculo final del tiempo
            $duration = $end_time - $start_time;

            $show = " 
                <style>
                    .duration-app-minos-golding {
                        position: fixed;
                        font-size: 16px;
                        background-color: rgb(73, 70, 100);
                        color: white;
                        font-family: Arial, sans-serif;
                        font-weight: lighter;
                        padding: 12px 2px; 
                        width: 100%;
                        bottom: 0;
                        left: 0; 
                    }
                    .duration-app-minos-golding p {
                        line-height: 1;
                        font-size: 13px; 
                        text-align: center; 
                        margin: 0;
                    }
                    .duration-app-minos-golding b { font-weight: bold; }
                </style>";

            $show .= "
            <div class='duration-app-minos-golding'>
                <p>Tiempo de ejecución: <b>{$duration}</b> - {$name_framework} v{$version_framework}</p>
            </div>";

            echo $show;
        }
    }
}