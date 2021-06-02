<?php

namespace Live\Collection;

class FileCollection implements CollectionInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $collection;

    /**
     * FileCollection constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->collection = file_exists($path) ? $this->read($path) : [];
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function read(string $path)
    {
        $fp = fopen($path, 'r');
        $content =  fread($fp, filesize($path));
        fclose($fp);

        return json_decode($content, true);
    }

    /**
     * @param array $data
     */
    public function write(array $data)
    {
        $fp = fopen($this->path, 'a+');
        fwrite($fp, json_encode($data));
        fclose($fp);
    }

    /**
     * @param string $index
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }
        return $this->collection[$index];
    }

    /**
     * @param string $index
     * @param mixed $value
     */
    public function set(string $index, $value)
    {
        $this->collection[$index] = [$value];

        $this->write([$value]);
    }

    /**
     * @param string $index
     * @return bool
     */
    public function has(string $index)
    {
        return array_key_exists($index, $this->collection);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->collection = [];
    }
}
