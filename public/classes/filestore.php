<?php

class Filestore {

    public $filename = '';
    public $is_csv = FALSE;


    public function __construct($filename = '') {
        $this->filename = $filename;
        if(substr($this->filename, -3) == "csv") {
        $this->is_csv = TRUE;
        }
    }

    public function read() {
        if ($this->is_csv) {
            return $this->read_csv();
        }   
        else {
            return $this->read_lines();    
        }
    }

    public function write($array) {
        if ($this->is_csv) {
            $this->write_csv($array);
        }   
        else {
            $this->write_lines($array);
        }
    }

    /**
     * Returns array of lines in $this->filename
     */
    private function read_lines() {
        $contents = [];
        if (is_readable($this->filename) && filesize($this->filename) > 0){
            $handle = fopen($this->filename, 'r');
            $bytes = filesize($this->filename);
            $contents = trim(fread($handle, $bytes));
            fclose($handle);
            $contents = explode("\n", $contents);
        }
        return $contents;
    }

    /**
     * Writes each element in $array to a new line in $this->filename
     */
    private function write_lines($array) {
        if (is_writable($this->filename)) {
            $handle = fopen($this->filename, 'w');
            foreach($array as $items) {
            fwrite($handle, PHP_EOL . $items);
            }
        fclose($handle); 
        }   
    }

    /**
     * Reads contents of csv $this->filename, returns an array
     */
    private function read_csv(){
        $contents = [];
        if (is_readable($this->filename) && filesize($this->filename) > 0){
            $handle = fopen($this->filename, 'r');
            while(!feof($handle)) {
                $row = fgetcsv($handle);
                if (is_array($row)) {
                $contents[] = $row;
                }
            }
            fclose($handle);
        }
        return $contents;
    }

    /**
     * Writes contents of $array to csv $this->filename
     */
    private function write_csv($array) {
        $handle = fopen($this->filename, 'w');
        foreach ($array as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle); 
    }

}