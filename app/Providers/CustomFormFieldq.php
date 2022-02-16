<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class SelectMultipleCustomFormFieldq extends AbstractHandler
{
    protected $codename = 'select_multiple_custom';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.number', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}