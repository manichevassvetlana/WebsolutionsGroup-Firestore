<?php

namespace WebsolutionsGroup\Firestore;

use Google\Cloud\Core\Timestamp;

class DocumentSnapshot extends \Google\Cloud\Firestore\DocumentSnapshot implements \Serializable
{
    /**
     * @var DocumentReference
     */
    private $reference;

    /**
     * @var ValueMapper
     */
    private $valueMapper;

    /**
     * @var array
     */
    private $info;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $exists;

    /**
     * @param DocumentReference $reference The document which created the snapshot.
     * @param ValueMapper $valueMapper A Firestore Value Mapper.
     * @param array $info Document information, such as create and update timestamps.
     * @param array $data Document field data.
     * @param bool $exists Whether the document exists in the Firestore database.
     */
    public function __construct(
        DocumentReference $reference = null,
        ValueMapper $valueMapper = null,
        array $info = null,
        array $data = null,
        $exists = null
    )
    {
        $this->reference = $reference;
        $this->valueMapper = $valueMapper;
        $this->info = $info;
        $this->data = $data;
        $this->exists = $exists;

        if(!is_null($this->data)){

            $this->id = $this->id();
            $this->created_at = $this->createTime();
            $this->updated_at = $this->updateTime();

            foreach ($data as $key => $value) {
                $this->$key = $value;
            }

            foreach ($this->appends as $append) {
                $method = $this->getMethodName($append);
                if (method_exists($this, $method)) $this->$append = $this->$method();
            }
        }

        $this->client = new FirestoreClient();

        if(is_null($this->table)) $this->table = $this->getTableName();
        $this->collection = is_null($this->table) ? null : $this->client->collection($this->table);



    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($data)
    {
        $this->data = unserialize($this->data);
    }


    /**
     * Get the reference of the document which created the snapshot.
     *
     * Example:
     * ```
     * $reference = $snapshot->reference();
     * ```
     *
     * @return DocumentReference
     */
    public function reference()
    {
        return $this->reference;
    }

    /**
     * Get the document name.
     *
     * Names are absolute. The result of this call would be of the form
     * `projects/<project-id>/databases/<database-id>/documents/<relative-path>`.
     *
     * Other methods are available to retrieve different parts of a collection name:
     * * {@see Google\Cloud\Firestore\DocumentSnapshot::id()} Returns the last element.
     * * {@see Google\Cloud\Firestore\DocumentSnapshot::path()} Returns the path, relative to the database.
     *
     * Example:
     * ```
     * $name = $snapshot->name();
     * ```
     *
     * @return string
     */
    public function name()
    {
        return !is_null($this->reference) ? $this->reference->name() : '';
    }

    /**
     * Get the document path.
     *
     * Paths identify the location of a document, relative to the database name.
     *
     * To retrieve the document ID (the last element of the path), use
     * {@see Google\Cloud\Firestore\DocumentSnapshot::id()}.
     *
     * Example:
     * ```
     * $path = $snapshot->path();
     * ```
     *
     * @return string
     */
    public function path()
    {
        return !is_null($this->reference) ? $this->reference->path() : '';
    }

    /**
     * Get the document identifier (i.e. the last path element).
     *
     * IDs are the path element which identifies a resource. To retrieve the
     * full path to a resource (the resource name), use
     * {@see Google\Cloud\Firestore\DocumentSnapshot::name()}.
     *
     * Example:
     * ```
     * $id = $snapshot->id();
     * ```
     *
     * @return string
     */
    public function id()
    {
        return !is_null($this->reference) ? $this->reference->id() : '';
    }

    /**
     * Get the Document Update Timestamp.
     *
     * Example:
     * ```
     * $updateTime = $snapshot->updateTime();
     * ```
     *
     * @return Timestamp|null
     */
    public function updateTime()
    {
        return isset($this->info['updateTime'])
            ? $this->info['updateTime']
            : null;
    }

    /**
     * Get the Document Read Timestamp.
     *
     * Example:
     * ```
     * $readTime = $snapshot->readTime();
     * ```
     *
     * @return Timestamp|null
     */
    public function readTime()
    {
        return isset($this->info['readTime'])
            ? $this->info['readTime']
            : null;
    }

    /**
     * Get the Document Create Timestamp.
     *
     * Example:
     * ```
     * $createTime = $snapshot->createTime();
     * ```
     *
     * @return Timestamp|null
     */
    public function createTime()
    {
        return isset($this->info['createTime'])
            ? $this->info['createTime']
            : null;
    }

    /**
     * Returns document data as an array, or null if the document does not exist.
     *
     * Example:
     * ```
     * $data = $snapshot->data();
     * ```
     *
     * @return array|null
     */
    public function data()
    {
        return $this->exists
            ? $this->data
            : null;
    }

    /**
     * Returns true if the document exists in the database.
     *
     * Example:
     * ```
     * if ($snapshot->exists()) {
     *     echo "The document exists!";
     * }
     * ```
     *
     * @return bool
     */
    public function exists()
    {
        return $this->exists;
    }

    /**
     * Get a field by field path.
     *
     * A field path is a string containing the path to a specific field, at the
     * top level or nested, delimited by `.`. For instance, the value `hello` in
     * the structured field `{ "foo" : { "bar" : "hello" }}` would be accessible
     * using a field path of `foo.bar`.
     *
     * Example:
     * ```
     * $value = $snapshot->get('wallet.cryptoCurrency.bitcoin');
     * ```
     *
     * ```
     * // Field names containing dots or symbols can be targeted using a FieldPath instance:
     * use Google\Cloud\Firestore\FieldPath;
     *
     * $value = $snapshot->get(new FieldPath(['wallet', 'cryptoCurrency', 'my.coin']));
     * ```
     *
     * @param string|FieldPath $fieldPath The field path to return.
     * @return mixed
     * @throws \InvalidArgumentException if the field path does not exist.
     */
    public function get($fieldPath)
    {
        $res = null;

        if (!is_null($this->data)) {
            if (is_string($fieldPath)) {
                $parts = explode('.', $fieldPath);
            } elseif ($fieldPath instanceof FieldPath) {
                $parts = $fieldPath->path();
            } else {
                throw new \InvalidArgumentException('Given path was not a string or instance of FieldPath.');
            }

            $len = count($parts);

            $fields = $this->data;
            foreach ($parts as $idx => $part) {
                if ($idx === $len - 1 && isset($fields[$part])) {
                    $res = $fields[$part];
                    break;
                } else {
                    if (!isset($fields[$part])) {
                        throw new \InvalidArgumentException(sprintf(
                            'Field path `%s` does not exist.',
                            $fieldPath
                        ));
                    }

                    $fields = $fields[$part];
                }
            }
        }

        return $res;
    }

    /**
     * @access private
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('DocumentSnapshots are read-only.');
    }

    /**
     * @access private
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @access private
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('DocumentSnapshots are read-only.');
    }

    /**
     * @access private
     */
    public function offsetGet($offset)
    {
        if (is_null($this->data)) return null;
        if (!$this->offsetExists($offset)) {
            trigger_error(sprintf(
                'Undefined index: %s. Document field does not exist.',
                $offset
            ), E_USER_NOTICE);

            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        return $this->data[$offset];

    }

    public function hasMany($collection, $tableField)
    {
        if (!$this->exists) return null;
        $collection = new $collection();
        return $collection->where($tableField, '=', $this->id());
    }

    public function belongsTo($collection, $tableField, $relationField = null)
    {
        if (!$this->exists) return null;
        $collection = new $collection();
        return is_null($relationField) ? $collection->find($this->$tableField) : $collection->where($relationField, $this->$tableField)->first();
    }

    public function morphMany($collection, $referenceField)
    {
        if (!$this->exists) return null;
        $collection = new $collection();
        return $collection->where($referenceField.'_type', '=', get_class($this))->where($referenceField.'_id', '=', $this->id);
    }

    public function getCollectionName()
    {
        if (!$this->exists) return null;
        $reference = $this->reference;
        return $collection = $reference->parent()->id();
    }


    public $searchField = 'name';

    protected $fillable = [];
    protected $appends = [];
    protected $table = null;

    private $collection;
    private $client;


    public function insert(array $fill)
    {
        foreach ($fill as $document => $settable) $this->create($settable);
    }


    public function create($settable)
    {
        if(is_null($this->collection)) return null;
        $fillable = [];
        foreach ($this->fillable as $field) {
            $fillable[$field] = array_key_exists($field, $settable) ? $settable[$field] : null;
        }
        $doc = $this->collection->add($fillable);
        return $doc->snapshot();
    }

    // TODO: OK
    public function removeCollection()
    {
        if(is_null($this->collection)) return null;
        $batchSize = 100;
        $documents = $this->collection->limit($batchSize)->documents();
        while (!$documents->isEmpty()) {
            foreach ($documents as $document) {
                $document->reference()->delete();
            }
            $documents = $this->collection->limit($batchSize)->documents();
        }
    }

    public function update(array $settable)
    {
        if(is_null($this->reference)) return null;
        $fillable = [];
        foreach ($this->fillable as $field) {
            if (array_key_exists($field, $settable)) $fillable[$field] = $settable[$field];
        }
        $doc = $this->reference->set($fillable, ['merge' => true]);
        return $doc;
    }

    public function delete()
    {
        return is_null($this->reference) ? null : $this->reference->delete();
    }

    /*Static functions*/

    public static function paginate($count, $startAt = null)
    {
        $class = get_called_class();
        $class = new $class();
        return is_null($startAt) ? collect($class->collection->limit($count)->documents()) : collect($class->collection->orderBy($class->searchField)->startAt([$startAt])->limit($count)->documents());
    }

    public static function where($field, $operator, $value = null)
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        $class = get_called_class();
        $class = new $class();
        return $class->collection->where($field, $operator, $value);
    }

    public static function orderBy($field, $operator)
    {
        $class = get_called_class();
        $class = new $class();
        return $class->collection->orderBy($field, $operator);
    }

    public static function findOrFail($id)
    {
        $class = get_called_class();
        $doc = (new $class())->document($id);
        if($doc->exists()) return $doc; else throw new \Exception('Document does not exists.');
    }

    public static function find($id)
    {
        if(!$id) return null;
        $class = get_called_class();
        $doc = (new $class())->document($id);
        return $doc->exists() ? $doc :  null;
    }

    public static function all()
    {
        $class = get_called_class();
        $class = new $class();
        return collect($class->collection->documents());
    }

    public static function first()
    {
        $class = get_called_class();
        $class = new $class();
        $doc = collect($class->collection->limit(1)->documents());
        return $doc->count() > 0 ? $doc[0] : null;
    }

    public function select($columns = ['*'])
    {
        return $this->collection->select($columns);
    }

    /*End static functions*/

    /*Private functions*/

    // TODO: OK
    private function getTableName()
    {
        if(strpos(get_class($this), 'Firestore') !== false) return null;
        $shortName = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace("/(?<=[a-zA-Z])(?=[A-Z])/", "_", $shortName));
    }

    private function getMethodName($attr)
    {
        $attr = explode('_', $attr);
        $method = '';
        foreach ($attr as $value) {
            $method .= ucfirst($value);
        }

        return $method = 'get' . $method . 'Attribute';
    }

    protected function document($id)
    {
        return $this->collection->document($id)->snapshot();
    }


    /*End private functions*/


}