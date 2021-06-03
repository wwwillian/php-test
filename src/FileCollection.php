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
     * @param string $index
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }

        if ($this->collection[$index]['token'] < time()) {
            return null;
        }

        return $this->collection[$index]['text'];
    }

    /**
     * @param string $index
     * @param mixed $value
     * @param int $time
     */
    public function set(string $index, $value, int $time = 1)
    {
        $token = time() + $time;

        $this->collection[$index] = ['text' => $value, 'token' => $token];

        $this->write($this->collection);
    }

    /**
     * @param string $index
     * @return bool
     */
    public function has(string $index)
    {
        if ($this->collection === null) {
            return false;
        }

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

    /**
     * @return void
     */
    public function write(array $collection)
    {
        $fp = fopen($this->path, 'w');
        fwrite($fp, json_encode($collection));
        fclose($fp);
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function read(string $path)
    {
        $fp = fopen($path, 'r');
        $content =  fread($fp, filesize($path));
        $text = "$content";
        fclose($fp);

        return json_decode($text, true);
    }
}
