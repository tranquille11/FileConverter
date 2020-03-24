<?php

class Converter
{

    private $path;
    private $separator;
    private $header = [];
    private $data;
    private $file;

    public function __construct(string $path, string $separator = "\s")
    {
        $this->path = $path;
        $this->separator = $separator;
    }

    public function convert()
    {

        $sizes = file($this->path);
        $arr = [];
        $data = [];
        for ($i = 0; $i < count($sizes); $i++) {
            $arr[$i] = preg_split('/' . $this->separator . '+/', $sizes[$i]);
        }

        for ($j = 0; $j < count($arr); $j++) {
            $data[] = $arr[$j];
        }

        $newData = [];

        foreach ($data as $item) {
            $obj = [];
            foreach ($item as $k => $v) {
                if (preg_match('/^"|"$/', $v) === 1){
                    $v = preg_replace('/"/', '', $v);
                }
                if (empty($this->header) && trim($v)) {
                    $obj[$k] = $v;
                } elseif (trim($this->header[$k])) {
                    $obj[$this->header[$k]] = $v;
                }
            }
            $newData[] = $obj;
        }
        $this->data = $newData;

        return $this;
    }

    /**
     * Separator refers to the syntax that separates values within the document;
     * Ex: "," -> separator for CSV files
     * Create new file using the existing data + new custom separator
     * @param $separator
     */
    public function createFile(string $separator)
    {
        if (file_exists($this->file)) {
            try {
                throw new Exception('WARNING: File already exists');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        $objData = $this->convert()->getData();

        $newFile = fopen($this->file, 'w');

        if ($newFile) {
            foreach ($objData as $entry) {
                foreach ($entry as $value) {
                    try {
                        fwrite($newFile, $value . $separator);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
            try {
                fwrite($newFile, "\n");
            } catch (Exception $e) {
                echo $e->getMessage();
            }

            fclose($newFile);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Number of values on the header must match number of values on each row of the file
     * @param array $headerLine
     * @return Converter
     */
    public function setHeader(array $headerLine)
    {
        foreach ($headerLine as $entry) {
            $this->header [] = $entry;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader():array
    {
        return $this->header;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setPath(string $file)
    {
        $this->file = $file;

        return $this;
    }

}
