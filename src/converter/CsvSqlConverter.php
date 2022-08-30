<?php

namespace AndreyPechennikov\TaskForce\converter;

use AndreyPechennikov\TaskForce\exception\ConverterException;
use DirectoryIterator;
use SplFileInfo;
use SplFileObject;

class CsvSqlConverter
{
    protected array $filesToConvert = [];

    /**
     * @throws ConverterException
     */
    public function __construct(string $directory)
    {
        if (!is_dir($directory))
        {
            throw new ConverterException('Указанная директория не существует');
        }

        $this->loadCsvFiles($directory);
    }

    /**
     * @throws ConverterException
     */
    public function convertFiles(string $outputDirectory): array
    {
        $result = [];

        foreach ($this->filesToConvert as $file) {
            $result[] = $this->convertFile($file, $outputDirectory);
        }

        return $result;
    }

    /**
     * @throws ConverterException
     */
    protected function convertFile(SplFileInfo $file, $outputDirectory): string
    {
        $fileObject = new SplFileObject($file->getRealPath());
        $fileObject->setFlags(SplFileObject::READ_CSV);

        $columns = $fileObject->fgetcsv();
        $values = [];

        while (!$fileObject->eof())
        {
            $values[] = $fileObject->fgetcsv();
        }

        $tableName = $file->getBasename('.csv');
        $sqlContent = $this->getSqlContent($tableName, $columns, $values);

        return $this->saveSqlContent($tableName, $outputDirectory, $sqlContent);
    }

    protected function getSqlContent(string $tableName, array $columns, array $values): string
    {
        $columnsString = implode(', ', $columns);
        $sql = "INSERT INTO $tableName ($columnsString) VALUES";

        foreach ($values as $value) {
            array_walk($value, function (&$value)
            {
                $value = addcslashes($value);
                $value = "'$value'";
            });

            $sql .= '( ' .implode(', ', $value) . '), ';
        }

        return substr($sql, 0, -2);
    }

    /**
     * @throws ConverterException
     */
    protected function saveSqlContent(string $tableName, $directory, string $content): string
    {
        if (!is_dir($directory)) {
            throw new ConverterException('Директории для сохранения не существует');
        }

        $filename = $directory . DIRECTORY_SEPARATOR . $tableName . '.sql';

        file_put_contents($filename, $content);

        return $filename;
    }

    protected function loadCsvFiles(string $directory): void
    {
        foreach (new DirectoryIterator($directory) as $file) {
            if ($file->getExtension() == 'csv')
            {
                $this->filesToConvert[] = $file->getFileInfo();
            }
        }
    }
}
