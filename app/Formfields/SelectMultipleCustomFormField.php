<?php

namespace App\Formfields;

use TCG\Voyager\FormFields\AbstractHandler;

class SelectMultipleCustomFormField extends AbstractHandler
{
    protected $codename = 'select_multiple_custom';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('vendor/voyager/formfields/select_multiple', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}