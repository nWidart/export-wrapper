<?php namespace nwidart\ExportWrapper;


use Exporter\Handler;
use Exporter\Source\ArraySourceIterator;

class Exporter
{
    /**
     * Contains the file extension
     * set in to()
     * @var String
     */
    protected $format;
    /**
     * Contains the file name and file extension
     * In case of a full export: also the full file path
     * set in to()
     * @var
     */
    protected $fileName;
    /**
     * The content type
     * Using $this->format to generate it
     * set in to()
     * @var String
     */
    protected $content_type;

    /**
     * The data the export
     * @var Array Data
     */
    protected $data;

    /**
     * @var
     */
    protected $export_to;

    /**
     * @var \Exporter\Source\ArraySourceIterator Object
     */
    protected $exporter_source;

    /**
     * @var
     */
    protected $exporter_writer;

    /**
     * @param String $sExportType
     * @return $this
     */
    public function to($sExportType)
    {
        $this->format = pathinfo($sExportType, PATHINFO_EXTENSION);
        $this->fileName = $sExportType;
        $this->content_type = 'text/' . $this->format;

        return $this;
    }

    /**
     * The data to export
     * @param Array $data
     * @return $this
     */
    public function with($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Streams the given content
     */
    public function stream()
    {
        // 0. set the export_to to php special output
        $this->export_to = 'php://output';
        // 1. Se the export source
        $this->exporter_source = new ArraySourceIterator($this->data);

        // Get an Instance of the Writer
        $this->exporter_writer = '\Exporter\Writer\\' . ucfirst($this->format) . 'Writer';

        $this->exporter_writer = new $this->exporter_writer($this->export_to);

        // Set the right headers
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-type: ' . $this->content_type);
        header('Content-Disposition: attachment; filename=' . $this->fileName . ';');
        header('Expires: 0');
        header('Pragma: public');

        // Export to the format
        Handler::create($this->exporter_source, $this->exporter_writer)->export();
    }

    public function export()
    {
        // Data to export
        $this->exporter_source = new ArraySourceIterator($this->data);

        // Get an Instance of the Writer
        $this->exporter_writer = '\Exporter\Writer\\' . ucfirst($this->format) . 'Writer';

        $this->exporter_writer = new $this->exporter_writer($this->fileName);

        // Export to the format
        Handler::create($this->exporter_source, $this->exporter_writer)->export();
    }
}