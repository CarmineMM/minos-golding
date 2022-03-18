<?php


namespace Core\Foundation;


use PDO;
use Exception;
use App\Config\Database;
use Core\Errors\HttpException;
use Core\Helper\SupportHelper;

class Model
{
    use Database;

    /**
     * Conexión a la base de datos
     *
     * @var mixed
     */
    private $connection;


    /**
     * Indica los campos que serán visibles
     *
     * @var array
     */
    protected $visible = ['*'];


    /**
     * Campos que pueden ser llenados de forma masiva
     *
     * @var array
     */
    protected $allowed = [];


    /**
     * Campos auditable
     *
     * @var string
     */
    protected $useTimestamps = false;
    protected $updated_field = 'updated_at';
    protected $created_field = 'created_at';


    /**
     * Soft deletes
     *
     * @var bool
     */
    protected $useSoftDeletes = false;
    protected $deleted_field  = 'deleted_at';


    /**
     * Tabla donde se ejecutan los scripts
     *
     * @var string
     */
    protected $table = '';


    /**
     * Query que se va ir construyendo conforme se llamen las funciones
     *
     * @var string
     */
    private $query_sql = '';


    /**
     * Establece cual es la columna dl Primary Key
     *
     * @var string
     */
    protected $column_id = 'id';


    /**
     * Parámetros pasador por el usuario para actualizar.
     *
     * @var array
     */
    private $parameters = [];


    /**
     * Parámetros forzados para introducir en la base de datos
     *
     * @var array
     */
    private $force_params = [];


    /**
     * Model constructor.
     */
    public function __construct()
    {
        global $gb_request;
        $dsn = "$this->drive:dbname=$this->database;host=$this->host;port=$this->port_db;charset=utf8";

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        }
        catch (\PDOException $e) {
            $gb_request->warningApp[] = "Ha ocurrido un error en establecer una conexión con la base de datos";
            $gb_request->warningApp[] = $e->getMessage();
            $this->connection = false;
        }

        // Configura la tabla de forma automática
        if ( $this->table === '' ) {
            $table = explode('\\', get_called_class());
            $this->table = strtolower(array_pop($table)).'s';
        }
    }


    /**
     * Hacer query's a la base de datos mediante los placeholder de PDO
     *
     * @param $sql
     * @param array $param
     * @param bool $count
     * @return array|bool|string
     */
    public function query($sql, $param = [])
    {
        if ( !$this->connection ) {
            global $gb_request;
            $gb_request->warningApp[] = "No se pudo llevar a cabo la consulta";
            $gb_request->warningApp[] = "Consulta preparada: ".$this->query_sql;
            return false;
        }
        $this->connection->beginTransaction();

        $query = $this->connection->prepare($sql);
        $this->query_sql = ''; # Libera la sentencia ejecutada

        // Manejo de errores
        if ( !$query->execute($param) ) {
            $this->connection->rollBack();
            // 0. El tipo del error
            // 1. Código del error
            // 2. Mensaje del error
            $error = $query->errorInfo()[2];
            throw new Exception($error);
        }

        // Detectar el tipo de sentencia SQL
        if ( strpos($sql, 'SELECT') !== false )
        {  
            $data = $query->fetchAll(PDO::FETCH_OBJ);
            $this->connection->commit();
            return $data;
        }

        // INSERTs
        elseif ( strpos($sql, 'INSERT') !== false )
        {
            $id = $this->connection->lastInsertId();
            $this->connection->commit();
            return $id;
        }

        // UPDATEs
        elseif ( strpos($sql, 'UPDATE') !== false )
        {
            $this->connection->commit();
            return true;
        }

        // DELETEs
        elseif ( strpos($sql, 'DELETE') !== false )
        {
            if ( $query->rowCount() > 0 ) {
                $this->connection->commit();
                return true;
            }

            $this->connection->rollBack();
            return false;
        }
        else{
            // ALTER TABLE | DROP TABLE | ETC...
            $this->connection->commit();
            return true;
        }
    }


    /**
     * Construye la sentencia SQL para traer los datos
     * Delimitando los campos visibles
     *
     * @param bool $softDeletes - Traer hasta los eliminador de forma lógica
     * @return $this
     */
    public function all($softDeletes = false)
    {
        $visible = implode(', ', $this->visible);

        $this->query_sql = "SELECT {$visible} FROM {$this->table}";

        // Traer los eliminados lógicamente
        if ( $this->useSoftDeletes && !$softDeletes ) {
            $this->query_sql .= " WHERE {$this->deleted_field} IS NULL";
        }
        return $this;
    }


    /**
     * Delimita los campos que se requieren traer
     * Solo traerá las columnas que indique la función
     *
     * @param mixed ...$only
     * @return $this
     */
    public function only(...$only)
    {
        $only = implode(', ', $only);
        $this->query_sql = str_replace($this->visible, $only, $this->query_sql);
        return $this;
    }


   
    /**
     * Suma una o mas columnas
     *
     * @param string $column
     * @return void
     */
    public function sum(...$columns)
    {
        $sums = '';
        $count = 1;
        foreach ($columns as $column) {
            $sums .= count($columns) === $count ? "sum($column) AS $column" : "sum($column) AS $column, ";
            $count++;
        }
        $this->query_sql = str_replace($this->visible, $sums, $this->query_sql);
        return $this->exec()[0];
    }


    /**
     * Trae solo a los eliminador de forma lógica
     *
     * @return $this
     */
    public function allDeletes()
    {
        if ( $this->useSoftDeletes )
            $this->query_sql = "SELECT * FROM {$this->table} WHERE {$this->deleted_field} IS NOT NULL";
        return $this;
    }


    /**
     * Delimita la sentencia SQL con un WHERE
     *
     * @param $column
     * @param $sentenceOrSpecial - Sentencia a comprar o un character especial para comparación
     * @param false $sentence
     * @return Model
     * @throws \Exception
     */
    public function where($column, $sentenceOrSpecial, $sentence = false)
    {
        if ( $this->query_sql === '' ) $this->all();

        $delimit     = strpos($this->query_sql, 'WHERE') === false ? 'WHERE' : 'AND';
        $placeholder = isset($this->param_to_query[":{$column}"]) ? ":{$column}".random_int(1, 200) : ":{$column}";

        if ( $sentence ) {
            $this->query_sql .= " {$delimit} {$column} {$sentenceOrSpecial} $placeholder";
            $this->parameters["$placeholder"] = $sentence;
        }else {
            $this->query_sql .= " {$delimit} {$column} = $placeholder";
            $this->parameters["$placeholder"] = $sentenceOrSpecial;
        }

        return $this;
    }


    /**
     * Busca por ID
     *
     * @param $id
     * @return array|mixed
     * @throws \Exception
     */
    public function find($id)
    {
        $find = $this->all(true)->where($this->column_id, $id)->exec();

        return isset($find[0])
            ? $find[0]
            : [];
    }


    /**
     * Crea un nuevo registro en la base de datos
     *
     * @param array $params
     * @return $this
     */
    public function create($params = [])
    {
        global $gb_request;
        $params = (array)$params;

        if ( !is_array($params) ) {
            $gb_request->warningApp[] = 'Parámetro pasado no permitido: Método CREATE';
            return $this;
        }

        // Llaves conforme al force de los parámetros o los pasados
        $keys = count($this->force_params) === 0
            ? implode(', ', $this->allowed)
            : implode(', ', array_keys($this->force_params));

        $placeholders = ''; # Llaves de acción
        $count        = 1;  # Conteo que ayuda a saber si es el ultimo elemento
        $insert       = count($this->force_params) === 0 ? $this->allowed : array_keys($this->force_params);
        $params       = count($this->force_params) === 0 ? $params : $this->force_params;


        foreach ( $insert as $key => $param )
        {
            // Parámetros permitidos
            if ( isset($params[$param]) ) {
                $placeholders .= count($insert) === $count ? ":{$param}" : ":{$param}, ";
                $this->parameters[":{$param}"] = $params[$param];
                $count++;
            }
        }


        // Habilita el uso de los timestamps
        if ( $this->useTimestamps ) {
            $keys         .= ", {$this->created_field}, {$this->updated_field}";
            $placeholders .= ", :{$this->created_field}, :{$this->updated_field}";

            $this->parameters[":{$this->created_field}"] = SupportHelper::now();
            $this->parameters[":{$this->updated_field}"] = SupportHelper::now();
        }

        $this->query_sql = "INSERT INTO {$this->table} ({$keys}) VALUES ({$placeholders})";

        return $this;
    }


    /**
     * Actualiza un registro especifico
     *
     * @param $data
     * @param $idOrOther - ID del registro, o una columna en la tabla
     * @param false $optional - Parámetro a buscar de la columna especifica
     * @return $this
     */
    public function update($data, $idOrOther, $optional = false)
    {
        global $gb_request;
        if ( count($data) === 0 ) {
            $gb_request->warningApp[] = 'Imposible actualizar sin datos';
            return $this;
        }

        $sets      = '';
        $count     = 1;
        $updated_f = count($this->force_params) === 0 ? $data          : $this->force_params;
        $allows    = count($this->force_params) === 0 ? $this->allowed : array_keys($this->force_params);

        // Prepara los parámetros y los placeholder
        foreach ( $allows as $allow ) {
            if ( isset($updated_f[$allow]) ) {
                $sets .= count($allows) === $count ? "{$allow}=:{$allow}" : "{$allow}=:{$allow}, ";
                $this->parameters[":{$allow}"] = $updated_f[$allow];
                $count++;
            }
        }

        // Habilita el uso de los timestamps
        if ( $this->useTimestamps ) {
            $sets .= ", {$this->updated_field}=:{$this->updated_field}";
            $this->parameters[":{$this->updated_field}"] = SupportHelper::now();
        }

        // Where
        $where = $optional ? "{$idOrOther}={$optional}" : "{$this->column_id}={$idOrOther}";

        // Sentencia preparada
        $this->query_sql = "UPDATE {$this->table} SET {$sets} WHERE {$where}";
        return $this;
    }


    /**
     * Establece los datos unicamente para la actualización
     *
     * @param array $params
     * @return $this
     */
    public function data(array $params)
    {
        $this->parameters = $params;
        return $this;
    }


    /**
     * Parámetros forzados para usar en las actualizaciones e inserciones
     *
     * @param array $force_params
     * @return Model
     */
    public function forceParams(array $force_params)
    {
        $this->force_params = $force_params;
        return $this;
    }


    /**
     * Elimina un registro de la base de datos
     *
     * @param $idOrOther - ID del registro, o una columna en la tabla
     * @param bool $optional - Parámetro a buscar de la columna especifica
     * @return Model
     */
    public function delete($idOrOther, $optional = false)
    {
        // Where
        $where = $optional ? "{$idOrOther}={$optional}" : "{$this->column_id}={$idOrOther}";

        $this->query_sql = "DELETE FROM {$this->table} WHERE {$where} LIMIT 1";

        return $this;
    }


    /**
     * Realiza un softDelete en la base de datos
     *
     * @param $idOrOther - ID del registro, o una columna en la tabla
     * @param bool $optional - Parámetro a buscar de la columna especifica
     * @return Model|bool
     */
    public function softDelete($idOrOther, $optional = false)
    {
        if ( $this->useSoftDeletes )
        return $this
                ->data([
                    $this->deleted_field => SupportHelper::now()
                ])
                ->update($idOrOther, $optional);

        return false;
    }


    /**
     * Ordena las consultas traídas
     *
     * @param string $order
     * @param string $column
     * @return $this
     */
    public function orderBy($order = "DESC", $column = 'id')
    {
        $column = $this->column_id;
        $this->query_sql .= " ORDER BY {$column} {$order}";
        return $this;
    }


    /**
     * Limita una consulta
     *
     * @param int $limitOrOffset
     * @return $this
     */
    public function limit(int $limitOrOffset = 10, int $count = 0)
    {
        $sentence = $count === 0 ? $limitOrOffset : "{$limitOrOffset}, {$count}";
        $this->query_sql .= " LIMIT {$sentence}";
        return $this;
    }


    /**
     * Búsqueda por LIKE
     *
     * @param $column
     * @param $search
     * @return $this
     * @throws \Exception
     */
    public function like($column, $search)
    {
        return $this->where($column, 'LIKE', "%{$search}%");
    }



    /**
     * Ejecuta la sentencia que se este preparando
     * Se puede establecer si se desea contar los registros devueltos.
     * Si se establece un true devolverá una estructura con el conteo y con los datos.
     *
     * @param bool $count
     * @return array|bool|string
     */
    public function exec()
    {
        if ( $this->table === '' || $this->query_sql === '' ) return false;

        global $gb_request;

        try {
            return $this->query($this->query_sql, $this->parameters);
        }
        catch (\Exception $e) {
            $gb_request->warningApp[] = 'Error al ejecutar Query a la base de datos';
            $gb_request->warningApp[] = 'Sentencia SQL ejecutada: ' . $this->query_sql;
            $gb_request->warningApp[] = $e->getMessage();
        }

        return false;
    }


    /**
     * Traer registros paginados. 
     * Establece si se deben traer registros eliminados
     *
     * @param int $limit
     * @param bool $softDeletes
     */
    public function paginate(int $limit = 10, bool $softDeletes = false)
    {
        global $gb_request;

        $page = isset($gb_request->get->page) ? (int) $gb_request->get->page : 1;

        // Donde iniciaran los registros
        $start = $page > 1 ? ($page * $limit) - $limit : 0;
        
        // Consulta
        $data = $this->all($softDeletes)->limit($start, $limit)->exec();

        // Si no hay registros, mandar un 404
        if ( count($data) === 0 ) return HttpException::not_found_404();

        // Cantidad total de registros en la base de datos
        $total_records = $this->count($softDeletes);

        // Conteo total de paginas
        $count_pages = (int) ceil($total_records / $limit);

        $links  = [];
        $previous = '';
        // Construcción de los Links
        for ($i = 1; $i <= $count_pages ; $i++) { 
            $url = $gb_request->url['current'] . "?page={$i}";

            // Agrega el path de variables a la url de las paginas
            foreach ($gb_request->get as $key => $value) {
                if ( $key !== 'page' ) {
                    $url .= "&{$key}={$value}";
                }
            }

            $links[] = [
                'link'    => $url,
                'current' => $i === $page
            ];
        }

        // Pagina siguiente
        $next = $page + 1 <= $count_pages 
            ? str_replace("page={$count_pages}", "page=".($page+1), $url) 
            : '';

        // Pagina anterior
        $previous = $page !== 1
            ? str_replace("page={$count_pages}", "page=".($page-1), $url) 
            : '';

        return [
            'data'     => $data,
            'paginate' => (object) [
                'count_records'=> $total_records,
                'count_pages'  => $count_pages,
                'links'        => $links,
                'first'        => $links[0]['link'],
                'last'         => $links[count($links)-1]['link'],
                'next'         => $next,
                'previous'     => $previous,
                'current'      => $page
            ]
        ];
    }


    /**
     * Cuenta la cantidad de registros.
     * No requiere ser ejecutado con 'exec'.
     *
     * @param boolean $softDeletes
     * @return Model
     */
    public function count($columnCount = false, bool $softDeletes = false)
    {
        $this->all($softDeletes);
        $columnCount = $columnCount ? $columnCount : $this->column_id;
        $this->query_sql = str_replace($this->visible, "count({$columnCount}) AS count", $this->query_sql);
        return $this->exec()[0]->count;
    }


    /**
     * Devuelve los datos, mas el conteo de datos totales en la base de datos.
     *
     * @param boolean $softDeletes
     * @return Model
     */
    public function countWithData(bool $softDeletes = false)
    {
        return (object)[
            'count' => $this->count(false, $softDeletes),
            'data'  => $this->all($softDeletes)->exec()
        ];
    }


    /**
     * Establece la tabla por fuera del Modelo
     *
     * @param string $table
     * @return $this
     */
    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
    }


    /**
     * Establece el uso de la eliminación logical por fuera del modelo
     *
     * @param bool $useSoftDeletes
     * @return $this
     */
    public function setUseSoftDeletes(bool $useSoftDeletes)
    {
        $this->useSoftDeletes = $useSoftDeletes;
        return $this;
    }


    /**
     * Establece el uso de los Timestamps por fuera del modelo
     *
     * @param string $useTimestamps
     * @return $this
     */
    public function setUseTimestamps(string $useTimestamps)
    {
        $this->useTimestamps = $useTimestamps;
        return $this;
    }



    /**
     * Devuelve el primer elemento encontrado.
     * Si no encuentra un elemento, devolverá false
     *
     * @return $this|bool
     */
    public function first()
    {
        return $this->limit(1)->exec()[0] ?? false;
    }
}