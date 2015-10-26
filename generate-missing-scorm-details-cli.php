<?php

// CLI script to identify missing SCORM content files and
// write the details to XML

define('CLI_SCRIPT', 1);

// Run from /admin/cli dir
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

// Where are the SCORM .zip files?
$scormfolder = '/'; // Include trailing slash

// Create arrays to record SCORM file details
$found = array();
$notfound = array();

$writer = new XMLWriter();
$writer->openUri('missing_scorm_content.xml');
$writer->startDocument();
$writer->setIndent(4);
$writer->startElement('resultset');

$scormcontent = simplexml_load_file('scorm_content.xml') or die('wtf');

foreach ($scormcontent->children() as $scormdetails) {

    $courseid = (int) $scormdetails->field['0'];
    $cmid = (int) $scormdetails->field['1'];
    $scormid = (int) $scormdetails->field['2'];
    $coursename = (string) $scormdetails->field['3'];
    $scormname = (string) $scormdetails->field['4'];
    $filename = (string) $scormdetails->field['5'];

    if (!file_exists($scormfolder . $filename)) {

        $notfound[] = $filename;

        $writer->startElement('row');

        // course_id
        $writer->startElement('field');
        $writer->writeAttribute('name', 'course_id');
        $writer->writeCdata($courseid);
        $writer->endElement();

        // course_module_id
        $writer->startElement('field');
        $writer->writeAttribute('name', 'course_module_id');
        $writer->writeCdata($cmid);
        $writer->endElement();

        // scorm_id
        $writer->startElement('field');
        $writer->writeAttribute('name', 'scorm_id');
        $writer->writeCdata($scormid);
        $writer->endElement();

        // course_name
        $writer->startElement('field');
        $writer->writeAttribute('name', 'course_name');
        $writer->writeCdata($coursename);
        $writer->endElement();

        // scorm_module_name
        $writer->startElement('field');
        $writer->writeAttribute('name', 'scorm_module_name');
        $writer->writeCdata($scormname);
        $writer->endElement();

        // scorm_filename
        $writer->startElement('field');
        $writer->writeAttribute('name', 'scorm_filename');
        $writer->writeCdata($filename);
        $writer->endElement();

        $writer->endElement();

        mtrace($filename . ' not found - details written to xml');
    } else {

        $found[] = $filename;
        mtrace($filename . ' found');
    }
}

$writer->endElement();
$writer->endDocument();
$writer->flush();

mtrace(count($found) . ' files were found');
mtrace(count($notfound) . ' files were not found');
